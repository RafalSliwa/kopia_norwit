<?php
/**
 * HTMLTemplateCustomPdf.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  HTMLTemplateCustomPdf
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * HTMLTemplateCustomPdf.
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

class HTMLTemplateCustomPdf extends HTMLTemplate
{
    public $custom_object;
    public $template;

    public function __construct($custom_object, $smarty)
    {
        $this->custom_object = $custom_object;
        $context = Context::getContext();
        $this->custom_object = $custom_object;
        if (isset($smarty)) {
            $this->smarty = $smarty;
        } else {
            $this->smarty = $context->smarty;
        }
        // header informations
        $this->title = HTMLTemplateCustomPdf::l('Quotation');
        $this->date = Tools::displayDate(date('Y-m-d', time()));
        $this->shop = new Shop($context->shop->id);
        if (Validate::isLoadedObject($this->shop)) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int) $this->shop->id);
        }

        /*
        $this->template = new QuotationAnswer(
        (int)Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_PDF'),
        $this->custom_object['id_lang']
        );
         */
        $this->template = new QuotationAnswer(
            (int) $custom_object['id_roja45_quotation_answer'],
            $this->custom_object['id_lang']
        );
        $this->template->renderHTMLTemplate($this->custom_object, $this->smarty);

        $this->assignCommonHeaderData();
    }

    public function assignCommonHeaderData()
    {
        $this->setShopId();
        $data = $this->template->getDefaultVars(
            Context::getContext()->language->id,
            (int) $this->shop->id
        );
        $this->smarty->assign(array(
            'date' => $this->date,
            'title' => $this->title,
            'date_now_formatted' => $this->date,

        ));
        $this->smarty->assign($data);
        $this->smarty->assign($this->custom_object);

        $this->custom_object = array_merge(
            $this->custom_object,
            $data
        );
    }

    /**
     * Returns the template's HTML content
     * @return string HTML content
     */
    public function getContent()
    {
        $address = false;
        if (isset($this->custom_object['customer_address_id'])) {
            $address = new Address($this->custom_object['customer_address_id']);
        } else {
            if (isset($this->custom_object['customer_id'])) {
                $id_address = Address::getFirstCustomerAddressId($this->custom_object['customer_id']);
                $address = new Address($id_address);
            }
        }

        $this->smarty->assign(array(
            'customer_address' => '',
        ));
        $this->custom_object['customer_address'] = '';
        if (Validate::isLoadedObject($address)) {
            $addressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
            $formatted_address = AddressFormat::generateAddress(
                $address,
                $addressPatternRules,
                '<br/>',
                ' ',
                array(
                )
            );
            $this->custom_object['customer_address'] = $formatted_address;
            $this->smarty->assign(array(
                'customer_address' => $formatted_address,
            ));
        }

        $shop_address = '';
        $shop_address_obj = $this->shop->getAddress();
        if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
            $shop_address = AddressFormat::generateAddress(
                $shop_address_obj,
                array(),
                '<br/>',
                ' ',
                array(
                )
            );
        }
        $this->custom_object['shop_address'] = $shop_address;
        $this->smarty->assign(array(
            'shop_address' => $shop_address,
        ));

        $layout = $this->computeLayout(['use_tax' => $this->custom_object['show_taxes']]);
        $this->smarty->assign(array(
            'layout' => $layout,
            'use_tax' => (int) $this->custom_object['show_taxes'],
        ));

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $content = $this->template->getTemplateContent();
            $content = QuotationAnswer::processRecursiveTemplate($content, $this->custom_object);
        } else {
            $tpls = array(
                'products_tab' => $this->smarty->fetch($this->getTemplate('quotation.content.productline')),
                'template_data' => $this->custom_object,
                'style_tab' => $this->smarty->fetch($this->getTemplate('quotation.styles')),
            );
            $this->smarty->assign($tpls);

            $content = $this->smarty->fetch($this->getTemplate('quotation.content'));
        }
        return $content;
    }

    public function getCss()
    {
        return $this->template->custom_css;
    }

    public function getHeader()
    {
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $header = $this->template->getTemplateHeader();
            $header = QuotationAnswer::processTemplate($header, $this->custom_object);
        } else {
            $this->smarty->assign(
                array(
                    'header' => $this->custom_object['header'],
                    'title' => $this->custom_object['title'],
                )
            );
            $header = $this->smarty->fetch($this->getTemplate('quotation.header'));
        }
        return $header;
    }

    public function getPagination()
    {
        //return $this->smarty->fetch($this->getTemplate('quotation.pagination'));
        return '';
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFooter()
    {
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $footer = $this->template->getTemplateFooter();
            $footer = QuotationAnswer::processTemplate($footer, $this->custom_object);
        } else {
            $footer = $this->smarty->fetch($this->getTemplate('quotation.footer'));
        }
        return $footer;
    }

    /**
     * Returns the template filename
     * @return string filename
     */
    public function getFilename()
    {
        if ($this->custom_object['filename']) {
            return $this->custom_object['filename'];
        } else {
            return 'quotation.pdf';
        }
    }

    /**
     * Returns the template filename when using bulk rendering
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'custom_pdf.pdf';
    }

    protected function getTemplate($template_name)
    {
        $template = false;
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            if (file_exists(
                _PS_THEME_DIR_ . 'modules/roja45quotationspro/views/templates/admin/custom/' . $template_name . '.tpl'
            )) {
                $template = _PS_THEME_DIR_ .
                    'modules/roja45quotationspro/views/templates/admin/custom/' .
                    $template .
                    '.tpl';
            } elseif (file_exists(
                _PS_ROOT_DIR_ . '/modules/' . $this->module->name .
                '/views/templates/admin/custom/' . $template_name . '.tpl'
            )) {
                $template =
                    _PS_MODULE_DIR_ .
                    'roja45quotationspro/views/templates/admin/custom/' .
                    $template_name .
                    '.tpl';
            }
        } else {
            if (version_compare(_PS_VERSION_, '8', '>=') == true) {
                $default_template =
                    _PS_MODULE_DIR_ .
                    'roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
                $overridden_template =
                    $this->shop->getDirectory() .
                    'modules/roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
            } else if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                $default_template =
                    _PS_MODULE_DIR_ .
                    'roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
                $overridden_template =
                $this->shop->getTheme() .
                    'modules/roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
            } else {
                $default_template = _PS_MODULE_DIR_ .
                    'roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
                $overridden_template = _PS_ALL_THEMES_DIR_ .
                $this->shop->getTheme() .
                    DIRECTORY_SEPARATOR .
                    'modules/roja45quotationspro/views/templates/front/' .
                    $template_name .
                    '.tpl';
            }
            if (file_exists($overridden_template)) {
                $template = $overridden_template;
            } elseif (file_exists($default_template)) {
                $template = $default_template;
            }
        }
        return $template;
    }

    protected function computeLayout($params)
    {
        $layout = [
            'image' => [
                'width' => 12,
            ],
            'product' => [
                'width' => 19,
            ],
            'customizations' => [
                'width' => 19,
            ],
            'comment' => [
                'width' => 18,
            ],
            'unit_price' => [
                'width' => 8,
            ],
            'offer_price' => [
                'width' => 8,
            ],
            'quantity' => [
                'width' => 8,
            ],
            'total_price' => [
                'width' => 8,
            ],
            'tax' => [
                'width' => 0,
            ],
            'tax_rate' => [
                'width' => 0,
            ],
        ];

        if (isset($params['use_tax']) && $params['use_tax']) {
            $layout['tax'] = ['width' => 6];
            $layout['tax_rate'] = ['width' => 8];
            $layout['product']['width'] -= 5;
            $layout['customizations']['width'] -= 5;
            $layout['comment']['width'] -= 4;
        }

        $total_width = 0;
        $free_columns_count = 0;
        foreach ($layout as $data) {
            if ($data['width'] === 0) {
                ++$free_columns_count;
            }

            $total_width += $data['width'];
        }

        $delta = 100 - $total_width;

        foreach ($layout as $row => $data) {
            if ($data['width'] === 0) {
                $layout[$row]['width'] = $delta / $free_columns_count;
            }
        }

        $layout['_colCount'] = count($layout);

        return $layout;
    }
}
