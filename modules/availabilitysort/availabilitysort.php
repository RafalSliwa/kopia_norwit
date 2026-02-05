<?php
/**
 * Availability stock sorting module.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class AvailabilitySort extends Module
{
    private const HOOKS = [
        'actionProductSearchProviderRunQueryBefore',
        'actionProductSearchProviderRunQueryAfter',
    ];

    public function __construct()
    {
        $this->name = 'availabilitysort';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

    $this->displayName = $this->l('Availability sort');
    $this->description = $this->l('Adds the "Availability" sorting option based on stock quantity and makes it the default for search listings.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach (self::HOOKS as $hookName) {
            if (!$this->registerHook($hookName)) {
                \PrestaShopLogger::addLog(sprintf('[%s] Failed to register hook %s.', $this->name, $hookName), 3, null, $this->name);

                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        foreach (self::HOOKS as $hookName) {
            if ($this->isRegisteredInHook($hookName) && !$this->unregisterHook($hookName)) {
                \PrestaShopLogger::addLog(sprintf('[%s] Failed to unregister hook %s.', $this->name, $hookName), 3, null, $this->name);
            }
        }

        return parent::uninstall();
    }

    public function hookActionProductSearchProviderRunQueryBefore(array $params)
    {
        if (empty($params['query']) || !($params['query'] instanceof ProductSearchQuery)) {
            return;
        }

        $query = $params['query'];

        if ($query->getQueryType() !== 'search') {
            return;
        }

        if (Tools::getValue('order')) {
            return;
        }

        $query->setSortOrder($this->buildAvailabilitySortOrder());
    }

    public function hookActionProductSearchProviderRunQueryAfter(array $params)
    {
        if (empty($params['result']) || !($params['result'] instanceof ProductSearchResult)) {
            return;
        }
        if (empty($params['query']) || !($params['query'] instanceof ProductSearchQuery)) {
            return;
        }

        $query = $params['query'];

        if ($query->getQueryType() !== 'search') {
            return;
        }

        $result = $params['result'];
        $availabilityOrder = $this->buildAvailabilitySortOrder();
        $availabilityKey = $availabilityOrder->toString();

        $availableSorts = $result->getAvailableSortOrders() ?: [];

        $filteredSorts = [];
        foreach ($availableSorts as $sortOrder) {
            if ($sortOrder->toString() === $availabilityKey) {
                continue;
            }
            $filteredSorts[] = $sortOrder;
        }

        array_unshift($filteredSorts, $availabilityOrder);
        $result->setAvailableSortOrders($filteredSorts);

        if ($query->getSortOrder() && $query->getSortOrder()->toString() === $availabilityKey) {
            $result->setCurrentSortOrder($availabilityOrder);
        }
    }

    private function buildAvailabilitySortOrder()
    {
        $label = $this->l('Availability');

        return (new SortOrder('product', 'quantity', 'desc'))->setLabel($label);
    }
}
