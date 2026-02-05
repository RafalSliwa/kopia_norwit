<?php
/**
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
use AdvancedSearch\Core;
use AdvancedSearch\Models\Search;
use AdvancedSearch\Models\Seo;
/**
 * @property \PM_AdvancedSearch4 $module
 */
class pm_advancedsearch4seositemapModuleFrontController extends ModuleFrontController
{
    protected $idSearch;
    protected $searchInstance;
    public function init()
    {
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('Content-type: text/xml');
        $this->idSearch = (int)Tools::getValue('id_search');
        $this->searchInstance = new Search((int)$this->idSearch, (int)$this->context->language->id);
        if (!Validate::isLoadedObject($this->searchInstance)) {
            Tools::redirect('404');
        }
        $xmlSiteMapHeader = trim(preg_replace('/<!--(.|\s)*?-->/', '', $this->module->display(_PM_AS_MODULE_NAME_ . '.php', 'views/templates/front/' . $this->module->getPrestaShopTemplateVersion() . '/sitemap.tpl')));
        $xml = new SimpleXMLElement($xmlSiteMapHeader);
        foreach (Language::getLanguages(true, (int)$this->context->shop->id) as $language) {
            if (!is_array($language) || !isset($language['id_lang'])) {
                continue;
            }
            $seoSearchs = Seo::getSeoSearchs($language['id_lang'], false, $this->idSearch);
            foreach ($seoSearchs as $seoSearch) {
                $nbCriteria = count(Core::decodeCriteria($seoSearch['criteria']));
                if ($nbCriteria <= 3) {
                    $priority = 0.7;
                } elseif ($nbCriteria <= 5) {
                    $priority = 0.6;
                } else {
                    $priority = 0.5;
                }
                $sitemap = $xml->addChild('url');
                $sitemap->addChild('loc', $this->context->link->getModuleLink(_PM_AS_MODULE_NAME_, 'seo', [
                    'id_seo' => (int)$seoSearch['id_seo'],
                    'seo_url' => $seoSearch['seo_url'],
                ], null, (int)$language['id_lang']));
                $sitemap->addChild('priority', (string)$priority);
                $sitemap->addChild('changefreq', 'weekly');
            }
        }
        die($xml->asXML());
    }
}
