<?php

namespace x13allegro\SyncManager\Order;

use x13allegro\Adapter\HookAdapter;
use x13allegro\Adapter\Module\x13freeorderconfirmationAdapter;
use x13allegro\Adapter\Module\x13orderindexAdapter;
use x13allegro\Adapter\Module\x13orderindexproAdapter;
use x13allegro\Adapter\Module\x13paragonlubfakturaAdapter;
use x13allegro\Adapter\Module\ganalyticsAdapter;
use x13allegro\Adapter\Module\mailalertsAdapter;
use x13allegro\Api\Model\Order\Enum\PaymentType;
use x13allegro\Component\Logger\Log;
use x13allegro\Component\Logger\LogType;
use x13allegro\SyncManager\Order\Data\Model\EmptyDelivery;
use x13allegro\SyncManager\Order\Data\Model\LineItem\LineItemCollection;
use x13allegro\SyncManager\Order\Data\Model\LineItem\LineItemInterface;
use x13allegro\SyncManager\Order\Data\Model\AssociationType;
use x13allegro\SyncManager\Order\Data\Provider\LineItemProvider;
use x13allegro\SyncManager\Order\Data\Provider\StockAvailableProvider;
use x13allegro\SyncManager\Order\Data\Provider\OrderProvider;
use x13allegro\SyncManager\Order\Data\Factory\ContextFactory;
use x13allegro\SyncManager\Order\Data\Factory\CustomerFactory;
use x13allegro\SyncManager\Order\Data\Factory\CartFactory;
use x13allegro\SyncManager\Order\Data\Factory\OrderMessageFactory;
use x13allegro\SyncManager\Order\Data\Factory\OrderFactory;
use x13allegro\SyncManager\Order\Data\Factory\OrderCarrierFactory;
use x13allegro\SyncManager\Order\Data\Factory\OrderHistoryFactory;
use x13allegro\SyncManager\Order\Data\Factory\OrderPaymentFactory;
use x13allegro\SyncManager\Order\Exception\NoItemsException;
use XAllegroConfiguration;
use XAllegroAccount;
use XAllegroAuction;
use XAllegroOrder;
use XAllegroStatus;
use Context;
use Configuration;
use Hook;
use Shop;
use Order;
use OrderState;
use OrderPayment;

final class OrderController
{
    /** @var Context */
    private $context;

    /** @var XAllegroOrder */
    private $allegroOrder;

    /** @var XAllegroAccount */
    private $allegroAccount;

    /** @var Order */
    private $order;

    /** @var \StdClass */
    private $checkoutForm;

    /** @var LineItemCollection */
    private $lineItemCollection;

