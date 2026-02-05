<?php

class Application {
    /**
     * Agreement date
     */
    public $AgreementDate;

    /**
     * Credit Agreement number
     */
    public $AgreementNumber;
    /**
     * Credit application number
     */
    public $ApplicationNumber;
    /**
     * Application state change date
     */
    public $ChangeDate;
    /**
     * Application state
     */
    public $CreditState;
    /**
     * Downpayment(reduces total price in credit application)
     */
    public $Downpayment;
    /**
     * This is order number. In PrestaShop - order reference (in previous module versions it was order id)
     */
    public $ShopApplicationNumber;
    /**
     * Shop number (in the Bank database)
     */
    public $ShopNumber;
    /**
     * Total Price paid for the order
     */
    public $TotalPrice;

    public $check_date;

}