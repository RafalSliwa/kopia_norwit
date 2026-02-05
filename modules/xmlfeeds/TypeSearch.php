<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

$_POST = [];
$_FILES = [];
$_GET['module'] = 'xmlfeeds';

require(dirname(__FILE__).'/../../config/config.inc.php');
require(dirname(__FILE__).'/../../modules/xmlfeeds/FeedType.php');

if (!defined('_PS_VERSION_')) {
    exit;
}
class TypeSearch extends ModuleFrontController
{
    public function __construct()
    {
        $this->module = 'xmlfeeds';

        parent::__construct();
    }

    public function init()
    {
        $this->context->smarty->assign([
            'tpl_dir' => _PS_MODULE_DIR_.'xmlfeeds/',
            'feedTypeList' => $this->filterTypes(),
            'contactUsUrl' => 'https://addons.prestashop.com/en/contact-us?id_product=5732',
        ]);

        echo $this->context->smarty->fetch(_PS_MODULE_DIR_.'xmlfeeds/views/templates/admin/page/searchFeedTypeApi.tpl');
    }

    protected function filterTypes()
    {
        $FeedType = new FeedType();
        $types = $FeedType->getAllTypes();

        $search = Tools::strtolower(htmlspecialchars(Tools::getValue('s'), ENT_QUOTES));

        uasort($types, function ($a, $b) {
            return (Tools::strtolower($a['name']) > Tools::strtolower($b['name'])) ? 1 : -1;
        });

        if (empty($search)) {
            return $types;
        }

        foreach ($types as $k => $v) {
            $name = Tools::strtolower($v['name']);

            if (strpos($name, $search) === false) {
                unset($types[$k]);
            }
        }

        return $types;
    }
}

$typeSearch = new TypeSearch();
$typeSearch->init();
