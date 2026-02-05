<?php
/**
 * 2007-2025 Sendinblue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@sendinblue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sendinblue <contact@sendinblue.com>
 * @copyright 2007-2025 Sendinblue
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Sendinblue
 */

namespace Sendinblue\Hooks;

use Sendinblue\Services\DataValidationService;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ActionObjectCustomerUpdateBeforeHook extends AbstractHook
{
    /**
     * @param CustomerCore $data
     * @return void
     */
    public function handleEvent($data)
    {
        try {
            $customerId = (int) $data['object']->id;
            $customerInDb = new \CustomerCore($customerId);
            if ($data['object']->newsletter == $customerInDb->newsletter) {
                return;
            }
            $dVService = new DataValidationService();
            $contactPayload = [
                'id' => $customerInDb->id,
                'email' => $customerInDb->email,
                'id_shop' => $this->getSendinblueConfigService()->shopId,
                'date_upd' => $dVService->checkAndGiveMeString(date('Y-m-d', strtotime($data['object']->date_upd))),
                'newsletter' => $data['object']->newsletter ? 'true' : 'false',
            ];
            $this->getApiClientService()->updateContact($contactPayload);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }
}
