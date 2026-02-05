<?php

require_once dirname(__FILE__) . "/../../config.php";

class PShowFbReviewsAjaxloaderModuleFrontController extends ModuleFrontController
{
    public function displayAjaxAddToCartPixel()
    {
        $id_product = (int)Tools::getValue("id_product");
        $id_product_attribute = (int)Tools::getValue("id_product_attribute");
        $id_customization = (int)Tools::getValue("id_customization");
        $quantity = 1;
        $product = new Product($id_product, false, (int) Context::getContext()->language->id);
        $params_for_fb = array();
        $params_for_fb['product_name'] = $product->name;
        $params_for_fb['id_product'] = $product->id;
        $params_for_fb['path_to_category'] = $this->getCategoryPath($product->id_category_default);
        $eventId = [
            "00000000000000",
            $this->context->cart->id,
            $product->id,
            $quantity,
            $id_product_attribute,
            $id_customization,
        ];
        $params_for_fb['event_id'] = sha1(implode('-', $eventId));
        $params_for_fb['quantity'] = $quantity;
        $params_for_fb['currency'] = $this->context->currency->iso_code;
        $params_for_fb['value'] = $product->getPrice(true, $id_product_attribute, 2);
        $eventToSend = array();
        if((int)Configuration::get("PSHOW_FBREVIEWS_FBPIXEL_SEND_METHOD") > 0) {
            $eventToSend = [
                'type' => "AddToCart",
                'content' => [
                    'content_name' => $params_for_fb['product_name'],
                    'content_ids' => [$params_for_fb['id_product']],
                    'content_type' => 'product',
                    'contents' => [['id' => ($product->reference ? $product->reference : $product->id), 'quantity' => $quantity]],
                    'currency' => $this->context->currency->iso_code,
                    'value' => $product->getPrice(true, $id_product_attribute, 2),
                ],
                'event_data' => ['eventID' => sha1(implode('-', $eventId))],
            ];
        }
        $json = json_encode($eventToSend);
        die($json);
    }
}
