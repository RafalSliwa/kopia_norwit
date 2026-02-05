<?php
/**
 * RojaPDF.
 *
 * @author    Roja45 <support@roja45.com>
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaPDF
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaPDF.
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

class RojaPDF extends PDF
{
    public function __construct($objects, $template, $smarty)
    {
        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $this->pdf_renderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'));
        } else {
            $this->pdf_renderer = new RojaPDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'));
        }

        $this->template = $template;

        $this->smarty = $smarty;
        $this->objects = $objects;
        if (!($objects instanceof Iterator) && !is_array($objects)) {
            $this->objects = array($objects);
        }

        $errorReportingLevel = E_ALL | E_STRICT;
        if (_PS_DISPLAY_COMPATIBILITY_WARNING_ === false) {
            $errorReportingLevel = $errorReportingLevel & ~E_DEPRECATED & ~E_USER_DEPRECATED;
        }
        @error_reporting($errorReportingLevel);
    }

    public function render($display = true)
    {
        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
            return parent::render($display);
        }

        $render = false;
        foreach ($this->objects as $object) {
            $template = $this->getTemplateObject($object);
            if (!$template) {
                continue;
            }
            if (empty($this->filename)) {
                $this->filename = $template->getFilename();
                if (count($this->objects) > 1) {
                    $this->filename = $template->getBulkFilename();
                }
            }
            $template->assignHookData($object);
            $css = $template->getCss();
            $header = $template->getHeader();
            $footer = $template->getFooter();
            $content = $template->getContent();

            $content = html_entity_decode($content);
            $header = html_entity_decode($header);
            $footer = html_entity_decode($footer);

            $this->pdf_renderer->createCSS($css);
            $this->pdf_renderer->createHeader($header);
            $this->pdf_renderer->createFooter($footer);
            $this->pdf_renderer->createContent($content);

            $this->pdf_renderer->writePage();
            $render = true;
            unset($template);
        }

        if ($render) {
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }

            return $this->pdf_renderer->render($this->filename, $display);
        }
    }

    public function configMPDF($object)
    {
        $quotation = new RojaQuotation($object->id_order);
        $this->pdf_renderer->mpdf->AddPage("P");
    }

    /**
     * @param string $template
     * @param RojaQuotation $quotation
     * @param bool $display
     * @return string|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function generatePDF($template, $quotation, $display, $custom_object_override = array())
    {
        $currency = new Currency($quotation->id_currency);
        $custom_object = $quotation->getSummaryDetails(
            $quotation->id_lang,
            $quotation->id_currency,
            $display
        );
        $custom_object['id_lang'] = $quotation->id_lang;
        $custom_object['filename'] = $quotation->reference . '.pdf';
        $custom_object['title'] = $quotation->reference;
        $custom_object['customer_title'] = '';
        $custom_object['customer_firstname'] = '';
        $custom_object['customer_lastname'] = '';
        $custom_object['customer_email'] = '';
        $custom_object['show_account'] = 0;
        $custom_object['show_customization_cost'] = isset($custom_object['quotation_has_customization_cost']) ?
        $custom_object['quotation_has_customization_cost'] :
        0;
        $custom_object['show_product_customizations'] = isset($custom_object['show_product_customizations']) ?
        $custom_object['show_product_customizations'] :
        0;
        $custom_object['show_product_discounts'] = isset($custom_object['quotation_has_discounts']) ?
        $custom_object['quotation_has_discounts'] :
        0;
        $custom_object['show_additional_shipping'] = isset($custom_object['quotation_has_additional_shipping']) ?
        $custom_object['quotation_has_additional_shipping'] :
        0;
        $custom_object['show_product_comments'] = isset($custom_object['quotation_has_comments']) ?
        $custom_object['quotation_has_comments'] :
        0;
        $custom_object['show_ecotax'] = $custom_object['quotation_has_ecotax'];
        $custom_object['show_prices'] = (int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY');
        $custom_object['show_summary'] = 1;
        if ($quotation->id_customer) {
            $customer = new Customer($quotation->id_customer);
            if ($customer->id_gender) {
                $gender = new Gender($customer->id_gender, $quotation->id_lang);
                $custom_object['customer_title'] = $gender->name;
            }

            $custom_object['customer_firstname'] = $customer->firstname;
            $custom_object['customer_lastname'] = $customer->lastname;
            $custom_object['customer_email'] = $customer->email;
        } elseif (isset($quotation->firstname)) {
            $custom_object['customer_firstname'] = $quotation->firstname;
            $custom_object['customer_lastname'] = $quotation->lastname;
            $custom_object['customer_email'] = $quotation->email;
        }

        $custom_object['header'] = '';
        $custom_object['notes'] = '';
        //$custom_object['show_taxes'] = $quotation->calculate_taxes;
        $custom_object['use_taxes'] = $custom_object['show_taxes'];
        $custom_object['show_exchange_rate'] = ((float) $currency->conversion_rate == (float) 1.0) ? 0 : 1;
        $custom_object['exchange_rate'] = (float) $currency->conversion_rate;
        $language = new Language($quotation->id_lang);
        $custom_object['tax_text'] = $custom_object['show_taxes'] ?
            Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)) :
            Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language));

        $custom_object = array_merge($custom_object, $custom_object_override);
        $pdf = new RojaPDF(array($custom_object), $template, Context::getContext()->smarty);
        return $pdf->render($display);
    }
}
