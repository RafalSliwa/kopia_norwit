<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}
class moduleConfiguration
{
    // General values and path
    const FPA_MODULE_NAME = 'FPA';
    const FPA_TABLE_PREFIX = 'fpa';
    const FPA_MODULE_SET_NAME = 'facebookproductad';
    const FPA_GENERIC_NAME = 'FacebookProductAd';
    const FPA_SUPPORT_ID = '23671';
    const FPA_SUPPORT_BT = false;
    const FPA_SUPPORT_URL = 'https://addons.prestashop.com/';
    const FPA_PATH_TPL = _PS_MODULE_DIR_ . 'facebookproductad/views/templates/';
    const FPA_SHOP_PATH_ROOT = _PS_ROOT_DIR_ . '/';
    const FPA_PATH_CONF = _PS_MODULE_DIR_ . 'facebookproductad/conf/';
    const FPA_PATH_SQL = _PS_MODULE_DIR_ . 'facebookproductad/sql/';
    const FPA_LIB_DAO = _PS_MODULE_DIR_ . 'facebookproductad/lib/dao/';
    const FPA_URL_JS = _MODULE_DIR_ . 'facebookproductad/views/js/';
    const FPA_URL_CSS = _MODULE_DIR_ . 'facebookproductad/views/css/';
    const FPA_MODULE_URL = _MODULE_DIR_ . 'facebookproductad/';
    const FPA_URL_IMG = _MODULE_DIR_ . 'facebookproductad/views/img/';
    const FPA_DEBUG = false;
    const FPA_USE_JS = true;
    const FPA_PARAM_CTRL_NAME = 'sController';
    const FPA_ADMIN_CTRL = 'admin';
    const FPA_JS_NAME = 'oPixelFacebook';
    const FPA_CTRL_CRON = 'cron';
    const FPA_CTRL_FLY = 'fly';
    const FPA_CTRL_PRODUCT_TAG = 'adminTagProduct';
    const FPA_REPORTING_DIR = _PS_MODULE_DIR_ . 'facebookproductad/reporting/';
    const FPA_PATH_LIB_HOOK = _PS_MODULE_DIR_ . 'facebookproductad/lib/hook/';
    const FPA_TPL_FRONT_PATH = 'front/';
    const FPA_TPL_HOOK_PATH = 'hook/';
    const FPA_PATH_LIB_INSTALL = _PS_MODULE_DIR_ . 'facebookproductad/lib/install/';
    const FPA_INSTALL_SQL_FILE = 'install.sql';
    const FPA_UNINSTALL_SQL_FILE = 'uninstall.sql';
    const FPA_LOG_JAM_SQL = false;
    const FPA_LOG_JAM_CONFIG = false;

    // Specific values for feeds
    const FPA_FEED_TITLE_LENGTH = 150;
    const FPA_IMG_LIMIT = 20;
    const FPA_CUSTOM_LABEL_LIMIT = 5;
    const FPA_TAG_LIST = ['material', 'pattern', 'agegroup', 'gender', 'adult', 'agegroup_product', 'gender_product', 'adult_product'];

    // Models table name
    const FPA_EXPORT_CAT = 'fpa_categories';
    const FPA_EXPORT_BRAND = 'fpa_brands';
    const FPA_FEEDS = 'fpa_feeds';
    const FPA_TAXONOMY = 'fpa_taxonomy';
    const FPA_CAT_TAG = 'fpa_features_by_cat';
    const FPA_REPORTING = 'fpa_reporting';
    const FPA_CAT_TAXO = 'fpa_taxonomy_categories';
    const FPA_TAGS = 'fpa_tags';
    const FPA_CL_DYN_BEST_SALES = 'fpa_tags_dynamic_best_sale';
    const FPA_CL_DYN_CAT = 'fpa_tags_dynamic_categories';
    const FPA_CL_DYN_FEATURE = 'fpa_tags_dynamic_features';
    const FPA_CL_DYN_LAST_ORDERED = 'fpa_tags_dynamic_last_product_ordered';
    const FPA_CL_DYN_NEW_PRODUCT = 'fpa_tags_dynamic_new_product';
    const FPA_CL_DYN_PRICE_RANGE = 'fpa_tags_price_range';
    const FPA_CL_DYN_PRODUCTS = 'fpa_tags_products';
    const FPA_CL_DYN_PROMO = 'fpa_tags_dynamic_promotion';
    const FPA_TMP_RULE = 'fpa_tmp_rules';
    const FPA_RULE_PRODUCTS = 'fpa_product_excluded';
    const FPA_ADV_EXCLUSION = 'fpa_advanced_exclusion';

    /**
     * return the default conf var
     *
     * @return array
     */
    public static function getTaxonomies()
    {
        $available_taxonomies = ['en-US', 'en-GB', 'fr-FR', 'de-DE', 'it-IT', 'es-ES', 'zh-CN', 'ja-JP', 'pt-BR', 'cs-CZ', 'ru-RU', 'sv-SE', 'da-DK', 'no-NO', 'pl-PL', 'ar-SA'];
        sort($available_taxonomies);

        return $available_taxonomies;
    }

