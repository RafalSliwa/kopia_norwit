<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

namespace JPresta\SpeedPack;

if (!defined('_PS_VERSION_')) {exit;}

use Db;
use HelperForm;
use Image;
use ImageType;
use Exception;
use PrestaShopException;
use Tools;
use Context;
use Configuration;
use Module;
use Shop;
use WebPConvert\Convert\ConverterFactory;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\Loggers\BufferLogger;

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaWebpModule')) {

    include_once(dirname(__FILE__) . '/../vendor/autoload.php');
    if (file_exists(dirname(__FILE__) . '/../webp.config.php')) {
        include_once(dirname(__FILE__) . '/../webp.config.php');
    }

    require_once 'JprestaSubModule.php';

    class JprestaWebpModule extends JprestaSubModule
    {
        public function install()
        {
            if (!defined('JprestaMigPCU2SP')) {
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_ENABLE', 1);
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_QUALITY', 90);
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EWWW_KEY', '');
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION', 0);
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', '/upload/');
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG', 1);
            }
            // Make sure permission are correctly set on binaries
            $binariesDir = _PS_MODULE_DIR_ . $this->module->name . '/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/';
            Tools::chmodr($binariesDir, 0755);
            // This is called before install() but wee need to call it after settings configuration values
            $this->enable();
            // Make sure the config file exists
            $this->updateConfigFile();
            return true;
        }

        public function uninstall()
        {
            if (!defined('JprestaMigPCU2SP')) {
                Configuration::deleteByName('SPEED_PACK_WEBP_ENABLE');
                Configuration::deleteByName('SPEED_PACK_WEBP_QUALITY');
                Configuration::deleteByName('SPEED_PACK_WEBP_EWWW_KEY');
                Configuration::deleteByName('SPEED_PACK_WEBP_FORCE_EXTENSION');
                Configuration::deleteByName('SPEED_PACK_WEBP_EXCLUDE_PATHS');
                Configuration::deleteByName('SPEED_PACK_WEBP_ENABLE_PNG');
            }
            return true;
        }

        public function enable()
        {
            // This is also called before install
            $this->updateHtaccessFile();
            return true;
        }

        public function disable()
        {
            // This is also called before uninstall
            $this->updateHtaccessFile(true);
            return true;
        }

        public function saveConfiguration()
        {
            $output = '';
            try {
                if (Tools::isSubmit('submitWebp')) {
                    if (_PS_MODE_DEMO_ && !Context::getContext()->employee->isSuperAdmin()) {
                        $output .= $this->module->displayError($this->module->l('In DEMO mode you cannot modify the configuration.', 'jprestaspeedpack'));
                    } else {
                        JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EWWW_KEY',
                            JprestaUtils::trimTo(Tools::getValue('SPEED_PACK_WEBP_EWWW_KEY'), false));

                        $oldQuality = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_QUALITY');
                        $newQuality = (int)Tools::getValue('SPEED_PACK_WEBP_QUALITY', $oldQuality);
                        JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_QUALITY', $newQuality);

                        $convertersReports = $this->checkConverters();

                        $oldEnableWebp = (int)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE');
                        $newEnableWebp = (int)Tools::getValue('SPEED_PACK_WEBP_ENABLE') && $convertersReports['atLeastOne'];
                        if ($newEnableWebp && !$oldEnableWebp) {
                            JprestaUtils::saveConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE', 1);
                            $this->updateHtaccessFile();
                        } elseif (!$newEnableWebp && $oldEnableWebp) {
                            JprestaUtils::saveConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE', 0);
                            $this->updateHtaccessFile();
                        }

                        if ($newQuality !== $oldQuality) {
                            // The quality has been changed, delete all webp files.
                            $this->deleteWebpImages();
                        }

                        $oldForceExtension = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION');
                        $newForceExtension = $newEnableWebp && (int)Tools::getValue('SPEED_PACK_WEBP_FORCE_EXTENSION');
                        if ($newForceExtension && !$oldForceExtension) {
                            $this->setForceExtension(1);
                            $output .= $this->module->displayWarning($this->module->l('Now you should clear cache of Prestashop in Performances page', 'jprestawebpmodule'));
                            $output .= $this->module->displayWarning($this->module->l('Now you should also clear your Cloudflare cache if any.', 'jprestawebpmodule'));
                        } elseif (!$newForceExtension && $oldForceExtension) {
                            $this->setForceExtension(0);
                            $output .= $this->module->displayWarning($this->module->l('Now you should clear cache of Prestashop in Performances page', 'jprestawebpmodule'));
                        }

                        $oldEnablePng = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG');
                        $newEnablePng = (int)Tools::getValue('SPEED_PACK_WEBP_ENABLE_PNG');
                        if ($newEnablePng && !$oldEnablePng) {
                            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG', 1);
                            $this->updateHtaccessFile();
                        } elseif (!$newEnablePng && $oldEnablePng) {
                            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG', 0);
                            $this->updateHtaccessFile();
                        }

                        $oldExcludePaths = JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS');
                        $newExcludePaths = Tools::getValue('SPEED_PACK_WEBP_EXCLUDE_PATHS', '');
                        // https://stackoverflow.com/a/38300330/1964939
                        $newExcludePaths = implode(',', preg_split('/\s*,\s*/', $newExcludePaths, -1, PREG_SPLIT_NO_EMPTY));
                        if (strcmp($oldExcludePaths, $newExcludePaths) !== 0) {
                            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', $newExcludePaths);
                            $this->updateHtaccessFile();
                        }

                        $this->updateConfigFile();

                        $output .= $this->module->displayConfirmation($this->module->l('Settings updated', 'jprestawebpmodule'));
                    }
                }
            } catch (Exception $e) {
                $output .= $this->module->displayError($e->getMessage());
            }
            return $output;
        }

        /**
         * @param $val boolean
         * @throws PrestaShopException
         */
        private function setForceExtension($val)
        {
            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION', $val ? 1 : 0);
            if (!Module::isEnabled('jprestathemeconfigurator')) {
                // jprestathemeconfigurator already include the needed override
                if ($val) {
                    try {
                        $this->enableOverride('classes/module/Module.php');
                    } catch (PrestashopException $e) {
                        JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION', 0);
                        throw $e;
                    }
                } else {
                    try {
                        $this->disableOverride('classes/module/Module.php');
                    } catch (PrestashopException $e) {
                        JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION', 1);
                        throw $e;
                    }
                }
            }
            $this->updateHtaccessFile();
            $this->module->clearCache('webp option force extension');
        }

        /**
         * @param $relPath
         * @throws PrestaShopException
         */
        private function enableOverride($relPath)
        {
            $overrideFullPath = _PS_MODULE_DIR_ . $this->module->name . '/override/' . $relPath;
            $overrideName = basename($overrideFullPath, '.php');
            if (!file_exists($overrideFullPath)) {
                if (!rename($overrideFullPath . '.off', $overrideFullPath)) {
                    throw new PrestaShopException($this->module->l('Link override not found in ' . $overrideFullPath . '.off'));
                }
            } else {
                // Already installed
                return;
            }
            if (!$this->module->addOverride($overrideName)) {
                if (file_exists($overrideFullPath)) {
                    rename($overrideFullPath, $overrideFullPath . '.off');
                }
                throw new PrestaShopException($this->module->l('Cannot install ' . $overrideName . ' override'));
            }
        }

        /**
         * @param $relPath
         * @throws PrestaShopException
         */
        private function disableOverride($relPath)
        {
            $overrideFullPath = _PS_MODULE_DIR_ . $this->module->name . '/override/' . $relPath;
            $overrideName = basename($overrideFullPath, '.php');
            if (file_exists($overrideFullPath)) {
                if (!$this->module->removeOverride($overrideName)) {
                    throw new PrestaShopException($this->module->l('Unable to remove ' . $overrideName . ' override'));
                }
            }
            if (file_exists($overrideFullPath)) {
                rename($overrideFullPath, $overrideFullPath . '.off');
            }
        }

        public function displayForm()
        {
            // Init Fields form array
            $fieldsForm = array();

            $convertersReports = $this->checkConverters();

            // Init Fields form array
            $fieldsForm[0]['form'] = [
                'legend' => [
                    'title' => $this->module->l('Images compression', 'jprestawebpmodule'),
                ]
            ];
            $fieldsForm[0]['form']['input'] = array();
            if (Shop::isFeatureActive()) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_info',
                    'name' => 'SPEED_PACK_WEBP_INFO',
                    'text' => $this->l('You can enable/disable image compression for each shop but the quality and the options will be the shared by all shops', 'jprestawebpmodule'),
                ];
            }
            if ($convertersReports['atLeastOne']) {
                //
                // Convertion can be enabled!
                //
                $webpUrl = false;
                $testImage = $this->getTestImage();
                if ($testImage) {
                    $webpUrl = $this->getWebpImageUrl($testImage['img']);
                }
                $homeImageType = ImageType::getByNameNType(JprestaUtils::getImageTypeFormattedName('home'), 'products');
                $imageWidth = 250;
                $imageHeight = 250;
                if ($homeImageType) {
                    $imageWidth = $homeImageType['width'];
                    $imageHeight = $homeImageType['height'];
                }

                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'converters_report',
                    'typeAlert' => 'alert-success',
                    'name' => 'SPEED_PACK_WEBP_INFO',
                    // Let this in ONE LINE or it will not be parsed by Prestashop
                    'text' => $this->l('Great we have a converter! _converter_ can be used to convert your images in WEBP format. No need to generate all images for hours, it will be done progressively.', 'jprestawebpmodule', array('_converter_' => '<b>' . $convertersReports['firstActiveConverter']['label'] . '</b>'), false, null, false, false),
                    'values' => $convertersReports
                ];

                if ((int)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE')) {
                    if (!JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION')) {
                        // Check 'Vary' header
                        $fieldsForm[0]['form']['input'][] = [
                            'type' => 'check_header_vary',
                            // Let this in ONE LINE or it will not be parsed by Prestashop
                            'text_error' => $this->l('Header "Vary: Accept" was not found. If you are using nginx you must add the following in your nginx.conf and then restart it:', 'jprestawebpmodule', null, false, null, false, false),
                            // Let this in ONE LINE or it will not be parsed by Prestashop
                            'text_ok' => $this->l('Header "Vary: Accept" is present, you can keep "Force .webp extension" option disabled.', 'jprestawebpmodule', null, false, null, false, false),
                            // Let this in ONE LINE or it will not be parsed by Prestashop
                            'text_dontknow' => $this->l('Cannot detect if header "Vary: Accept" is present, try to display your shop on iPhone or Mac to know if you must enable "Force .webp extension" option.', 'jprestawebpmodule', null, false, null, false, false),
                            'url_to_test' => $this->getTestImageUrl()
                        ];
                        $fieldsForm[0]['form']['input'][] = [
                            'type' => 'alert_info',
                            'name' => 'SPEED_PACK_WEBP_INFO',
                            'text' => $this->l('Be aware that URLs of images still ends with ".jpg" but format is WEBP; this make it more efficient for HTML cache.', 'jprestawebpmodule', null, false, null, false, false),
                        ];
                    }
                }

                if ($convertersReports['firstActiveConverter']['id'] === 'ewww') {
                    $fieldsForm[0]['form']['input'][] = [
                        'type' => 'text',
                        'label' => $this->module->l('EWWW cloud service API KEY', 'jprestawebpmodule'),
                        'name' => 'SPEED_PACK_WEBP_EWWW_KEY',
                        'desc' => $this->module->l('API key provided by EWWW website', 'jprestawebpmodule'),
                    ];
                }
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'switch',
                    'label' => $this->module->l('Enable', 'Admin.Actions'),
                    'name' => 'SPEED_PACK_WEBP_ENABLE',
                    'is_bool' => true,
                    'disabled' => !$convertersReports['atLeastOne'],
                    'desc' => $this->module->l('Enable compression of images with WEBP', 'jprestawebpmodule'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->module->l('Enabled', 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->module->l('Disabled', 'Admin.Global')
                        )
                    ),
                ];
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'webp_slider_quality',
                    'label' => $this->module->l('Quality', 'jprestawebpmodule'),
                    'name' => 'SPEED_PACK_WEBP_QUALITY',
                    'desc' => $this->module->l('Choose the minimum quality that is acceptable for you and your commercial team', 'jprestawebpmodule'),
                    'min' => 30,
                    'max' => 100,
                    'unit' => '%',
                    'disabled' => !(int)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE') || !$convertersReports['atLeastOne'],
                    'step' => 1,
                    'before' => array('url' => $webpUrl . '&original=1', 'width' => $imageWidth, 'height' => $imageHeight),
                    'after' => array('url' => $webpUrl, 'width' => $imageWidth, 'height' => $imageHeight, 'quality' => (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_QUALITY')),
                ];
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'switch',
                    'label' => $this->module->l('Force .webp extension', 'jprestawebpmodule'),
                    'name' => 'SPEED_PACK_WEBP_FORCE_EXTENSION',
                    'is_bool' => true,
                    // Let this in ONE LINE or it will not be parsed by Prestashop
                    'desc' => $this->module->l('Force .webp extension in HTML to fix issue with Cloudflare or when HTTP header "Vary: Accept" is not present. Only enable this option when images are not displayed on Safari or iPhones.', 'jprestawebpmodule'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->module->l('Enabled', 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->module->l('Disabled', 'Admin.Global')
                        )
                    ),
                ];
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'switch',
                    'label' => $this->module->l('Enable WEBP compression for PNG', 'jprestawebpmodule'),
                    'name' => 'SPEED_PACK_WEBP_ENABLE_PNG',
                    'is_bool' => true,
                    // Let this in ONE LINE or it will not be parsed by Prestashop
                    'desc' => $this->module->l('Old versions of GD (and possibly other converters) do not support transparency in PNG images. Therefore, here you can disable WEBP conversion for PNG images.', 'jprestawebpmodule'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->module->l('Enabled', 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->module->l('Disabled', 'Admin.Global')
                        )
                    ),
                ];
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'text',
                    'label' => $this->module->l('Excluded URL paths', 'jprestawebpmodule'),
                    'name' => 'SPEED_PACK_WEBP_EXCLUDE_PATHS',
                    // Let this in ONE LINE or it will not be parsed by Prestashop
                    'desc' => $this->module->l('Comma separated list of paths to exclude the WEBP compression, for example "/upload/,/wp-blog/"', 'jprestawebpmodule'),
                ];
                $faqUrl = 'htt' . 'ps://jpresta' . '.c' . 'om/' . Context::getContext()->language->iso_code . '/faq/webp';
                Context::getContext()->smarty->assign('faq_url', $faqUrl);
                Context::getContext()->smarty->assign('faq_url_start', '<a href="' . $faqUrl . '" target="_blank">');
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'html',
                    'col' => 12,
                    'label' => '',
                    'name' => 'SPEED_PACK_WEBP_FAQ',
                    'html_content' => (Tools::version_compare(_PS_VERSION_, '1.7', '>=') && method_exists($this->module, 'fetch')) ? $this->module->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/webp-faq.tpl') : $this->module->display($this->module->name, '/views/templates/admin/webp-faq-ps16.tpl'),
                ];
            } else {
                //
                // Convertion CANNOT be enabled :(
                //
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'converters_report',
                    'typeAlert' => 'alert-warning',
                    'name' => 'SPEED_PACK_WEBP_INFO',
                    // Let this in ONE LINE or it will not be parsed by Prestashop
                    'text' => $this->module->l('Bad news! No converter is working on your server, check details bellow to know why. As a last chance to be able to compress your images you can buy the minimum credits (3$) for EWWW cloud service at https://ewww.io/buy-credits/ (compress API plan is enougth) and set your API key bellow.', 'jprestawebpmodule'),
                    'values' => $convertersReports
                ];
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'text',
                    'label' => $this->module->l('EWWW cloud service API KEY', 'jprestawebpmodule'),
                    'name' => 'SPEED_PACK_WEBP_EWWW_KEY',
                    'desc' => $this->module->l('API key provided by EWWW website', 'jprestawebpmodule'),
                ];
            }
            $fieldsForm[0]['form']['submit'] = [
                'title' => $this->module->l('Save', 'Admin.Actions'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitWebp'
            ];

            $helper = new HelperForm();
            $helper->module = $this->module;

            // Load current value
            $helper->fields_value['SPEED_PACK_WEBP_ENABLE'] = (int)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE');
            $helper->fields_value['SPEED_PACK_WEBP_FORCE_EXTENSION'] = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION');
            $helper->fields_value['SPEED_PACK_WEBP_QUALITY'] = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_QUALITY');
            $helper->fields_value['SPEED_PACK_WEBP_ENABLE_PNG'] = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG');
            $helper->fields_value['SPEED_PACK_WEBP_EXCLUDE_PATHS'] = JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS');

            if (_PS_MODE_DEMO_ && !Context::getContext()->employee->isSuperAdmin()) {
                $helper->fields_value['SPEED_PACK_WEBP_EWWW_KEY'] = 'XXXXXXXXXXXXXXXXXXXXXX';
            } else {
                $helper->fields_value['SPEED_PACK_WEBP_EWWW_KEY'] = JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EWWW_KEY');
            }

            return $helper->generateForm($fieldsForm);
        }

        private function checkConverters()
        {
            static $report = false;
            if (!$report) {

                // Reset errors
                JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EWWW_ERROR', false);

                $src = _PS_MODULE_DIR_ . $this->module->name . '/views/img/test_pattern.jpg';
                $dst = _PS_MODULE_DIR_ . $this->module->name . '/views/img/test_pattern.webp';
                $converters = [
                    'cwebp' => ['label' => 'Cwebp binary'],
                    'vips' => ['label' => 'Vips PHP extension'],
                    'imagick' => ['label' => 'Imagick PHP extension'],
                    'gmagick' => ['label' => 'Gmagick PHP extension'],
                    'imagemagick' => ['label' => 'Imagemagick binary'],
                    'graphicsmagick' => ['label' => 'Graphicsmagick binary (gm)'],
                    'gd' => ['label' => 'Gd PHP extension'],
                    'ewww' => ['label' => 'EWWW cloud service'],
                ];
                $report = array();
                $report['firstActiveConverter'] = false;
                $report['atLeastOne'] = false;
                $report['atLeastOneNotEwww'] = false;
                foreach ($converters as $converterId => $options) {
                    $logger = new BufferLogger();
                    $report[$converterId] = array();
                    $report[$converterId]['id'] = $converterId;
                    $report[$converterId]['log'] = $converterId . ': ';
                    $report[$converterId]['label'] = $options['label'];
                    $report[$converterId]['error'] = false;
                    try {
                        $converterInstance = ConverterFactory::makeConverter($converterId, $src, $dst, self::getConverterOptions(), $logger);
                        $converterInstance->checkOperationality();
                        $startTime = microtime(true) * 1000;
                        $converterInstance->doConvert();
                        $report[$converterId]['duration_ms'] = microtime(true) * 1000 - $startTime;
                        $report[$converterId]['disabled'] = false;
                        $report[$converterId]['disable_png'] = false;
                        $report['atLeastOne'] = true;
                        $report['atLeastOneNotEwww'] = $report['atLeastOneNotEwww'] || $converterId !== 'ewww';
                        if ($report['firstActiveConverter'] === false) {
                            $report['firstActiveConverter'] = $report[$converterId];
                        } elseif ($report['firstActiveConverter']['duration_ms'] > $report[$converterId]['duration_ms']) {
                            $report['firstActiveConverter'] = $report[$converterId];
                        }
                    } catch (ConversionFailedException $conversionFailedException) {
                        $report[$converterId]['disabled'] = true;
                        $logger->logLn($conversionFailedException->getMessage());
                        $report[$converterId]['error'] = $conversionFailedException->getMessage();
                    }
                    $report[$converterId]['log'] .= $logger->getHtml();
                }

                // Save the converter to use
                if ($report['atLeastOne']) {
                    JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_TO_USE', $report['firstActiveConverter']['id']);
                } else {
                    JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_TO_USE', false);
                }

                $this->updateConfigFile();
            }
            return $report;
        }

        public static function handleError()
        {
            // Will be reset as soon as we check converters again
            JprestaUtils::saveConfigurationAllShop('SPEED_PACK_WEBP_EWWW_ERROR', true);
        }

        /**
         * Save configuration into a file that can be included in webp controller to avoid loading Prestashop engine
         * when converting an image. It also avoid max_user_connections errors when a lot of images are converted.
         */
        public function updateConfigFile()
        {
            file_put_contents(_PS_MODULE_DIR_ . $this->module->name . '/webp.config.php',
                "<?php\n\$jprestaWebpConverterOptions = " . var_export($this->getConverterOptions(), true) . ";\n");
        }

        public static function getConverterOptions($quality = false)
        {
            if ($quality) {
                $quality = min(100, max(0, (int)$quality));
            } else {
                $quality = (int)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_QUALITY');
            }
            $quality = min(100, max(0, (int)$quality));
            $jprestaWebpConverterOptions = array(
                'default-quality' => $quality,
                'quality' => $quality,
                'converters' => array(JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_CONVERTER_TO_USE')),
                'ewww-api-key' => JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EWWW_KEY'),
                'converter-options' => array(
                    'ewww' => array(
                        'api-key' => JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EWWW_KEY'),
                        'check-key-status-before-converting' => JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EWWW_ERROR')
                    )
                ),
            );
            return $jprestaWebpConverterOptions;
        }

        /**
         * @param $file string Image file that has been modified
         */
        public function onImageModification($file)
        {
            if (!$file){
                return;
            }
            $source = realpath($file);
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            if (in_array($extension, array('png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'))) {
                $webpFile = preg_replace('/(.*)\.' . $extension . '$/i', '$1.webp', $source);
                JprestaUtils::deleteFile($webpFile);
            }
        }

        /**
         * Only used for PS < 1.7
         */
        public function hookActionDispatcher($params)
        {
            if ((bool)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE')
                && (bool)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION')
                && JprestaUtils::currentVisitorAcceptWebp()
            ) {
                Context::getContext()->smarty->registerFilter('output', array(get_class($this), 'filterOutput'));
                Context::getContext()->smarty->assignGlobal('jpresta_physical_uri', Context::getContext()->shop->physical_uri);
            }
        }

        /**
         * Called by Smarty for PS < 1.7
         */
        public static function filterOutput($output, $smarty)
        {
            // Do not use 'self', keep 'JprestaWebpModule'
            $context = Context::getContext();
            return \JPresta\Webp\JprestaWebpModule::replaceWithWebpExtension($output,
                $context->shop->getBaseURL(true),
                $context->shop->physical_uri
            );
        }

        /**
         * Called by the parent module for PS >= 1.7
         */
        public function hookActionOutputHTMLBefore($params)
        {
            if ((bool)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE')
                && (bool)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION')
                && JprestaUtils::currentVisitorAcceptWebp()
            ) {
                $context = Context::getContext();
                $params['html'] = self::replaceWithWebpExtension(
                    $params['html'],
                    $context->shop->getBaseURL(true),
                    $context->shop->physical_uri);
            }
        }

        public function hookActionAjaxDieSearchControllerdoProductSearchBefore($params)
        {
            $this->hookActionAjaxDieCategoryControllerdoProductSearchBefore($params);
        }

        public function hookActionAjaxDieCategoryControllerdoProductSearchBefore($params)
        {
            if ((bool)JprestaUtils::getConfigurationOfCurrentShop('SPEED_PACK_WEBP_ENABLE')
                && (bool)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION')
                && JprestaUtils::currentVisitorAcceptWebp()
            ) {
                try {
                    $extensionToReplace = ['.jpg', '.png', '.jpeg'];
                    if (!JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG')) {
                        $extensionToReplace = ['.jpg', '.jpeg'];
                    }
                    $context = Context::getContext();
                    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    $json = json_decode($params['value'], true);
                    if (isset($json['rendered_products_header'])) {
                        $json['rendered_products_header'] = self::replaceWithWebpExtension(
                            $json['rendered_products_header'],
                            $context->shop->getBaseURL(true),
                            $context->shop->physical_uri);
                    }
                    if (isset($json['rendered_products_top'])) {
                        $json['rendered_products_top'] = self::replaceWithWebpExtension(
                            $json['rendered_products_top'],
                            $context->shop->getBaseURL(true),
                            $context->shop->physical_uri);
                    }
                    if (isset($json['rendered_products'])) {
                        $json['rendered_products'] = self::replaceWithWebpExtension(
                            $json['rendered_products'],
                            $context->shop->getBaseURL(true),
                            $context->shop->physical_uri);
                    }
                    if (isset($json['rendered_products_bottom'])) {
                        $json['rendered_products_bottom'] = self::replaceWithWebpExtension(
                            $json['rendered_products_bottom'],
                            $context->shop->getBaseURL(true),
                            $context->shop->physical_uri);
                    }
                    foreach ($json['products'] as &$product) {
                        if (isset($product['cover'])) {
                            foreach ($product['cover']['bySize'] as $size => &$image) {
                                $image['url'] = str_replace($extensionToReplace, '.webp', $image['url']);
                            }
                            if (isset($product['cover']['small'])) {
                                $product['cover']['small']['url'] = str_replace($extensionToReplace, '.webp',
                                    $product['cover']['small']['url']);
                            }
                            if (isset($product['cover']['medium'])) {
                                $product['cover']['medium']['url'] = str_replace($extensionToReplace, '.webp',
                                    $product['cover']['medium']['url']);
                            }
                            if (isset($product['cover']['large'])) {
                                $product['cover']['large']['url'] = str_replace($extensionToReplace, '.webp',
                                    $product['cover']['large']['url']);
                            }
                        }
                    }
                    echo json_encode($json);
                    exit;
                } catch (Exception $e) {
                    // Ignore it and process with standard code
                }
            }
        }

        public static function replaceWithWebpExtension($html, $baseURL, $physicalUri, $cdn1 = null, $cdn2 = null, $cdn3 = null, $preservePng = null, $pathsToExclude = null)
        {
            if ($cdn1 === null && defined('_MEDIA_SERVER_1_')) {
                $cdn1 = _MEDIA_SERVER_1_;
            }
            if ($cdn2 === null && defined('_MEDIA_SERVER_2_')) {
                $cdn2 = _MEDIA_SERVER_2_;
            }
            if ($cdn3 === null && defined('_MEDIA_SERVER_3_')) {
                $cdn3 = _MEDIA_SERVER_3_;
            }
            if ($preservePng === null) {
                $preservePng = !JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG');
            }
            if ($pathsToExclude === null) {
                $pathsToExclude = JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', '');
                $pathsToExclude = preg_split('/\s*,\s*/', $pathsToExclude, -1, PREG_SPLIT_NO_EMPTY);
            }
            if (!JprestaUtils::endsWith($baseURL, '/')) {
                $baseURL .= '/';
            }
            if (!JprestaUtils::startsWith($physicalUri, '/')) {
                $physicalUri = '/' . $physicalUri;
            }
            if (!JprestaUtils::endsWith($physicalUri, '/')) {
                $physicalUri .= '/';
            }
            $patterns = array();

            $extensionRegex = '(?:jpe?g|png)(\?[^"]*)?';
            if ($preservePng) {
                $extensionRegex = '(?:jpe?g)(\?[^"]*)?';
            }

            $baseURLRegex = preg_quote($baseURL, '/');
            $baseURLRegex = preg_replace('/https?:/i', '(?:https?:)?', $baseURLRegex);

            $escapedPathsList = '';
            if (count($pathsToExclude) > 0) {
                // Remove leading slash because we know we have one
                $escapedPathsList = implode('|', array_map(function ($str) {
                    return preg_quote(ltrim($str, '/'), '/');
                }, $pathsToExclude));
            }

            // For normal URLs with domain name
            $exludeUploadDir = '';
            if ($escapedPathsList) {
                $exludeUploadDir = '(?!' . $baseURLRegex . '(?:'.$escapedPathsList.'))';
            }
            $patterns[] = '/"' . $exludeUploadDir . '(' . $baseURLRegex . '[^"]+)\.' . $extensionRegex . '"/i';

            // For direct URLs without domain
            $exludeUploadDir = '';
            if ($escapedPathsList) {
                $exludeUploadDir = '(?!' . preg_quote($physicalUri, '/') . '(?:'.$escapedPathsList.'))';
            }
            $patterns[] = '/"' . $exludeUploadDir . '(' . preg_quote($physicalUri, '/') . '[^"]+)\.' . $extensionRegex . '"/i';

            // For URLs using CDN
            if ($cdn1) {
                $server1URLRegex = preg_quote($cdn1, '/');
                $exludeUploadDir1 = '';
                if ($escapedPathsList) {
                    $exludeUploadDir1 = '(?!' . $server1URLRegex . preg_quote($physicalUri, '/') . '(?:'.$escapedPathsList.'))';
                }
                $patterns[] = '/"((?:https?:)?\/\/' . $exludeUploadDir1 . $server1URLRegex . preg_quote($physicalUri, '/') . '[^"]+)\.' . $extensionRegex . '"/i';
                if ($cdn2) {
                    $server2URLRegex = preg_quote($cdn2, '/');
                    $exludeUploadDir2 = '';
                    if ($escapedPathsList) {
                        $exludeUploadDir2 = '(?!' . $server2URLRegex . preg_quote($physicalUri, '/') . '(?:'.$escapedPathsList.'))';
                    }
                    $patterns[] = '/"((?:https?:)?\/\/' . $exludeUploadDir2 . $server2URLRegex . preg_quote($physicalUri, '/') . '[^"]+)\.' . $extensionRegex . '"/i';
                    if ($cdn3) {
                        $server3URLRegex = preg_quote($cdn3, '/');
                        $exludeUploadDir3 = '';
                        if ($escapedPathsList) {
                            $exludeUploadDir3 = '(?!' . $server3URLRegex . preg_quote($physicalUri, '/') . '(?:'.$escapedPathsList.'))';
                        }
                        $patterns[] = '/"((?:https?:)?\/\/' . $exludeUploadDir3 . $server3URLRegex . preg_quote($physicalUri, '/') . '[^"]+)\.' . $extensionRegex . '"/i';
                    }
                }
            }
            return preg_replace($patterns, '"$1.webp$2"', $html);
        }

        public function updateHtaccessFile($disableAll = false)
        {
            $path = _PS_ROOT_DIR_ . '/.htaccess';

            // Check current content of .htaccess and save all code outside of prestashop comments
            $specific_before = $specific_after = '';
            if (file_exists($path)) {
                $content = Tools::file_get_contents($path);
                if (preg_match('#^(.*)\# ~~start-jprestaspeedpack~~.*\# ~~end-jprestaspeedpack~~[^\n]*(.*)$#s', $content, $m)) {
                    $specific_before = $m[1];
                    $specific_after = $m[2];
                } else {
                    $specific_after = $content;
                }
            }

            $speedpackcontent = '';
            if ($specific_before) {
                $speedpackcontent .= trim($specific_before) . "\n";
            }

            $domains = JprestaUtils::getDomains();

            // Check if at least one shop is enabled for WEBP
            $allDisabled = true;
            $allForceExtension = true;

            if (!$disableAll) {
                $speedpackcontentTmp = "# ~~start-jprestaspeedpack~~ Do not remove this comment, Jprestaspeedpack will update it automatically\n";

                $speedpackcontentTmp .= "\n# Allow webp files to be sent by Apache 2.2\n";
                $speedpackcontentTmp .= "<IfModule !mod_authz_core.c>\n\t<Files ~ \"\\.(webp)$\">\n\t\tAllow from all\n\t</Files>\n</IfModule>\n";
                $speedpackcontentTmp .= "\n# Allow webp files to be sent by Apache 2.4\n";
                $speedpackcontentTmp .= "<IfModule mod_authz_core.c>\n\t<Files ~ \"\\.(webp)$\">\n\t\tRequire all granted\n\t</Files>\n</IfModule>\n";

                $speedpackcontentTmp .= "\n# Send WEBP image if browser accept and if file exists\n";
                $speedpackcontentTmp .= "# Otherwise, redirect to the controller that will genererate the WEBP image\n";
                $speedpackcontentTmp .= "<IfModule mod_rewrite.c>\n";
                $speedpackcontentTmp .= "<IfModule mod_env.c>\nSetEnv HTTP_MOD_REWRITE On\n</IfModule>\n";
                $speedpackcontentTmp .= "RewriteEngine on\n";

                $medias = false;
                if (!$medias && Configuration::getMultiShopValues('PS_MEDIA_SERVER_1')
                    && Configuration::getMultiShopValues('PS_MEDIA_SERVER_2')
                    && Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
                ) {
                    $medias = array(
                        Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                        Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                        Configuration::getMultiShopValues('PS_MEDIA_SERVER_3'),
                    );
                }

                $media_domains = '';
                foreach ($medias as $media) {
                    foreach ($media as $media_url) {
                        if ($media_url) {
                            $media_domains .= 'RewriteCond %{HTTP_HOST} ^' . $media_url . '$ [OR]' . PHP_EOL;
                        }
                    }
                }
                $disablePng = !JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_ENABLE_PNG');
                $accept_webp_cond = 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
                $webpUrl = 'modules/' . $this->module->name . '/controllers/front/webp.php?';
                foreach ($domains as $domain => $list_uri) {
                    // As we use regex in the htaccess, ipv6 surrounded by brackets must be escaped
                    $domain = str_replace(['[', ']'], ['\[', '\]'], $domain);
                    $domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^' . $domain . '$' . PHP_EOL;

                    $speedpackcontentTmp .= "\n#Domain: $domain\n";
                    // URIs must be sorted from longest to shortest to avoid sub folders to be ignored by parent folders
                    usort($list_uri, function ($uri1, $uri2) {
                        if (isset($uri1['virtual']) && isset($uri2['virtual'])) {
                            return strlen($uri2['virtual']) - strlen($uri1['virtual']);
                        }
                        return 0;
                    });
                    foreach ($list_uri as $uri) {
                        $moduleEnabledOnShop = (bool)JprestaUtils::isModuleEnabledByShopId($this->module->id, $uri['id_shop']);
                        $webpEnabledOnShop = (bool)JprestaUtils::getConfigurationByShopId('SPEED_PACK_WEBP_ENABLE', $uri['id_shop']);
                        $forceExtensionEnabledOnShop = (bool)JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_FORCE_EXTENSION');
                        $excludedPathsList = JprestaUtils::getConfigurationAllShop('SPEED_PACK_WEBP_EXCLUDE_PATHS', '');
                        $excludedPaths = [];
                        if ($excludedPathsList) {
                            $excludedPaths = explode(',', $excludedPathsList);
                        }
                        $rewriteURLEnabledOnShop = (bool)JprestaUtils::getConfigurationByShopId('PS_REWRITING_SETTINGS', $uri['id_shop']);

                        if (!$moduleEnabledOnShop || !$webpEnabledOnShop) {
                            // WEBP not enabled on this shop
                            continue;
                        }

                        $allDisabled = false;
                        $imageExtension = 'jpg';
                        if ($forceExtensionEnabledOnShop) {
                            $imageExtension = 'webp';
                        } else {
                            $allForceExtension = false;
                        }

                        if (Shop::isFeatureActive()) {
                            $speedpackcontentTmp .= PHP_EOL . '# URLs under ' . $uri['physical'] . $uri['virtual'] . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteCond %{HTTP_HOST} ^' . $domain . '$' . PHP_EOL;
                        }
                        $speedpackcontentTmp .= 'RewriteRule . - [E=REWRITEBASE:' . $uri['physical'] . ']' . PHP_EOL;
                        $virtualFolder = trim($uri['virtual'], '/') . '/';
                        if ($virtualFolder === '/') {
                            $virtualFolder = '';
                        }

                        if ($rewriteURLEnabledOnShop) {
                            if (Module::isEnabled('smartblog')) {
                                $speedpackcontentTmp .= "\n# Images of Smartblog module\n";
                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/modules/smartblog/images/$1$2$3.webp -s' . PHP_EOL;
                                $speedpackcontentTmp .=
                                    'RewriteRule ^' . $virtualFolder . 'blog/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.' . $imageExtension . '$ %{ENV:REWRITEBASE}modules/smartblog/images/$1$2$3.webp [T=image/webp,L]' . PHP_EOL;

                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2$3.webp !-s' . PHP_EOL;
                                $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . 'blog/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                    '%{ENV:REWRITEBASE}' . $webpUrl .
                                    'src=modules/smartblog/images/$1$2$3.jpg' .
                                    ' [B,L]' . PHP_EOL;

                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/modules/smartblog/images/$1$2.webp -s' . PHP_EOL;
                                $speedpackcontentTmp .=
                                    'RewriteRule ^' . $virtualFolder . 'blog/([a-zA-Z_-]+)(-[0-9]+)?/.+\.' . $imageExtension . '$ %{ENV:REWRITEBASE}modules/smartblog/images/$1$2.webp [T=image/webp,L]' . PHP_EOL;

                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2.webp !-s' . PHP_EOL;
                                $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . 'blog/([a-zA-Z_-]+)(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                    '%{ENV:REWRITEBASE}' . $webpUrl .
                                    'src=modules/smartblog/images/$1$2.jpg' .
                                    ' [B,L]' . PHP_EOL;
                            }

                            $speedpackcontentTmp .= "\n# Images of products\n";
                            // Rewrite product images < 100 millions
                            for ($i = 1; $i <= 8; ++$i) {
                                $img_path = $img_name = '';
                                for ($j = 1; $j <= $i; ++$j) {
                                    $img_path .= '$' . $j . '/';
                                    $img_name .= '$' . $j;
                                }
                                $img_name .= '$' . $j;
                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/p/' . $img_path . $img_name . '$' . ($j + 1) . '.webp -s' . PHP_EOL;
                                $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . '' . str_repeat('([0-9])', $i) .
                                    '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                    '%{ENV:REWRITEBASE}img/p/' . $img_path . $img_name . '$' . ($j + 1) . '.webp ' .
                                    '[T=image/webp,L]' . PHP_EOL;

                                $speedpackcontentTmp .= $media_domains;
                                $speedpackcontentTmp .= $domain_rewrite_cond;
                                if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                                $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/p/' . $img_path . $img_name . '$' . ($j + 1) . '.webp !-s' . PHP_EOL;
                                $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . '' . str_repeat('([0-9])', $i) .
                                    '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                    '%{ENV:REWRITEBASE}' . $webpUrl .
                                    'src=img/p/' . $img_path . $img_name . '$' . ($j + 1) . '.jpg' .
                                    ' [B,L]' . PHP_EOL;
                            }

                            // Rewrite images of categories
                            $speedpackcontentTmp .= "\n# Images of categories\n";
                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2$3.webp -s' . PHP_EOL;
                            $speedpackcontentTmp .=
                                'RewriteRule ^' . $virtualFolder . 'c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.' . $imageExtension . '$ %{ENV:REWRITEBASE}img/c/$1$2$3.webp [T=image/webp,L]' . PHP_EOL;

                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2$3.webp !-s' . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . 'c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                '%{ENV:REWRITEBASE}' . $webpUrl .
                                'src=img/c/$1$2$3.jpg' .
                                ' [B,L]' . PHP_EOL;

                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2.webp -s' . PHP_EOL;
                            $speedpackcontentTmp .=
                                'RewriteRule ^' . $virtualFolder . 'c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.' . $imageExtension . '$ %{ENV:REWRITEBASE}img/c/$1$2.webp [T=image/webp,L]' . PHP_EOL;

                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            if (!$forceExtensionEnabledOnShop) $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}/img/c/$1$2.webp !-s' . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . 'c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.' . $imageExtension . '$ ' .
                                '%{ENV:REWRITEBASE}' . $webpUrl .
                                'src=img/c/$1$2.jpg' .
                                ' [B,L]' . PHP_EOL;
                        }

                        $speedpackcontentTmp .= "\n# Other images of modules or theme\n";
                        if (!$forceExtensionEnabledOnShop) {
                            $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{REQUEST_FILENAME} -s' . PHP_EOL;
                            foreach ($excludedPaths as $excludedPath) {
                                $speedpackcontentTmp .= 'RewriteCond %{REQUEST_URI} !'.$excludedPath.' [NC]' . PHP_EOL;
                            }
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}$1.webp -s' . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . '(.*)\.((?i)jpe?g' . ($disablePng ? '' : '|png') . ')$ %{ENV:REWRITEBASE}$1.webp [T=image/webp,L]' . PHP_EOL;
                            $speedpackcontentTmp .= $accept_webp_cond;
                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{REQUEST_FILENAME} -s' . PHP_EOL;
                            foreach ($excludedPaths as $excludedPath) {
                                $speedpackcontentTmp .= 'RewriteCond %{REQUEST_URI} !'.$excludedPath.' [NC]' . PHP_EOL;
                            }
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}$1.webp !-s' . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . '(.*)\.((?i)jpe?g' . ($disablePng ? '' : '|png') . ')$ ' .
                                '%{ENV:REWRITEBASE}' . $webpUrl .
                                'src=$1.$2' .
                                ' [B,L]' . PHP_EOL;
                        } else {
                            $speedpackcontentTmp .= $media_domains;
                            $speedpackcontentTmp .= $domain_rewrite_cond;
                            $speedpackcontentTmp .= 'RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}$1.webp !-s' . PHP_EOL;
                            $speedpackcontentTmp .= 'RewriteRule ^' . $virtualFolder . '(.+)\.webp$ ' .
                                '%{ENV:REWRITEBASE}' . $webpUrl .
                                'src=$1.jpg' .
                                ' [B,L]' . PHP_EOL;
                        }
                    }
                    $speedpackcontentTmp .= "#/Domain: $domain\n";
                }
                $speedpackcontentTmp .= "</IfModule>\n";

                if (!$allForceExtension) {
                    $speedpackcontentTmp .= "\n# Add 'Vary' header to indicate to proxies that images depends on 'Accept' header\n";
                    $speedpackcontentTmp .= "<IfModule mod_setenvif.c>\n\tSetEnvIf Request_URI \"\\.((?i)jpe?g" . ($disablePng ? '' : '|png') . "|webp)$\" REQUEST_image\n</IfModule>\n";
                    $speedpackcontentTmp .= "<IfModule mod_headers.c>\n\tHeader append Vary Accept env=REQUEST_image\n</IfModule>\n";
                }

                $speedpackcontentTmp .= "\n# Make Apache handle image/webp content type\n";
                $speedpackcontentTmp .= "<IfModule mod_mime.c>\n\tAddType image/webp .webp\n</IfModule>\n";

                if ((int)Configuration::get('PS_HTACCESS_CACHE_CONTROL') > 0) {
                    $speedpackcontentTmp .= "\n<IfModule mod_expires.c>\n";
                    $speedpackcontentTmp .= "    ExpiresActive On\n";
                    $speedpackcontentTmp .= "    ExpiresByType image/webp \"access plus 1 month\"\n";
                    $speedpackcontentTmp .= "</IfModule>\n";
                }

                $speedpackcontentTmp .= "\n# ~~end-jprestaspeedpack~~ Do not remove this comment, Jprestaspeedpack will update it automatically\n";

                if (!$allDisabled) {
                    $speedpackcontent .= $speedpackcontentTmp;
                }
            }

            if ($specific_after) {
                $speedpackcontent .= "\n" . trim($specific_after);
            }

            // Write .htaccess data
            if (!$write_fd = @fopen($path, 'wb')) {
                JprestaUtils::addLog('WEBP: webp cannot be enabled because I cannot open file for writting ' . $path, 3);
                return false;
            }
            // Write in one shot to avoid error during writting
            if (fwrite($write_fd, $speedpackcontent) === FALSE) {
                JprestaUtils::addLog('WEBP: webp cannot be enabled because I cannot write into file ' . $path, 3);
                return false;
            }
            fclose($write_fd);

            return true;
        }

        /**
         * Delete all files with .webp extension in modules and images directories
         */
        private function deleteWebpImages()
        {
            $files = JprestaUtils::glob_recursive(_PS_MODULE_DIR_ . '*.webp');
            $files = array_merge($files, JprestaUtils::glob_recursive(_PS_IMG_DIR_ . '*.webp'));
            foreach ($files as $filename) {
                $jpegFile = str_replace('.webp', '.jpg', $filename);
                $pngFile = str_replace('.webp', '.png', $filename);
                if (file_exists($jpegFile) || file_exists($pngFile)) {
                    // Avoid deleting WEBP images not generated by the module
                    JprestaUtils::deleteFile($filename);
                }
            }
        }

        /**
         * @return bool|array First image of first active product, or false
         */
        private function getTestImage()
        {
            $result = false;
            $sql = 'SELECT *
                    FROM `' . _DB_PREFIX_ . 'product` p ' . Shop::addSqlAssociation('product', 'p') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)' . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' . Shop::addSqlAssociation('image', 'i') . '
                    WHERE p.`active` = 1';
            $row = Db::getInstance()->getRow($sql);
            if ($row) {
                $img = new Image($row['id_image']);
                $result = [];
                $result['img'] = $img;
                $result['rewrite'] = $row['link_rewrite'];
            }
            return $result;
        }

        /**
         * @return string URL to test HTTP headers of images
         */
        private function getTestImageUrl()
        {
            $imgTest = $this->getTestImage();
            $imgLink = Context::getContext()->link->getImageLink($imgTest['rewrite'], $imgTest['img']->id, JprestaUtils::getImageTypeFormattedName('home'));
            return $imgLink;
        }

        /**
         * @param Image $img
         * @param string $format
         * @return string URL to convert the image in WEBP format
         */
        private function getWebpImageUrl($img, $format = 'home')
        {
            if (Shop::isFeatureActive()) {
                $id_shop = array_values(Shop::getContextListShopID())[0];
                $shop = new Shop($id_shop);
                $baseUri = $shop->getBaseURL(true);
            } else {
                $baseUri = __PS_BASE_URI__;
            }
            //$webpUrl = $baseUri . 'index.php?fc=module&module='.$this->module->name.'&controller=webp&';
            $webpUrl = $baseUri . 'modules/' . $this->module->name . '/controllers/front/webp.php?';

            if ($img) {
                $imageTest = '/img/p/' . $img->getExistingImgPath() . '-' . JprestaUtils::getImageTypeFormattedName($format);
                $webpUrl .= 'src=' . $imageTest . '.jpg';
            }
            return $webpUrl;
        }
    }
}
