<?php
/**
 * NOTICE OF LICENSE
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2023 Ceneo
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use CeneoXml\Model\AttributeMapping;

class Ceneo_XmlAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->name = 'ceneo_xml_ajax';
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }

    public function displayAjax()
    {
        if (
            Tools::getIsset('action')
            && Tools::getValue('action') == 'suggest'
            && Tools::getIsset('id_category')
            && $id_category = Tools::getValue('id_category')
        ) {
            $category = new Category($id_category, Context::getContext()->language->id);

            $ceneo_categories = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'ceneo_xml_category 
            where LOWER(path) like "%' . pSQL(mb_strtolower($category->name)) . '%"');
            if (!$ceneo_categories) {
                echo 0;
                exit;
            }
            $id_ceneo_category = $ceneo_categories[0]['id_ceneo_category'];
            $name = $ceneo_categories[0]['path'];

            $attributes = [];
            $attrs = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'ceneo_xml_attribute 
            where id_ceneo_category = ' . (int) Tools::getValue('id_ceneo_category'));
            if ($attrs) {
                $model = new AttributeMapping(Context::getContext()->shop->id);
                $mapping = json_decode($model->attributes, true);
                foreach ($attrs as $attr) {
                    $selected = isset($mapping[$id_ceneo_category], $mapping[$id_ceneo_category][Tools::getValue('id_category')]);
                    if ($selected) {
                        foreach ($mapping[$id_ceneo_category][Tools::getValue('id_category')] as $row) {
                            if ($row['ceneo'] == $attr['name']) {
                                $selected = $row['shop'];
                                break;
                            }
                        }
                    }
                    $is_key = $attr['is_key_attribute'] === 'True';
                    $attributes[] = [
                        'name' => $attr['name'] . ($is_key ? ' *' : ''),
                        'required' => $is_key,
                        'selected' => $selected,
                    ];
                }
            }
            echo json_encode(['id_ceneo_category' => $id_ceneo_category, 'name' => $name, 'attributes' => $attributes]);
            exit;
        }
        if (
            Tools::getIsset('action')
            && Tools::getValue('action') == 'list'
            && Tools::getIsset('term')
            && $term = Tools::getValue('term')['term']
        ) {
            $ceneo_categories = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'ceneo_xml_category 
            where LOWER(path) like "%' . pSQL(mb_strtolower($term)) . '%"');
            if (!$ceneo_categories) {
                echo 0;
                exit;
            }
            $categories = [];
            foreach ($ceneo_categories as $c) {
                $categories[] = ['id' => $c['id_ceneo_category'], 'text' => $c['path']];
            }
            echo json_encode($categories);
            exit;
        }
        if (Tools::getIsset('id_ceneo_category') && $id_ceneo_category = Tools::getValue('id_ceneo_category')) {
            $attributes = [];
            $attrs = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'ceneo_xml_attribute 
            where id_ceneo_category = '
                . (int) Tools::getValue('id_ceneo_category'));
            if ($attrs) {
                $model = new AttributeMapping(Context::getContext()->shop->id);
                $mapping = json_decode($model->attributes, true);
                foreach ($attrs as $attr) {
                    $selected = isset($mapping[$id_ceneo_category], $mapping[$id_ceneo_category][Tools::getValue('id_category')]);
                    if ($selected) {
                        foreach ($mapping[$id_ceneo_category][Tools::getValue('id_category')] as $row) {
                            if ($row['ceneo'] == $attr['name']) {
                                $selected = $row['shop'];
                                break;
                            }
                        }
                    }
                    $is_key = $attr['is_key_attribute'] === 'True';
                    $attributes[] = [
                        'name' => $attr['name'] . ($is_key ? ' *' : ''),
                        'required' => $is_key,
                        'selected' => $selected,
                    ];
                }
            }
            echo json_encode($attributes);
        }
    }
}
