<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TinyPNGOptimizer extends OptimizerModel
{
    public function __construct($saved_data = [])
    {
        $this->id = 2;
        $this->label = 'TinyPNG';
        $this->fields = [
            'api_key' => [
                'type' => 'text',
                'value' => '',
                'validate' => 'isControllerName', // alphanumeric
                'required' => 1,
            ],
        ];
        $this->supported_formats = ['jpeg', 'png', 'webp'];
        $this->applySavedData($saved_data);
    }

    public function processSupportedFormat($src_path, $dst_path)
    {
        if ($compressed_file = $this->getCompressedFile($src_path)) {
            return (bool) Tools::copy($compressed_file, $dst_path);
        }

        return false;
    }

    public function getCompressedFile($src_path)
    {
        $data = [
            'url' => 'https://api.tinify.com/shrink',
            'login:password' => 'api:' . $this->fields['api_key']['value'],
            'post_fields' => Tools::file_get_contents($src_path),
        ];
        $response = $this->curlRequest($data);
        if (!$this->errors) {
            if (isset($response['error'])) {
                $this->errors[] = isset($response['message']) ? $response['message'] : $response['error'];
            } elseif (!isset($response['output']['url'])) {
                $this->errors[] = 'no output';
            }
        }

        return !$this->errors ? $response['output']['url'] : false;
    }
}
