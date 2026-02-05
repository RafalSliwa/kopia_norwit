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

namespace InPost\Shipping\Repository\ObjectModel;

use InPost\Shipping\ShipX\Resource\Status;

class ShipmentRepository
{
    private $db;

    public function __construct(\Db $db = null)
    {
        $this->db = $db ?: \Db::getInstance();
    }

    /**
     * @param bool $sandbox
     * @param int $organizationId
     * @param int[] $shipmentIds
     *
     * @return \Generator<int, \InPostShipmentModel>
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getNotFinalizedShipments($sandbox, $organizationId, array $shipmentIds = [])
    {
        $qb = (new \DbQuery())
            ->from('inpost_shipment', 'i')
            ->where('i.sandbox = ' . $sandbox ? 1 : 0)
            ->where('i.organization_id = ' . (int) $organizationId)
            ->where('i.status NOT IN ("' . implode('","', Status::FINAL_STATUSES) . '")')
            ->orderBy('i.id_shipment')
            ->limit(100);

        if ([] !== $shipmentIds) {
            $qb->where('i.id_shipment IN (' . implode(array_map('intval', $shipmentIds)) . ')');
        }

        return $this->getIterator($qb);
    }

    /**
     * @param \DbQuery $qb query ordered by shipment ID
     *
     * @return \Generator<int, \InPostShipmentModel>
     */
    private function getIterator(\DbQuery $qb)
    {
        $currentQb = $qb;
        $shipmentId = 0;

        do {
            if (false === $result = $this->db->query($currentQb)) {
                throw new \PrestaShopDatabaseException($this->db->getMsgError());
            }

            $rowCount = $this->db->numRows();
            while ($row = $this->db->nextRow($result)) {
                $shipmentId = (int) $row['id_shipment'];

                yield $shipmentId => $this->hydrate($row);
            }

            $currentQb = clone $qb;
            $currentQb->where('i.id_shipment > ' . $shipmentId);
        } while ($rowCount > 0);
    }

    /**
     * @return \InPostShipmentModel|null
     */
    private function hydrate(array $data)
    {
        if ([] === $data) {
            return null;
        }

        $shipment = new \InPostShipmentModel();
        $shipment->hydrate($data);

        return $shipment;
    }
}
