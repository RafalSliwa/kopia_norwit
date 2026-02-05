<?php

class Order extends OrderCore
{
    public $user_ip;
    
    public $user_agent;
    
    public function __construct($id = null, $idLang = null)
    {
        self::$definition['fields']['user_ip'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['user_agent'] = array('type' => self::TYPE_STRING);
        
        parent::__construct($id, $idLang);
    }
}
