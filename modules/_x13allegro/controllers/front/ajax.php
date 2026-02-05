<?php

require_once (dirname(__FILE__) . '/../../x13allegro.php');

class x13allegroAjaxModuleFrontController extends ModuleFrontController
{
    /** @var x13allegro */
    public $module;

    public function postProcess()
    {
        if (Tools::isSubmit('getAllegroAuctionLink')
            && $href = $this->module->generateProductAllegroAuctionLink(Tools::getValue('id_product'), Tools::getValue('id_product_attribute'))
        ) {
            die(json_encode([
                'result' => true,
                'href' => $href
            ]));
        }

        die(json_encode(['result' => false]));
    }
}
