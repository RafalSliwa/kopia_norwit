<?php
/**
 * Carrier.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Carrier
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * Carrier.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Carrier extends CarrierCore
{
    public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
    {
        $carriers = parent::getCarriers(
            $id_lang,
            $active,
            $delete,
            $id_zone,
            $ids_group,
            $modules_filters
        );
        if ($id_roja45_quotation = (int) Context::getContext()->cookie->__get(
            'ROJA45QUOTATIONSPRO_ID_QUOTATION'
        )) {
            $quotation = new RojaQuotation($id_roja45_quotation);
            $shipping_charges = $quotation->getQuotationShippingCharges($quotation->id_lang);
            foreach ($carriers as $key => $carrier) {
                $remove = true;
                foreach ($shipping_charges as $shipping_charge) {
                    if (($shipping_charge['id_carrier'] == $carrier['id_carrier'])) {
                        $remove = false;
                    }
                }
                if ($remove) {
                    unset($carriers[$key]);
                }
            }
        }
        return $carriers;
    }
}
