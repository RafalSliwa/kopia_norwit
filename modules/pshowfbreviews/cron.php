<?php

require_once dirname(__FILE__) . "/../../config/config.inc.php";
require_once dirname(__FILE__) . "/config.php";

use Prestashow\PShowFbReviews\Model\FacebookEvent;
use Prestashow\PShowFbReviews\Model\FacebookEventFactory;
use Prestashow\PShowFbReviews\Entity\PShowFbReviewsEvent;

if (!PSHOW_FBREVIEWS_FB_API_CONFIGURED) {
    http_response_code(401);
    exit;
}

// authorize access
if (Tools::getValue('token') != PSHOW_FBREVIEWS_CRON_TOKEN) {
    http_response_code(403);
    exit;
}

$eventFactory = new FacebookEventFactory(
    PSHOW_FBREVIEWS_FB_PIXEL_ID,
    PSHOW_FBREVIEWS_FB_ACCESS_TOKEN
);

$q = "SELECT o.`id_order`, o.`reference`, o.`id_cart`, o.`total_paid`, c.`iso_code`, o.`id_customer`, o.`user_agent`, o.`user_ip`, o.`fbp`, o.`fbc` "
    . "FROM `" . _DB_PREFIX_ . "orders` o "
    . "LEFT JOIN `" . _DB_PREFIX_ . "currency` c ON (o.`id_currency` = c.`id_currency`) "
    . "WHERE o.`is_send_to_fb` != 1 AND o.`current_state` in (".Configuration::get('PSHOW_FBREVIEWS_FBPIXEL_ORDERS_STATUSES').")";
