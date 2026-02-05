<?php
/**
 * Norwit Delivery - Custom delivery time for out of stock products
 *
 * @author    Norwit
 * @copyright 2024-2026 Norwit
 * @license   Proprietary
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;

class NorwitDelivery extends Module
{
    public function __construct()
    {
        $this->name = 'norwitdelivery';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->l('Norwit Delivery - Czas dostawy');
        $this->description = $this->l('Wyświetla tekst z pola "Etykieta gdy brak w magazynie" dla produktów niedostępnych.');
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('actionPresentProduct')
            && $this->installTab();
    }

    public function uninstall(): bool
    {
        return $this->uninstallTab() && parent::uninstall();
    }

    private function installTab(): bool
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminNorwitDelivery';
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Czas dostawy (bulk)';
        }

        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;

        return $tab->add();
    }

    private function uninstallTab(): bool
    {
        $idTab = (int) Tab::getIdFromClassName('AdminNorwitDelivery');
        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }

    public function hookActionPresentProduct(array $params): void
    {
        if (!isset($params['presentedProduct'])) {
            return;
        }

        $product = $params['presentedProduct'];

        if (!($product instanceof ProductLazyArray)) {
            return;
        }

        if (!$this->shouldOverrideMessage($product)) {
            return;
        }

        $availableLater = $this->getAvailableLater($product);
        if (!empty($availableLater)) {
            $product->offsetSet('availability_message', $availableLater, true);
        }
    }

    private function shouldOverrideMessage(ProductLazyArray $product): bool
    {
        $quantity = $this->getProductQuantity($product);

        if ($quantity > 0) {
            return false;
        }

        if ($this->isProductRetired($product)) {
            return false;
        }

        $availableLater = $this->getAvailableLater($product);

        return !empty($availableLater);
    }

    private function isProductRetired(ProductLazyArray $product): bool
    {
        if (!$product->offsetExists('id_product')) {
            return false;
        }

        $idProduct = (int) $product->offsetGet('id_product');
        if ($idProduct <= 0) {
            return false;
        }

        $retired = Db::getInstance()->getValue(
            'SELECT `retired` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product` = ' . $idProduct
        );

        return (bool) $retired;
    }

    private function getProductQuantity(ProductLazyArray $product): int
    {
        if ($product->offsetExists('quantity')) {
            return (int) $product->offsetGet('quantity');
        }

        if ($product->offsetExists('quantity_available')) {
            return (int) $product->offsetGet('quantity_available');
        }

        return 0;
    }

    private function getAvailableLater(ProductLazyArray $product): string
    {
        if ($product->offsetExists('available_later')) {
            return (string) $product->offsetGet('available_later');
        }

        return '';
    }
}
