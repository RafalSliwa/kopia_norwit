<?php

class ClientIdentity {
    public $Login;
    public $Password;
	/*sklep testowy 99995 mozna wykorzystać jeśli jest przypisany do loginu sklepu.
		Jeśli nie - testy można wykonywać na własnym sklepie.*/
    public $ShopNumber;

    public function __construct($Login, $Password, $ShopNumber) {
        $this->Login = $Login;
        $this->Password = $Password;
        $this->ShopNumber = $ShopNumber;
    }
};
