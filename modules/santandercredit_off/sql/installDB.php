<?php
/**
 * service: HTTP_POST/ProposalServiceHybrid
 * method: POST/GetOrderStatus
 * 
 */


$sql = array();

//(0 if there is no ehp db)
$dbv = SantanderCredit::getEhpDbVersion();

//next db version
if($dbv < 1){
$sql = [
'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'scb_ehp_log (
    id INT(10) NOT NULL AUTO_INCREMENT ,
    id_order int(10),
    pos_app_number varchar(64) NOT NULL,    
    application_number varchar(64) NULL ,
    agreement_number varchar(64) NULL ,
    agreement_date DATETIME NULL ,
    shop_number VARCHAR(10) NOT NULL ,
    ehp_token VARCHAR(128) NULL ,    
    request_date DATETIME  NOT NULL ,
    success INT(1) NOT NULL ,
    application_status VARCHAR(60) NULL ,
    app_status_chg_date DATETIME NULL ,
    downpayment DECIMAL(10,2) NULL,
    total_price DECIMAL(10,2) NULL,
    message TEXT NULL,
    PRIMARY KEY (`id`),    
    INDEX `scb_ehp_san_idx` (`pos_app_number`),
    INDEX `scb_ehp_app_number_idx` (`application_number`),
    INDEX `scb_ehp_id_order_idx` (`id_order`));',
'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping (    
    id_order int(10) NOT NULL, 
    date_add DATETIME  NOT NULL ,
    pos_app_number varchar(64) NOT NULL,
    reference varchar(64) NULL , 
    application_number varchar(64) NULL , 
    agreement_number varchar(64) NULL ,
    agreement_date DATETIME NULL ,    
    ehp_token VARCHAR(128) NULL ,    
    order_status VARCHAR(60) NULL ,
    application_status VARCHAR(60) NULL ,
    app_status_chg_date DATETIME NULL ,
    shop_number VARCHAR(10) NOT NULL ,    
    downpayment DECIMAL(10,2) NULL,
    total_price DECIMAL(10,2) NULL,    
    post_data TEXT NULL,
    check_date DATETIME NULL,    
    check_it INT (1) NOT NULL ,
    PRIMARY KEY (`pos_app_number`), 
    INDEX `scb_ehp_app_number_idx` (`application_number`),
    INDEX `scb_ehp_check_date_idx` (`check_date`),
    INDEX `scb_ehp_check_it_idx` (`check_it`),
    INDEX `scb_ehp_id_order_idx` (`id_order`));',
'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'scb_ehp_phist (
        id int(10) NOT NULL AUTO_INCREMENT,
        h_date datetime NOT NULL ,
        db_ver int(10) NULL ,
        module_ver varchar(10),
        params VARCHAR(1024) NULL ,
        PRIMARY KEY (`id`));'
];
}

$dbv = 1;

$qUpdDbv = 'INSERT INTO ' . _DB_PREFIX_ . 'scb_ehp_phist (h_date, db_ver, module_ver) VALUES (';
$qUpdDbv = $qUpdDbv.'\''.date("Y-m-d H:i:s").'\','.strval($dbv).',\''.$this->version.'\')';
array_push($sql, $qUpdDbv);
foreach ($sql as $query) {	
    Db::getInstance()->execute($query);
}
