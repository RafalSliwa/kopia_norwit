<?php
class OrderCarrier extends OrderCarrierCore
{
    /*
    * module: x13allegro
    * date: 2025-09-29 17:04:13
    * version: 7.7.6
    */
    public function sendInTransitEmail($order)
    {
        $allegro = _PS_MODULE_DIR_ . 'x13allegro/x13allegro.php';
        if (file_exists($allegro)) {
            require_once ($allegro);
            if (Module::isEnabled('x13allegro')
                && XAllegroForm::orderExists($order->id)
                && !(bool)XAllegroConfiguration::get('ORDER_SEND_CUSTOMER_MAIL')
            ) {
                return true;
            }
        }
        return parent::sendInTransitEmail($order);
    }
}
