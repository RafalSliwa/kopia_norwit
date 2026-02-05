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

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use CeneoBs\Api\Client;
use CeneoBs\Exception\DatabaseException;
use CeneoBs\Handler\OrderHandler;
use CeneoBs\Helper\Html;
use CeneoBs\Installer\Installer;
use CeneoBs\Installer\StateInstaller;
use CeneoBs\Installer\TabInstaller;
use CeneoBs\Repository\RepositoryBasketservice;

class Ceneo_BasketService extends Module
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $tab;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $author;
    /**
     * @var int
     */
    public $need_instance;
    /**
     * @var true
     */
    public $bootstrap;
    /**
     * @var string
     */
    public $module_key;
    /**
     * @var Html
     */
    public $helper;

    private $_html = '';
    private $_postErrors = [];
    public $api;

    protected $hooks = [
        'displayAdminOrderTop',
        'actionOrderStatusUpdate',
        'actionGetExtraMailTemplateVars',
        'actionObjectOrderCarrierUpdateAfter',
    ];

    public function __construct()
    {
        $this->name = 'ceneo_basketservice';
        $this->tab = 'front_office_features';
        $this->version = '1.0.6';
        $this->author = 'Ceneo.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'c1c0d5d9d21abc6964ce2eb1d14a3322';
        parent::__construct();

        $this->displayName = $this->l('Marketplace Ceneo (Buy Now)');
        $this->description = $this->l('Integrates store with Ceneo Marketplace (Buy Now)');
        $this->confirmUninstall = $this->l('Are You sure You want to uninstall module?');

        $this->key = Configuration::get('CENEO_BS_KEY');
        $this->order_state = Configuration::get('CENEO_BS_STATE');
        $this->date_from = Configuration::get('CENEO_BS_DATE_FROM');

        $this->helper = new Html($this, $this->context);
        $this->api = new Client();

        $this->ps_versions_compliancy = ['min' => '1.7.1', 'max' => _PS_VERSION_];
    }

    /**
     * @throws DatabaseException
     */
    public function install()
    {
        if (parent::install()) {
            foreach ($this->hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }

            if (!(new Installer($this))->install()) {
                $this->_errors[] = $this->l('Installer error');
                return false;
            }

            if (!(new TabInstaller($this))->install()) {
                $this->_errors[] = $this->l('Install tab error');
                return false;
            }

            if (!(new StateInstaller($this))->addOrderState($this->l('New from Ceneo'), $this->context)) {
                $this->_errors[] = $this->l('Install state order error');
                return false;
            }

            Configuration::updateValue('CENEO_BS_KEY', '');
            Configuration::updateValue('CENEO_BS_TOKEN_EXPIRES', time());
            Configuration::updateValue('CENEO_BS_DATE_FROM', time());

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function uninstall()
    {
        if (parent::uninstall()) {
            if (!(new Installer($this))->uninstall()) {
                return false;
            }
            return true;
        }

        return false;
    }

    private function _postProcess()
    {
        if (Configuration::updateValue('CENEO_BS_KEY', Tools::getValue('CENEO_BS_KEY'))) {
            Configuration::updateValue('CENEO_BS_TOKEN_EXPIRES', time());
            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        } else {
            $this->_html .= $this->displayError($this->l('Settings failed'));
        }
    }

    private function _postValidation()
    {
        if (!Tools::getValue('CENEO_BS_KEY')) {
            $this->_postErrors[] = $this->l('Please provide all data');
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->helper->displayInfoHeader();
        $this->_html .= $this->renderForm();
        $this->_html .= '<br />';
        return $this->_html;
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Marketplace Ceneo (Buy Now) CONFIGURATION'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('API KEY'),
                        'desc' => $this->l('API KEY given by Ceneo'),
                        'name' => 'CENEO_BS_KEY',
                        'size' => 225,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save settings'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues(): array
    {
        $return = [];
        $return['CENEO_BS_KEY'] = Tools::getValue('CENEO_BS_KEY', Configuration::get('CENEO_BS_KEY'));

        return $return;
    }

    public function hookdisplayAdminOrderTop($params)
    {
        $id_order = $params['id_order'];
        $order = new Order($id_order);
        $repository = new RepositoryBasketservice();
        $ceneoOrder = $repository->getAllCeneoOrderId($id_order);

        print_r($id_order);
        if ($ceneoOrder) {
            return $this->helper->displayAdminOrder(
                $order->getFirstMessage(),
                $ceneoOrder['shipping_method'],
                $ceneoOrder['payment_method']
            );
        }
        return null;
    }

    /**
     * Action order status update
     * @param $params
     */
    public function hookActionOrderStatusUpdate($params)
    {
        $id_order = $params['id_order'];
        $newOrderStatusId = $params['newOrderStatus']->id;

        $repository = new RepositoryBasketservice();
        $api = new Client();

        $orderState = new OrderHandler($repository, $api);
        $orderState->orderChangeState($newOrderStatusId, $id_order);
    }

    public function hookActionGetExtraMailTemplateVars($params)
    {
        if ($params['template'] != 'shipped') {
            return null;
        }

        $orderID = $params['template_vars']['{id_order}'];
        $order = new Order((int) $orderID);
        $orderCarrier = new OrderCarrier($order->getIdOrderCarrier());
        $trackingNumber = $orderCarrier->tracking_number;

        if ($trackingNumber) {
            $this->updateTrackingNumber($order, $trackingNumber);
        }
    }

    public function hookActionObjectOrderCarrierUpdateAfter($params)
    {
        $orderCarrier = $params['object'];

        if (isset($orderCarrier->tracking_number)) {
            $orderID = $orderCarrier->id_order;
            $order = new Order($orderID);

            $this->updateTrackingNumber($order, $orderCarrier->tracking_number);
        }
    }

    public function updateTrackingNumber($order, $trackingNumber)
    {
        $repository = new RepositoryBasketservice();
        $ceneoOrderId = $repository->getCeneoOrderId($order->id);
        $ceneoCarriers = $repository->getCeneoCarrierById($order->id_carrier);
        $ceneoCarrierId = null;

        if (!empty($ceneoCarriers) && isset($ceneoCarriers['ceneo_carrier_id'])) {
            $ceneoCarrierId = $ceneoCarriers['ceneo_carrier_id'];
        }

        if ($trackingNumber && $ceneoOrderId && $ceneoCarrierId) {
            $this->api->setOrderShipment($ceneoOrderId, $trackingNumber, $ceneoCarrierId);
        }
    }
}
