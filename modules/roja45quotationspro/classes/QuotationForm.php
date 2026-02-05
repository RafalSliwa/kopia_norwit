<?php
/**
 * QuotationForm.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationForm
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationForm.
 *
 * @category  Class
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

class QuotationForm extends ObjectModel
{
    public $id_quotation_form;
    public $id_shop;
    public $form_name;
    public $form_columns;
    public $form_column_titles;
    public $default_form;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_form',
        'primary' => 'id_quotation_form',
        'multilang' => false,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'form_columns' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'form_name' => array('type' => self::TYPE_STRING, 'required' => false),
            'form_column_titles' => array('type' => self::TYPE_STRING, 'required' => false),
            'default_form' => array('type' => self::TYPE_STRING, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public function save($null_values = false, $auto_date = true)
    {
        if (parent::save($null_values, $auto_date) && $this->default_form) {
            $sql =
                'UPDATE '._DB_PREFIX_.'roja45_quotationspro_form 
            SET `default_form`= 0 
            WHERE id_quotation_form !='. (int) $this->id . ' 
            AND id_shop=' . (int) $this->id_shop;
            Db::getInstance()->execute($sql);
        }
        return $this->id;
    }

    public function delete()
    {
        if (parent::delete()) {
            $sql =
                'DELETE FROM  ' . _DB_PREFIX_ . 'roja45_quotationspro_form_product 
                WHERE id_roja45_quotation_form=' . (int)$this->id;
            return Db::getInstance()->execute($sql);
        }
    }

    public static function getForms($id_shop = null, $default = false)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_form', 'f');
        if ($id_shop) {
            $sql->where('f.id_shop='.(int) $id_shop);
        }
        if ($default) {
            $sql->where('f.default_form=1');
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getForm($id_roja45_quotation_form)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_form', 'qf');
        $sql->where('qf.id_quotation_form=' . (int) $id_roja45_quotation_form);
        return Db::getInstance()->executeS($sql);
    }

    public static function getFormForProduct($id_product)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_form', 'qf');
        $sql->leftJoin(
            'roja45_quotationspro_form_product',
            'qfp',
            'qf.id_quotation_form = qfp.id_roja45_quotation_form'
        );
        $sql->where('qfp.id_product=' . (int) $id_product);
        return Db::getInstance()->executeS($sql);
    }

    public static function getFormIdForProduct($id_product, $id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }
        $sql = new DbQuery();
        $sql->select('qf.id_quotation_form');
        $sql->from('roja45_quotationspro_form', 'qf');
        $sql->leftJoin(
            'roja45_quotationspro_form_product',
            'qfp',
            'qf.id_quotation_form = qfp.id_roja45_quotation_form'
        );
        $sql->where('qf.id_shop=' . (int) $id_shop);
        $sql->where('qfp.id_product=' . (int) $id_product);
        return Db::getInstance()->getValue($sql);
    }

    public static function getDefaultFormId($id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = new DbQuery();
        $sql->select('qf.id_quotation_form');
        $sql->from('roja45_quotationspro_form', 'qf');
        $sql->where('qf.default_form=1');
        $sql->where('qf.id_shop=' . (int) $id_shop);

        $id_quotation_form = Db::getInstance()->getValue($sql);
        $id_shop_default = (int) Configuration::get('PS_SHOP_DEFAULT');
        if (!$id_quotation_form && ($id_shop_default != $id_shop)) {
            $id_quotation_form = self::getDefaultFormId();
        } elseif (!$id_quotation_form && ($id_shop_default == $id_shop)) {
            $sql = new DbQuery();
            $sql->select('qf.id_quotation_form');
            $sql->from('roja45_quotationspro_form', 'qf');
            $sql->orderBy('qf.id_quotation_form ASC');
            $id_quotation_form = Db::getInstance()->getValue($sql);
        }

        return $id_quotation_form;
    }

    public static function getFirstFormId()
    {
        $sql = new DbQuery();
        $sql->select('qf.id_quotation_form');
        $sql->from('roja45_quotationspro_form', 'qf');
        $sql->orderBy('qf.id_quotation_form ASC');
        return Db::getInstance()->getValue($sql);
    }

    public function getFormData()
    {
        $form = array();
        $form['id'] = $this->id_quotation_form;
        $form['form_name'] = $this->form_name;
        $form['default_form'] = $this->default_form;
        $form['cols'] = $this->form_columns;
        // explode titles.
        $titles = array();
        parse_str($this->form_column_titles, $titles);

        $form['titles'] = $titles;
        $sql = new DbQuery();
        $sql->select(
            'form_element_id as id, 
            form_element_name as name, 
            form_element_type as type, 
            form_element_column as col, 
            form_element_deletable as deletable, 
            form_element_config as configuration'
        );
        $sql->from('roja45_quotationspro_form_element');
        $sql->where('id_quotation_form=' . (int) $this->id_quotation_form);

        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $row) {
                $form['fields'][$row['col']][] = $row;
            }
        }
        return $form;
    }

    public function getDefaultForm()
    {
        $form = array();
        $form['id'] = null;
        $form['form_name'] = null;
        $form['default_form'] = 0;
        $form['cols'] = '1';
        // explode titles.
        $titles = array();
        parse_str($this->form_column_titles, $titles);

        $form['titles'] = array(
            'form_element_column_title_1' => ""
        );

        $languages = Language::getLanguages(true);
        $contact_firstname_config = 'form_element_name=ROJA45QUOTATIONSPRO_FIRSTNAME&form_element_size=&' .
            'form_element_required=1&form_element_validation=isName&form_element_validation_custom=&';
        $contact_lastname_config = 'form_element_name=ROJA45QUOTATIONSPRO_LASTNAME&form_element_size=&' .
            'form_element_required=1&form_element_validation=isName&form_element_validation_custom=&';
        $contact_email_config = 'form_element_name=ROJA45QUOTATIONSPRO_EMAIL&form_element_size=&' .
            'form_element_required=1&form_element_validation=isEmail&form_element_validation_custom=&';
        foreach ($languages as $language) {
            $contact_firstname_config .= 'form_element_label_'.
                $language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldFirstName',
                    $language
                ).'&form_element_description_'.$language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldFirstNameDesc',
                    $language
                ).'&';
            $contact_lastname_config .= 'form_element_label_'.
                $language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldLastName',
                    $language
                ).'&form_element_description_'.$language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldLastNameDesc',
                    $language
                ).'&';
            $contact_email_config .= 'form_element_label_'.$language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldEmail',
                    $language
                ).'&form_element_description_'.$language['id_lang'].'='.
                RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    Module::getInstanceByName('roja45quotationspro'),
                    'FormFieldEmailDesc',
                    $language
                ).'&';
        }
        $contact_firstname_config = Tools::substr(
            $contact_firstname_config,
            0,
            Tools::strlen($contact_firstname_config)-1
        );
        $contact_lastname_config = Tools::substr(
            $contact_lastname_config,
            0,
            Tools::strlen($contact_lastname_config)-1
        );
        $contact_email_config = Tools::substr(
            $contact_email_config,
            0,
            Tools::strlen($contact_email_config)-1
        );
        $form['fields'] = array(
            1 => array(
                array(
                    'id' => 'ROJA45QUOTATIONSPRO_FIRSTNAME',
                    'name' => 'ROJA45QUOTATIONSPRO_FIRSTNAME',
                    'type' => 'TEXT',
                    'col' => '1',
                    'deletable' => '0',
                    'configuration' => $contact_firstname_config,
                ),
                array(
                    'id' => 'ROJA45QUOTATIONSPRO_LASTNAME',
                    'name' => 'ROJA45QUOTATIONSPRO_LASTNAME',
                    'type' => 'TEXT',
                    'col' => '1',
                    'deletable' => '0',
                    'configuration' => $contact_lastname_config,
                ),
                array(
                    'id' => 'ROJA45QUOTATIONSPRO_EMAIL',
                    'name' => 'ROJA45QUOTATIONSPRO_EMAIL',
                    'type' => 'TEXT',
                    'col' => '1',
                    'deletable' => '0',
                    'configuration' => $contact_email_config,
                )
            )
        );
        return $form;
    }

    public function addConditions($conditions)
    {
        $result = Db::getInstance()->insert(
            'roja45_quotationspro_formconditiongroup',
            array(
                'id_roja45_quotation_form' => (int)$this->id
            )
        );
        if (!$result) {
            return false;
        }
        $id_roja45_quotation_formconditiongroup = (int)Db::getInstance()->Insert_ID();
        foreach ($conditions as $condition) {
            $new_condition = new QuotationFormCondition();
            $new_condition->id_roja45_quotation_formconditiongroup = (int) $id_roja45_quotation_formconditiongroup;
            $new_condition->type = pSQL($condition['type']);
            $new_condition->value = (float)$condition['value'];
            if (!$new_condition->save()) {
                return false;
            }
        }
        return true;
    }

    public function getConditions()
    {
        $conditions = QuotationFormCondition::getConditions((int)$this->id);
        $conditions_group = array();
        if ($conditions) {
            foreach ($conditions as &$condition) {
                $conditions_group[(int)$condition['id_roja45_quotation_formconditiongroup']][] = $condition;
            }
        }
        return $conditions_group;
    }

    public function applyConditions()
    {
        $this->resetFormProducts();
        $products = $this->getAffectedProducts();
        foreach ($products as $product) {
            QuotationForm::applyFormToProduct(
                (int)$this->id,
                (int)$product['id_product']
            );
        }
    }

    public function deleteConditions()
    {
        QuotationFormCondition::deleteConditions((int)$this->id);
    }

    public function resetFormProducts($products = false)
    {
        $where = '';
        if ($products && count($products)) {
            $where .= ' AND id_product IN (' . implode(', ', array_map('intval', $products)) . ')';
        }

        return Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'roja45_quotationspro_form_product 
            WHERE id_roja45_quotation_form=' . (int)$this->id . $where
        );
    }

    public function getAffectedProducts()
    {
        $conditions_group = $this->getConditions();
        $result = array();
        if ($conditions_group) {
            foreach ($conditions_group as $condition_group) {
                $query = new DbQuery();
                $query->select('p.`id_product`');
                $query->from('product', 'p');
                foreach ($condition_group as $id_condition => $condition) {
                    if ($condition['type'] == 'category') {
                        $query->leftJoin(
                            'category_product',
                            'cp' . (int) $id_condition,
                            'p.`id_product` = cp' . (int) $id_condition . '.`id_product`'
                        );
                        $query->where(
                            'cp' . (int) $id_condition . '.id_category = ' . (int) $condition['value']
                        );
                    }
                }
                $result = array_merge($result, Db::getInstance()->executeS($query));
            }
        }

        return $result;
    }

    public static function applyFormToProduct($id_roja45_quotation_form, $id_product)
    {
        $sql = new DbQuery();
        $sql->select('fp.`id_roja45_quotation_form_product`');
        $sql->from('roja45_quotationspro_form_product', 'fp');
        $sql->where('fp.`id_product` = ' . (int)$id_product);
        $sql->where('fp.`id_roja45_quotation_form` = ' . (int)$id_roja45_quotation_form);
        if (Db::getInstance()->getValue($sql)) {
            return false;
        }

        return Db::getInstance()->insert(
            'roja45_quotationspro_form_product',
            array(
                'id_product' => (int) $id_product,
                'id_roja45_quotation_form' => (int) $id_roja45_quotation_form
            )
        );
    }
}
