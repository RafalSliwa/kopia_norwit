<?php

class ZadarmaCallbackCallbackModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        header('Content-Type: application/json');

        // ✅ Sprawdzenie tokena CSRF
        $token = Tools::getValue('token');
        if ($token !== Tools::getToken(false)) {
            die(json_encode(['error' => 'Nieprawidłowy token zabezpieczający.']));
        }

        // 📞 Pobranie numerów
        $phone = trim(Tools::getValue('phone'));
        $from = trim(Tools::getValue('from'));

        // 🧹 Usuwamy +
        $phone = ltrim($phone, '+');
        $from = ltrim($from, '+');

        // ✅ Walidacja numerów
        if (!preg_match('/^[0-9]{6,15}$/', $phone)) {
            die(json_encode(['error' => 'Nieprawidłowy numer telefonu (klienta).']));
        }

        if (!preg_match('/^[0-9]{6,15}$/', $from)) {
            $from = ltrim(Configuration::get('ZADARMA_FROM_NUMBER'), '+');
            if (!preg_match('/^[0-9]{6,15}$/', $from)) {
                die(json_encode(['error' => 'Brak poprawnego numeru FROM.']));
            }
        }

        // 🔐 Dane API
        $apiKey = Configuration::get('ZADARMA_API_KEY');
        $apiSecret = Configuration::get('ZADARMA_API_SECRET');

        if (!$apiKey || !$apiSecret) {
            die(json_encode(['error' => 'Brakuje danych API w konfiguracji modułu.']));
        }

        // 📦 Autoload biblioteki Zadarma
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        try {
            $api = new \Zadarma_API\Api($apiKey, $apiSecret);
            $response = $api->requestCallback($from, $phone);

            if (!empty($response->from) && !empty($response->to)) {
                die(json_encode(['success' => true]));
            } else {
                die(json_encode(['error' => 'Nie udało się zainicjować callback.']));
            }

        } catch (Exception $e) {
            die(json_encode(['error' => 'Błąd API: ' . $e->getMessage()]));
        }
    }
}
