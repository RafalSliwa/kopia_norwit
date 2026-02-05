<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderCountdown extends Module
{
    /** @var Context */
    protected $context;

    public function __construct()
    {
        $this->name = 'ordercountdown';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'norwit.pl';
        $this->context = Context::getContext();

        parent::__construct();

        $this->displayName = $this->trans('Order Countdown', [], 'Modules.Ordercountdown.Admin');
        $this->description = $this->trans('Displays a countdown to placing an order with the option of same-day shipping.', [], 'Modules.Ordercountdown.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayOrderCountdown')
            && $this->registerHook('displayOrderCountdownMobile');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Register assets only on product page.
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        if (!isset($this->context->controller) || $this->context->controller->php_self !== 'product') {
            return;
        }

        // JS
        $this->context->controller->registerJavascript(
            'module-'.$this->name.'-js',
            'modules/'.$this->name.'/views/js/ordercountdown.js',
            [
                'position' => 'bottom',
                'priority' => 50,
                'server'   => 'current',
            ]
        );

        // CSS
        $this->context->controller->registerStylesheet(
            'module-'.$this->name.'-css',
            'modules/'.$this->name.'/views/css/ordercountdown.css',
            [
                'media'   => 'all',
                'priority'=> 50,
                'server'  => 'current',
            ]
        );

        // Messages exported to JS (translations)
        Media::addJsDef([
            'orderCountdownMessages' => [
                'fridayBeforeCutoff'   => $this->l('Arrives on Monday'),
                'fridayAfterCutoff'    => $this->l('Arrives on Tuesday'),
                'thursdayBeforeCutoff' => $this->l('Arrives tomorrow'),
                'thursdayAfterCutoff'  => $this->l('Arrives on Monday'),
                'weekend'              => $this->l('Arrives on Tuesday'),
                'weekdayBeforeCutoff'  => $this->l('Arrives tomorrow'),
                'mondayAfterCutoff'    => $this->l('Arrives on Wednesday'),
                'tuesdayAfterCutoff'   => $this->l('Arrives on Thursday'),
                'wednesdayAfterCutoff' => $this->l('Arrives on Friday'),
            ],
        ]);
    }

    public function hookDisplayOrderCountdown($params)
    {
        return $this->renderCountdown($params, 'displayCountdown.tpl');
    }

    public function hookDisplayOrderCountdownMobile($params)
    {
        return $this->renderCountdown($params, 'displayCountdownMobile.tpl');
    }

    /**
     * Shared rendering logic (desktop/mobile).
     */
    private function renderCountdown(array $params, $template)
    {
        // Product ID (from hook)
        $idProduct = (int)($params['product']['id_product'] ?? 0);

        // Try current combination from request (present after variant change via refresh)
        $idProductAttribute = (int)Tools::getValue('id_product_attribute', 0);

        // If not provided, try to resolve from product payload
        if (!$idProductAttribute && !empty($params['product'])) {
            if (!empty($params['product']['id_product_attribute'])) {
                $idProductAttribute = (int)$params['product']['id_product_attribute'];
            } elseif (!empty($params['product']['id_product_attribute_default'])) {
                $idProductAttribute = (int)$params['product']['id_product_attribute_default'];
            } elseif (!empty($params['product']['cache_default_attribute'])) {
                $idProductAttribute = (int)$params['product']['cache_default_attribute'];
            }
        }

        // Final fallback: query default combination from DB
        if (!$idProductAttribute && $idProduct) {
            $idProductAttribute = (int)Product::getDefaultAttribute($idProduct);
        }

        // Available quantity for the resolved combination (shop-aware)
        $idShop = (int)$this->context->shop->id;
        $quantity = (int)StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute, $idShop);

        // Timezone based on shop config (safer than global date_default_timezone_set)
        $tzName = (string)Configuration::get('PS_TIMEZONE');
        if (!$tzName) {
            $tzName = @date_default_timezone_get() ?: 'Europe/Warsaw';
        }
        $tz   = new DateTimeZone($tzName);
        $now  = new DateTime('now', $tz);

        // Compute nearest cutoff (13:00 local time, skip weekends)
        $cutoff = clone $now;
        $cutoff->setTime(13, 0, 0);

        $dayOfWeek = (int)$now->format('N'); // 1=Mon ... 7=Sun
        if ($dayOfWeek >= 6) {
            // Saturday/Sunday → next Monday 13:00
            $cutoff = new DateTime('next monday 13:00', $tz);
        } elseif ($now > $cutoff) {
            // After 13:00 → tomorrow 13:00
            $cutoff->modify('+1 day');
        }

        $interval = $now->diff($cutoff);

        // Get available_later from product (custom delivery time text)
        $idLang = (int)$this->context->language->id;
        $availableLater = '';
        if ($quantity <= 0 && $idProduct) {
            $availableLater = $this->getAvailableLater($idProduct, $idProductAttribute, $idLang);
        }

        // Determine availability message for out of stock products
        // Use available_later if set, otherwise fall back to PS config
        $outOfStockMessage = '';
        if (!empty($availableLater)) {
            $outOfStockMessage = $availableLater;
        } else {
            // Get default label from PrestaShop configuration
            $config = Configuration::get('PS_LABEL_OOS_PRODUCTS_BOD', null, null, $idShop);
            if (is_array($config) && isset($config[$idLang])) {
                $outOfStockMessage = $config[$idLang];
            } elseif (is_string($config)) {
                $outOfStockMessage = $config;
            }
        }

        // Assign to Smarty
        $this->context->smarty->assign([
            'img_url'                 => __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/',
            'serverTime'              => $now->format('c'),
            'cutoffTime'              => $cutoff->format('c'),
            'hours'                   => (int)$interval->h,
            'minutes'                 => (int)$interval->i,
            'seconds'                 => (int)$interval->s,
            'show_timer'              => ($quantity > 0),
            'product_quantity'        => $quantity,
            'product_show_quantities' => (bool)($params['product']['show_quantities'] ?? false),
            'available_later'         => $availableLater,
            // Base message for 0 qty
            'message'                 => ($quantity > 0)
                ? ($params['message'] ?? '')
                : $outOfStockMessage,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/' . $template);
    }

    /**
     * Get available_later text for product (combination first, then product)
     */
    private function getAvailableLater(int $idProduct, int $idProductAttribute, int $idLang): string
    {
        $idShop = (int)$this->context->shop->id;

        // First try combination-specific available_later
        if ($idProductAttribute > 0) {
            $sql = 'SELECT available_later FROM ' . _DB_PREFIX_ . 'product_attribute_lang
                    WHERE id_product_attribute = ' . $idProductAttribute . '
                    AND id_lang = ' . $idLang;
            $result = Db::getInstance()->getValue($sql);
            if (!empty($result)) {
                return (string)$result;
            }
        }

        // Fall back to product-level available_later
        $sql = 'SELECT available_later FROM ' . _DB_PREFIX_ . 'product_lang
                WHERE id_product = ' . $idProduct . '
                AND id_lang = ' . $idLang . '
                AND id_shop = ' . $idShop;
        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? (string)$result : '';
    }
}