    /**
     * @param XAllegroOrder $allegroOrder
     * @param XAllegroAccount $allegroAccount
     * @param \StdClass $checkoutForm
     */
    public function __construct(XAllegroOrder $allegroOrder, XAllegroAccount $allegroAccount, \StdClass $checkoutForm)
    {
        $this->allegroOrder = $allegroOrder;
        $this->allegroAccount = $allegroAccount;
        $this->checkoutForm = $checkoutForm;

        // when checkoutForm.delivery is null
        // this could happen when order is CANCELLED and event is on FILLED_IN status
        if ($this->checkoutForm->delivery === null) {
            $this->checkoutForm->delivery = new EmptyDelivery();
        }

        if ($this->checkoutForm->marketplace->id === 'allegro-business-pl') {
            $this->checkoutForm->marketplace->id = 'allegro-pl';
        }

        // !!! TEMPORARY FIX
        if ($this->checkoutForm->marketplace->id === 'allegro-business-cz') {
            $this->checkoutForm->marketplace->id = 'allegro-cz';
        }

        $this->context = Context::getContext();

        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            $serviceContainer = \PrestaShop\PrestaShop\Adapter\ContainerBuilder::getContainer(
                'front',
                _PS_MODE_DEV_
            );
            $this->context->container = $serviceContainer;
        }
    }

    /**
     * @uses bought()
     * @uses filledIn()
     * @uses readyForProcessing()
     * @uses revertPurchase()
     * @uses cancelled()
     * @uses fulfilmentStatusChanged()
     *
     * @param string|null $action
     * @return XAllegroOrder
     */
    public function execute($action = null)
    {
        Log::instance()->info(LogType::ORDER_PROCESS_INITIALIZE(), [
            'id' => $this->checkoutForm->id,
            'status' => $this->checkoutForm->status,
            'marketplace' => $this->checkoutForm->marketplace->id
        ]);

        // initialize Order
        $this->order = new Order($this->allegroOrder->id_order);

        // initialize Context
        $this->context = (new ContextFactory($this->context, $this->checkoutForm, $this->order))->build();

        // initialize items list
        $lineItemProvider = new LineItemProvider(
            (bool)XAllegroConfiguration::get('ORDER_IMPORT_UNASSOC_PRODUCTS'),
            (int)XAllegroConfiguration::get('EMPTY_PRODUCT_ID'),
            $this->context->language->id
        );

        $this->lineItemCollection = $lineItemProvider->getLineItemCollection($this->checkoutForm->lineItems);

        if (!empty($this->lineItemCollection->itemsNotMapped)) {
            Log::instance()->info(LogType::ASSOCIATION_NOT_FOUND(), $this->lineItemCollection->itemsNotMapped);
        }

        // if Order exists
        if ($this->order instanceof Order && $this->order->id) {
            Log::instance()->info(LogType::ORDER_EXISTS(), ['id' => $this->order->id]);

            // fix for GDPR modules
            if (!$this->context->customer->id) {
                $this->context->customer = (new CustomerFactory($this->checkoutForm->buyer, $this->context))->build();
            }

            // fix for MyPresta.eu dboptimization module
            if (!$this->context->cart->id) {
                $this->context->cart = (new CartFactory(
                    $lineItemProvider->isSimulateUnassociatedItems()
                        ? $this->lineItemCollection->items
                        : $this->lineItemCollection->getAssociatedItems(),
                    $this->context))->build();
            }

            $orderProvider = new OrderProvider($this->order, $this->context);

            // fix for GDPR modules
            if ($this->order->id_customer != $this->context->customer->id) {
                $orderProvider->updateCustomer($this->context->customer->id);
            }

            // fix for MyPresta.eu dboptimization module
            if ($this->order->id_cart != $this->context->cart->id) {
                $orderProvider->updateCart($this->context->cart->id);
            }

            // change delivery Address if needed
            if ($this->checkoutForm->delivery->address) {
                $orderProvider->updateAddressDelivery($this->checkoutForm->delivery->address);
            }

            // change invoice Address if needed
            if ($this->checkoutForm->invoice->required && $this->checkoutForm->invoice->address) {
                $orderProvider->updateAddressInvoice($this->checkoutForm->invoice->address);
            }
            else if (!$this->checkoutForm->invoice->required && $this->checkoutForm->delivery->address) {
                $orderProvider->updateAddressInvoice($this->checkoutForm->delivery->address);
            }

            // change Order payment name if needed
            if ($this->checkoutForm->payment->type != $this->allegroOrder->checkout_form_content->payment->type
                || $this->checkoutForm->payment->provider != $this->allegroOrder->checkout_form_content->payment->provider
            ) {
                $orderProvider->updatePaymentName($this->checkoutForm->payment);
            }

            // change OrderCarrier or shipping cost if needed
            if ($this->checkoutForm->delivery->method->id !== $this->allegroOrder->delivery_method
                || (float)$this->checkoutForm->delivery->cost->amount !== (float)$this->allegroOrder->checkout_form_content->delivery->cost->amount
            ) {

                if (!empty($this->checkoutForm->delivery)) {
                    $orderProvider->updateCarrier($this->checkoutForm->delivery, $this->allegroAccount->id);
                }

                $orderProvider->updateTotalToPay($this->checkoutForm->summary->totalToPay->amount);
            }

            // update OrderMessage if needed
            (new OrderMessageFactory($this->order, $this->allegroAccount, $this->lineItemCollection, $this->checkoutForm, $this->allegroOrder->checkout_form_content))->build();

            // update x13rio if needed
            if ($this->checkoutForm->invoice->required !== $this->allegroOrder->checkout_form_content->invoice->required) {
                (new x13paragonlubfakturaAdapter())->addReceiptOrInvoice($this->context->cart, $this->checkoutForm->invoice->required);
            }

            // reload Order after changes
            $this->order = new Order($this->order->id);

            // update Cart addresses
            if ($this->context->cart->id_address_delivery != $this->order->id_address_delivery
                || $this->context->cart->id_address_invoice != $this->order->id_address_invoice
            ) {
                $this->context->cart->id_address_delivery = $this->order->id_address_delivery;
                $this->context->cart->id_address_invoice = $this->order->id_address_invoice;
                $this->context->cart->save();
            }

            Hook::exec('actionX13AllegroOrderUpdateAfter', [
                'id_xallegro_account' => $this->allegroAccount->id,
                'order' => $this->order,
                'allegroCheckoutForm' => $this->checkoutForm
            ]);
        }
        // if Order doesn't exists
        // check that we can create new one
        else if ((bool)XAllegroConfiguration::get('IMPORT_ORDERS')) {
            // check for lineItems in previous orders
            // if they exists we need to cancel this orders -- joined payment
            // old checkoutForms will be deleted by Allegro
            $ordersToClose = [];

            foreach ($this->lineItemCollection->items as $item) {
                if ($orderId = XAllegroOrder::getPreviousOrderByLineItemId($item->id)) {
                    $ordersToClose[$item->id] = $orderId;
                }
            }

            if (!empty($ordersToClose)) {
                Log::instance()->info(LogType::ORDER_PREVIOUS_CLOSE(), $ordersToClose);

                foreach (array_unique($ordersToClose) as $orderToClose) {
                    (new OrderHistoryFactory(new Order($orderToClose), new OrderState(Configuration::get('PS_OS_CANCELED'), $this->context->language->id)))->build();
                }
            }

            // create new Order
            $this->createOrder($lineItemProvider->isSimulateUnassociatedItems()
                ? $this->lineItemCollection->items
                : $this->lineItemCollection->getAssociatedItems()
            );
        }

        // fill XAllegroOrder data before execute action
        // then save XAllegroOrder
        $this->buildAllegroOrder();
        $this->allegroOrder->save();

        // execute event action
        if ($action !== null && method_exists($this, $action)) {
            $this->$action();

            Hook::exec('actionX13AllegroOrderAction' . ucfirst($action), [
                'id_xallegro_account' => $this->allegroAccount->id,
                'order' => $this->order,
                'allegroCheckoutForm' => $this->checkoutForm
            ]);
        }

        return $this->allegroOrder;
    }

    private function createOrder(array $associatedLineItems)
    {
        if (empty($associatedLineItems)) {
            throw new NoItemsException("No items");
        }

        // initialize Shop by first product from list
        reset($associatedLineItems);
        $this->context->shop = new Shop(current($associatedLineItems)->association->allegroAuction->id_shop);

        // initialize Customer
        $this->context->customer = (new CustomerFactory($this->checkoutForm->buyer, $this->context))->build();

        // initialize & add products to Cart
        $this->context->cart = (new CartFactory($associatedLineItems, $this->context))->build();

        // initialize Order using:
        // CustomerAddressFactory
        // OrderDetailFactory
        $this->order = (new OrderFactory($this->checkoutForm, $associatedLineItems, $this->allegroAccount, $this->context))->build();

        // generate Order document
        // generate Order reference
        (new x13paragonlubfakturaAdapter())->addReceiptOrInvoice($this->context->cart, $this->checkoutForm->invoice->required);
        $this->order = (new x13OrderindexAdapter())->generateReference($this->order, $this->allegroAccount->id);
        $this->order = (new x13OrderindexproAdapter())->generateReference($this->order, $this->allegroAccount->id);

        // update Cart addresses
        $this->context->cart->id_address_delivery = $this->order->id_address_delivery;
        $this->context->cart->id_address_invoice = $this->order->id_address_invoice;
        $this->context->cart->save();

        // initialize OrderMessage
        (new OrderMessageFactory($this->order, $this->allegroAccount, $this->lineItemCollection, $this->checkoutForm))->build();

        // initialize OrderCarrier
        (new OrderCarrierFactory($this->order))->build();

        // initialize first OrderState
        $orderStatus = XAllegroStatus::FILLED_IN()->getOrderState($this->checkoutForm->marketplace->id, $this->context->language->id);

        // Hook validate order
        try {
            $excludedModules = [];
            $excludedModules[] = (new mailalertsAdapter())->getModuleId();
            $excludedModules[] = (new x13freeorderconfirmationAdapter())->getModuleId();

            HookAdapter::execOverride('actionValidateOrder', [
                'cart' => $this->context->cart,
                'order' => $this->order,
                'customer' => $this->context->customer,
                'currency' => $this->context->currency,
                'orderStatus' => $orderStatus,
                'allegroCheckoutForm' => $this->checkoutForm
            ], $excludedModules, 2);

            Log::instance()->info(LogType::HOOK_ACTION_VALIDATE_ORDER());
        }
        catch (\Exception $ex) {
            Log::instance()->exception($ex);
        }

        // initialize OrderHistory -- change OrderState
        (new OrderHistoryFactory($this->order, $orderStatus))->build();

        // reload Order after OrderState change
        $this->order = new Order($this->order->id);

        // Stock Management
        if (XAllegroConfiguration::get('QUANITY_SHOP_UPDATE')) {
            /** @var LineItemInterface $lineItem */
            foreach ($associatedLineItems as $lineItem) {
                if (Configuration::get('PS_STOCK_MANAGEMENT', null, $lineItem->association->allegroAuction->id_shop_group, $lineItem->association->allegroAuction->id_shop)
                    && $lineItem->association->type->equals(AssociationType::RELATED_PRODUCT())
                ) {
                    $stockAvailableProvider = new StockAvailableProvider(new Shop($lineItem->association->allegroAuction->id_shop));
                    $stockAvailableProvider->updateStock($lineItem);

                    XAllegroAuction::updateAuctionQuantity(max($lineItem->association->allegroAuction->quantity - $lineItem->quantity, 0), $lineItem->offer->id);
                }
            }
        }

        // external modules adapters
        (new ganalyticsAdapter())->addGASent($this->order);

        Hook::exec('actionX13AllegroOrderCreateAfter', [
            'id_xallegro_account' => $this->allegroAccount->id,
            'order' => $this->order,
            'allegroCheckoutForm' => $this->checkoutForm
        ]);
    }

    private function bought()
    {
        // this action is triggered when we don't import orders, but update stock in Shop

        foreach ($this->lineItemCollection->getAssociatedItems() as $lineItem) {
            if (Configuration::get('PS_STOCK_MANAGEMENT', null, $lineItem->association->allegroAuction->id_shop_group, $lineItem->association->allegroAuction->id_shop)
                && $lineItem->association->type->equals(AssociationType::RELATED_PRODUCT())
            ) {
                $stockAvailableProvider = new StockAvailableProvider(new Shop($lineItem->association->allegroAuction->id_shop));
                $stockAvailableProvider->updateStock($lineItem);

                XAllegroAuction::updateAuctionQuantity(max($lineItem->association->allegroAuction->quantity - $lineItem->quantity, 0), $lineItem->offer->id);
            }
        }
    }

    private function revertPurchase()
    {
        // this action is triggered when we don't import orders, but update stock in Shop

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItemCollection->getAssociatedItems() as $lineItem) {
            if (Configuration::get('PS_STOCK_MANAGEMENT', null, $lineItem->association->allegroAuction->id_shop_group, $lineItem->association->allegroAuction->id_shop)
                && $lineItem->association->type->equals(AssociationType::RELATED_PRODUCT())
            ) {
                $stockAvailableProvider = new StockAvailableProvider(new Shop($lineItem->association->allegroAuction->id_shop));
                $stockAvailableProvider->updateStock($lineItem, 'up');

                XAllegroAuction::updateAuctionQuantity($lineItem->association->allegroAuction->quantity + $lineItem->quantity, $lineItem->offer->id);
            }
        }
    }

    private function cancelled()
    {
        (new OrderHistoryFactory($this->order, new OrderState(Configuration::get('PS_OS_CANCELED'), $this->order->id_lang)))->build();
    }

    private function filledIn()
    {
        // when checkoutForm.payment is null
        // this could happen when order is CANCELLED and event is on FILLED_IN status
        if ($this->checkoutForm->payment === null) {
            return;
        }

        // -- payment failed / or awaiting
        // payment.finishedAt is empty
        // payment.paidAmount = null
        if (!$this->checkoutForm->payment->finishedAt && !$this->checkoutForm->payment->paidAmount) {
            (new OrderHistoryFactory($this->order, XAllegroStatus::FILLED_IN()->getOrderState($this->checkoutForm->marketplace->id, $this->context->language->id)))->build();
        }

        // -- payment canceled
        // payment.finishedAt is filled
        // payment.paidAmount = null
        if ($this->checkoutForm->payment->finishedAt && !$this->checkoutForm->payment->paidAmount) {
            (new OrderHistoryFactory($this->order, XAllegroStatus::CANCELLED()->getOrderState($this->checkoutForm->marketplace->id, $this->context->language->id)))->build();
        }
    }

    private function readyForProcessing()
    {
        // Supported payment scheme
        //
        // payment.type        payment.provider
        // ------------------- ------------------- ---------------------------------------------------------------------
        // ONLINE              > P24, PAYU         > paidAmount = null (before payment) / paidAmount = xxx.xx (after completed payment)
        // EXTENDED_TERM       > EPT               > paidAmount = null (before payment) / paidAmount = xxx.xx (after completed payment)
        // CASH_ON_DELIVERY    > null              > paidAmount = null (outside Allegro)
        // SPLIT_PAYMENT       > OFFLINE           > paidAmount = null (outside Allegro)

        $orderPayments = [];
        foreach (OrderPayment::getByOrderReference($this->order->reference) as $payment) {
            $orderPayments[] = new \DateTime($payment->date_add);
        }

        // add first OrderPayment
        if (empty($orderPayments)) {
            $orderPaymentFactory = new OrderPaymentFactory(
                $this->order,
                $this->checkoutForm->payment,
                $this->checkoutForm->summary,
                (bool)XAllegroConfiguration::get('ORDER_ADD_PAYMENT_WHEN_COD')
            );

            if ($orderPaymentFactory->build()) {
                Log::instance()->info(LogType::ORDER_PAYMENT_CREATE(), [
                    'payment.id' => $this->checkoutForm->payment->id,
                    'payment.type' => $this->checkoutForm->payment->type,
                    'payment.provider' => $this->checkoutForm->payment->provider
                ]);

                $orderPayments[] = $orderPaymentFactory->getPaymentDate();
            }
        }

        // check for surcharges
        if (in_array($this->checkoutForm->payment->type, [PaymentType::ONLINE, PaymentType::EXTENDED_TERM])
            && !empty($this->checkoutForm->surcharges)
        ) {
            foreach ($this->checkoutForm->surcharges as $surcharge) {
                // check if surcharge is finished
                // and if paidAmount is not null
                if ($surcharge->finishedAt && $surcharge->paidAmount !== null) {
                    // if there is any payment or surcharge
                    $orderPaymentLast = end($orderPayments);
                    if ($orderPaymentLast) {
                        $orderPaymentFactory = new OrderPaymentFactory($this->order, $surcharge);
                        $surchargeDate = $orderPaymentFactory->getPaymentDate();
                        $paymentDateLast = $orderPaymentLast;

                        // check if surcharge is newer than last OrderPayment
                        if ($surchargeDate > $paymentDateLast && $orderPaymentFactory->build()) {
                            Log::instance()->info(LogType::ORDER_PAYMENT_CREATE(), [
                                'payment.id' => $surcharge->id,
                                'payment.type' => $surcharge->type,
                                'payment.provider' => $surcharge->provider,
                                'isSurcharge' => true
                            ]);

                            $orderPayments[] = $surchargeDate;
                        }
                    }
                }
            }
        }

        // change OrderState
        switch ($this->checkoutForm->payment->type) {
            case PaymentType::CASH_ON_DELIVERY:
                $status = XAllegroStatus::CASH_ON_DELIVERY();
                break;

            case PaymentType::SPLIT_PAYMENT:
                $status = XAllegroStatus::SPLIT_PAYMENT();
                break;

            // ONLINE
            // EXTENDED_TERM
            // after surcharges
            default:
                if (!XAllegroConfiguration::get('ORDER_IMPORT_UNASSOC_PRODUCTS')
                    && XAllegroConfiguration::get('ORDER_IMPORT_UNASSOC_SUMMARY')
                ) {
                    $totalToPay = $this->checkoutForm->summary->totalToPay->amount;
                } else {
                    $totalToPay = $this->order->total_paid;
                }

                if ($this->order->total_paid_real < (float)$totalToPay) {
                    $status = XAllegroStatus::UNDERPAYMENT();
                } else if ($this->order->total_paid_real > (float)$totalToPay) {
                    $status = XAllegroStatus::OVERPAYMENT();
                } else {
                    $status = XAllegroStatus::COMPLETED();
                }
        }

        (new OrderHistoryFactory($this->order, $status->getOrderState($this->checkoutForm->marketplace->id, $this->context->language->id), true))->build();
    }

    private function fulfilmentStatusChanged()
    {
        Log::instance()->info(LogType::ORDER_FULFILLMENT_STATUS_CHANGE(), ['status' => $this->checkoutForm->fulfillment->status]);
    }

    private function buildAllegroOrder()
    {
        // backward compatibility with versions older than 6.7.0
        $checkoutFormContent = $this->checkoutForm;
        $checkoutFormContent->lineItems = $this->lineItemCollection->getCollectionToDatabase();

        $this->allegroOrder->checkout_form_content = $checkoutFormContent;
        $this->allegroOrder->id_order = $this->order->id;
        $this->allegroOrder->id_xallegro_account = $this->allegroAccount->id;
        $this->allegroOrder->marketplace = $this->checkoutForm->marketplace->id;
        $this->allegroOrder->checkout_form = $this->checkoutForm->id;
        $this->allegroOrder->fulfillment_status = $this->checkoutForm->fulfillment->status;
        $this->allegroOrder->delivery_method = $this->checkoutForm->delivery->method->id;
    }
}
