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

class ActionCustomerAccountUpdateHook extends AbstractHook
{
    /**
     * @param \CustomerCore $customer
     */
    public function handleEvent($customer)
    {
        try {
            if (!$customer->id) {
                return;
            }

            $customer_groups = $customer->getGroups();
            $all_groups = \Group::getGroups(\ConfigurationCore::get('PS_LANG_DEFAULT'));
            array_walk($customer_groups, function (&$customer_groups, $key, $all_groups) {
                foreach ($all_groups as $group) {
                    if ($customer_groups == $group['id_group']) {
                        $customer_groups = $group['name'];
                    }
                }
            }, $all_groups);
            $customer_groups = !empty($customer_groups) ? implode(', ', $customer_groups) : '';

            $dVService = new DataValidationService();

            $contactPayload = [
                'id' => $customer->id,
                'id_default_group' => $customer->id_default_group,
                'id_group' => $customer_groups,
                'id_shop' => $this->getSendinblueConfigService()->shopId,
                'id_lang' => !is_null($this->getContextlanguage()) ? $dVService->checkAndGiveMeString($this->getContextlanguage()->iso_code) : null,
                'email' => $customer->email,
                'firstname' => $dVService->checkAndGiveMeString($customer->firstname),
                'lastname' => $dVService->checkAndGiveMeString($customer->lastname),
                'id_gender' => $customer->id_gender,
                'birthday' => $customer->birthday,
                'date_add' => $dVService->checkAndGiveMeString(date('Y-m-d', strtotime($customer->date_add))),
                'date_upd' => $dVService->checkAndGiveMeString(date('Y-m-d', strtotime($customer->date_upd))),
            ];

            if ($customer->newsletter) {
                $contactPayload['newsletter'] = 'true';
                $contactPayload['newsletter_date_add'] = $dVService->checkAndGiveMeString(date('Y-m-d', strtotime($customer->newsletter_date_add)));

                if ($customer->newsletter_date_add === $customer->date_upd) {
                    $this->getApiClientService()->createContact($contactPayload);
                } else {
                    $this->getApiClientService()->updateContact($contactPayload);
                }
            } elseif ($this->getSendinblueConfigService()->isOrderAutoSyncEnabled()) {
                $contactPayload['newsletter'] = 'false';
                $contactPayload['newsletter_date_add'] = '';
                $this->getApiClientService()->updateContact($contactPayload);
            }
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
        }
    }
}
