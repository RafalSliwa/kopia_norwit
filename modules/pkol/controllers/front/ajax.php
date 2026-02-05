<?php
/**
 * @author    Pko Leasing
 * @copyright 2024 PKO leasing
 * @license   MIT License
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PkolAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
    }

    public function displayAjax()
    {
        $pkol = new Pkol();
        if (isset($_GET['att'])) {
            $att = (int) $_GET['att'];
            $pid = (int) $_GET['pid'];

            $response = $pkol->getProduct($pid, $att);
        } else {
            $response = $pkol->previewCart();
        }

        exit(json_encode($response));
    }
}
