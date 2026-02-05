<?php
/**
 * Product.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Carrier
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * Product.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCustomerThreadsController extends AdminCustomerThreadsControllerCore
{
    /**
     * Imap synchronization method.
     *
     * @return array errors list
     */
    public function syncImap()
    {
        if (!($url = Configuration::get('PS_SAV_IMAP_URL'))
            || !($port = Configuration::get('PS_SAV_IMAP_PORT'))
            || !($user = Configuration::get('PS_SAV_IMAP_USER'))
            || !($password = Configuration::get('PS_SAV_IMAP_PWD'))) {
            return ['hasError' => true, 'errors' => ['IMAP configuration is not correct']];
        }
        $conf = Configuration::getMultiple([
            'PS_SAV_IMAP_OPT_POP3', 'PS_SAV_IMAP_OPT_NORSH', 'PS_SAV_IMAP_OPT_SSL',
            'PS_SAV_IMAP_OPT_VALIDATE-CERT', 'PS_SAV_IMAP_OPT_NOVALIDATE-CERT',
            'PS_SAV_IMAP_OPT_TLS', 'PS_SAV_IMAP_OPT_NOTLS']);
        $conf_str = '';
        if ($conf['PS_SAV_IMAP_OPT_POP3']) {
            $conf_str .= '/pop3';
        }
        if ($conf['PS_SAV_IMAP_OPT_NORSH']) {
            $conf_str .= '/norsh';
        }
        if ($conf['PS_SAV_IMAP_OPT_SSL']) {
            $conf_str .= '/ssl';
        }
        if ($conf['PS_SAV_IMAP_OPT_VALIDATE-CERT']) {
            $conf_str .= '/validate-cert';
        }
        if ($conf['PS_SAV_IMAP_OPT_NOVALIDATE-CERT']) {
            $conf_str .= '/novalidate-cert';
        }
        if ($conf['PS_SAV_IMAP_OPT_TLS']) {
            $conf_str .= '/tls';
        }
        if ($conf['PS_SAV_IMAP_OPT_NOTLS']) {
            $conf_str .= '/notls';
        }
        if (!function_exists('imap_open')) {
            return ['hasError' => true, 'errors' => ['imap is not installed on this server']];
        }
        $mbox = @imap_open('{' . $url . ':' . $port . $conf_str . '}', $user, $password);
        $errors = imap_errors();
        if (is_array($errors)) {
            $errors = array_unique($errors);
        }
        $str_errors = '';
        $str_error_delete = '';
        if ($errors && count($errors) && is_array($errors)) {
            $str_errors = '';
            foreach ($errors as $error) {
                $str_errors .= $error . ', ';
            }
            $str_errors = rtrim(trim($str_errors), ',');
        }
        if (!$mbox) {
            return ['hasError' => true, 'errors' => ['Cannot connect to the mailbox :<br />' . ($str_errors)]];
        }
        $check = imap_check($mbox);
        if (!$check) {
            return ['hasError' => true, 'errors' => ['Fail to get information about the current mailbox']];
        }
        if ($check->Nmsgs == 0) {
            return ['hasError' => true, 'errors' => ['NO message to sync']];
        }
        $result = imap_fetch_overview($mbox, "1:{$check->Nmsgs}", 0);
        $message_errors = [];
        foreach ($result as $overview) {
            if (isset($overview->subject)) {
                $subject = $overview->subject;
            } else {
                $subject = '';
            }
            $md5 = md5($overview->date . $overview->from . $subject . $overview->msgno);
            $exist = Db::getInstance()->getValue(
                'SELECT `md5_header`
						 FROM `' . _DB_PREFIX_ . 'customer_message_sync_imap`
						 WHERE `md5_header` = \'' . pSQL($md5) . '\''
            );
            if ($exist) {
                if (Configuration::get('PS_SAV_IMAP_DELETE_MSG')) {
                    if (!imap_delete($mbox, $overview->msgno)) {
                        $str_error_delete = ', Fail to delete message';
                    }
                }
            } else {
                $match_found = false;
                if (Module::isEnabled('roja45quotationspro') && (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                )) {
                    $tracking = explode(':', $overview->references);

                    if (isset($tracking[0], $tracking[1])) {
                        $match_found = true;
                        $id_ct = $tracking[1];
                        $id_tc = $tracking[0];
                    }
                } else {
                    preg_match('/\#ct([0-9]*)/', $subject, $matches1);
                    preg_match('/\#tc([0-9-a-z-A-Z]*)/', $subject, $matches2);

                    if (isset($matches1[1], $matches2[1])) {
                        $match_found = true;
                        $id_ct = $matches1[1];
                        $id_tc = $matches2[1];
                    }
                }

                $new_ct = (Configuration::get('PS_SAV_IMAP_CREATE_THREADS') && !$match_found && (strpos($subject, '[no_sync]') == false));
                $fetch_succeed = true;
                if ($match_found || $new_ct) {
                    if ($new_ct) {
                        $from_parsed = [];
                        if (!isset($overview->from)
                            || (!preg_match('/<(' . Tools::cleanNonUnicodeSupport('[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+') . ')>/', $overview->from, $from_parsed)
                                && !Validate::isEmail($overview->from))) {
                            $message_errors[] = $this->trans('Cannot create message in a new thread.', [], 'Admin.Orderscustomers.Notification');
                            continue;
                        }
                        $from = $overview->from;
                        if (isset($from_parsed[1])) {
                            $from = $from_parsed[1];
                        }
                        $contacts = Contact::getContacts($this->context->language->id);
                        if (!$contacts || !count($contacts)) {
                            continue;
                        }
                        foreach ($contacts as $contact) {
                            if (isset($overview->to) && strpos($overview->to, $contact['email']) !== false) {
                                $id_contact = $contact['id_contact'];
                            }
                        }
                        if (!isset($id_contact)) { // if not use the default contact category
                            $id_contact = $contacts[0]['id_contact'];
                        }

                        if ($id_contact) {
                            throw new Exception('Unable to find the default customer service account.');
                        }

                        $customer = new Customer();
                        $client = $customer->getByEmail($from); //check if we already have a customer with this email
                        $ct = new CustomerThread();
                        if (isset($client->id)) { //if mail is owned by a customer assign to him
                            $ct->id_customer = $client->id;
                        }
                        $ct->email = $from;
                        $ct->id_contact = $id_contact;
                        $ct->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                        $ct->id_shop = $this->context->shop->id; //new customer threads for unrecognized mails are not shown without shop id
                        $ct->status = 'open';
                        $ct->token = RojaFortyFiveQuotationsProCore::referenceGen(12);
                        $ct->add();
                    } else {
                        //$ct = new CustomerThread((int) $matches1[1]);
                        $ct = new CustomerThread((int) $id_ct);
                    } //check if order exist in database
                    //if (Validate::isLoadedObject($ct) && ((isset($matches2[1]) && $ct->token == $matches2[1]) || $new_ct)) {
                    if (Validate::isLoadedObject($ct) && ((isset($id_tc) && $ct->token == $id_tc) || $new_ct)) {
                        $structure = imap_bodystruct($mbox, $overview->msgno, '1');
                        if ($structure->type == 0) {
                            $message = imap_fetchbody($mbox, $overview->msgno, '1');
                        } elseif ($structure->type == 1) {
                            $structure = imap_bodystruct($mbox, $overview->msgno, '1.1');
                            $message = imap_fetchbody($mbox, $overview->msgno, '1.1');
                        } else {
                            continue;
                        }
                        imap_headerinfo($mbox, $overview->msgno);
                        switch ($structure->encoding) {
                            case 3:
                                $message = imap_base64($message);
                                break;
                            case 4:
                                $message = imap_qprint($message);
                                break;
                        }
                        $message = iconv($this->getEncoding($structure), 'utf-8', $message);
                        $message = nl2br($message);
                        if (!$message || strlen($message) == 0) {
                            $message_errors[] = $this->trans('The message body is empty, cannot import it.', [], 'Admin.Orderscustomers.Notification');
                            $fetch_succeed = false;
                            continue;
                        }
                        $cm = new CustomerMessage();
                        $cm->id_customer_thread = $ct->id;
                        if (empty($message) || !Validate::isCleanHtml($message)) {
                            $str_errors .= $this->trans('Invalid message content for subject: %s', [$subject], 'Admin.Orderscustomers.Notification');
                        } else {
                            try {
                                $cm->message = $message;
                                $cm->add();
                            } catch (PrestaShopException $pse) {
                                $message_errors[] = $this->trans('The message content is not valid, cannot import it.', [], 'Admin.Orderscustomers.Notification');
                                $fetch_succeed = false;
                                continue;
                            }
                        }
                    }
                }
                if ($fetch_succeed) {
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'customer_message_sync_imap` (`md5_header`) VALUES (\'' . pSQL($md5) . '\')');
                }
            }
        }
        imap_expunge($mbox);
        imap_close($mbox);
        if (count($message_errors) > 0) {
            if (($more_error = $str_errors . $str_error_delete) && strlen($more_error) > 0) {
                $message_errors = array_merge([$more_error], $message_errors);
            }
            return ['hasError' => true, 'errors' => $message_errors];
        }
        if ($str_errors . $str_error_delete) {
            return ['hasError' => true, 'errors' => [$str_errors . $str_error_delete]];
        } else {
            return ['hasError' => false, 'errors' => ''];
        }
    }
}
