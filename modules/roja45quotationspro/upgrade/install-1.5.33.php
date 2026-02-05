<?php
/**
 * upgrade_module_1_5_33
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_33
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_33
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Function
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_33($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: ' . $module->name);
    $return = true;

    $id_roja45_status = (int) Configuration::get(
        'ROJA45_QUOTATIONSPRO_STATUS_SENT'
    );

    $sent_status = new QuotationStatus($id_roja45_status);

    $quotation_answer = new QuotationAnswer($sent_status->id_roja45_quotation_answer);
    if (Validate::isLoadedObject($quotation_answer)) {
        foreach (Language::getLanguages(true) as $language) {
            $quotation_answer->subject[$language['id_lang']] = str_replace(
                '[#ct%2$s]',
                '',
                $quotation_answer->subject[$language['id_lang']]
            );
            $quotation_answer->subject[$language['id_lang']] = str_replace(
                '[#tc%3$s]',
                '',
                $quotation_answer->subject[$language['id_lang']]
            );
            $quotation_answer->subject[$language['id_lang']] = str_replace(
                ' : ',
                '',
                $quotation_answer->subject[$language['id_lang']]
            );
        }
        $quotation_answer->update();
    }

    $module->registerHook('actionMailAlterMessageBeforeSend');

    return $return;
}
