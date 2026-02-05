<?php
class AppStateDocument {
	public $Identity;
	public $ShopApplicationNumbers;
	public function __construct($i, $a){
		$this->Identity = $i;
		$this->ShopApplicationNumbers = $a;
	}
};
