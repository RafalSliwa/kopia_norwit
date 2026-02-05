<?php

use Leasenow\Payment\Api;

/**
 * Class LeaseNowNotificationModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 * @property bool display_footer
 * @property bool display_header
 */
class LeaseNowCheckleasingModuleFrontController extends ModuleFrontController
{

    /**
     * @var string
     */
    const CONTACT_TYPE_PHONE = 'PHONE';

    /**
     * @var string
     */
    const CONTACT_TYPE_EMAIL = 'EMAIL';

    /**
     * Initialize controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->display_footer = false;
        $this->display_header = false;

        parent::init();
    }

    /**
     * @throws PrestaShopException
     * @throws Exception
     */
    public function process()
    {

        $token = Tools::getValue('token');
        $reservationId = Tools::getValue('reservationId');

        if (empty($reservationId) || empty($token)) {
            echo $this->response();
            exit();
        }

        if ($token !== Tools::getAdminToken($reservationId)) {
            echo $this->response();
            exit();
        }

        $credentials = $this->module->getCredentials();

        if (!$credentials) {
            echo $this->response();
            exit();
        }

        $leasing = $this->module->getLeasing($reservationId, true);

        if ($leasing['success']
            && isset($leasing['body']['status']) && $leasing['body']['status']) {
            echo $this->response(true, $this->leasenow_map_status($leasing['body']['status']));
            exit();
        }

        echo $this->response();
        exit();
    }

    /**
     * @param bool $success
     *
     * @return string
     */
    private function response($success = false, $status = '')
    {

        $response = [
            'success' => $success,
        ];

        if ($status) {
            $response['status'] = $status;
        }

        return json_encode($response);
    }

    /**
     * @param string $status
     *
     * @return string|void
     */
    private function leasenow_map_status($status)
    {

        switch ($status) {
            case Api::S_CREATED:
                return $this->module->l('New leasing offer', 'checkleasing');
            case Api::S_ASSIGNED:
                return $this->module->l('The customer clicked used - started the process of filling in the application', 'checkleasing');
            case Api::S_FILLED:
                return $this->module->l('The client completed the application and received a preliminary positive decision', 'checkleasing');
            case Api::S_SETTLED:
                return $this->module->l('Leasing started', 'checkleasing');
            case Api::S_DECLINED:
                return $this->module->l('Leasing declined', 'checkleasing');
            default:
                return $this->module->l('Leasing status unknown', 'checkleasing');
        }
    }

}
