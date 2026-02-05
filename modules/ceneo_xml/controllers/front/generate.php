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

use CeneoXml\Model\AttributeMapping;
use CeneoXml\Model\Mapping;

class Ceneo_XmlGenerateModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        try {
            $this->initGenerate();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    private function sendEmail($id_shop)
    {
        $secureKey = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $urlController = Context::getContext()->link->getModuleLink(
            'ceneo_xml',
            'generate',
            [
                'secure_key' => $secureKey,
                'id_shop' => $id_shop,
                'show_output' => 1,
            ]
        );

        Mail::Send(
            (int) $id_lang,
            'log_generate',
            '[Prestashop] ' . _PS_BASE_URL_ . ' - wygenerowanie pliku xml',
            [
                'shop_generate_url' => $urlController,
            ],
            'wtyczki@ceneo.pl',
            null,
            (string) Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
            (string) Configuration::get('PS_SHOP_NAME', null, null, $id_shop),
            null,
            null,
            _PS_MODULE_DIR_ . 'ceneo_xml/mails/',
            true
        );
    }

    private function initGenerate()
    {
        $id_shop = Tools::getValue('id_shop');

        $directory_path = _PS_MODULE_DIR_ . 'ceneo_xml/export/';
        $xml_files = glob($directory_path . '*.xml');

        $firstGenerate = count($xml_files) === 0;

        if ($this->module->ips) {
            $ips = explode(',', $this->module->ips);
            if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                exit('Permission denied');
            }
        }

