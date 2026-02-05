<?php
use Composer\CaBundle\CaBundle;

class JprestaUpgradeUtils
{
    /**
     * @param $message
     * @param int $severity 1 = info, 2 = warning, 3 = error, 4 = critical error
     * @param null $errorCode
     * @param null $objectType
     * @param null $objectId
     * @param bool $allowDuplicate
     * @param null $idEmployee
     */
    public static function addLog($message, $severity = 1, $errorCode = null, $objectType = null, $objectId = null, $allowDuplicate = false, $idEmployee = null)
    {
        if (class_exists('PrestaShopLogger')) {
            // Since PS 1.6.0.2
            PrestaShopLogger::addLog($message, $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
        }
        else {
            Logger::addLog($message, $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
        }
    }

    public static function getConfigurationAllShop($key, $default = false, $idLang = null) {
        if (Tools::version_compare(_PS_VERSION_,'1.7','<')) {
            if (Configuration::hasKey($key, $idLang, 0, 0)) {
                return Configuration::get($key, $idLang, 0, 0);
            }
            return $default;
        }
        return Configuration::get($key, $idLang, 0, 0, $default);
    }

    public static function saveConfigurationAllShop($key, $value) {
        // Make sure no value is store for a specific shop
        Configuration::deleteByName($key);
        // Then save the new value in the global context
        Configuration::updateValue($key, $value, false, 0, 0);
    }

    public static function isModuleInstalled($moduleName) {
        return Module::isInstalled($moduleName);
    }

    /**
     * jTraceEx() - provide a Java style exception trace
     * @param Throwable $e
     * @param array $seen array passed to recursive calls to accumulate trace lines already seen leave as NULL when
     *              calling this function
     * @return string One entry per trace line
     */
    public static function jTraceEx($e, $seen = null) {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) $seen = array();
        $trace  = $e->getTrace();
        $prev   = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(' ... %d more', count($trace)+1);
                break;
            }
            $result[] = sprintf(' at %s%s%s(%s%s%s)',
                count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line);
            if (is_array($seen))
                $seen[] = "$file:$line";
            if (!count($trace))
                break;
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev) {
            $result .= "\n" . self::jTraceEx($prev, $seen);
        }
        return $result;
    }

    /**
     *
     * @param $url string The URL to download
     * @param $outputFile string File path where to download
     * @return int Number of bytes written
     * @throws PrestaShopException
     */
    public static function downloadFile($url, $outputFile)
    {
        $fileContent = self::downloadContent($url);
        $writtenBytes = file_put_contents($outputFile, $fileContent);
        if ($writtenBytes === false) {
            throw new PrestaShopException('Cannot download file to ' . $outputFile);
        }
        return $writtenBytes;
    }

    /**
     * Delete a file
     *
     * @param $file string The file to delete
     * @return bool true if the file has been deleted
     */
    public static function deleteFile($file) {
        if (is_file($file) && @unlink($file) === false) {
            $error = error_get_last();
            if ($error && stripos($error['message'], 'No such file or directory') === false) {
                // Ignore error when the directory does not exist anymore
                self::addLog('Cannot delete file ' . $file . ' : ' . $error['message'], 3);
                return false;
            }
        }
        return true;
    }

    public static function startsWith($haystack, $needle)
    {
        $length = Tools::strlen($needle);
        return (Tools::substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = Tools::strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (Tools::substr($haystack, -$length) === $needle);
    }

    /**
     * @param string $url URL to fetch
     * @param string $jak Optionnal JPresta Account Key
     * @return string Content returned by the server
     * @throws PrestaShopException
     */
    public static function downloadContent($url, $jak = null)
    {
        if (is_file($url)) {
            return Tools::file_get_contents($url);
        }

        $timeout = 30;

        $headers = [];
        $headers[] = "Referer: jprestaupgrade";
        $headers[] = 'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'JPresta Upgrade');
        if ($jak) {
            $headers[] = "x-jpresta-account-key: $jak";
        }
        $stream_context = @stream_context_create(
            [
                'http' => [
                    'timeout' => $timeout,
                    'header' => implode("\r\n", $headers),
                ],
                /* Remove this check because it often fails :'(
                 * 'ssl' => [
                    'verify_peer' => true,
                    'cafile' => CaBundle::getBundledCaBundlePath(),
                ],*/
            ]
        );
        $fileContent = Tools::file_get_contents($url, false, $stream_context, $timeout, true);
        if (!$fileContent) {
            if (method_exists('Tools', 'refreshCACertFile')) {
                Tools::refreshCACertFile();
            }

            if (function_exists('curl_init')) {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
                if (defined('_PS_CACHE_CA_CERT_FILE_')) {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($curl, CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
                }
                else {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                }
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_MAXREDIRS, 2);
                curl_setopt($curl, CURLOPT_REFERER, 'jprestaupgrade');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                $fileContent = curl_exec($curl);

                if (!$fileContent) {
                    $errorCode = curl_errno($curl);
                    if ($errorCode == 60 && defined('_PS_CACHE_CA_CERT_FILE_')) {
                        $errorMessage = 'There is an SSL certificate issue, make sure the file ' . _PS_CACHE_CA_CERT_FILE_ . ' is up-to-date';
                    } elseif ($errorCode !== 0) {
                        $errorMessage = sprintf('Cannot download content from URL \'%s\' usign cURL : error #%d - %s',
                            $url,
                            $errorCode,
                            curl_error($curl)
                        );
                    } else {
                        $errorMessage = sprintf('Cannot download content from URL \'%s\' usign cURL',
                            $url
                        );
                    }
                    throw new PrestaShopException($errorMessage);
                }

                curl_close($curl);

            } elseif (in_array(ini_get('allow_url_fopen'), ['On', 'on', '1'])) {
                $headers = [];
                $headers[] = "Referer: jprestaupgrade";
                if ($jak) {
                    $headers[] = "x-jpresta-account-key: $jak";
                }
                $stream_context = @stream_context_create(
                    [
                        'http' => [
                            'timeout' => $timeout,
                            'header' => implode("\r\n", $headers),
                        ],
                        'ssl' => [
                            'verify_peer' => true,
                            'cafile' => CaBundle::getBundledCaBundlePath(),
                        ],
                    ]
                );
                $fileContent = file_get_contents($url, false, $stream_context);
                if (!$fileContent) {
                    throw new PrestaShopException("Cannot download content from URL '$url' usign fopen");
                }
            } else {
                throw new PrestaShopException("You must enable cURL or allow_url_fopen to be able to download URLs");
            }
        }
        return $fileContent;
    }

    public static function extractZipToDir($fileName, $toDir) {
        $zip = new ZipArchive();
        $res = $zip->open($fileName);
        if ($res !== true) {
            throw new PrestaShopException("Cannot open zip file $fileName: " . $res);
        }
        try {
            if (!$zip->extractTo($toDir)) {
                throw new PrestaShopException("Cannot extract zip file $fileName to $toDir");
            }
        } finally {
            $zip->close();
        }
    }

    public static function extractModuleNeededVersion($tempFile)
    {
        $suffix = '-PS1.7';
        $suffix2 = '-PS1.7-PS8';
        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $suffix = $suffix2 = '-PS1.5-PS1.6';
        }
        $tempDir = _PS_CACHE_DIR_ . 'jprestaupgrade-' . time() . $suffix2;
        $zip = new ZipArchive();
        $res = $zip->open($tempFile);
        if ($res !== true) {
            throw new PrestaShopException('Cannot open zip file: ' . $res);
        }
        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (JprestaUpgradeUtils::endsWith($filename, $suffix . '.zip') || JprestaUpgradeUtils::endsWith($filename, $suffix2 . '.zip')) {
                    if (!$zip->extractTo($tempDir, $filename)) {
                        throw new PrestaShopException('Cannot extract zip file: ' . $tempFile . ' / ' . $filename);
                    }
                    return $tempDir . '/' . $filename;
                }
            }
        } finally {
            $zip->close();
        }
        throw new PrestaShopException('Cannot find appropriate zip file for this Prestashop');
    }
}