    // Available data feed (will me moved later on database)
    const FPA_AVAILABLE_COUNTRIES = [
        'en' => [
            'IE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'GB' => ['currency' => ['GBP', 'KES', 'NGN', 'PAB', 'PKR', 'DZD', 'AOA', 'BYN', 'KHR', 'XAF', 'XOF', 'ETB', 'GHS', 'JOD', 'KZT', 'KWD', 'LBP', 'MGA', 'MUR', 'MAD', 'MZN', 'MMK', 'NPR', 'NIO', 'OMR', 'PYG', 'PEN', 'RON', 'XOF', 'LKR', 'UGX', 'UYU', 'UZS', 'ZMW'], 'taxonomy' => 'en-US'],
            'US' => ['currency' => ['USD', 'KES', 'NGN', 'PAB', 'PKR', 'DZD', 'AOA', 'BYN', 'KHR', 'XAF', 'XOF', 'ETB', 'GHS', 'JOD', 'KZT', 'KWD', 'LBP', 'MGA', 'MUR', 'MAD', 'MZN', 'MMK', 'NPR', 'NIO', 'OMR', 'PYG', 'PEN', 'RON', 'XOF', 'LKR', 'UGX', 'UYU', 'UZS', 'ZMW'], 'taxonomy' => 'en-US'],
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'en-US'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'en-US'],
            'IN' => ['currency' => ['INR'], 'taxonomy' => 'en-US'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'en-US'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'en-US'],
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'en-US'],
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-US'],
            'ID' => ['currency' => ['RP'], 'taxonomy' => 'en-US'],
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'en-US'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'en-US'],
            'MX' => ['currency' => ['MXN'], 'taxonomy' => 'en-US'],
            'NZ' => ['currency' => ['NZD'], 'taxonomy' => 'en-US'],
            'PH' => ['currency' => ['PHP'], 'taxonomy' => 'en-US'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'en-US'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'en-US'],
            'AE' => ['currency' => ['AED', 'DZD', 'EGP', 'TND'], 'taxonomy' => 'en-US'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'en-US'],
            'ZA' => ['currency' => ['ZAR'], 'taxonomy' => 'en-US'],
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'en-US'],
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-US'],
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-US'],
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-US'],
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-US'],
            'AR' => ['currency' => ['ARS', 'CRC', 'DOP', 'GTQ'], 'taxonomy' => 'en-US'],
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'en-US'],
            'CL' => ['currency' => ['CLP'], 'taxonomy' => 'en-US'],
            'CO' => ['currency' => ['COP'], 'taxonomy' => 'en-US'],
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'en-US'],
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'en-US'],
            'RU' => ['currency' => ['RUB', 'GEL'], 'taxonomy' => 'en-US'],
            'PT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'SA' => ['currency' => ['AED, SAR', 'DZD', 'EGP'], 'taxonomy' => 'en-US'],
            'ES' => ['currency' => ['EUR', 'GTQ'], 'taxonomy' => 'en-US'],
            'GE' => ['currency' => ['KAS'], 'taxonomy' => 'en-US'],
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-US'],
            'VE' => ['currency' => ['VEF'], 'taxonomy' => 'en-US'],
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-US'],
            'KW' => ['currency' => ['KWD'], 'taxonomy' => 'en-US'],
        ],
        'gb' => [
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'en-GB'],
            'IE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'IN' => ['currency' => ['INR'], 'taxonomy' => 'en-GB'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'en-GB'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'en-GB'],
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'en-GB'],
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-GB'],
            'ID' => ['currency' => ['IDR'], 'taxonomy' => 'en-GB'],
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'en-GB'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'en-GB'],
            'MX' => ['currency' => ['MXN'], 'taxonomy' => 'en-GB'],
            'NZ' => ['currency' => ['NZD'], 'taxonomy' => 'en-GB'],
            'PH' => ['currency' => ['PHP'], 'taxonomy' => 'en-GB'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'en-GB'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'en-GB'],
            'SA' => ['currency' => ['AED, SAR', 'DZD', 'EGP', 'TND'], 'taxonomy' => 'en-GB'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'en-GB'],
            'ZA' => ['currency' => ['ZAR'], 'taxonomy' => 'en-GB'],
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'en-GB'],
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-GB'],
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-GB'],
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-GB'],
            'US' => ['currency' => ['USD'], 'taxonomy' => 'en-GB'],
            'GB' => ['currency' => ['GBP'], 'taxonomy' => 'en-GB'],
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-GB'],
            'AR' => ['currency' => ['ARS', 'CRC', 'DOP', 'GTQ'], 'taxonomy' => 'en-GB'],
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'en-GB'],
            'CL' => ['currency' => ['CLP'], 'taxonomy' => 'en-GB'],
            'CO' => ['currency' => ['COP'], 'taxonomy' => 'en-GB'],
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'en-GB'],
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'en-GB'],
            'RU' => ['currency' => ['RUB', 'GEL'], 'taxonomy' => 'en-GB'],
            'PT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'ES' => ['currency' => ['EUR', 'GTQ'], 'taxonomy' => 'en-GB'],
            'GE' => ['currency' => ['KAS'], 'taxonomy' => 'en-GB'],
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-GB'],
            'VE' => ['currency' => ['VEF'], 'taxonomy' => 'en-GB'],
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-GB'],
        ],
        'fr' => [
            'FR' => ['currency' => ['EUR', 'TND', 'DZD', 'XAF', 'XOF', 'MGA', 'MAD', 'XPF'], 'taxonomy' => 'fr-FR'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'fr-FR'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'fr-FR'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'fr-FR'],
            'SA' => ['currency' => ['DZD'], 'taxonomy' => 'fr-FR'],
        ],
        'de' => [
            'EN' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'de-DE'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
        ],
        'it' => [
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'it-IT'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'it-IT'],
        ],
        'nl' => [
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'nl-NL'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'nl-NL'],
        ],
        'es' => [
            'ES' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'MX' => ['currency' => ['MXN', 'EUR', 'ARS', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'AR' => ['currency' => ['ARS', 'EUR', 'MXN', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'CL' => ['currency' => ['CLP', 'EUR', 'MXN', 'ARS', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'CO' => ['currency' => ['COP', 'EUR', 'MXN', 'ARS', 'CLP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'US' => ['currency' => ['USD', 'EUR', 'MXN', 'ARS', 'CLP', 'COP', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
        ],

        'mx' => [
            'ES' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'MX' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP'], 'taxonomy' => 'es-ES'],
            'AR' => ['currency' => ['ARS', 'EUR', 'MXN', 'CLP', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'CL' => ['currency' => ['CLP', 'EUR', 'MXN', 'ARS', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'CO' => ['currency' => ['COP', 'EUR', 'MXN', 'ARS', 'CLP', 'USD'], 'taxonomy' => 'es-ES'],
            'US' => ['currency' => ['USD', 'EUR', 'MXN', 'ARS', 'CLP', 'COP'], 'taxonomy' => 'es-ES'],
        ],
        'ca' => [
            'ES' => ['currency' => ['EUR'], 'taxonomy' => 'es-ES'],
        ],
        'zh' => [
            'CN' => ['currency' => ['CNY'], 'taxonomy' => 'zh-CN'],
            'EN' => ['currency' => ['CNY'], 'taxonomy' => 'zh-CN'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'zh-CN'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'zh-CN'],
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'zh-CN'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'zh-CN'],
            'US' => ['currency' => ['USD'], 'taxonomy' => 'zh-CN'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'zh-CN'],
        ],
        'ja' => [
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'ja-JP'],
        ],
        'br' => [
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'pt-BR'],
        ],
        'cs' => [
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'cs-CZ'],
        ],
        'ru' => [
            'RU' => ['currency' => ['RUB', 'BYR', 'GEL', 'BYN', 'KZT', 'KWD', 'UZS', 'MDL'], 'taxonomy' => 'ru-RU'],
            'UA' => ['currency' => ['UAH'], 'taxonomy' => 'ru-RU'],
        ],
        'sv' => [
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'sv-SE'],
            'EN' => ['currency' => ['SEK'], 'taxonomy' => 'sv-SE'],
        ],
        'da' => [
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'da-DK'],
            'EN' => ['currency' => ['DKK'], 'taxonomy' => 'da-DK'],
        ],
        'no' => [
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'no-NO'],
        ],
        'pl' => [
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'pl-PL'],
        ],
        'tr' => [
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'tr-TR'],
        ],
        'ms' => [
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-US'],
        ],
        'pt' => [
            'PT' => ['currency' => ['EUR', 'AOA', 'MZN'], 'taxonomy' => 'es-ES'],
        ],
        'ar' => [
            'SA' => ['currency' => ['SAR', 'AED', 'DZD', 'CRC', 'EGP', 'TND', 'DZD', 'JOD', 'LBP', 'MAD', 'OMR'], 'taxonomy' => 'ar-SA'],
            'AE' => ['currency' => ['AED', 'SAR', 'DZD', 'EGP', 'DZD', 'JOD'], 'taxonomy' => 'ar-SA'],
            'KW' => ['currency' => ['KWD'], 'taxonomy' => 'ar-SA'],
        ],
        'id' => [
            'ID' => ['currency' => ['IDR'], 'taxonomy' => 'en-US'],
        ],
        'he' => [
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-US'],
        ],
        'vn' => [
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-US'],
        ],
        'uk' => [
            'UA' => ['currency' => ['UAH'], 'taxonomy' => 'en-US'],
        ],
        'th' => [
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-US'],
        ],
        'ko' => [
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-US'],
        ],
        'fi' => [
            'FI' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'hu' => [
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-GB'],
        ],
        'ag' => [
            'AR' => ['currency' => ['CRC', 'DOP', 'GTQ'], 'taxonomy' => 'es-ES'],
        ],
        'ur' => [
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-US'],
        ],
        've' => [
            'VE' => ['currency' => ['VEF'], 'taxonomy' => 'es-ES'],
        ],
        'sk' => [
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'ro' => [
            'RO' => ['currency' => ['RON', 'MDL'], 'taxonomy' => 'en-GB'],
        ],
        'el' => [
            'GR' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'lt' => [
            'LT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'et' => [
            'EE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
        ],
        'si' => [
            'SI' => ['currency' => ['EUR', 'USD'], 'taxonomy' => 'en-GB'],
        ],
        'hr' => [
            'HR' => ['currency' => ['EUR', 'USD'], 'taxonomy' => 'en-GB'],
        ],
        'qc' => [
            'CA' => ['currency' => ['cAD'], 'taxonomy' => 'fr-FR'],
        ],
        'pe' => [
            'PE' => ['currency' => ['USD', 'EUR', 'MXN', 'ARS', 'CLP', 'COP', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
        ],
        'sr' => [
            'CS' => ['currency' => ['RSD'], 'taxonomy' => 'en-GB'],
            'SR' => ['currency' => ['RSD'], 'taxonomy' => 'en-GB'],
        ],
    ];

    const FPA_WEIGHT_UNITS = ['kg', 'lb', 'g', 'oz'];

    const FPA_HOOKS = [
        ['name' => 'displayHeader', 'use' => false, 'title' => 'Header'],
    ];

    const FPA_TABS = [
        [
            'name' => [
                'en' => 'Tag attribution',
            ],
            'class_name' => 'AdminTagProduct',
            'icon' => 'settings_applications',
            'hide' => true,
            'parent' => '',
        ],
        [
            'name' => [
                'en' => 'Taxonomie attribution',
            ],
            'class_name' => 'AdminTaxonomy',
            'icon' => 'settings_applications',
            'hide' => true,
            'parent' => '',
        ],
    ];

    const FPA_TAGS_TYPE = [
        'home' => 'home',
        'category' => 'category',
        'product' => 'product',
        'cart' => 'cart',
        'purchase' => 'purchase',
        'search' => 'searchresults',
        'other' => 'other',
        'manufacturer' => 'manufacturer',
        'promotion' => 'promotion',
        'newproducts' => 'newproducts',
        'bestsales' => 'bestsales',
        'paymentInfo' => 'paymentInfo',
        'instantSearch' => 'instantSearch',
        'checkout' => 'checkout',
        'contact' => 'contact',
    ];

    const FPA_HOME_CAT_NAME = [
        'en' => 'home',
        'fr' => 'accueil',
        'it' => 'ignazio',
        'es' => 'ignacio',
    ];

    const FPA_LABEL_LIST = [
        'cats' => 'category',
        'brands' => 'brand',
        'suppliers' => 'supplier',
    ];

    const FPA_PARAM_FOR_XML = [
        'iShopId',
        'sFilename',
        'iLangId',
        'sLangIso',
        'sCountryIso',
        'sCurrencyIso',
        'iFloor',
        'iStep',
        'iTotal',
        'iProcess',
        'bExcludedProduct',
    ];

    const FPA_RULES_LABEL_TYPE = [
        'word' => [
            'en' => 'A word or a sequence of words',
            'es' => 'Una palabra o serie de palabras',
            'it' => 'Una parola o una sequenza di parole',
            'fr' => 'Un mot ou une suite de mots',
        ],
        'feature' => [
            'en' => 'A feature',
            'es' => 'Una característica',
            'it' => 'Una caratteristica',
            'fr' => 'Une caractéristique',
        ],
        'attribute' => [
            'en' => 'An attribute',
            'es' => 'Un atributo',
            'it' => 'Un attributo',
            'fr' => 'Un attribut',
        ],
        'specificProduct' => [
            'en' => 'A specific product or combination',
            'es' => 'Un producto o una combinación específico(a)',
            'it' => 'Un prodotto o una combinazione specifico(a)',
            'fr' => 'Un produit ou une déclinaison spécifique',
        ],
    ];

    const FPA_RULES_WORD_TYPE = [
        'title' => [
            'en' => 'Product title',
            'es' => 'Título del producto',
            'it' => 'Titolo del prodotto',
            'fr' => 'Titre du produit',
        ],
        'description' => [
            'en' => 'Description',
            'es' => 'Descripción',
            'it' => 'Descrizione',
            'fr' => 'Description',
        ],
        'both' => [
            'en' => 'Product title + description',
            'es' => 'Título del producto + descripción',
            'it' => 'Titolo del prodotto + descrizione',
            'fr' => 'Titre du produit + description',
        ],
    ];

    const FPA_EXCLUSION_TYPE_WORD = [
        'title' => [
            'en' => 'Product title',
            'fr' => 'Titre du produit',
            'es' => 'Título del producto',
            'it' => 'Titolo del prodotto',
        ],
        'description' => [
            'en' => 'Product description',
            'fr' => 'Description du produit',
            'es' => 'Descripción del producto',
            'it' => 'Descrizione del prodotto',
        ],
        'both' => [
            'en' => 'Product title + description',
            'fr' => 'Titre du produit + description',
            'es' => 'Título del producto + descripción',
            'it' => 'Titolo del prodotto + descrizione',
        ],
    ];

    const FPA_CUSTOM_LABEL_TYPE = [
        'en' => [
            'custom_label' => 'Basic',
            'dynamic_categorie' => 'Categories (dynamic mode)',
            'dynamic_features_list' => 'Features (dynamic mode)',
            'dynamic_new_product' => 'New product (dynamic mode)',
            'dynamic_best_sale' => 'Best sales (dynamic mode)',
            'dynamic_price_range' => 'Price range (dynamic mode)',
            'dynamic_last_order' => 'Products ordered (dynamic mode)',
            'dynamic_promotion' => 'Products in promotion (dynamic mode)',
        ],
        'fr' => [
            'custom_label' => 'Basique',
            'dynamic_categorie' => 'Catégories (mode dynamique)',
            'dynamic_features_list' => 'Caractéristiques (mode dynamique)',
            'dynamic_new_product' => 'Nouveaux produits (mode dynamique)',
            'dynamic_best_sale' => 'Meilleures ventes (mode dynamique)',
            'dynamic_price_range' => 'Tranche de prix (mode dynamique)',
            'dynamic_last_order' => 'Produits commandés (mode dynamique)',
            'dynamic_promotion' => 'Produits en promotion (mode dynamique)',
        ],
        'it' => [
            'custom_label' => 'Di base',
            'dynamic_categorie' => 'Categorie (dinamica di modo)',
            'dynamic_features_list' => 'Caratteristiche (modalità dinamica)',
            'dynamic_new_product' => 'Nuovo prodotto (modalità dinamica)',
            'dynamic_best_sale' => 'Le migliori vendite (modalità dinamica)',
            'dynamic_price_range' => 'Fascia di prezzo (modalità dinamica)',
            'dynamic_last_order' => 'Prodotti ordinati (modalità dinamica)',
            'dynamic_promotion' => 'Prodotti promozionali (modalità dinamica)',
        ],
        'es' => [
            'custom_label' => 'Básica',
            'dynamic_categorie' => 'Categorías (modo dinámico)',
            'dynamic_features_list' => 'Atributos (modo dinámico)',
            'dynamic_new_product' => 'Nuevo producto (modo dinámico)',
            'dynamic_best_sale' => 'Las mejores ventas (modo dinámico)',
            'dynamic_price_range' => 'Rango de precios (modo dinámico)',
            'dynamic_last_order' => 'Productos pedidos (modo dinámico)',
            'dynamic_promotion' => 'Productos promocionales (modo dinámico)',
        ],
    ];

    const FPA_CL_PRODUCT_ASSOCIATION = [
        'en' => 'There is no product for this custom label configuration. We invite you to edit it the custom label from the list by closing the windows',
        'fr' => 'Il n y\'a pas de produits associés à la configuration du custom label. Vous pouvez l\'éditer depuis la liste en fermant cette fenêtre',
        'it' => 'Non esiste alcun prodotto per questa configurazione di etichetta personalizzata. Ti invitiamo a modificare l\'etichetta personalizzata dall\'elenco chiudendo le finestre',
        'es' => 'No hay ningún producto para esta configuración de etiqueta personalizada. Le invitamos a editar la etiqueta personalizada de la lista cerrando las ventanas',
    ];

    const FPA_CUSTOM_LABEL_BEST_TYPE = [
        'en' => ['unit' => 'Unit', 'price' => 'Revenue generated'],
        'fr' => ['unit' => 'Entités vendues', 'price' => 'Chiffre d\'affaire généré'],
        'it' => ['unit' => 'Articoli venduti', 'price' => 'Ricavi generati'],
        'es' => ['unit' => 'Cosas vendidas', 'price' => 'Ingresos generados'],
    ];

    const FPA_CUSTOM_LABEL_BEST_PERIOD_TYPE = [
        'period' => 'Period',
        'days' => 'For X lasts days',
    ];

    const FPA_CUSTOM_LABEL_PRODUCT_FILTER = [
        'category' => [
            'sFieldSelect' => 'id_category',
            'sPopulateTable' => 'fpa_tags_cats',
            'bUsePsTable' => 1,
            'bUseCategory' => 1,
            'sPsTable' => 'category_product',
            'sPsTableWhere' => 'id_category',
        ],
        'brand' => [
            'sFieldSelect' => 'id_brand',
            'sPopulateTable' => 'fpa_tags_brands',
            'bUsePsTable' => 1,
            'sPsTable' => 'product',
            'sPsTableWhere' => 'id_manufacturer',
        ],
        'product' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_products',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_cat' => [
            'sFieldSelect' => 'id_category',
            'sPopulateTable' => 'fpa_tags_dynamic_categories',
            'bUsePsTable' => 1,
            'sPsTable' => 'category_product',
            'sPsTableWhere' => 'id_category',
        ],
        'dyn_feature' => [
            'sFieldSelect' => 'id_feature',
            'sPopulateTable' => 'fpa_tags_dynamic_features',
            'bUsePsTable' => 1,
            'sPsTable' => 'feature_product',
            'sPsTableWhere' => 'id_feature',
        ],
        'dyn_new_product' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_dynamic_new_product',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_best_dale' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_dynamic_best_sale',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_price_range' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_price_range',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_promotion' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_dynamic_promotion',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_ordered' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'fpa_tags_dynamic_last_product_ordered',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
    ];

    /**
     * @return array
     */
    public static function getConfVar()
    {
        return [
            'FPA_VERSION' => '',
            'FPA_HOME_CAT' => '',
            'FPA_LINK' => '',
            'FPA_ID_PREFIX' => '',
            'FPA_AJAX_CYCLE' => 1000,
            'FPA_EXPORT_OOS' => 1,
            'FPA_COND' => 'new',
            'FPA_P_COMBOS' => 0,
            'FPA_P_DESCR_TYPE' => 3,
            'FPA_IMG_SIZE' => \ImageType::getFormattedName('large'),
            'FPA_EXC_NO_EAN' => 0,
            'FPA_EXC_NO_MREF' => 0,
            'FPA_MIN_PRICE' => 0,
            'FPA_INC_STOCK' => 1,
            'FPA_INC_FEAT' => 0,
            'FPA_FEAT_OPT' => 0,
            'FPA_INC_GENRE' => 0,
            'FPA_GENRE_OPT' => 0,
            'FPA_INC_SIZE' => 0,
            'FPA_SIZE_OPT' => 0,
            'FPA_INC_COLOR' => '',
            'FPA_COLOR_OPT' => '',
            'FPA_INC_MATER' => 0,
            'FPA_MATER_OPT' => 0,
            'FPA_INC_PATT' => 0,
            'FPA_PATT_OPT' => 0,
            'FPA_INC_GEND' => 0,
            'FPA_GEND_OPT' => 0,
            'FPA_INC_ADULT' => 0,
            'FPA_ADULT_OPT' => 0,
            'FPA_INC_AGE' => 0,
            'FPA_AGE_OPT' => 0,
            'FPA_SHIP_CARRIERS' => '',
            'FPA_NO_TAX_SHIP_CARRIERS' => '',
            'FPA_FREE_SHIP_CARRIERS' => '',
            'FPA_FREE_PROD_PRICE_SHIP_CARRIERS' => '',
            'FPA_REPORTING' => 1,
            'FPA_HOME_CAT_ID' => 1,
            'FPA_MPN_TYPE' => 'supplier_ref',
            'FPA_INC_ID_EXISTS' => 0,
            'FPA_ADD_CURRENCY' => 0,
            'FPA_UTM_CAMPAIGN' => '',
            'FPA_UTM_SOURCE' => '',
            'FPA_UTM_MEDIUM' => '',
            'FPA_FEED_TOKEN' => md5(rand(1000, 1000000) . time()),
            'FPA_EXPORT_MODE' => 0,
            'FPA_ADV_PRODUCT_NAME' => 0,
            'FPA_ADV_PROD_TITLE' => 0,
            'FPA_CHECK_EXPORT' => '',
            'FPA_INC_TAG_ADULT' => 0,
            'FPA_SHIPPING_USE' => 1,
            'FPA_PROD_EXCL' => '',
            'FPA_GTIN_PREF' => 'ean',
            'FPA_DISP_ADVICE' => 1,
            'FPA_PIXEL' => '',
            'FPA_BUSINESS_ID' => '',
            'FPA_ACTIVATE_PIXEL' => 1,
            'FPA_USE_TAX' => 1,
            'FPA_USE_TEST_EVENT_CODE' => 0,
            'FPA_TEST_EVENT_CODE' => '',
            'FPA_USE_SHIPPING' => 1,
            'FPA_USE_WRAPPING' => 1,
            'FPA_CUSTOM_DOM_ELEM' => 0,
            'FPA_JS_WISH_SELECTOR_PROD' => 'button.wishlist-button-add',
            'FPA_JS_WISH_SELECTOR_CAT' => 'a[rel="_PRODUCT_ID_"].addToWishlist',
            'FPA_CONF_STEP_1' => 0,
            'FPA_CONF_STEP_2' => 0,
            'FPA_CONF_STEP_3' => 0,
            'FPA_CONF_STEP_4' => 0,
            'FPA_ADD_LANG_ID' => 1,
            'FPA_USE_CONSENT' => 0,
            'FPA_URL_NUM_ATTR_REWRITE' => 0,
            'FPA_URL_ATTR_ID_INCL' => 0,
            'FPA_COMBO_SEPARATOR' => 'v',
            'FPA_ADD_IMAGES' => 1,
            'FPA_URL_PROD_ERROR' => 0,
            'FPA_PROD_PRICE_TAX' => 1,
            'FPA_TRACK_ADD_CART_PAGE' => 0,
            'FPA_CL_AUTO_UPDATE' => 1,
            'FPA_ELEMENT_HTML_ID' => '',
            'FPA_ELEMENT_HTML_SECOND_ID' => '',
            'FPA_USE_API' => 0,
            'FPA_ADVANCED_MATCHING' => 0,
            'FPA_HAS_WARNING' => 0,
            'FPA_TOKEN_API' => '',
            'FPA_EXCLUDED_WORDS' => '',
            'FPA_FEED_PREF_ID' => 'tag-id-basic',
            'FPA_INCL_ATTR_VALUE' => 1,
            'FPA_INCL_ANCHOR' => 0,
            'FPA_API_PAGE_VIEW' => 0,
            'FPA_USE_GENDER_PRODUCT' => 0,
            'FPA_USE_AGEGROUP_PRODUCT' => 0,
            'FPA_USE_ADULT_PRODUCT' => 0,
            'FPA_USE_AXEPTIO' => 0,
            'FPA_HANDLE_TAXO_JSON' => 0,
        ];
    }

    /**
     * return the default JS messages
     *
     * @return array
     */
    public static function getJsMessage()
    {
        return [
            'pixelId' => \FacebookProductAd::$oModule->l('You have not indicated the pixel id', 'moduleTools'),
            'testEventCode' => \FacebookProductAd::$oModule->l('You have not indicated the test event code', 'moduleTools'),
            'customlabel' => \FacebookProductAd::$oModule->l('You have not indicated a name for your custom label', 'moduleTools'),
            'link' => \FacebookProductAd::$oModule->l('You have not filled in the shop URL', 'moduleTools'),
            'token' => \FacebookProductAd::$oModule->l('Field is required and token must be 32 characters', 'moduleTools'),
            'ruleName' => \FacebookProductAd::$oModule->l('You have not indicated a name for your exclusion rule', 'moduleTools'),
            'category' => \FacebookProductAd::$oModule->l('You have not selected any category to be exported', 'moduleTools'),
            'brand' => \FacebookProductAd::$oModule->l('You have not selected any brand to be exported', 'moduleTools'),
            'color' => \FacebookProductAd::$oModule->l('You have not selected any attribute or feature to be associated with the color tag', 'moduleTools'),
            'customDom' => \FacebookProductAd::$oModule->l('Please set a value', 'moduleTools'),
            'cycle' => \FacebookProductAd::$oModule->l('Please set a numeric value', 'moduleTools'),
            'bannerId' => \FacebookProductAd::$oModule->l('You have not specified the HTML element that corresponds to the cookie acceptance button', 'moduleTools'),
        ];
    }

    /**
     * method return all e-commerce event type available
     */
    public static function getEventType()
    {
        return [
            'home' => \FacebookProductAd::$oModule->l('View homepage', 'moduleTools'),
            'category' => \FacebookProductAd::$oModule->l('View category', 'moduleTools'),
            'product' => \FacebookProductAd::$oModule->l('View product', 'moduleTools'),
            'cart' => \FacebookProductAd::$oModule->l('Add to cart', 'moduleTools'),
            'purchase' => \FacebookProductAd::$oModule->l('Purchase', 'moduleTools'),
            'search' => \FacebookProductAd::$oModule->l('Search', 'moduleTools'),
            'manufacturer' => \FacebookProductAd::$oModule->l('View manufacturer', 'moduleTools'),
            'promotion' => \FacebookProductAd::$oModule->l('View promotions', 'moduleTools'),
            'newproducts' => \FacebookProductAd::$oModule->l('View new products', 'moduleTools'),
            'bestsales' => \FacebookProductAd::$oModule->l('View best sales', 'moduleTools'),
            'paymentInfo' => \FacebookProductAd::$oModule->l('Payment information', 'moduleTools'),
            'checkout' => \FacebookProductAd::$oModule->l('Checkout', 'moduleTools'),
            'other' => \FacebookProductAd::$oModule->l('Other', 'moduleTools'),
        ];
    }

    /**
     * return the default month string
     *
     * @return array
     */
    public static function getMonths()
    {
        return [
            'en' => [
                'short' => ['', 'Jan.', 'Feb.', 'March', 'Apr.', 'May', 'June', 'July', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.'],
                'long' => ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ],
            'fr' => [
                'short' => ['', 'Jan.', 'F&eacute;v.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Aout', 'Sept.', 'Oct.', 'Nov.', 'D&eacute;c.'],
                'long' => ['', 'Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre'],
            ],
            'de' => [
                'short' => ['', 'Jan.', 'Feb.', 'M' . chr(132) . 'rz', 'Apr.', 'Mai', 'Juni', 'Juli', 'Aug.', 'Sept.', 'Okt.', 'Nov.', 'Dez.'],
                'long' => ['', 'Januar', 'Februar', 'M' . chr(132) . 'rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            ],
            'it' => [
                'short' => ['', 'Gen.', 'Feb.', 'Marzo', 'Apr.', 'Mag.', 'Giu.', 'Lug.', 'Ago.', 'Sett.', 'Ott.', 'Nov.', 'Dic.'],
                'long' => ['', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
            ],
            'es' => [
                'short' => ['', 'Ene.', 'Feb.', 'Marzo', 'Abr.', 'Mayo', 'Junio', 'Jul.', 'Ago.', 'Sept.', 'Oct.', 'Nov.', 'Dic.'],
                'long' => ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            ],
        ];
    }

    /**
     * return the array of table and SQL files to use
     *
     * @return array
     */
    public static function getSqlUpdateData()
    {
        return [
            'table' => [
                '1400' => 'update-1400.sql',
                '1500' => 'update-1500.sql',
            ],
            'field' => [
                'custom_label_set_postion' => ['table' => 'tags', 'file' => 'update-label-position.sql'],
                'feed_is_default' => ['table' => 'feeds', 'file' => 'update-fpa-feed-is-default.sql'],
            ],
        ];
    }

    /**
     * return the array of available request params
     *
     * @return array
     */
    public static function getRequestParams()
    {
        return [
            'basic' => ['action' => 'update', 'type' => 'basic'],
            'feed' => ['action' => 'update', 'type' => 'feed'],
            'feedDisplay' => ['action' => 'display', 'type' => 'feed'],
            'facebook' => ['action' => 'update', 'type' => 'facebook'],
            'taxonomy' => ['action' => 'display', 'type' => 'taxonomy'],
            'feedList' => ['action' => 'display', 'type' => 'feedList'],
            'feedListUpdate' => ['action' => 'update', 'type' => 'feedList'],
            'reporting' => ['action' => 'update', 'type' => 'reporting'],
            'reportingBox' => ['action' => 'display', 'type' => 'reportingBox'],
            'tag' => ['action' => 'display', 'type' => 'tag'],
            'tagUpdate' => ['action' => 'update', 'type' => 'tag'],
            'facebookCat' => ['action' => 'display', 'type' => 'facebookCategories'],
            'facebookCatSync' => ['action' => 'update', 'type' => 'facebookCategoriesSync'],
            'custom' => ['action' => 'display', 'type' => 'customLabel'],
            'customUpdate' => ['action' => 'update', 'type' => 'label'],
            'customDelete' => ['action' => 'delete', 'type' => 'label'],
            'autocomplete' => ['action' => 'display', 'type' => 'autocomplete'],
            'searchProduct' => ['action' => 'display', 'type' => 'searchProduct'],
            'dataFeed' => ['action' => 'update', 'type' => 'xml'],
            'advice' => ['action' => 'display', 'type' => 'advice'],
            'adviceUpd' => ['action' => 'update', 'type' => 'advice'],
            'pixel' => ['action' => 'update', 'type' => 'pixel'],
            'newCustomFeed' => ['action' => 'display', 'type' => 'newCustomFeed'],
            'newFeed' => ['action' => 'update', 'type' => 'newFeed'],
            'exclusionRule' => ['action' => 'display', 'type' => 'exclusionRule'],
            'exclusionRuleDelete' => ['action' => 'delete', 'type' => 'exclusionRule'],
            'deleteFeed' => ['action' => 'delete', 'type' => 'feed'],
            'rulesSummary' => ['action' => 'display', 'type' => 'rulesSummary'],
            'rulesList' => ['action' => 'update', 'type' => 'rulesList'],
            'exclusionRuleForm' => ['action' => 'update', 'type' => 'exclusionRule'],
            'excludeValue' => ['action' => 'display', 'type' => 'excludeValue'],
            'rulesActivate' => ['action' => 'update', 'type' => 'rulesActivate'],
            'exclusionRuleProducts' => ['action' => 'display', 'type' => 'exclusionRuleProducts'],
            'customActivate' => ['action' => 'update', 'type' => 'labelState'],
            'customProduct' => ['action' => 'display', 'type' => 'customLabelProduct'],
            'position' => ['action' => 'update', 'type' => 'position'],
            'consent' => ['action' => 'update', 'type' => 'consent'],
            'feedListSynch' => ['action' => 'update', 'type' => 'feedListSynch'],
            'log' => ['action' => 'update', 'type' => 'log'],
        ];
    }

    /**
     * return the array of available request params
     *
     * @return array
     */
    public static function getCustomLabelPosition()
    {
        return [
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4',
        ];
    }
}
