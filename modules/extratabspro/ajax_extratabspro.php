<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('extratabspro.php');

$thismodule = new extratabspro();

//if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $thismodule->secure_key || !Tools::getValue('action'))
	//die();

if (Tools::getValue('action') == 'LoadAllTabs') {
    echo $thismodule->returnListOfTabs();
}

if (Tools::getValue('action') == 'updateSlidesPosition') {
    $extratabs = Tools::getValue('productextratab');
    foreach ($extratabs as $position => $id_tab) {
        $res = Db::getInstance()->execute('
	   UPDATE `' . _DB_PREFIX_ . 'extratabspro` SET `position` = ' . pSQL((int)$position) . '
	   WHERE `id_tab` = ' . pSQL((int)$id_tab) . '');
    }
    $thismodule->cccc('*');
}

if (Tools::getValue('search_feature','false') != 'false') {
    $result = $thismodule->searchfeature(Tools::getValue('search_feature'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">'.$value['name'].' 
            <span class="addbtn" onclick="$(\'.etab_feature_selected\').append(etab_addinput(\''.$value['name'].'\',\''.$value['id_feature_value'].'\'));">
            '.$thismodule->addcategory.'
            </span>
            </p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}

if (Tools::getValue('action') == 'removeTab' && Tools::getValue('id_tab')) {
    $extratab = new Extratabpro(Tools::getValue('id_tab'));
    $extratab->delete();
    $thismodule->cccc('*');
}

if (isset($_POST['search_supplier'])) {
    $result = $thismodule->searchsupplier(Tools::getValue('search_supplier'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ex_suppliers_ids\').val($(\'.ex_suppliers_ids\').val()+\'' . $value['id_supplier'] . ',\')">' . $thismodule->addcategory . '</span></p>';
        }
    } else {
        echo $thismodule->nosuppliersfound;
    }
}

if (Tools::getValue('action') == 'unhookTab' && Tools::getValue('id_tab') && Tools::getValue('id_product')) {
    $extratab = new extratabpro(Tools::getValue('id_tab'));
    $explode = explode(",", $extratab->products);
    $array_products = array();
    foreach ($explode as $product) {
        if ($product != Tools::getValue('id_product')) {
            $array_products[] = $product;
        }
    }

    $array_products_to_db = '';
    foreach ($array_products as $product) {
        $array_products_to_db = $array_products_to_db . $product . ",";
    }

    $extratab->products = $array_products_to_db;
    $extratab->update();
    $thismodule->cccc('*');
}


if (Tools::getValue('action') == 'AddTabToProduct' && Tools::getValue('id_tab') && Tools::getValue('id_product')) {
    $items = '';
    $id_tab = str_replace('productExtratab_', '', Tools::getValue('id_tab'));
    $product_extratab = new Extratabpro($id_tab);
    $product_extratab->block_type3 = 1;
    $products = $product_extratab->products;
    $array = explode(",", $products);
    $array[] = Tools::getValue('id_product');
    foreach ($array as $ar => $item) {
        if ($item != "") {
            $items .= $item . ",";
        }
    }
    $items = trim($items);

    $product_extratab->products = $items;
    $product_extratab->update();
    $employee_idlang = Context::getContext()->language->id;
    echo '$("#productextratab").append(\'<li id="productextratab_' . $id_tab . '" class="' . ($product_extratab->block_type == 2 ? 'global_block' : '') . ' ' . ($product_extratab->block_type2 == 1 ? 'global_manufacturers_block' : '') . ' ' . ($product_extratab->block_type3 == 1 ? 'global_products_block' : '') . '"><span class="name">' . ($product_extratab->name[$employee_idlang]) . '</span>' . ($product_extratab->block_type3 == 1 ? '<span class="unhook" onclick="extratab_unhook(' . $product_extratab->id_tab . ',' . Tools::getValue('id_product') . ')"></span>' : '<span class="unhook"><a style="background:none; width:24px; height:24px; display:block;" class="unhook" href="?extratabspro=1&updateproduct&editblock=' . $product_extratab->id_tab . '&_token='.Tools::getValue('token').'#hooks"></a></span>') . '<span class="remove" onclick="extratab_remove(' . $product_extratab->id_tab . ')"></span><span class="edit"><a class="edit" href="?extratabspro=1&editblock=' . $product_extratab->id_tab . '&_token='.Tools::getValue('token').'#hooks"></a></span><span class="' . ($product_extratab->active == 1 ? 'on' : 'off') . '" onclick="extratab_toggle(' . $product_extratab->id_tab . ')"></span></li>\');';
    $thismodule->cccc('*');
}


if (Tools::getValue('action') == 'toggleTab' && Tools::getValue('id_tab')) {
    $id_tab = Tools::getValue('id_tab');
    $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'extratabspro` SET `active` = !active
    WHERE `id_tab` = ' . (int)$id_tab . '');

    $res = Db::getInstance()->executeS('SELECT active  FROM `' . _DB_PREFIX_ . 'extratabspro` WHERE `id_tab` = ' . pSQL((int)$id_tab));
    if ($res[0]['active'] == 1) {
        echo "$(\"#productextratab_$id_tab span.off\").attr('class','on');";
    } else {
        echo "$(\"#productextratab_$id_tab span.on\").attr('class','off');";
    }
    $thismodule->cccc('*');
}


if (isset ($_POST['search'])) {
    $result = $thismodule->searchcategory(Tools::getValue('search'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ex_pr_ids\').val($(\'.ex_pr_ids\').val()+\'' . $value['id_category'] . ',\')">' . $thismodule->addcategory . '</span></p>';
        }
    } else {
        echo $thismodule->nocategoriesfound;
    }
}

if (isset ($_POST['search_product'])) {
    $result = $thismodule->searchproduct(Tools::getValue('search_product'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ex_products_ids\').val($(\'.ex_products_ids\').val()+\'' . $value['id_product'] . ',\')">' . $thismodule->addcategory . '</span></p>';
        }
    } else {
        echo $thismodule->noproductsfound;
    }
}

if (isset ($_POST['search_manufacturer'])) {
    $result = $thismodule->searchmanufacturer(Tools::getValue('search_manufacturer'));
    if (count($result) > 0) {
        foreach ($result as $key => $value) {
            echo '<p style="display:block; clear:both; padding:0px; padding-top:3px; margin:0px;">' . $value['name'] . '<span style="display:inline-block; background:#FFF; cursor:pointer; border:1px solid black; padding:1px 3px;margin-left:5px;" onclick="$(\'.ex_manufacturers_ids\').val($(\'.ex_manufacturers_ids\').val()+\'' . $value['id_manufacturer'] . ',\')">' . $thismodule->addcategory . '</span></p>';
        }
    } else {
        echo $thismodule->nomanufacturersfound;
    }
}