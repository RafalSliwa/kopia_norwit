<?php
/**
 * Copyright since 2021 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright Since 2021 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

namespace InPost\Shipping\Handler\Shipment;

use InPost\Shipping\Builder\Shipment\CreateShipmentPayloadBuilder;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\Translations\ValidationErrorTranslator;

class BulkCreateShipmentHandler extends CreateShipmentHandler
{
    const TRANSLATION_SOURCE = 'BulkCreateShipmentHandler';

    /**
     * @var \Db
     */
    private $db;

    public function __construct(\InPostShipping $module, ValidationErrorTranslator $errorTranslator, CreateShipmentPayloadBuilder $payloadBuilder, ShipXConfiguration $shipXConfiguration, \Db $db = null)
    {
        parent::__construct($module, $errorTranslator, $payloadBuilder, $shipXConfiguration);
        $this->db = $db ?: \Db::getInstance();
    }

    public function handle(array $request)
    {
        $this->resetErrors();

        if (empty($request['orderIds'])) {
            $this->addError($this->module->l('No orders selected', self::TRANSLATION_SOURCE));

            return [];
        }

        if ([] === $orders = $this->getOrdersWithoutShipment($request['orderIds'])) {
            $this->addError($this->module->l('No orders without shipment found.', self::TRANSLATION_SOURCE));

            return [];
        }

        $shipments = [];

        foreach ($orders as $order) {
            try {
                if ($payload = $this->payloadBuilder->buildPayload($order)) {
                    $shipments[] = $this->saveShipment($order, Shipment::create($payload));
                }
            } catch (ValidationFailedException $exception) {
                if ($errors = $exception->getValidationErrors()) {
                    foreach ($this->translateErrors($errors) as $error) {
                        $this->addOrderError($order->reference, $error);
                    }
                } else {
                    $this->addError($exception->getDetails());
                }
            } catch (\Exception $exception) {
                $this->addOrderError($order->reference, $exception->getMessage());
            }
        }

        try {
            $this->waitForTransactionsData($shipments);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
        }

        if (empty($shipments) && !$this->hasErrors()) {
            $this->addError($this->module->l('None of the selected orders were placed with an InPost carrier as a delivery option', self::TRANSLATION_SOURCE));
        }

        return $shipments;
    }

    /**
     * @param \InPostShipmentModel[] $shipments
     */
    private function waitForTransactionsData(array $shipments)
    {
        $shipmentIds = [];
        $remaining = [];

        foreach ($shipments as $shipmentModel) {
            $remaining[$shipmentModel->shipx_shipment_id] = $shipmentModel;
            $shipmentIds[$shipmentModel->shipx_shipment_id] = $shipmentModel->shipx_shipment_id;
        }

        $i = 0;
        while (!empty($remaining) && $i++ < self::REFRESH_RETRY_NUMBER) {
            sleep(1);

            foreach (Shipment::getCollection(['id' => $shipmentIds]) as $shipment) {
                $transactions = $shipment->transactions;
                if (!empty($transactions)) {
                    $shipmentModel = $remaining[$shipment->id];

                    $transaction = current($transactions);
                    if ($transaction['status'] !== 'success') {
                        $this->addOrderError(
                            $shipmentModel->getOrder()->reference,
                            $this->getTransactionError($transaction)
                        );
                    } else {
                        $shipmentModel->status = $shipment->status;
                        $shipmentModel->tracking_number = $shipment->tracking_number;
                        $shipmentModel->update();

                        $shipmentModel->updateParcelTrackingNumbers($shipment);
                        $shipmentModel->updateOrderTrackingNumber();
                    }

                    unset(
                        $remaining[$shipmentModel->shipx_shipment_id],
                        $shipmentIds[$shipmentModel->shipx_shipment_id]
                    );
                }
            }
        }
    }

    protected function addOrderError($reference, $error)
    {
        return $this->addError(sprintf(
            $this->module->l('Order "%s": %s', self::TRANSLATION_SOURCE),
            $reference,
            $error
        ));
    }

    /**
     * @param int[] $orderIds
     *
     * @return \Order[]
     */
    private function getOrdersWithoutShipment(array $orderIds)
    {
        $qb = (new \DbQuery())
            ->select('o.*')
            ->from('orders', 'o')
            ->innerJoin('carrier', 'c', 'c.id_carrier = o.id_carrier')
            ->leftJoin('inpost_shipment', 's', \implode(' AND ', [
                's.id_order = o.id_order',
                's.sandbox = ' . (int) $this->shipXConfiguration->useSandboxMode(),
                's.organization_id = ' . $this->shipXConfiguration->getOrganizationId(),
            ]))
            ->where(sprintf('o.id_order IN (%s)', \implode(',', array_map('\intval', $orderIds))))
            ->where('s.id_shipment IS NULL');

        if (!$data = $this->db->executeS($qb)) {
            return [];
        }

        return \Order::hydrateCollection(\Order::class, $data);
    }
}
