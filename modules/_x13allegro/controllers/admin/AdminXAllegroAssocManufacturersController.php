<?php

require_once (dirname(__FILE__) . '/../../x13allegro.php');

final class AdminXAllegroAssocManufacturersController extends XAllegroController
{
    /** @var XAllegroManufacturer */
    public $object;

    public function __construct()
    {
        $this->table = 'xallegro_manufacturer';
        $this->identifier = 'id_xallegro_manufacturer';
        $this->className = 'XAllegroManufacturer';
        $this->multiple_fieldsets = true;

        parent::__construct();

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->allegroAutoLogin = true;
            $this->allegroAccountSwitch = true;
        }

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminXAllegroAssocManufacturers'));
        $this->tpl_folder = 'x_allegro_manufacturers/';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
    }

    public function init()
    {
        parent::init();

        $this->loadObject(true);
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display) && version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->page_header_toolbar_btn['allegro_manufacturer'] = array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAssocManufacturers') . '&add' . $this->table,
                'desc' => $this->l('Dodaj powiązanie producenta'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')
            && Tools::getValue('controller') == 'AdminXAllegroAssocManufacturers'
            && empty($this->errors)
        ) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAssoc'));
        }

        $this->initPageHeaderToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'm.`name` as id_manufacturer';
        $this->_join = 'JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (a.`id_manufacturer` = m.`id_manufacturer`)';

        $this->fields_list = array(
            'id_manufacturer' => array(
                'title' => $this->l('Producent'),
                'search' => false,
                'filter' => false
            )
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!Validate::isLoadedObject($this->object)) {
            $this->warnings[] = $this->l('Musisz zapisać tego producenta przed mapowaniem tagów.');
        }

        $manufacturers = array_merge(
            array(
                array(
                    'id_manufacturer' => 0,
                    'name' => $this->l('-- Wybierz --')
                )
            ),
            Manufacturer::getManufacturers()
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Producent')
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Producent'),
                    'name' => 'id_manufacturer',
                    'required' => true,
                    'options' => array(
                        'query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Zapisz'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Zapisz i zostań'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        $tagManager = new XAllegroHelperTagManager();
        $tagManager->setMapType(XAllegroTagManager::MAP_MANUFACTURER);
        $tagManager->setContainer('xallegro_manufacturer_form');

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Tagi producenta'),
            ),
            'input' => array(
                array(
                    'type' => 'tag-manager',
                    'name' => 'tag-manager',
                    'content' => (Validate::isLoadedObject($this->object) ? $tagManager->renderTagManager($this->object->tags) : '')
                )
            ),
            'submit' => array(
                'title' => $this->l('Zapisz'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Zapisz i zostań'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)
            || Tools::isSubmit('submitAdd' . $this->table . 'AndStay')
        ) {
            $id_manufacturer = Tools::getValue('id_manufacturer');

            if (!$id_manufacturer) {
                $this->errors[] = $this->l('Nie wybrano producenta.');
                return false;
            }
            else if ((Validate::isLoadedObject($this->object)
                    && $this->object->id_manufacturer != $id_manufacturer
                    && XAllegroManufacturer::isAssigned($id_manufacturer))
                || (!Validate::isLoadedObject($this->object)
                    && XAllegroManufacturer::isAssigned($id_manufacturer))
            ) {
                $this->errors[] = $this->l('Posiadasz już powiązanie tego producenta.');
                return false;
            }

            foreach (Tools::getValue('xallegro_tag', array()) as $user_id => $tags) {
                $this->object->tags[$user_id] = $tags;
            }

            $this->object->id_manufacturer = $id_manufacturer;
            $this->object->save();

            if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAssocManufacturers') .
                    '&conf=4&update' . $this->table . '&' . $this->identifier . '=' . $this->object->id);
            }

            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAssoc') . '&conf=4');
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAssocManufacturers') . '&conf=4');
        }

        return parent::postProcess();
    }

    public function initContent()
    {
        if (!empty($this->errors)) {
            $this->display = 'edit';
        }

        parent::initContent();
    }
}
