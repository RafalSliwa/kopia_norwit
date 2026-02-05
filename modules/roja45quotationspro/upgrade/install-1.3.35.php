<?php
/**
 * upgrade_module_1_3_35
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_35
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_35.
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

function upgrade_module_1_3_35($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_status`';
    $return &= Db::getInstance()->execute($sql);
    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_status_lang`';
    $return &= Db::getInstance()->execute($sql);
    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status` AUTO_INCREMENT = 1;';
    $return &= Db::getInstance()->execute($sql);

    $def_states = array(
        array(
            'code' => QuotationStatus::$RCVD,
            'color' => '#FF8C00',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 1,
            'name' => 'Quotation Request Received',
            'answer_template' => 'quotation_request_received'
        ),
        array(
            'code' => QuotationStatus::$SENT,
            'color' => '#32CD32',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Customer Quotation Sent',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$CART,
            'color' => '#4169E1',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'In Customer Cart',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$MESG,
            'color' => '#FF8C00',
            'unremovable' => 1,
            'send_email' => 1,
            'notify_admin' => 1,
            'name' => 'Customer Message Received',
            'answer_template' => 'customer_message_received'
        ),
        array(
            'code' => QuotationStatus::$CUSR,
            'color' => '#4169E1',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Customer Response Sent',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$ORDR,
            'color' => '#4169E1',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 1,
            'name' => 'Customer Order Raised',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$CLSD,
            'color' => '#108510',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Closed - Completed',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$INCP,
            'color' => '#DC143C',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Closed - Incomplete',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$DLTD,
            'color' => '#FF0000',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Quotation Deleted',
            'answer_template' => null
        ),
        array(
            'code' => QuotationStatus::$CCLD,
            'color' => '#FF0000',
            'unremovable' => 1,
            'send_email' => 1,
            'notify_admin' => 0,
            'name' => 'Quotation Request Cancelled',
            'answer_template' => 'quotation_request_cancelled'
        ),
        array(
            'code' => QuotationStatus::$CORD,
            'color' => '#FF0000',
            'unremovable' => 1,
            'send_email' => 1,
            'notify_admin' => 1,
            'name' => 'Customer Order Request',
            'answer_template' => 'customer_order_request'
        ),
        array(
            'code' => QuotationStatus::$NWQT,
            'color' => '#FF8C00',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'New Quotation',
            'answer_template' => 'customer_order_request'
        ),
    );

    foreach ($def_states as $state) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_status',
            array(
                'color' => pSQL($state['color']),
                'code' => pSQL($state['code']),
                'unremovable' => (int)$state['unremovable'],
                'send_email' => (int)$state['send_email'],
                'notify_admin' => (int)$state['notify_admin'],
                'answer_template' => pSQL($state['answer_template']),
            )
        );
        $id_status = Db::getInstance()->Insert_ID();
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_STATUS_'.$state['code'], $id_status);
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_status_lang',
                array(
                    'id_roja45_quotation_status' => (int)$id_status,
                    'id_lang' => (int)$language['id_lang'],
                    'status' => pSQL(RojaFortyFiveQuotationsProCore::getLocalTranslation(
                        $module,
                        $state['code'],
                        $language
                    )),
                )
            );
        }
    }

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
            'input[name=qty]'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
            ''
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
            ''
        );
    } else {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
            '#quantity_wanted'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
            '.button.ajax_add_to_cart_button'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
            '.button-container'
        );
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_request`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'abandoned') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_request` 
            ADD `abandoned` TINYINT(1) AFTER `form_data`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_request` 
            ADD `requested` TINYINT(1) AFTER `form_data`';
        $return &= Db::getInstance()->execute($sql);
    }

    Db::getInstance()->execute(
        'UPDATE ' . _DB_PREFIX_ . 'roja45_quotationspro SET is_template = 0'
    );
    Db::getInstance()->execute(
        'UPDATE ' . _DB_PREFIX_ . 'roja45_quotationspro_request SET requested = 1 WHERE form_data != ""'
    );
    Db::getInstance()->execute(
        'UPDATE '._DB_PREFIX_.'roja45_quotationspro_request SET abandoned = 1 WHERE form_data IS NULL || form_data = ""'
    );

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        $id_tab = Tab::getIdFromClassName('QuotationCarts');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCarts';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $module->name;
            $tab->icon = 'question_answer';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }
        if (!$id_tab = Tab::getIdFromClassName('QuotationStatuses')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationStatuses';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $module->name;
            $tab->icon = 'check_circle';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }
        if (!$id_tab = Tab::getIdFromClassName('QuotationAnswers')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationAnswers';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $module->name;
            $tab->icon = 'question_answer';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }
    } else {
        $id_tab = Tab::getIdFromClassName('QuotationCarts');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCarts';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $module->tabClassName);
            $tab->module = $module->name;
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }
    }
    $module->registerHook('actionAuthentication');
    $module->registerHook('displayProductAdditionalInfo');

    $invisible = (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_INVISIBLECAPTCHA');
    if ($invisible) {
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_CAPTCHATYPE', 1);
    }
    Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_INVISIBLECAPTCHA');
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL',
        1
    );
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART',
        0
    );
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION',
        0
    );
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_USEAJAX',
        1
    );
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA',
        0
    );

    return $return;
}
