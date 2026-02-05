<?php
/**
 * NOTICE OF LICENSE
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2023 Ceneo
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Ceneo_XmlCheckModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        try {
            if ($this->isOwner()) {
                $this->checkFile();
            }
            exit('Access denied');
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function isOwner(): bool
    {
        $allowedIPs = [
            '89.64.25.118',
            '178.21.154.6',
            '178.21.156.11',
            '178.21.156.14',
            '178.21.153.124',
            '178.21.153.125',
            '91.194.188.0/23',
            '178.21.152.0/21',
            '193.23.48.0/24',
            '194.0.251.0/24',
            '5.134.208.0/21',
            '91.207.14.0/23',
            '193.203.222.0/23',
            '159.253.247.1',
            '159.253.247.2',
            '159.253.247.3',
            '159.253.247.4',
            '159.253.247.5',
            '159.253.247.6',
            '78.8.255.193',
            '78.8.255.194',
            '78.8.255.195',
            '78.8.255.196',
            '78.8.255.197',
            '78.8.255.198',
            '91.194.188.180',
            '5.134.208.158',
            '178.21.159.240/28',
            '213.241.24.144/29',
            '91.217.19.216/29',
            '91.217.19.218',
            '194.0.251.69',
        ];

        $clientIp = $this->getClientIp();

        if ($this->isIPAllowed($clientIp, $allowedIPs)) {
            return true;
        }

        return false;
    }

    public function isIPAllowed($clientIP, $allowedIPs): bool
    {
        $allowedIPRanges = [];
        foreach ($allowedIPs as $allowedIP) {
            if (strpos($allowedIP, '/') !== false) {
                [$subnet, $mask] = explode('/', $allowedIP);
                $allowedIPRanges[] = ip2long($subnet) & ~((1 << (32 - $mask)) - 1);
            } else {
                $allowedIPRanges[] = ip2long($allowedIP);
            }
        }

        $clientIP = ip2long($clientIP);
        foreach ($allowedIPRanges as $allowedRange) {
            if (($clientIP & $allowedRange) == $allowedRange) {
                return true;
            }
        }

        return false;
    }

    public function checkFile()
    {
        $directory_path = _PS_MODULE_DIR_ . 'ceneo_xml/export/';
        $xml_files = glob($directory_path . '*.xml');
        $secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));

        if (count($xml_files) !== 0) {
            foreach ($xml_files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'xml') {
                    $file = preg_replace('/[^0-9]/', '', $file);

                    echo Context::getContext()->link->getModuleLink(
                        'ceneo_xml',
                        'generate',
                        [
                            'secure_key' => $secure_key,
                            'id_shop' => $file,
                            'show_output' => 1,
                        ]
                    );
                    echo '<br />';
                }
            }
        } else {
            exit('No generated files');
        }
        exit;
    }

    public function getClientIp()
    {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ipHeaders as $header) {
            if (($ip = getenv($header)) !== false) {
                return $ip;
            }
        }

        return $this->getClientIpServer();
    }

    public function getClientIpServer()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }

        return $ip;
    }
}