        // Secure key check
        if (Tools::getValue('secure_key')) {
            $secureKey = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));

            $dateLastGeneration = Configuration::get('CENEO_XML_LAST');

            if (!empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
                $this->generate($id_shop, Tools::getValue('show_output'));

                $xml_files_after_generation = glob($directory_path . '*.xml');

                if ($firstGenerate && count($xml_files_after_generation) > 0 && $dateLastGeneration === '0000-00-00 00:00:00') {
                    $this->sendEmail($id_shop);
                }
            }
        }
        exit;
    }

    public function generate($id_shop, $show_output)
    {
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $link = new Link();
        $message = '';
        $ceneo_categories = Db::getInstance()->executeS(
            'select * from ' . _DB_PREFIX_ . 'ceneo_xml_category'
        );
        $ceneo_names = [];
        if ($ceneo_categories) {
            $ceneo_names = [];
            foreach ($ceneo_categories as $row) {
                $ceneo_names[$row['id_ceneo_category']] = $row['path'];
            }
        }

        $all_settings = [];
        $excluded = [];
        $excluded_combinations = [];
        $all_settings_result = Db::getInstance()->executeS(
            'select * from ' . _DB_PREFIX_ . 'ceneo_xml_product_settings'
        );

        if ($all_settings_result) {
            foreach ($all_settings_result as $p) {
                $all_settings[$p['id_product']][$p['id_product_attribute']] = $p;
                if ($p['exclude'] == 1 && !$p['id_product_attribute']) {
                    $excluded[] = $p['id_product'];
                }
                if ($p['exclude'] == 1 && $p['id_product_attribute']) {
                    $excluded_combinations[] = $p['id_product_attribute'];
                }
            }
        }

        foreach ([$id_shop] as $id_shop) {
            $mapping = new Mapping($id_shop);
            $mapping_attributes = new AttributeMapping($id_shop);

            $ceneo_category = false;
            $categories = json_decode($mapping->categories, 1);
            $attributes = json_decode($mapping_attributes->attributes, 1);

            $xml_writer = new XMLWriter();
            $xml_writer->openMemory();

            $xml_writer->setIndent(true);
            $xml_writer->startDocument('1.0', 'UTF-8');
            $xml_writer->startElement('offers');
            $xml_writer->startAttribute('xmlns:xsi');
            $xml_writer->text('http://www.w3.org/2001/XMLSchema-instance');
            $xml_writer->startAttribute('version');
            $xml_writer->text('1');

            $products = $this->getProducts($id_lang, $id_shop, $excluded, $excluded_combinations);

            foreach ($products as $n => $p) {
                $product = new Product($p['id_product'], false, $id_lang, $id_shop);
                $id_product_attribute = $p['id_product_attribute'] ?? 0;

                if (!isset($all_settings[$p['id_product']])) {
                    $product_settings = [
                        'exclude' => 0,
                        'avail' => (int) Configuration::get('CENEO_XML_AVAIL'),
                        'basket' => 2,
                    ];
                } else {

                    $all_settings[$p['id_product']][0]['id_product_attribute'] = (string) $id_product_attribute;
                    $product_settings = $all_settings[$p['id_product']][0];

                }
                $xml_writer->startElement('o');

                $xml_writer->startAttribute('id');
                $xml_writer->text($p['id_product'] . (isset($p['id_product_attribute']) ? '-' . $p['id_product_attribute'] : ''));

                $xml_writer->startAttribute('url');
                $xml_writer->text($link->getProductLink($product->id, $product->link_rewrite, null, null, $id_lang, $id_shop, $id_product_attribute, false, false, true));

                $xml_writer->startAttribute('price');
                $xml_writer->text($p['price']);

                $xml_writer->startAttribute('avail');

                $avail = $product_settings['avail'] ? $product_settings['avail'] : Configuration::get('CENEO_XML_AVAIL');
                switch ($avail) {
                    case 1:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 99);
                        break;
                    case 2:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 3);
                        break;
                    case 3:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 7);
                        break;
                    case 4:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 14);
                        break;
                    case 5:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 90);
                        break;
                    case 6:
                        $xml_writer->text($p['true_quantity'] > 0 ? 1 : 110);
                        break;
                    case 7:
                        $xml_writer->text(1);
                        break;
                    case 8:
                        $xml_writer->text(3);
                        break;
                    case 9:
                        $xml_writer->text(7);
                        break;
                    case 10:
                        $xml_writer->text(14);
                        break;
                    case 11:
                        $xml_writer->text(90);
                        break;
                    case 12:
                        $xml_writer->text(110);
                        break;
                    case 13:
                        $xml_writer->text(99);
                        break;
                    default:
                        $xml_writer->text($p['quantity'] > 0 ? 1 : 99);
                        break;
                }

                $xml_writer->startAttribute('basket');
                $xml_writer->text(isset($product_settings['basket']) && $product_settings['basket'] != 2 ? $product_settings['basket'] : Configuration::get('CENEO_XML_BASKET'));

                if ($p['weight']) {
                    $xml_writer->startAttribute('weight');
                    $xml_writer->text($p['weight']);
                }

                $xml_writer->startAttribute('stock');
                $xml_writer->text((int) $p['quantity'] < 0 ? 0 : $p['quantity']);

                $xml_writer->startElement('cat');
                $category_to_write = '';
                if (isset($categories[$p['id_category_default']]) && $categories[$p['id_category_default']]) {
                    $ceneo_category = $categories[$p['id_category_default']];
                    $category_to_write = $ceneo_names[$ceneo_category];
                } else {
                    $category = new Category($p['id_category_default'], $id_lang);
                    $suffix = $category->name;
                    $id_parent = $category->id_parent;
                    while ($id_parent > 1) {
                        if (isset($categories[$id_parent]) && !empty($categories[$id_parent])) {
                            $category_to_write = mb_substr($ceneo_names[$categories[$category->id_parent]] . '/' . $suffix, 0, 255);
                            break;
                        } else {
                            $category = new Category($id_parent, $id_lang);
                            $id_parent = $category->id_parent;
                        }
                    }
                }
                if (empty($category_to_write)) {
                    $category = new Category($p['id_category_default'], $id_lang);
                    $cats = [];
                    $cats[] = $category->name;
                    while ($category->id_parent) {
                        $category = new Category($category->id_parent, $id_lang);
                        if ($category->id !== 1) {
                            $cats[] = $category->name;
                        }
                    }
                    $cats = array_reverse($cats);
                    $category_to_write = mb_substr(join('/', $cats), 0, 255);
                }

                $xml_writer->writeCData(htmlspecialchars($category_to_write, ENT_COMPAT, 'UTF-8'));
                $xml_writer->endElement();

                $xml_writer->startElement('name');
                $xml_writer->writeCData(htmlspecialchars(mb_substr($p['name'], 0, 150), ENT_COMPAT, 'UTF-8'));
                $xml_writer->endElement();

                $xml_writer->startElement('desc');
                $xml_writer->writeCData(htmlspecialchars(mb_substr($product->description, 0, 100000), ENT_COMPAT, 'UTF-8'));
                $xml_writer->endElement();

                if (count($p['images'])) {
                    $xml_writer->startElement('imgs');

                    $img_count = 0;
                    $cover_exist = false;
                    foreach ($p['images'] as $i) {
                        ++$img_count;
                        if (strlen(self::getImageLinkUrl($p['link_rewrite'], $p['id_product'] . '-' . $i['id_image'], $id_shop)) > 255) {
                            continue;
                        }

                        if ($i['cover'] == 1) {
                            $cover_exist = true;
                        }

                        if ($cover_exist) {
                            if ($i['cover'] == 1 && $img_count == 1) {
                                $xml_writer->startElement('main');
                                $xml_writer->startAttribute('url');
                                $xml_writer->text(self::getImageLinkUrl($p['link_rewrite'], $p['id_product']
                                    . '-' . $i['id_image'], $id_shop));
                                $xml_writer->endAttribute();
                                $xml_writer->endElement();
                            } else {
                                $xml_writer->startElement('i');
                                $xml_writer->startAttribute('url');
                                $xml_writer->text(self::getImageLinkUrl($p['link_rewrite'], $p['id_product']
                                    . '-' . $i['id_image'], $id_shop));
                                $xml_writer->endAttribute();
                                $xml_writer->endElement();
                            }
                        } else {
                            if ($img_count == 1) {
                                $xml_writer->startElement('main');
                                $xml_writer->startAttribute('url');
                                $xml_writer->text(self::getImageLinkUrl($p['link_rewrite'], $p['id_product']
                                    . '-' . $i['id_image'], $id_shop));
                                $xml_writer->endAttribute();
                                $xml_writer->endElement();
                            } else {
                                $xml_writer->startElement('i');
                                $xml_writer->startAttribute('url');
                                $xml_writer->text(self::getImageLinkUrl($p['link_rewrite'], $p['id_product']
                                    . '-' . $i['id_image'], $id_shop));
                                $xml_writer->endAttribute();
                                $xml_writer->endElement();
                            }
                        }
                    }

                    $xml_writer->endElement();
                }

                $xml_writer->startElement('attrs'); // start attrs element
                if (!empty($p['manufacturer_name'])) {
                    $xml_writer->startElement('a');
                    $xml_writer->startAttribute('name');
                    $xml_writer->text('Producent');
                    $xml_writer->endAttribute();
                    $xml_writer->writeCData(htmlspecialchars($p['manufacturer_name'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                if (!empty($p['ean13'])) {
                    $xml_writer->startElement('a');
                    $xml_writer->startAttribute('name');
                    $xml_writer->text('EAN');
                    $xml_writer->endAttribute();
                    $xml_writer->writeCData(htmlspecialchars($p['ean13'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                if (!empty($p['reference'])) {
                    $xml_writer->startElement('a');
                    $xml_writer->startAttribute('name');
                    $xml_writer->text('Kod producenta');
                    $xml_writer->endAttribute();
                    $xml_writer->writeCData(htmlspecialchars($p['reference'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                if (!empty($p['isbn'])) {
                    $xml_writer->startElement('a');
                    $xml_writer->startAttribute('name');
                    $xml_writer->text('Kod ISBN');
                    $xml_writer->endAttribute();
                    $xml_writer->writeCData(htmlspecialchars($p['isbn'], ENT_COMPAT, 'UTF-8'));
                    $xml_writer->endElement();
                }

                if (isset($p['attributes']) && $p['attributes']) {
                    foreach ($p['attributes'] as $f) {
                        $selected = isset($attributes[$ceneo_category], $attributes[$ceneo_category][$p['id_category_default']]);
                        if ($selected) {
                            $selected = false;
                            foreach ($attributes[$ceneo_category][$p['id_category_default']] as $row) {
                                if ($row['shop'] == 'attribute-' . $f['id_attribute_group']) {
                                    $selected = str_replace('_*', '', $row['ceneo']);
                                    break;
                                }
                            }
                        }
                        if ($selected) {
                            $xml_writer->startElement('a');
                            $xml_writer->startAttribute('name');
                            $xml_writer->text(htmlspecialchars($selected, ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endAttribute();
                            if (isset($f['id_feature'])) {
                                $xml_writer->writeCData(
                                    htmlspecialchars(
                                        self::getProductFeatureValue($p['id_product'], $f['id_feature']),
                                        ENT_COMPAT,
                                        'UTF-8'
                                    )
                                );
                            }
                            $xml_writer->endElement();
                        } else {
                            $xml_writer->startElement('a');
                            $xml_writer->startAttribute('name');
                            $xml_writer->text(htmlspecialchars($f['key'], ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endAttribute();
                            $xml_writer->writeCData(htmlspecialchars($f['value'], ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endElement();
                        }
                    }
                }

                $features = Product::getFeaturesStatic($p['id_product']);
                if ($features) {
                    foreach ($features as $f) {
                        $selected = isset($attributes[$ceneo_category]) && isset($attributes[$ceneo_category][$p['id_category_default']]) ? true : false;
                        if ($selected) {
                            $selected = false;
                            foreach ($attributes[$ceneo_category][$p['id_category_default']] as $row) {
                                if ($row['shop'] == 'feature-' . $f['id_feature']) {
                                    $selected = $row['ceneo'];
                                    break;
                                }
                            }
                        }
                        if ($selected) {
                            $xml_writer->startElement('a');
                            $xml_writer->startAttribute('name');
                            $xml_writer->text(htmlspecialchars($selected, ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endAttribute();
                            $xml_writer->writeCData(htmlspecialchars(self::getProductFeatureValue($p['id_product'], $f['id_feature']), ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endElement();
                        } else {
                            $feature = new Feature($f['id_feature'], $id_lang);
                            $xml_writer->startElement('a');
                            $xml_writer->startAttribute('name');
                            $xml_writer->text(htmlspecialchars($feature->name, ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endAttribute();
                            $xml_writer->writeCData(htmlspecialchars(self::getProductFeatureValue($p['id_product'], $f['id_feature']), ENT_COMPAT, 'UTF-8'));
                            $xml_writer->endElement();
                        }
                    }
                }

                $xml_writer->endElement();

                // END PRODUCT
                $xml_writer->endElement();
            }

            $xml_writer->endDocument(); // End document

            $this->module->last = date('Y-m-d H:i:s');
            Configuration::updateValue('CENEO_XML_LAST', $this->module->last, false, null, $id_shop);
            Configuration::updateValue('CENEO_XML_COUNT', count($products), false, null, $id_shop);
            if ($show_output) {
                $xmlContent = $xml_writer->flush();
                header('Content-Type: application/xml');
                echo $xmlContent;

                $generateFilePath = _PS_MODULE_DIR_ . 'ceneo_xml/export/ceneo-' . (int) $id_shop . '.xml';
                file_put_contents($generateFilePath, $xmlContent);
            } else {
                $generate_file_path = _PS_MODULE_DIR_ . 'ceneo_xml/export/ceneo-' . (int) $id_shop . '.xml';
                file_put_contents($generate_file_path, $xml_writer->flush(true));
                $secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));

                $file_url = Context::getContext()->link->getModuleLink(
                    'ceneo_xml',
                    'download',
                    [
                        'secure_key' => $secure_key,
                        'id_shop' => Context::getContext()->shop->id,
                        'show_output' => 0,
                    ]
                );

                $message = '<p>' . $this->module->l('File generated') . ': <a target="_blank" href="' . $file_url . '">' .
                    $file_url . '</a></p>';
            }
            break;
        }
        echo $message;
    }

    public static function getImageLinkUrl($link_rewrite, $ids, $id_shop)
    {
        $use_ssl = Configuration::get('PS_SSL_ENABLED', null, null, $id_shop);
        $protocol_content = ($use_ssl) ? 'https://' : 'http://';
        $uri_path = self::getImageLink($link_rewrite, $ids);
        $domain = ShopUrl::getMainShopDomain($id_shop);

        return $protocol_content . $domain . $uri_path;
    }

    public static function getImageLink($name, $ids)
    {
        $type = ImageType::getFormattedName('large');
        $not_default = false;
        $allow = (int) Configuration::get('PS_REWRITING_SETTINGS');

        if (
            (Configuration::get('PS_LEGACY_IMAGES')
                && file_exists(_PS_PROD_IMG_DIR_ . $ids . ($type ? '-' . $type : '') . '.jpg'))
            || ($not_default = strpos($ids, 'default') !== false)
        ) {
            if ($allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__ . $ids . ($type ? '-' . $type : '') . '/' . $name . '.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_ . $ids . ($type ? '-' . $type : '') . '.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);

            if ($allow == 1) {
                $uri_path = __PS_BASE_URI__ . $id_image . ($type ? '-' . $type : '') . '/' . $name . '.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_ . Image::getImgFolderStatic($id_image) . $id_image . ($type ? '-' . $type : '') . '.jpg';
            }
        }

        return $uri_path;
    }

    public function getProducts($id_lang, $id_shop, $excluded_products, $excluded_combinations)
    {
        $products = $this->getProductsDBLight($id_lang, $id_shop, $excluded_products);

        $return = [];

        $pr_ids = [];
        foreach ($products as $k => $p) {
            $pr_ids[] = $p['id_product'];
        }

        $all_combinations = $this->getAttributeCombinationsBulk($pr_ids, $excluded_combinations, $id_lang, $id_shop);
        $all_images = $this->getProductImagesBulk($id_lang, $pr_ids);

        foreach ($products as $k => $p) {
            if (empty(trim($p['name']))) {
                continue;
            }
            if (isset($all_combinations[$p['id_product']]) && !empty($all_combinations[$p['id_product']]) ) {
                $combinations = $all_combinations[$p['id_product']];
            } else {
                $combinations = [];
            }

            $price_default = $price = Product::getPriceStatic((int) $p['id_product'], true, null, 2);

            if ($this->module->merge_combinations || !count($combinations)) {
                $quantity = StockAvailable::getQuantityAvailableByProduct($p['id_product'], null, $id_shop);
                $p['quantity'] = $quantity;
                $p['true_quantity'] = $quantity;

                // STOCK MANAGMENT AND OUT_OF_STOCK
                // Allow to order the product when out of stock?
                $product_out_of_stock = (int) $p['out_of_stock'];

                if ($this->module->exclude_oos && $this->module->stock_management) {
                    if ($quantity <= 0) {
                        continue;
                    }
                } elseif (!$this->module->exclude_oos && $this->module->stock_management) {
                    if ($quantity > 0) {
                        $p['quantity'] = $quantity;
                    } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                        continue;
                    } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 1) {
                        $p['quantity'] = 999;
                    } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                        continue;
                    } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                        $p['quantity'] = 999;
                    }
                } elseif ($this->module->stock_management == false) {
                    $p['quantity'] = 999;
                }
                // END STOCK MANAGMENT

                if (isset($all_images[$p['id_product']]) && !empty($all_images[$p['id_product']])) {
                    $p['images'] = isset($all_images[$p['id_product']]) ? $all_images[$p['id_product']] : [];
                } else {
                    $p['images'] = [];
                }

                $p['price'] = Product::getPriceStatic((int) $p['id_product'], true, null, 2);

                $p['weight'] = Tools::ps_round($p['weight'], 2);

                $p['product_name'] = trim($p['name']);

                $p['attributes'] = [];
                $tmp = [];
                if ($combinations) {
                    foreach ($combinations as $combination) {
                        $attribute_price = Product::getPriceStatic((int) $p['id_product'], true, (int) $combination['id_product_attribute'], 2);
                        if ($attribute_price != $price_default) {
                            continue;
                        }
                        $quantity_attribute = StockAvailable::getQuantityAvailableByProduct($p['id_product'], $combination['id_product_attribute'], $id_shop);
                        if ($this->module->exclude_oos && $this->module->stock_management) {
                            if ($quantity_attribute <= 0) {
                                continue;
                            }
                        } elseif (!$this->module->exclude_oos && $this->module->stock_management) {
                            if ($quantity_attribute <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                                continue;
                            } elseif ($quantity_attribute <= 0 && $this->module->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                                continue;
                            }
                        }
                        foreach ($combination['attributes'] as $a => $comb) {
                            $tmp[$comb['key']][] = ['value' => $comb['value'], 'id_attribute_group' => $comb['id_attribute_group']];
                        }
                    }
                    foreach ($tmp as $key => $val) {
                        $string = [];
                        $g = 0;
                        foreach ($val as $v) {
                            $g = $v['id_attribute_group'];
                            $string[] = $v['value'];
                        }
                        $unique_attrs = array_unique($string);
                        $p['attributes'][] = ['key' => $key, 'value' => join(';', $unique_attrs), 'id_attribute_group' => $g];
                    }
                }

                if ($this->module->exclude_by_price_max && ($p['price'] < $this->module->exclude_by_price_max)) {
                    continue;
                }

                $return[] = $p;

                foreach ($combinations as $ca => $a) {
                    $price = Product::getPriceStatic((int) $p['id_product'], true, (int) $a['id_product_attribute'], 2);
                    if ($price == $price_default) {
                        continue;
                    }

                    $quantity = StockAvailable::getQuantityAvailableByProduct((int) $p['id_product'], (int) $a['id_product_attribute'], $id_shop);
                    $p['quantity'] = $quantity;
                    $p['true_quantity'] = $quantity;

                    $product_out_of_stock = (int) $p['out_of_stock'];

                    if ($this->module->exclude_oos && $this->module->stock_management) {
                        if ($quantity <= 0) {
                            continue;
                        }
                    } elseif (!$this->module->exclude_oos && $this->module->stock_management) {
                        if ($quantity > 0) {
                            $p['quantity'] = $quantity;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                            continue;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 1) {
                            $p['quantity'] = 999;
                        } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                            continue;
                        } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                            $p['quantity'] = 0;
                        }
                    } elseif ($this->module->stock_management == false) {
                        $p['quantity'] = 999;
                    }
                    // END STOCK MANAGMENT
                    $p['images'] = $this->getProductImages($id_lang, (int) $p['id_product'], (int) $a['id_product_attribute'], 20);

                    // if images for attributes don't exist get main ones
                    if (!count($p['images'])) {
                        $p['images'] = $this->getProductImages($id_lang, (int) $p['id_product'], null, 20);
                    }
                    $p['price'] = $price;
                    $p['id_product_attribute'] = $a['id_product_attribute'];
                    $p['reference'] = $a['reference'] ? $a['reference'] : $p['reference'];
                    $p['ean13'] = $a['ean13'] ? $a['ean13'] : $p['ean13'];
                    $p['attributes'] = $a['attributes'];

                    // Sort out attribute weight impact on product
                    if ($a['weight_impact'] > 0) {
                        $p['weight'] = Tools::ps_round($a['weight_base'] + $a['weight_impact'], 2);
                    } else {
                        $p['weight'] = Tools::ps_round($a['weight_base'] - abs($a['weight_impact']), 2);
                    }

                    if ($this->module->exclude_by_price_max && ($p['price'] < $this->module->exclude_by_price_max)) {
                        continue;
                    }

                    $return[] = $p;
                }
            } else {
                foreach ($combinations as $ca => $a) {
                    $price = Product::getPriceStatic((int) $p['id_product'], true, (int) $a['id_product_attribute'], 2);

                    $quantity = StockAvailable::getQuantityAvailableByProduct((int) $p['id_product'], (int) $a['id_product_attribute'], $id_shop);
                    $p['quantity'] = $quantity;
                    $p['true_quantity'] = $quantity;

                    $product_out_of_stock = (int) $p['out_of_stock'];

                    // if use only avaiable and stock managment is on then skip that product if is not in stock
                    if ($this->module->exclude_oos && $this->module->stock_management) {
                        if ($quantity <= 0) {
                            continue;
                        }
                    } elseif (!$this->module->exclude_oos && $this->module->stock_management) {
                        if ($quantity > 0) {
                            $p['quantity'] = $quantity;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 0) {
                            continue;
                        } elseif ($quantity <= 0 && $product_out_of_stock != '' && $product_out_of_stock == 1) {
                            $p['quantity'] = 999;
                        } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 0 && $product_out_of_stock == 2) {
                            continue;
                        } elseif ($quantity <= 0 && $this->module->order_out_of_stock == 1 && $product_out_of_stock == 2) {
                            $p['quantity'] = 0;
                        }
                    } elseif ($this->module->stock_management == false) {
                        $p['quantity'] = 999;
                    }
                    // END STOCK MANAGMENT

                    $p['images'] = $this->getProductImages($id_lang, (int) $p['id_product'], (int) $a['id_product_attribute'], 20);

                    // if images for attributes don't exist get main ones
                    if (!count($p['images'])) {
                        $p['images'] = $this->getProductImages($id_lang, (int) $p['id_product'], null, 20);
                    }

                    $p['price'] = $price;
                    $p['id_product_attribute'] = $a['id_product_attribute'];
                    $p['reference'] = $a['reference'] ? $a['reference'] : $p['reference'];
                    $p['ean13'] = $a['ean13'] ? $a['ean13'] : $p['ean13'];
                    $p['attributes'] = $a['attributes'];

                    // Sort out attribute weight impact on product
                    if ($a['weight_impact'] > 0) {
                        $p['weight'] = Tools::ps_round($a['weight_base'] + $a['weight_impact'], 2);
                    } else {
                        $p['weight'] = Tools::ps_round($a['weight_base'] - abs($a['weight_impact']), 2);
                    }

                    if ($this->module->exclude_by_price_max && ($p['price'] < $this->module->exclude_by_price_max)) {
                        continue;
                    }

                    $return[] = $p;
                }
            }
        }

        return $return;
    }

    public function getProductsDBLight($id_lang, $id_shop, $excluded_products)
    {
        $shop = new Shop($id_shop);
        $shop_group = $shop->getGroup();
        $id_shop_group = $shop_group->id;
        $share_stock = $shop_group->share_stock;
        $sql = 'SELECT DISTINCT  p.`id_product`, p.`weight`, p.`id_category_default`, p.`reference`, p.`ean13`, p.`upc`,
                p.`supplier_reference`, p.`available_date`, p.`isbn`, p.`width`, p.`height`, p.`depth`,
                product_shop.`id_tax_rules_group`,
                product_shop.`active`, product_shop.`unit_price_ratio`,
                pl.`name`, pl.`description_short`, pl.`description`, pl.`id_product`, pl.`link_rewrite`,
                m.`name` AS manufacturer_name,
                s.`name` AS supplier_name, 
                ps.`product_supplier_reference` AS supplier_reference,
                sav.`out_of_stock`
    
                FROM `' . _DB_PREFIX_ . 'product` p
                INNER JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = ' . (int) $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_shop = ' . (int) $id_shop . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = p.`id_product` AND ps.`id_product_attribute` = 0)';

        if ($share_stock) {
            $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop = 0 AND  sav.id_shop_group = ' . (int) $id_shop_group . ')';
        } else {
            $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop = ' . (int) $id_shop . ')';
        }

        $sql .= 'WHERE pl.`id_lang` = ' . (int) $id_lang .
            ($excluded_products ? (' AND p.`id_product` not in (' . implode(', ', array_map('intval', $excluded_products)) . ')') : '') .
            ($this->module->exclude_inactive ? ' AND product_shop.`active` = 1' : '') . '
                ORDER BY p.`id_product`';

        return Db::getInstance()->executeS($sql);
    }

    public function getProductImages($id_lang, $id_product, $id_product_attribute = null, $limit = 1)
    {
        $attribute_filter = ($id_product_attribute ? ' AND ai.`id_product_attribute` = ' . (int) $id_product_attribute : '');
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'image` i
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($id_product_attribute) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` = ' . (int) $id_product . ' AND il.`id_lang` = ' . (int) $id_lang . $attribute_filter . '
            ORDER BY i.`position` ASC
            LIMIT ' . (int) $limit;

        return Db::getInstance()->executeS($sql);
    }

    public function getProductImagesBulk($id_lang, $pr_ids)
    {

        $array = [];

        if (empty($pr_ids)) {
            return $array;
        }


        $id_product_attribute = null;
        $array = [];
        $attribute_filter = ($id_product_attribute ? ' AND ai.`id_product_attribute` = ' . (int) $id_product_attribute : '');
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'image` i
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($id_product_attribute) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` in (' . implode(', ', array_map('intval', $pr_ids)) . ') AND il.`id_lang` = ' . (int) $id_lang . $attribute_filter . '
            ORDER BY i.`position` ASC';
        $result = Db::getInstance()->executeS($sql);

        if ($result) {
            foreach ($result as $r) {
                if (isset($array[$r['id_product']]) && count($array[$r['id_product']]) > 20) {
                    continue;
                }
                $array[$r['id_product']][] = $r;
            }
        }

        return $array;
    }

    public static function getProductFeatureValue($id_product, $id_feature, $delimeter = ', ')
    {
        $string = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'feature_product fp left join ' . _DB_PREFIX_ . 'feature_value_lang fv
          on fp.id_feature_value = fv.id_feature_value where id_lang = ' . Context::getContext()->language->id . ' and id_product = ' . (int) $id_product . ' and fp.id_feature = ' . (int) $id_feature);
        if (!$string) {
            return '';
        }
        $array = [];
        foreach ($string as $s) {
            $array[] = $s['value'];
        }

        return join($delimeter, $array);
    }

    private function getAttributeCombinationsBulk(array $id_product, array $id_product_attribute, $id_lang, $id_shop)
    {

        $return = [];

        if ($id_product_attribute && $id_product) {
            $sql = 'SELECT pa.`id_product_attribute`, pa.`id_product`, pa.`reference`, pa.`ean13`, pa.`supplier_reference`, pas.`weight` as weight_impact, p.`weight` as weight_base, 
                ag.`id_attribute_group`, ag.`is_color_group`, agl.`public_name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, ps.`product_supplier_reference` AS supplier_reference
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = ' . (int) $id_shop . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pa.`id_product`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = pa.id_product AND ps.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE pa.`id_product` in (' . implode(', ', array_map('intval', $id_product)) . ')
                AND pa.`id_product_attribute` not in (' . implode(', ', array_map('intval', $id_product_attribute)) . ')
                ORDER BY pa.`id_product_attribute`';
        } else if ($id_product) {
            $sql = 'SELECT pa.`id_product_attribute`, pa.`id_product`, pa.`reference`, pa.`ean13`, pa.`supplier_reference`, pas.`weight` as weight_impact, p.`weight` as weight_base, 
                ag.`id_attribute_group`, ag.`is_color_group`, agl.`public_name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, ps.`product_supplier_reference` AS supplier_reference
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = ' . (int) $id_shop . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pa.`id_product`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.`id_supplier` = p.`id_supplier` AND ps.`id_product` = pa.id_product AND ps.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE pa.`id_product` in (' . implode(', ', array_map('intval', $id_product)) . ')
                ORDER BY pa.`id_product_attribute`';
        } else {
            $sql = false;
        }

        if ($sql) {
            $results = Db::getInstance()->ExecuteS($sql);
        }

        if (isset($results) && is_array($results) && count($results)) {
            foreach ($results as $r) {
                if (!isset($return[$r['id_product']][$r['id_product_attribute']]['attribute_name'])) {
                    $return[$r['id_product']][$r['id_product_attribute']]['attribute_name'] = '';
                }

                $return[$r['id_product']][$r['id_product_attribute']]['attributes'][] = ['key' => $r['group_name'], 'value' => $r['attribute_name'], 'id_attribute_group' => $r['id_attribute_group'], 'id_attribute' => $r['id_attribute']];
                $return[$r['id_product']][$r['id_product_attribute']]['id_product_attribute'] = $r['id_product_attribute'];
                $return[$r['id_product']][$r['id_product_attribute']]['attribute_name'] .= ', ' . Tools::ucfirst($r['group_name']) . ': ' . Tools::ucfirst($r['attribute_name']) . '';
                $return[$r['id_product']][$r['id_product_attribute']]['reference'] = $r['reference'];
                $return[$r['id_product']][$r['id_product_attribute']]['supplier_reference'] = $r['supplier_reference'];
                $return[$r['id_product']][$r['id_product_attribute']]['ean13'] = $r['ean13'];
                $return[$r['id_product']][$r['id_product_attribute']]['weight_impact'] = $r['weight_impact'];
                $return[$r['id_product']][$r['id_product_attribute']]['weight_base'] = $r['weight_base'];
            }
        }

        return $return;
    }
}
