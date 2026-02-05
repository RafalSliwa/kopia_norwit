<?php
/**
 * 2007-2018 PrestaShop.
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoBs\Api\Client;
use CeneoBs\Entity\Delivery;
use CeneoBs\Repository\RepositoryBasketservice;
use Configuration as Cfg;

class AdminCeneoBasketserviceSettingsController extends ModuleAdminController
{
    /**
     * @var true
     */
    public $bootstrap;
    public $meta_title;
    public $repository;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->repository = new RepositoryBasketservice();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true));
        }
    }

    public function renderView()
    {
        return $this->renderForm();
    }

    public function initContent()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        parent::initContent();

        $this->content .= $this->renderTable();

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);

        $currentPage = 'global';
        $getPage = Tools::getValue('page');
        if (!empty($getPage)) {
            $currentPage = $getPage;
        }

        $this->context->smarty->assign([
            'ps_version' => _PS_VERSION_,
            'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
            'languages' => Language::getLanguages(),
            'currentPage' => $currentPage,
            'defaultFormLanguage' => (int) $this->context->employee->id_lang,
        ]);
    }

    public function getShippingCodesByCountry($repository)
    {
        return $repository->getAll();
    }

    public function getCarriersList()
    {
        return Db::getInstance()->executeS('
            SELECT `c`.* 
            FROM `' . _DB_PREFIX_ . 'carrier` `c`
            WHERE `c`.`deleted` = 0
            AND `c`.`active` = 1
        ');
    }

    public function getCeneoCarriers()
    {
        $repository = new RepositoryBasketservice();
        return $repository->getAllCarriers();
    }

    public function getActiveCarriers(): array
    {
        $result = [];
        $api = new Client();
        $apiCarriers = $api->getParcelCarriers();

        if (isset($apiCarriers['d'])) {
            $result = $apiCarriers['d'];
        }

        return $result;
    }

    public function getZonesAndCountries($carrierId, $countryParam = 'name')
    {
        $carrier = new Carrier($carrierId);
        $carrierZones = $carrier->getZones();
        $carrierCountries = [];
        foreach ($carrierZones as $carrierZone) {
            $zoneCountries = Country::getCountriesByZoneId($carrierZone['id_zone'], Cfg::get('PS_LANG_DEFAULT'));
            foreach ($zoneCountries as $zoneCountry) {
                if ($zoneCountry['active']) {
                    $carrierCountries[] = $zoneCountry[$countryParam];
                }
            }
        }
        return [$carrierZones, $carrierCountries];
    }

    public function renderTable()
    {
        if ($this->getCarriersList()) {
            $existingCeneoCarriers = $this->repository->getAllCeneoCarriers();
            $existingCarrierIds = array_column($existingCeneoCarriers, 'carrier_id');

            $currentCarrierIds = [];

            foreach ($this->getCarriersList() as $carrier) {
                $carrierId = $carrier['id_carrier'];
                [$carrierZones, $carrierCountries] = $this->getZonesAndCountries($carrierId);
                $countries = implode(', ', $carrierCountries);
                $list = $this->repository->getAllCeneoCarriers();
                $first_names = array_column($list, 'carrier_id');

                $currentCarrierIds[] = $carrierId;

                if (!in_array($carrierId, $first_names, true)) {
                    $c = new Delivery();
                    $c->carrier_id = (int) $carrier['id_carrier'];
                    $c->name = $carrier['name'];
                    $c->countries = $countries;
                    $c->ceneo_carrier_id = $carrierId;
                    $c->save();
                } else {
                    $allCarriers = $this->repository->getCeneoCarrierById($carrierId);
                    $c = new Delivery($allCarriers['id']);
                    $c->carrier_id = (int) $carrierId;
                    $c->name = $carrier['name'];
                    $c->countries = $countries;
                    $c->update();
                }
            }

            foreach ($existingCarrierIds as $existingCarrierId) {
                if (!in_array($existingCarrierId, $currentCarrierIds, true)) {
                    $allCarriers = $this->repository->getCeneoCarrierById($existingCarrierId);
                    $c = new Delivery($allCarriers['id']);
                    $c->delete();
                }
            }
        }

        $list = $this->repository->getAllCeneoCarriers();
        $ceneoDeliveries = $this->getActiveCarriers();
        $link = new Link();
        $ajax_controller = $link->getAdminLink('AdminCeneoBasketserviceAjax');

        $this->context->smarty->assign(
            [
                'list' => $list,
                'select' => $ceneoDeliveries,
                'ajax_controller' => $ajax_controller,
                'ajax_token' => Tools::getAdminTokenLite('AdminCeneoBasketserviceAjax'),
            ]
        );

        return $this->module->display(
            $this->module->name,
            'views/templates/admin/helpers/container_list.tpl'
        );
    }
}
