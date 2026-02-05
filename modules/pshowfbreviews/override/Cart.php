<?php

class Cart extends CartCore
{
    public function getCartTotalPrice()
    {
        if (version_compare(_PS_VERSION_, '1.7.7.8', '<')) {
            $summary = $this->getSummaryDetails();
            
            $id_order = (int) Order::getIdByCartId($this->id);
            $order = new Order($id_order);
            
            if (Validate::isLoadedObject($order)) {
                $taxCalculationMethod = $order->getTaxCalculationMethod();
            } else {
                $taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
            }
            
            return $taxCalculationMethod == PS_TAX_EXC ?
            $summary['total_price_without_tax'] :
            $summary['total_price'];
        }
        else {
            return parent::getCartTotalPrice();
        }
    }
}
