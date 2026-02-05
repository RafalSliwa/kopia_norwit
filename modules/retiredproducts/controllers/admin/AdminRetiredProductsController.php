<?php

class AdminRetiredProductsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true; 
        $this->allow_export = false;

        parent::__construct();

        
        $this->_where = 'AND a.active = 0';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';

        // Definicja pól w tabeli
        $this->fields_list = [
            'id_product' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'name' => [
                'title' => $this->l('Name')
            ],
            'price' => [
                'title' => $this->l('Price'),
                'type' => 'price',
                'currency' => true
            ],
            'active' => [
                'title' => $this->l('Active'),
                'type' => 'bool',
                'active' => 'status',
                'orderby' => false
            ]
        ];
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('enable');
        return parent::renderList();
    }
}
