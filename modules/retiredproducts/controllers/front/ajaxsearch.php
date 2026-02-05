<?php

class RetiredProductsAjaxSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        header('Content-Type: application/json');

        $query = Tools::getValue('query');
        $id_lang = (int)Context::getContext()->language->id;

        if (!$query) {
            die(json_encode([]));
        }

        $results = Db::getInstance()->executeS(
            'SELECT p.id_product, pl.name, p.reference, m.name as manufacturer_name
             FROM '._DB_PREFIX_.'product p
             INNER JOIN '._DB_PREFIX_.'product_lang pl
               ON (p.id_product = pl.id_product AND pl.id_lang = '.$id_lang.')
             LEFT JOIN '._DB_PREFIX_.'manufacturer m
               ON (p.id_manufacturer = m.id_manufacturer)
             WHERE (
               pl.name LIKE "%'.pSQL($query).'%" 
               OR p.reference LIKE "%'.pSQL($query).'%" 
               OR m.name LIKE "%'.pSQL($query).'%"
             )
             ORDER BY pl.name ASC LIMIT 20'
        );

        die(json_encode($results));
    }
}
