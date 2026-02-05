<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class OptimizerModel
{
    public $errors = [];

    public function __construct($saved_data = [])
    {
        $this->id = 0;
        $this->label = 'None';
        $this->fields = [];
        $this->supported_formats = [];
        $this->applySavedData($saved_data);
    }

    public function applySavedData($saved_data)
    {
        if (isset($saved_data['settings'])) {
            $saved_data['settings'] = json_decode($saved_data['settings'], true);
            foreach (array_keys($this->fields) as $k) {
                if (isset($saved_data['settings'][$k])) {
                    $this->fields[$k]['value'] = $saved_data['settings'][$k];
                }
            }
        }
        $this->active = !empty($saved_data['active']);
    }

    public function process($src_path, $dst_path)
    {
        if (in_array($this->getImageFormat($src_path), $this->supported_formats)
            && method_exists($this, 'processSupportedFormat')) {
            $ret = $this->processSupportedFormat($src_path, $dst_path);
        } else {
            $ret = (bool) Tools::copy($src_path, $dst_path);
        }

        return $ret ?: $this->label . ': ' . (!empty($this->errors) ? implode('<br>', $this->errors) : 'error');
    }

    public function getImageFormat($file_path)
    {
        return str_replace('image/', '', $this->getMimeType($file_path));
    }

    /*
    * based on ImageManager::getMimeType (since PS 1.7.8)
    */
    public static function getMimeType($file_path)
    {
        $mime_type = '';
        if (is_file($file_path)) {
            if (function_exists('getimagesize')) {
                if ($image_info = getimagesize($file_path)) {
                    $mime_type = $image_info['mime'];
                }
            }
            if (!$mime_type && function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file_path);
                finfo_close($finfo);
            }
            if (!$mime_type && function_exists('mime_content_type')) {
                $mime_type = mime_content_type($file_path);
            }
        }

        return $mime_type;
    }

    public function curlRequest($data, $decode_response = true)
    {
        $response = $decode_response ? [] : '';
        if (function_exists('curl_init')) {
            if (!empty($data['get_fields'])) {
                if (is_array($data['get_fields'])) {
                    $data['get_fields'] = http_build_query($data['get_fields']);
                }
                $data['url'] .= '?' . $data['get_fields'];
            }
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $data['url']);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0); // debug
            // curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0); // debug
            if (!empty($data['headers'])) {
                curl_setopt($session, CURLOPT_HTTPHEADER, $data['headers']);
            }
            if (!empty($data['post_fields'])) {
                curl_setopt($session, CURLOPT_POSTFIELDS, $data['post_fields']);
            }
            if (!empty($data['login:password'])) {
                curl_setopt($session, CURLOPT_USERPWD, $data['login:password']);
            }
            if (!empty($data['timeout'])) {
                curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $data['timeout']);
            }
            $response = curl_exec($session);
            $possible_error = curl_error($session);
            curl_close($session);
            if ($possible_error) {
                $this->errors[] = $possible_error;
            } else {
                $response = $decode_response ? json_decode($response, true) : $response;
            }
        } else {
            $this->errors[] = 'no_curl';
        }

        return $response;
    }
}
