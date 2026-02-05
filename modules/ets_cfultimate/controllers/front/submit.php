<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class Ets_CfUltimateSubmitModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if ($id = (int)Tools::getValue('_ets_cfu_container_post')) {
	        header('Content-Type: application/json');
            $item = ets_cfu_contact_form($id);
            if (!$item instanceof ETS_CFU_Contact_Form || !$item->id || !$item->active || !ETS_CFU_Contact::giveAccessToCustomerGroup($item->id, $this->context)) {
                die(json_encode([
                    'status' => 'not_found',
                    'message' => !$item instanceof ETS_CFU_Contact_Form || !$item->id ? $this->l('Contact form not found.', 'submit') : $this->l('Contact form is not available.', 'submit'),
                ]));
            }
            $item->id_product = Tools::getValue('_ets_cfu_product_id');
            $item->unit_tag = Tools::isSubmit('_ets_cfu_unit_tag') ? Tools::getValue('_ets_cfu_unit_tag') : '';
            $item->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? Tools::substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '';
            $item->container_post_id = Tools::isSubmit('_ets_cfu_container_post') ? (int)Tools::getValue('_ets_cfu_container_post') : 0;
            $result = $item->submit();
            $unit_tag = Tools::getValue('_ets_cfu_unit_tag');
            $response = array(
                'into' => '#' . ets_cfu_sanitize_unit_tag($unit_tag),
                'status' => $result['status'],
                'message' => $result['message'],
            );
            if ('validation_failed' == $result['status']) {
                $invalid_fields = array();
                foreach ((array)$result['invalid_fields'] as $name => $field) {
                    $invalid_fields[] = array(
                        'into' => 'span.ets_cfu_form-control-wrap.' . ets_cfu_sanitize_html_class($name),
                        'message' => $field['reason'],
                        'idref' => $field['idref'],
                    );
                }

                $response['invalidFields'] = $invalid_fields;
            }
            die(json_encode($response));
        }
    }
}