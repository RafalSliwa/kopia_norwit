<?php

require_once (dirname(__FILE__) . '/../../x13allegro.php');

final class AdminXAllegroAssocController extends XAllegroController
{
    private $categoriesController;
    private $manufacturersController;

    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAssocCategories'));
        }

        $this->table = 'xallegro_configuration';
        $this->identifier = 'id_xallegro_configuration';
        $this->className = 'XAllegroConfiguration';

        parent::__construct();

        $this->categoriesController = new AdminXAllegroAssocCategoriesController();
        $this->categoriesController->token = $this->token;
        $this->categoriesController->init();

        $this->manufacturersController = new AdminXAllegroAssocManufacturersController();
        $this->manufacturersController->token = $this->token;
        $this->manufacturersController->init();

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminXAllegroAssoc'));
    }

    public function postProcess()
    {
        foreach ($_GET as $get => $value) {
            // add, update, delete
            if (preg_match('/^((?!id_).*)xallegro_(.*)$/', $get, $m)) {
                if ($m[2] == 'category') {
                    $controller = $this->context->link->getAdminLink('AdminXAllegroAssocCategories');
                    $identifier = $this->categoriesController->identifier;
                }
                else if ($m[2] == 'manufacturer') {
                    $controller = $this->context->link->getAdminLink('AdminXAllegroAssocManufacturers');
                    $identifier = $this->manufacturersController->identifier;
                }
                else {
                    continue;
                }

                $url = $controller . '&' . $get . '&' . $identifier . '=' . Tools::getValue($identifier);
                if (strpos($m[1], 'submitBulk') !== false) {
                    unset($_POST['token']);
                    $url .= '&' . http_build_query($_POST);
                }

                Tools::redirectAdmin($url);
            }

            unset($m);
        }

        return parent::postProcess();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['allegro_category'] = array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAssocCategories') . '&add' . $this->categoriesController->table,
                'desc' => $this->l('Dodaj powiązanie kategorii'),
                'icon' => 'process-icon-new'
            );

            $this->page_header_toolbar_btn['allegro_manufacturer'] = array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAssocManufacturers') . '&add' . $this->manufacturersController->table,
                'desc' => $this->l('Dodaj powiązanie producenta'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function init()
    {
        parent::init();

        $this->fields_options = $this->categoriesController->getFieldsOptions();
    }

    public function beforeUpdateOptions()
    {
        $this->redirect_after = $this->context->link->getAdminLink('AdminXAllegroAssoc') . '&conf=6';
    }

    public function initContent()
    {
        parent::initContent();

        $messages = [
            'confirmations',
            'informations',
            'warnings',
            'errors'
        ];

        foreach ($messages as $message) {
            $this->{$message} = array_merge(
                $this->{$message},
                $this->categoriesController->{$message},
                $this->manufacturersController->{$message}
            );
        }
    }

    public function renderList()
    {
        return $this->categoriesController->renderList() .
            $this->manufacturersController->renderList();
    }
}
