<?php

require_once dirname(__FILE__) . "/../../config/config.inc.php";
require_once dirname(__FILE__) . "/config.php";

use FacebookAds\Object\ServerSide\Gender;
use Prestashow\PShowFbReviews\Model\FacebookEvent;
use Prestashow\PShowFbReviews\Model\FacebookEventFactory;

//header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);

if (!PSHOW_FBREVIEWS_FB_API_CONFIGURED) {
    http_response_code(401);
    exit;
}

$requestEvent = Tools::getValue('params', ['event_type' => '']);

// authorize access
if (Tools::getValue('token') != sha1(_COOKIE_KEY_ . $requestEvent['event_type'])) {
    http_response_code(403);
    exit;
}

$userAgent = trim((string)Tools::getValue('user_agent', ''));
if ($userAgent) {
    $_SERVER['HTTP_USER_AGENT'] = $userAgent;
}

$userIp = trim((string)Tools::getValue('user_ip', ''));
if ($userIp) {
    $_SERVER['REMOTE_ADDR'] = $userIp;
}

$fbp = trim((string)Tools::getValue('fbp', ''));
if ($fbp) {
    $_COOKIE['_fbp'] = $fbp;
}

$fbc = trim((string)Tools::getValue('fbc', ''));
if ($fbc) {
    $_COOKIE['_fbc'] = $fbc;
}

$eventFactory = new FacebookEventFactory(
    PSHOW_FBREVIEWS_FB_PIXEL_ID,
    PSHOW_FBREVIEWS_FB_ACCESS_TOKEN
);

// test api calls
if (Tools::getIsset('test_api')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $event = $eventFactory->make();
    $response = $event->sendPurchase('usd', 1.23)->wait();
    echo $response->getBody();

    echo '<hr>';

    $event = $eventFactory->make();
    $promise = $event->sendPurchase('usd', 1.23, [
        [
            'id' => 1,
            'name' => 'product 1',
            'quantity' => 2,
        ],
    ]);
    $response = $promise->wait();
    echo $response->getBody();

    echo '<hr>';

    $event = $eventFactory->make();
    $promise = $event->sendPageView('testing');
    $response = $promise->wait();
    echo $response->getBody();

    echo '<hr>';

    $event = $eventFactory->make();
    $promise = $event->sendAddToCart(
        1, 'testowy', 'sklep/kat1/kat2'
    );
    $response = $promise->wait();
    echo $response->getBody();

    echo '<hr>';

    $event = $eventFactory->make();
    $promise = $event->sendSearch('search something');
    $response = $promise->wait();
    echo $response->getBody();

    echo '<hr>';

    $event = $eventFactory->make();
    $promise = $event->sendLead(
        'John',
        'New York',
        'PL',
        Gender::MALE
    );
    $response = $promise->wait();
    echo $response->getBody();

    exit;
}

// process event data
if (!is_array($requestEvent)) {
    http_response_code(400);
    exit;
}

// prepare and send event
if (isset($requestEvent['params'])) {
    $params = $requestEvent['params'];
    $event = $eventFactory->make();
    if (!$event) {
        http_response_code(400);
        exit;
    }
    if (isset($params['event_id'])) {
        $event->setEventId($requestEvent['event_type'] . '::' . $params['event_id']);
    }

    $eventSourceUrl = trim((string)Tools::getValue('event_source_url', ''));
    if (!$eventSourceUrl || filter_var($eventSourceUrl, FILTER_VALIDATE_URL) === false) {
        $eventSourceUrl = Context::getContext()->link->getPageLink('index');
    }
    $event->setEventSourceUrl($eventSourceUrl);

    if (($testEventCode = trim(Tools::getValue('test_event_code', '')))) {
        $event->setEventOption('test_event_code', $testEventCode);
    }

    $event->setUserPersonalData((array)$requestEvent['user'] ?? []);
    
    try {
        $promise = null;
        switch ($requestEvent['event_type']) {
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
            case FacebookEvent::EVENT_PURCHASE:
                $promise = $event->sendPurchase($params['currency'], $params['total_price'], $params['products']);
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
            http_response_code(400);
            exit;
        }

        // process response
        http_response_code(200);
        $promise->wait();
    } catch (\Throwable $th) {
        $event->logger->error($th->getTraceAsString());
    }
}
