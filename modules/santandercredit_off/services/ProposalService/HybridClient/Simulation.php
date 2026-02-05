<?php

class Simulation {
	public $Downpayment;
    public $InstalmentsNumber;
    public $ProductNumber;
    public $TotalPrice;
			public function __construct($dnp, $inr, $prd, $price){
				$this->Downpayment = $dnp;
				$this->InstalmentsNumber = $inr;
				$this->ProductNumber = $prd;
				$this->TotalPrice = $price;
			}
};