$orders = Db::getInstance()->executeS($q);
$orders_count_success = 0;
$orders_count_failed = 0;
if ($orders) {
    foreach ($orders as $order) {
        $params = array();
        $params['total_price'] = Db::getInstance()->getValue(
            'SELECT SUM(total_paid)
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `reference` = \'' . pSQL($order['reference']) . '\'
            AND `id_cart` = ' . (int) $order['id_cart']
        );
        $date_add = Db::getInstance()->getValue(
            'SELECT `date_add`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `reference` = \'' . pSQL($order['reference']) . '\'
            AND `id_cart` = ' . (int) $order['id_cart']
        );
        $params['currency'] = $order['iso_code'];
        $params['event_id'] = sha1('ORDER-' . $order['id_order']);
        
        $q = "SELECT pl.`id_product` AS id, pl.`name`, od.`product_quantity` as quantity "
            . "FROM `" . _DB_PREFIX_ . "order_detail` od "
            . "LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON "
            . " (od.`product_id` = pl.`id_product` AND "
            . "  pl.`id_lang` = " . (int)Context::getContext()->language->id . ") "
            . "WHERE `id_order` = " . (int)$order['id_order'];
        $params['products'] = Db::getInstance()->executeS($q);
        
        if (isset($order['fbp']) && $order['fbp']) {
            $_COOKIE['_fbp'] = $order['fbp'];
        }
        
        if (isset($order['fbc']) && $order['fbc']) {
            $_COOKIE['_fbc'] = $order['fbc'];
        }
        
        $event = $eventFactory->make();
        if (!$event) {
            http_response_code(400);
            exit;
        }
            
        $customer = new Customer((int)$order['id_customer']);
        $addressId = Address::getFirstCustomerAddressId($customer->id);
        $address = $addressId ? new Address($addressId) : null;
        if(Validate::isEmail($customer->email)) {
            try {
                $userParams = [
                    'email' => $customer->email,
                    'firstname' => mb_strtolower($customer->firstname),
                    'lastname' => mb_strtolower($customer->lastname),
                    'user_agent' => $order['user_agent'],
                    'user_ip' => $order['user_ip'],
                    'birthday' => $customer->birthday
                    ? date('Ymd', strtotime($customer->birthday))
                    : '',
                ];
                if (Validate::isLoadedObject($address)) {
                    $userParams['city'] = mb_strtolower($address->city);
                    $userParams['postcode'] = preg_replace(
                        '/[^a-z0-9]+/',
                        '',
                        mb_strtolower($address->postcode)
                        );
                    $userParams['country'] = mb_strtolower((new CountryCore($address->id_country))->iso_code);
                }
                $event->setUserPersonalData($userParams);
                $event->setEventId('5::' . $params['event_id']);
                $event->setEventTime(strtotime($date_add));
                $promise = $event->sendPurchase($params['currency'], round($params['total_price'], 2), $params['products']);
                $response = $promise->wait();
                
                if (strcasecmp($promise->getState(), "rejected")) {
                    if($response) {
                        $sql = "UPDATE `" . _DB_PREFIX_ . "orders` "
                            . "SET `is_send_to_fb` = 1 "
                                . "WHERE `id_order` = " . (int)$order['id_order'] . ";";
                                Db::getInstance()->query($sql);
                                echo $response->getBody();
                                $orders_count_success++;
                    }
                    else {
                        $orders_count_failed++;
                    }
                }
            } catch(Exception $e) {
                echo PHP_EOL.$e->getMessage();
                $orders_count_failed++;
            }
        }
    }
}
$event = false;
$events_count_success = 0;
$events_count_failed = 0;
if(Configuration::get('PSHOW_FBREVIEWS_FBPIXEL_SEND_BY_CRON')) {
    $q = "SELECT * FROM `" . _DB_PREFIX_ . "pshowfbreviews_event`;";
    $events = Db::getInstance()->executeS($q);
    foreach ($events as $item) {
        $eventObj = new PShowFbReviewsEvent($item['id_pshowfbreviews_event']);
        $data = json_decode($eventObj->event, true);
        
        
        if (isset($data['user_agent']) && $data['user_agent']) {
            $_SERVER['HTTP_USER_AGENT'] = $data['user_agent'];
        }
        
        if (isset($data['user_ip']) && $data['user_ip']) {
            $_SERVER['REMOTE_ADDR'] = $data['user_ip'];
        }
        
        if (isset($data['fbp']) && $data['fbp']) {
            $_COOKIE['_fbp'] = $data['fbp'];
        }
        
        if (isset($data['fbc']) && $data['fbc']) {
            $_COOKIE['_fbc'] = $data['fbc'];
        }
        
        if (isset($data['params'])) {
            $params = $data['params']['params'];
            $event = $eventFactory->make();
            if (!$event) {
                http_response_code(400);
                exit;
            }
            if (isset($params['event_id'])) {
                $event->setEventId($data['params']['event_type'] . '::' . $params['event_id']);
            }
            if (isset($params['event_time'])) {
                $event->setEventTime($params['event_time']);
            }
            
            $eventSourceUrl = trim((string)$data['event_source_url']);
            if (!$eventSourceUrl || filter_var($eventSourceUrl, FILTER_VALIDATE_URL) === false) {
                $eventSourceUrl = Context::getContext()->link->getPageLink('index');
            }
            $event->setEventSourceUrl($eventSourceUrl);
            
            if (($testEventCode = trim($data['test_event_code']))) {
                $event->setEventOption('test_event_code', $testEventCode);
            }
            if(isset($data['params']['user']))
                $event->setUserPersonalData((array)$data['params']['user'] ?? []);
            
            try {
                $promise = null;
                switch ($data['params']['event_type']) {
                    case FacebookEvent::EVENT_PAGE_VIEW:
                        if (isset($params['page_name']))
                            $promise = $event->sendPageView($params['page_name']);
                            break;
                    case FacebookEvent::EVENT_VIEW_CONTENT:
                        $promise = $event->sendViewContent(
                        $params['content_name'] ?? '',
                        $params['content_type'] ?? '',
                        $params['content_category'] ?? '',
                        $params['content_ids'] ?? [],
                        $params['contents'] ?? []
                        );
                        break;
                    case FacebookEvent::EVENT_ADD_TO_CART:
                        $promise = $event->sendAddToCart(
                        (int)$params['id_product'], $params['product_name'], $params['path_to_category'], $params['quantity'], $params['currency'], $params['value']
                        );
                        break;
                    case FacebookEvent::EVENT_SEARCH:
                        $promise = $event->sendSearch($params['search_string']);
                        break;
                    case FacebookEvent::EVENT_CONTACT:
                        $promise = $event->sendContact($params['email']);
                        break;
                    case FacebookEvent::EVENT_INITIATE_CHECKOUT:
                        $promise = $event->sendInitiateCheckout(
                        $params['currency'], $params['value'], $params['num_items'], $params['content_type'], $params['content_ids']
                        );
                        break;
                    case FacebookEvent::EVENT_LEAD:
                        $promise = $event->sendLead($params['first_name'], $params['email']);
                        break;
                    case FacebookEvent::EVENT_VIEW_CATEGORY:
                        $promise = $event->sendViewCategory($params['content_name'], $params['content_type'], $params['content_category'], $params['content_ids']);
                        break;
                    case FacebookEvent::EVENT_VIEW_CMS:
                        $promise = $event->sendViewCms($params['content_name'], $params['content_category']);
                        break;
                    default:
                        http_response_code(404);
                        exit;
                }
                
                if (!$promise) {
                    $eventObj->delete();
                    continue;
                }
                $response = $promise->wait();
                if($response) {
                    echo $response->getBody();
                    $events_count_success++;
                }
                else {
                    $events_count_failed++;
                }
            } catch (\Throwable $th) {
                $event->logger->error($th->getTraceAsString());
                $events_count_failed++;
            }
        }
        $eventObj->delete();
    }
}
Configuration::updateValue("PSHOWFBREVIEWS_CRON_INFO_DATE", date("Y-m-d H:i:s"));
Configuration::updateValue("PSHOWFBREVIEWS_CRON_INFO_ORDERS_SUCCESS", $orders_count_success);
Configuration::updateValue("PSHOWFBREVIEWS_CRON_INFO_ORDERS_FAILED", $orders_count_failed);
Configuration::updateValue("PSHOWFBREVIEWS_CRON_INFO_EVENTS_SUCCESS", $events_count_success);
Configuration::updateValue("PSHOWFBREVIEWS_CRON_INFO_EVENTS_FAILED", $events_count_failed);
// process response
http_response_code(200);