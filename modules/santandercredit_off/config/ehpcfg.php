<?php
/*
Tylko PAYMENT_DECLARED powinien być ustawiany ręcznie. Reszta przez ProposalSeviceHybrid 
*/
define('EHP_APP_STATES', [
    'PAYMENT_DECLARED' => 'EHP 01: Wybrano finansowanie kredytem',
    'APPLICATION_PROCEDING' => 'EHP 02: oczekiwanie na decyzję Banku',  //yellow
    'ABANDONED' => 'EHP 03: Porzucono wniosek kredytowy',	//red
    'CREDIT_GRANTED' => 'EHP 04: Udzielono kredytu',  //green
    'CREDIT_REJECTED' => 'EHP 05: Bank odmówił udzielenia kredytu',    //red    
    'CANCELLED' => 'EHP 06: Anulowano wniosek kredytowy',    //red
    'GOODS_CONFIRMATION' => 'EHP 07: Potwierdź dostępność towaru'
]);

define('EHP_ORDER_STATE_PREFIX', 'SCB_EHP_ST_');

define('BANK2SHOP_STATE_MAP', [
    'Bank' => 'APPLICATION_PROCEDING',
    'Bank (-101)' => 'APPLICATION_PROCEDING',
    'Bank (-105)' => 'APPLICATION_PROCEDING',
    'Klient' => 'APPLICATION_PROCEDING',
    'Klient (-100)' => 'APPLICATION_PROCEDING',
    'Klient (-104)' => 'APPLICATION_PROCEDING',
    'Klient(-127)' => 'APPLICATION_PROCEDING',
    'Odmowa' => 'CREDIT_REJECTED',
    'Sklep' => 'GOODS_CONFIRMATION',
    'Wydaj_towar' => 'CREDIT_GRANTED',
    'Zakonczona' => 'CREDIT_GRANTED'
]);

define('CHECK_THIS_STATES', [
    'PAYMENT_DECLARED',
    'APPLICATION_PROCEDING',
    'ABANDONED',
]);

define('CAN_SEND_APPLICATION',['NO' => 0, 'YES' => 1, 'MAYBE' => 2]);

define('EHP_DEF_URLS',[
    'EHP_DEF_URL_SYMULATOR' => 'https://wniosek.eraty.pl/symulator/oblicz/',
    'EHP_DEF_URL_WNIOSEK' => 'https://wniosek.eraty.pl/formularz/',
    'EHP_DEF_SVC_LOCATION' => 'https://api.santanderconsumer.pl/ProposalServiceHybrid'
]);

define('EHP_DEF_QUERIES',[
    'EHP_DEF_QTY_QUERY' => str_replace('<','&lt;', str_replace('>', '&gt;', str_replace('"','&quot;',str_replace("'", "&#039;", "$('#quantity_wanted').val();")))),
    'EHP_DEF_PRICE_QUERY' => str_replace('<','&lt;', str_replace('>', '&gt;', str_replace('"','&quot;',str_replace("'", "&#039;", "$('div.current-price > span[itemprop=\"price\"],div.current-price > span.current-price-value').attr(\"content\");"))))
]);