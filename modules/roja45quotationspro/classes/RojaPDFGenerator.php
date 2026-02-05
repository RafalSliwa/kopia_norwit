<?php
/**
 * RojaPDFGenerator.
 *
 * @author    Roja45 <support@roja45.com>
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaPDFGenerator
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaPDFGenerator.
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

class RojaPDFGenerator
{
    public $mpdf = null;
    public $css = null;

    public function __construct($use_cache = false)
    {
        if ((int) Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR')) {
            $temp_dir = _PS_CACHE_DIR_ . 'mpdf/';
        } else {
            $temp_dir = sys_get_temp_dir();
        }

        $mpdf_config = [
            'mode' => '+aCJK',
            "autoScriptToLang" => true,
            "autoLangToFont" => true,
            "tempDir" => $temp_dir,
        ];

        $this->mpdf = new \Mpdf\Mpdf($mpdf_config);
        $this->mpdf->debug = false;
        $this->mpdf->useSubstitutions = false;
        $this->mpdf->simpleTables = false;
        $this->mpdf->shrink_tables_to_fit = false;

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG')) {
            $this->mpdf->showImageErrors = true;
            $this->mpdf->debug = true;
        }
        $errorReportingLevel = E_ALL | E_STRICT;
        if (_PS_DISPLAY_COMPATIBILITY_WARNING_ === false) {
            $errorReportingLevel = $errorReportingLevel & ~E_DEPRECATED & ~E_USER_DEPRECATED;
        }
        @error_reporting($errorReportingLevel);
    }

    public function writePage()
    {
        if ($this->css) {
            $this->mpdf->WriteHTML($this->css, 1);
            $this->mpdf->WriteHTML($this->content, 2);
        } else {
            $this->mpdf->WriteHTML($this->content);
        }
    }

    public function render($filename, $display = true)
    {
        if (empty($filename)) {
            throw new PrestaShopException('Missing filename.');
        }
        if ($display === true) {
            $output = 'D';
        } elseif ($display === false) {
            $output = 'S';
        } elseif ($display == 'D') {
            $output = 'D';
        } elseif ($display == 'S') {
            $output = 'S';
        } elseif ($display == 'F') {
            $output = 'F';
        } else {
            $output = 'I';
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        return $this->mpdf->Output($filename, $output);
    }

    public function createCSS($css)
    {
        $this->css = $css;
    }

    public function createHeader($header)
    {
        if (!empty($header)) {
            $this->mpdf->setAutoTopMargin = 'stretch';
            $this->mpdf->SetHTMLHeader($header, '', true);
        }
    }

    public function createContent($content)
    {
        $this->content = $content;
    }

    public function createFooter($footer)
    {
        if (!empty($footer)) {
            $this->mpdf->setAutoBottomMargin = 'stretch';
            $this->mpdf->SetHTMLFooter($footer);
        }
    }
}
