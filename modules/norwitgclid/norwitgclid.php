<?php
/**
 * NorwitGCLID - Server-side Conversion Tracking dla Google Ads
 *
 * Obsługuje:
 * - gclid (standard Google Ads Click ID)
 * - wbraid (iOS Web-to-App, po iOS 14.5)
 * - gbraid (iOS App-to-Web)
 * - ga_client_id (Google Analytics Client ID dla SEO)
 *
 * @author Norwit.pl
 * @version 1.1.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class NorwitGclid extends Module
{
    public function __construct()
    {
        $this->name = 'norwitgclid';
        $this->tab = 'analytics_stats';
        $this->version = '1.1.0';
        $this->author = 'Norwit.pl';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Norwit Conversion Tracker');
        $this->description = $this->l('Server-side tracking for Google Ads offline conversions (gclid/wbraid/gbraid)');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->createDatabaseTables();
    }

    public function uninstall()
    {
        // Uwaga: Tabele NIE są usuwane przy odinstalowaniu, żeby zachować dane
        // Jeśli chcesz usunąć tabele, odkomentuj poniższy kod:
        // $this->dropDatabaseTables();

        return parent::uninstall();
    }

    /**
     * Usuwa tabele bazy danych (opcjonalne)
     */
    private function dropDatabaseTables()
    {
        Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "norwit_gclid`");
        Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "norwit_conversion_queue`");
    }

    private function createDatabaseTables()
    {
        $result = true;

        // Tabela główna - tracking GCLID/wbraid/gbraid
        $sql1 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "norwit_gclid` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `gclid` VARCHAR(255) DEFAULT NULL,
            `wbraid` VARCHAR(255) DEFAULT NULL,
            `gbraid` VARCHAR(255) DEFAULT NULL,
            `ga_client_id` VARCHAR(255) DEFAULT NULL,
            `session_id` VARCHAR(255) DEFAULT NULL,
            `zadarma_number` VARCHAR(50) DEFAULT NULL,
            `phone_displayed` VARCHAR(50) DEFAULT NULL,
            `ip_address` VARCHAR(45) DEFAULT NULL,
            `user_agent` TEXT DEFAULT NULL,
            `landing_page` TEXT DEFAULT NULL,
            `referrer` TEXT DEFAULT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME DEFAULT NULL,
            `conversion_sent` TINYINT(1) DEFAULT 0,
            `conversion_value` DECIMAL(10,2) DEFAULT NULL,
            `conversion_type` VARCHAR(50) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_gclid` (`gclid`),
            INDEX `idx_wbraid` (`wbraid`),
            INDEX `idx_gbraid` (`gbraid`),
            INDEX `idx_ga_client_id` (`ga_client_id`),
            INDEX `idx_zadarma_number` (`zadarma_number`),
            INDEX `idx_session_id` (`session_id`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4;";

        $result = $result && Db::getInstance()->execute($sql1);

        // Tabela kolejki konwersji do wysłania do Google Ads
        $sql2 = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "norwit_conversion_queue` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `gclid_record_id` INT(11) NOT NULL,
            `gclid` VARCHAR(255) DEFAULT NULL,
            `wbraid` VARCHAR(255) DEFAULT NULL,
            `gbraid` VARCHAR(255) DEFAULT NULL,
            `conversion_action` VARCHAR(50) NOT NULL,
            `conversion_value` DECIMAL(10,2) NOT NULL,
            `call_datetime` DATETIME NOT NULL,
            `call_duration` INT(11) DEFAULT 0,
            `status` ENUM('pending', 'sent', 'error') DEFAULT 'pending',
            `error_message` TEXT DEFAULT NULL,
            `retry_count` INT(11) DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `sent_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_status` (`status`),
            INDEX `idx_gclid_record_id` (`gclid_record_id`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4;";

        $result = $result && Db::getInstance()->execute($sql2);

        return $result;
    }

    /**
     * Hook: displayHeader - zapisuje identyfikatory z URL do sesji i bazy
     */
    public function hookDisplayHeader($params)
    {
        // Pobierz identyfikatory z URL
        $gclid = Tools::getValue('gclid');
        $wbraid = Tools::getValue('wbraid');
        $gbraid = Tools::getValue('gbraid');

        // Pobierz GA Client ID z cookie _ga
        $gaClientId = $this->getGaClientId();

        $hasAnyId = $gclid || $wbraid || $gbraid || $gaClientId;

        if ($hasAnyId) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Zapisz w sesji (priorytet: gclid > wbraid > gbraid)
            if ($gclid) {
                $_SESSION['norwit_gclid'] = $gclid;
                $_SESSION['norwit_id_type'] = 'gclid';
            } elseif ($wbraid) {
                $_SESSION['norwit_gclid'] = $wbraid;
                $_SESSION['norwit_id_type'] = 'wbraid';
            } elseif ($gbraid) {
                $_SESSION['norwit_gclid'] = $gbraid;
                $_SESSION['norwit_id_type'] = 'gbraid';
            }

            if ($gaClientId) {
                $_SESSION['norwit_ga_client_id'] = $gaClientId;
            }

            $_SESSION['norwit_gclid_time'] = time();

            // Zapisz w bazie
            $this->saveToDatabase($gclid, $wbraid, $gbraid, $gaClientId);
        }

        // Dodaj dane do window object (dla Zadarma tracking)
        return $this->getJsOutput();
    }

    /**
     * Pobierz GA Client ID z cookie _ga
     */
    private function getGaClientId(): ?string
    {
        if (!isset($_COOKIE['_ga'])) {
            return null;
        }

        // Format: GA1.2.XXXXXXXXXX.XXXXXXXXXX
        $parts = explode('.', $_COOKIE['_ga']);
        if (count($parts) >= 4) {
            return $parts[2] . '.' . $parts[3];
        }

        return null;
    }

    /**
     * Zapisuje identyfikatory do bazy danych
     */
    private function saveToDatabase(?string $gclid, ?string $wbraid, ?string $gbraid, ?string $gaClientId)
    {
        $sessionId = session_id();

        // Sprawdź czy już istnieje dla tej sesji
        $existingId = Db::getInstance()->getValue(
            "SELECT id FROM `" . _DB_PREFIX_ . "norwit_gclid`
             WHERE session_id = '" . pSQL($sessionId) . "'
             AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );

        $data = [
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($gclid) {
            $data['gclid'] = pSQL($gclid);
        }
        if ($wbraid) {
            $data['wbraid'] = pSQL($wbraid);
        }
        if ($gbraid) {
            $data['gbraid'] = pSQL($gbraid);
        }
        if ($gaClientId) {
            $data['ga_client_id'] = pSQL($gaClientId);
        }

        if ($existingId) {
            Db::getInstance()->update('norwit_gclid', $data, "id = " . (int)$existingId);
        } else {
            $data['session_id'] = pSQL($sessionId);
            $data['ip_address'] = pSQL(Tools::getRemoteAddr());
            $data['user_agent'] = pSQL($_SERVER['HTTP_USER_AGENT'] ?? '');
            $data['landing_page'] = pSQL($_SERVER['REQUEST_URI'] ?? '');
            $data['referrer'] = pSQL($_SERVER['HTTP_REFERER'] ?? '');
            $data['created_at'] = date('Y-m-d H:i:s');

            Db::getInstance()->insert('norwit_gclid', $data);
        }
    }

    /**
     * Generuje output JS
     */
    private function getJsOutput(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [];

        if (!empty($_SESSION['norwit_gclid'])) {
            $data['id'] = $_SESSION['norwit_gclid'];
            $data['type'] = $_SESSION['norwit_id_type'] ?? 'gclid';
        }
        if (!empty($_SESSION['norwit_ga_client_id'])) {
            $data['gaClientId'] = $_SESSION['norwit_ga_client_id'];
        }

        if (empty($data)) {
            return '';
        }

        $data['sessionId'] = session_id();

        return '<script>window.NorwitTracking = ' . json_encode($data) . ';</script>';
    }

    /**
     * Pobiera aktualny identyfikator z sesji
     */
    public function getCurrentId(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['norwit_gclid']) && isset($_SESSION['norwit_gclid_time'])) {
            $age = time() - $_SESSION['norwit_gclid_time'];
            if ($age < (90 * 24 * 60 * 60)) {
                return [
                    'id' => $_SESSION['norwit_gclid'],
                    'type' => $_SESSION['norwit_id_type'] ?? 'gclid',
                ];
            }
        }

        return null;
    }

    /**
     * API: Zapisz numer Zadarma dla sesji
     */
    public static function saveZadarmaNumber($sessionId, $zadarmaNumber, $phoneDisplayed)
    {
        return Db::getInstance()->update('norwit_gclid', [
            'zadarma_number' => pSQL($zadarmaNumber),
            'phone_displayed' => pSQL($phoneDisplayed),
            'updated_at' => date('Y-m-d H:i:s'),
        ], "session_id = '" . pSQL($sessionId) . "'");
    }

    /**
     * API: Pobierz rekord dla numeru Zadarma
     */
    public static function getGclidByZadarmaNumber($zadarmaNumber)
    {
        return Db::getInstance()->getRow(
            "SELECT * FROM `" . _DB_PREFIX_ . "norwit_gclid`
             WHERE zadarma_number = '" . pSQL($zadarmaNumber) . "'
             ORDER BY created_at DESC"
        );
    }

    /**
     * API: Pobierz rekord dla numeru telefonu
     */
    public static function getGclidByPhoneDisplayed($phoneDisplayed)
    {
        return Db::getInstance()->getRow(
            "SELECT * FROM `" . _DB_PREFIX_ . "norwit_gclid`
             WHERE phone_displayed = '" . pSQL($phoneDisplayed) . "'
             ORDER BY created_at DESC"
        );
    }

    /**
     * API: Oznacz konwersję jako wysłaną
     */
    public static function markConversionSent($id, $conversionType, $conversionValue)
    {
        return Db::getInstance()->update('norwit_gclid', [
            'conversion_sent' => 1,
            'conversion_type' => pSQL($conversionType),
            'conversion_value' => (float)$conversionValue,
            'updated_at' => date('Y-m-d H:i:s'),
        ], "id = " . (int)$id);
    }

    /**
     * Hook: actionFrontControllerSetMedia
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['norwit_gclid']) || !empty($_SESSION['norwit_ga_client_id'])) {
            $this->context->controller->registerJavascript(
                'module-norwitgclid-bridge',
                'modules/' . $this->name . '/views/js/zadarma_bridge.js',
                ['position' => 'bottom', 'priority' => 999]
            );
        }
    }
}
