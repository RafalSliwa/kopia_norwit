<?php
/**
 * upgrade_module_1_5_0
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_0
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_0
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

function upgrade_module_1_5_0($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = '
      CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_customization` (
      `id_roja45_quotation_customization` int(10) unsigned NOT NULL auto_increment,
      `id_customization` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id_roja45_quotation_customization`)
    ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang` (
        `id_roja45_quotation_answer` int(10) unsigned NOT NULL auto_increment,
        `id_lang` int(10) unsigned NOT NULL,
        `template` varchar(255) NULL,
        PRIMARY KEY (`id_roja45_quotation_answer`, `id_lang`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'total_shipping_exc') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE ' . _DB_PREFIX_ . 'roja45_quotationspro 
            CHANGE total_shipping total_shipping_exc DECIMAL(20,6) null;';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE ' . _DB_PREFIX_ . 'roja45_quotationspro 
            CHANGE total_shipping_wt total_shipping_inc DECIMAL(20,6) null;';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_document`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_shop') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_document` 
            ADD `id_shop` INT(10) unsigned NOT NULL AFTER `id_roja45_document`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_request`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'reference') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_request` 
            ADD `reference` VARCHAR(255) AFTER `requested`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_answer`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'type') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_answer` 
            ADD `type` INT(10) AFTER `name`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_answer` 
            ADD `custom_css` TEXT AFTER `type`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'name') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $answers = QuotationAnswer::getQuotationAnswers();

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang` 
            ADD `name` VARCHAR(255) AFTER `id_lang`';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang` 
            ADD `subject` VARCHAR(255) AFTER `name`';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_answer` 
            DROP `name`';
        $return &= Db::getInstance()->execute($sql);

        if ($answers) {
            foreach ($answers as $answer) {
                $template = $answer['name'];
                $old_answer = new QuotationAnswer((int) $answer['id']);
                $new_answer = new QuotationAnswer();
                $new_answer->type = QuotationAnswer::$OLD;
                $new_answer->enabled = 1;
                foreach (Language::getLanguages(true) as $language) {
                    $new_answer->name[$language['id_lang']] = $template;
                    $new_answer->subject[$language['id_lang']] = null;

                    $iso_template = $language['iso_code'].'/'.$template;
                    if (file_exists(_PS_THEME_DIR_.'modules/'.$module->name.'/mails/'.$iso_template.'.txt') ||
                        file_exists(_PS_THEME_DIR_.'modules/'.$module->name.'/mails/'.$iso_template.'.html')) {
                        $template_path = _PS_THEME_DIR_ . 'modules/' . $module->name . '/mails/';
                    } elseif (file_exists(_PS_THEME_DIR_.'mails/'.$iso_template.'.txt') ||
                        file_exists(_PS_THEME_DIR_.'mails/'.$iso_template.'.html')) {
                        $template_path = _PS_THEME_DIR_.'mails/';
                    } elseif (file_exists(_PS_ROOT_DIR_.'/modules/'.$module->name.'/mails/'.$iso_template.'.txt') ||
                        file_exists(_PS_ROOT_DIR_.'/modules/'.$module->name.'/mails/'.$iso_template.'.html')) {
                        $template_path = _PS_ROOT_DIR_.'/modules/'.$module->name.'/mails/';
                    } else {
                        $template_path = _PS_THEME_DIR_.'modules/'.$module->name.'/mails/';
                    }

                    $new_template_path = _PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/';
                    if ($new_template_path && !file_exists($new_template_path)) {
                        mkdir($new_template_path, 0777, true);
                    }
                    $html_template = $template_path.$iso_template.'.html';
                    $new_html_template = $new_template_path.$template.'_'.$language['iso_code'].'.tpl';
                    if (file_exists($html_template)) {
                        if (!copy($html_template, $new_html_template)) {
                            return false;
                        }
                    }
                    $text_template = $template_path.$iso_template.'.txt';
                    $new_text_template = $new_template_path.$template.'_'.$language['iso_code'].'-txt.tpl';
                    if (file_exists($text_template)) {
                        if (!copy($text_template, $new_text_template)) {
                            return false;
                        }
                    }
                    $new_answer->template[$language['id_lang']] = $template.'_'.$language['iso_code'];
                }
                $new_answer->save();
                $old_answer->delete();
            }
        }
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_status`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_roja45_quotation_answer') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status` 
            ADD `id_roja45_quotation_answer` INT(10) UNSIGNED AFTER `answer_template`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status` 
            ADD `id_roja45_quotation_answer_admin` INT(10) UNSIGNED AFTER `id_roja45_quotation_answer`';
        $return &= Db::getInstance()->execute($sql);
    }

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            CHANGE `purchase_date` `purchase_date` DATETIME NULL DEFAULT NULL;';
    $return &= Db::getInstance()->execute($sql);

    $sql = 'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            SET purchase_date=NULL WHERE purchase_date="0000-00-00 00:00:00"';
    $return &= Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            CHANGE `reference` `reference` VARCHAR(12)';
    $return &= Db::getInstance()->execute($sql);

    $employees = Employee::getEmployeesByProfile(1, true);
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER',
        $employees[0]->id_employee
    );
    $sql = 'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            SET id_employee=' . (int)  Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER') . ' 
            WHERE id_employee=0';
    $return &= Db::getInstance()->execute($sql);

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_address_invoice') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_invoice` INT(10) NULL AFTER `id_address`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_delivery` INT(10) NULL AFTER `id_address_invoice`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_tax` INT(10) NULL DEFAULT \'21\' AFTER `id_address_delivery`';
        $return &= Db::getInstance()->execute($sql);

        if ($quotations = RojaQuotation::getQuotations()) {
            foreach ($quotations as $quotation) {
                $quotationObj = new RojaQuotation($quotation['id_roja45_quotation']);
                $quotationObj->id_address_delivery = $quotation['id_address'];
                $quotationObj->id_address_invoice = $quotation['id_address'];
                $quotationObj->id_address_tax = RojaQuotation::TAX_INVOICE_ADDRESS;
                $quotationObj->save();
            }
        }

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` DROP `id_address`';
        $return &= Db::getInstance()->execute($sql);
    }

    // TODO upgrade custom mail templates
    $css = Tools::file_get_contents(_PS_ROOT_DIR_.'/modules/'.$module->name.'/views/css/pdf-styles.css');
    if (!$id_roja45_quotation_answer = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$PDF,
                'custom_css' => $css,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Quotation Request PDF'),
                    'template' => pSQL('pdf_request_' . $language['iso_code']),
                )
            );
        }
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF', $id_roja45_quotation_answer);
    }

    if (!$id_roja45_quotation_answer = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_PDF')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$PDF,
                'custom_css' => $css,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Customer Quotation PDF'),
                    'template' => pSQL('pdf_quotation_' . $language['iso_code']),
                )
            );
        }
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_QUOTATION_PDF', $id_roja45_quotation_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_BLANK')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Template Email'),
                    'template' => pSQL('mail_blank_template_'.$language['iso_code']),
                )
            );
        }
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_BLANK', $id_roja45_quotation_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_REQUEST')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_customer_request_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_customer_request_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Customer Request Received Email'),
                    'subject' => pSQL('We have received your request.'),
                    'template' => pSQL('mail_customer_request_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_RCVD');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer = $id_roja45_quotation_customer_request_answer;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_QUOTE', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_REQUEST')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_admin_request_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_admin_request_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Admin Request Received Email'),
                    'subject' => pSQL('Quotation Request Received'),
                    'template' => pSQL('mail_admin_request_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_RCVD');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer_admin = $id_roja45_quotation_admin_request_answer;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_REQUEST', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_QUOTE')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_quotation_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Send Customer Quote Email'),
                    'subject' => pSQL('Your quotation.'),
                    'template' => pSQL('mail_send_quote_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_SENT');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer = $id_roja45_quotation_quotation_answer;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_QUOTE', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_NOTIFY')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_notifyadmin = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer_notifyadmin,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Notify Admin Email'),
                    'subject' => pSQL('Quotation status has changed.'),
                    'template' => pSQL('mail_notify_admin_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_MESG');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer_admin = $id_roja45_quotation_answer_notifyadmin;
        $status->save();
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_ORDR');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer_admin = $id_roja45_quotation_answer_notifyadmin;
        $status->save();
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_CCLD');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer_admin = $id_roja45_quotation_answer_notifyadmin;
        $status->save();
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_CORD');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer_admin = $id_roja45_quotation_answer_notifyadmin;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_NOTIFY', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_THANKS')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Thank You Email'),
                    'subject' => pSQL('Thank you'),
                    'template' => pSQL('mail_thank_you_' . $language['iso_code']),
                )
            );
        }
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_THANKS', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_MSG_RECEIVED')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_messagereceived = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer_messagereceived,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Message Received Email'),
                    'subject' => pSQL('Thank you for your message'),
                    'template' => pSQL('mail_message_received_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_MESG');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer = $id_roja45_quotation_answer_messagereceived;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_MSG_RECEIVED', $id_answer);
    }

    if (!$id_answer = Configuration::get('ROJA45_QUOTATIONSPRO_MAIL_ORDER_REQUEST')) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int)QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_orderrequest = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int)$id_roja45_quotation_answer_orderrequest,
                    'id_lang' => (int)$language['id_lang'],
                    'name' => pSQL('Order Request Email'),
                    'subject' => pSQL('We have received your order request'),
                    'template' => pSQL('mail_customer_order_request_' . $language['iso_code']),
                )
            );
        }
        $id_roja45_quotation_status = (int)Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_CORD');
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->id_roja45_quotation_answer = $id_roja45_quotation_answer_orderrequest;
        $status->save();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_MAIL_ORDER_REQUEST', $id_answer);
    }

    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES',
        'module'
    );
    // for each entry in the theme/modles/roja diretory, change these to mail entries in the new custom folder.

    $def_states = array(
        array(
            'code' => QuotationStatus::$OPEN,
            'color' => '#108510',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'Quotation Open',
            'answer_template' => null,
            'id_roja45_quotation_answer' => 0,
            'id_roja45_quotation_answer_admin' => 0
        )
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
                'id_roja45_quotation_answer' => (int)$state['id_roja45_quotation_answer'],
                'id_roja45_quotation_answer_admin' => (int)$state['id_roja45_quotation_answer_admin'],
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

    $quotations = RojaQuotation::getQuotations(Context::getContext()->language->id);
    foreach ($quotations as $quotation) {
        $quotation = new RojaQuotation($quotation['id_roja45_quotation']);
        if (empty($quotation->form_data)) {
            $request = new QuotationRequest($quotation->id_request);
            if (Validate::isLoadedObject($request)) {
                if (!empty($request->form_data)) {
                    $quotation->form_data = json_encode($request->getFormData());
                    $quotation->save();
                }
            }
        }
    }

    $module->registerHook('addWebserviceResources');
    if ($return) {
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_ORDER_REQUEST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_MSG_RECEIVED');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_THANKS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_NOTIFY');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_QUOTE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_REQUEST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_CUSTOMER_REQUEST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_ADMIN_REQUEST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MAIL_BLANK');
    }

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        if ($id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
            if (!$id_tab = Tab::getIdFromClassName('ParentAdminQuotationsPro')) {
                $tab = new Tab();
                $tab->active = 1;
                $tab->class_name = 'ParentAdminQuotationsPro';
                $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
                $tab->module = $module->name;
                $tab->icon = 'list';
                $tab->name = array();
                foreach (Language::getLanguages(true) as $lang) {
                    $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                        $module,
                        'AdminQuotationsPro',
                        $lang
                    );
                }
                $return &= $tab->add();

                if ($id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('QuotationForms')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('QuotationDocuments')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('QuotationStatuses')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('QuotationCarts')) {
                    $tab = new Tab($id_tab);
                    $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
                    $tab->save();
                }

                if ($id_tab = Tab::getIdFromClassName('QuotationAnswers')) {
                    $tab = new Tab($id_tab);
                    $tab->delete();

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
            }
        }
    }

    return $return;
}
