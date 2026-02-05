<?php
/**
 * QuotationCartsController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationCartsController
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationCartsController.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationCartsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotation_carts/';
        $this->tpl_folder = 'quotation_carts/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_request';
        $this->identifier = 'id_roja45_quotation_request';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'QuotationRequest';
        $this->action = 'QuotationRequest';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;

        $this->explicitSelect = false;
        $this->addRowAction('updateEnabled');
        if (!Tools::getValue('id_roja45_quotation_request')) {
            $this->multishop_context_group = false;
        }
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );
        $this->_defaultOrderBy = 'id_roja45_quotation_request';
        $this->deleted = false;
        $this->_orderBy = null;
        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationCarts'),
                'confirm' => $this->l('Delete selected items?', 'QuotationCarts'),
                'icon' => 'icon-check',
            )
        );

        $this->fields_list = array(
            'id_roja45_quotation_request' => array(
                'title' => $this->l('Id', 'QuotationCarts'),
                'align' => 'text-center',
                'class' => 'fixed-width-sm',
            ),
            'id_roja45_quotation' => array(
                'title' => $this->l('Quotation Id', 'QuotationCarts'),
                'width' => 'auto',
            ),
            'reference' => array(
                'title' => $this->l('Reference', 'QuotationCarts'),
                'width' => 'auto',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer', 'QuotationCarts'),
                'width' => 'auto',
            ),
            'num_products' => array(
                'title' => $this->l('# Products', 'QuotationCarts'),
                'align' => 'text-center',
            ),
            'requested_text' => array(
                'title' => $this->l('Requested', 'QuotationCarts'),
                'width' => 'auto',
                'color' => 'requested_color',
                'tmpTableFilter' => true,
                'orderby' => false,
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
                'orderby' => false,
                'search' => false,
            ),
            'abandoned_text' => array(
                'title' => $this->l('Abandoned', 'QuotationCarts'),
                'width' => 'auto',
                'color' => 'abandoned_color',
                'tmpTableFilter' => true,
                'orderby' => false,
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
                'orderby' => false,
                'search' => false,
            ),
            'date_add' => array(
                'title' => $this->l('Date', 'QuotationCarts'),
                'width' => 'auto',
                'orderby' => true,
                'havingFilter' => true,
                'type' => 'datetime',
            ),
        );

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shop_name'] = array(
                'title' => $this->l('Default shop', 'QuotationCarts'),
            );
        }

        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationCarts')
        );
    }

    public function init()
    {
        if (Tools::isSubmit('add' . $this->table)) {
            $this->display = 'add';
        } elseif (Tools::isSubmit('update' . $this->table)) {
            $this->display = 'edit';
        }
        return parent::init();
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        $id_lang_shop = true;
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
        foreach ($this->_list as &$list_item) {
            $request = new QuotationRequest($list_item['id_roja45_quotation_request']);
            $list_item['num_products'] = count($request->getProducts());
            $id_roja45_quotation = RojaQuotation::getQuotationForRequest(
                $list_item['id_roja45_quotation_request']
            );
            if ($id_roja45_quotation) {
                $list_item['id_roja45_quotation'] = $id_roja45_quotation;
            } else {
                $list_item['id_roja45_quotation'] = '--';
            }

            if ($list_item['id_customer']) {
                $customer = new Customer($list_item['id_customer']);
                $list_item['customer_name'] = $customer->firstname . ' ' . $customer->lastname;
            } else {
                $list_item['customer_name'] = '--';
            }
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                $shop = new Shop($list_item['id_shop']);
                $list_item['shop_name'] = $shop->name;
            }

            if ($list_item['requested'] > 0) {
                $list_item['requested_text'] = 'YES';
                $list_item['requested_color'] = '#32CD32';
            } else {
                $list_item['requested_text'] = 'NO';
                $list_item['requested_color'] = '#FF0000';
            }
            if ($list_item['abandoned'] > 0) {
                $list_item['abandoned_text'] = 'YES';
                $list_item['abandoned_color'] = '#FF0000';
            } else {
                $list_item['abandoned_text'] = '--';
                $list_item['abandoned_color'] = '#FFFFFF';
            }
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ?
            array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        /** @var QuotationRequest $quotation_request */
        $quotation_request = $this->loadObject(true);

        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->l('Edit Quotation Cart: ', 'QuotationCarts') . $quotation_request->id;
                break;
            case 'view':
                $this->toolbar_title[] = $this->l('View Quotation Cart: ', 'QuotationCarts') . $quotation_request->id;
                break;
            default:
                $this->toolbar_title[] = $this->l('Quotation Carts', 'QuotationCarts');
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
            return parent::postProcess();
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->table . 'Box') as $selection) {
                $cart = new QuotationRequest($selection);
                $cart->delete();
            }
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            return parent::postProcess();
        } else {
            return parent::postProcess();
        }
    }

    public function renderView()
    {
        /** @var QuotationRequest $quotation_request */
        if (!($quotation_request = $this->loadObject(true))) {
            return;
        }

        $quotation = null;
        if ($id_roja45_quotation = RojaQuotation::getQuotationForRequest($quotation_request->id)) {
            $quotation = new RojaQuotation($id_roja45_quotation);
        }

        $customer = null;
        if ($quotation_request->id_customer) {
            $customer = new Customer($quotation_request->id_customer);
        } elseif ($quotation_request->requested && isset($quotation)) {
            $customer = new Customer($quotation->id_customer);
        }

        $tpl = $this->context->smarty->createTemplate(
            $this->getTemplatePath('quotationcart_view.tpl') . 'quotationcart_view.tpl'
        );
        $number_of_orders = 0;
        if (isset($customer)) {
            $requests = QuotationRequest::getRequestsForCustomer($customer->id);
            foreach ($requests as $request) {
                $other_quotation = RojaQuotation::getQuotationForRequest($request['id_roja45_quotation_request']);
                $orders = QuotationOrder::getList($other_quotation);
                if (count($orders)) {
                    $number_of_orders++;
                }
            }

            $tpl->assign(
                array(
                    'customer_controller' => $this->context->link->getAdminLink(
                        'Customers',
                        true
                    ),
                    'customer' => $customer,
                    'registration_date' => Tools::displayDate($customer->date_add, null, true),
                    'number_of_requests' => count($requests),
                    'number_of_orders' => $number_of_orders
                )
            );
        }

        $tpl->assign(
            array(
                'quotation_controller' => $this->context->link->getAdminLink(
                    'AdminQuotationsPro',
                    true
                ),
                'customer_controller' => $this->context->link->getAdminLink(
                    'Customers',
                    true
                ),
                'quotation' => $quotation,
                'quotation_products' => $quotation_request->getProducts($this->context->language->id),
            )
        );
        return $tpl->fetch();
    }

    protected function getShopContextError()
    {
        return '
            <p class="alert alert-danger">'.
            $this->l('Please enable products from the shop context.', 'QuotationCarts').
            '</p>';
    }
}
