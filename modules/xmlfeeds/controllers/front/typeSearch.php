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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class XmlfeedsTypeSearchModuleFrontController
 *
 * /index.php?fc=module&module=xmlfeeds&controller=typeSearch
 * /module/xmlfeeds/typeSearch
 */
class XmlfeedsTypeSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (!class_exists('FeedType', false)) {
            require(dirname(__FILE__) . '/../../FeedType.php');
        }

        $this->context->smarty->assign([
            'feedTypeList' => $this->filterTypes(),
            'contactUsUrl' => 'https://addons.prestashop.com/en/contact-us?id_product=5732',
            'tpl_dir' => _PS_MODULE_DIR_.'xmlfeeds/'
        ]);

        echo $this->context->smarty->fetch(_PS_MODULE_DIR_.'xmlfeeds/views/templates/admin/page/searchFeedTypeApi.tpl');
        die;
    }

    protected function filterTypes()
    {
        $FeedType = new FeedType();
        $types = $FeedType->getAllTypes();

        $search = Tools::strtolower(htmlspecialchars(Tools::getValue('name'), ENT_QUOTES));

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
