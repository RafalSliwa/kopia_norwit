<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ReSmushitOptimizer extends OptimizerModel
{
    public function __construct($saved_data = [])
    {
        $this->id = 3;
        $this->label = 'ReSmushit';
        $this->fields = [
            'quality_jpg' => [
                'type' => 'text',
                'value' => '90',
                'validate' => 'isInt',
                'min' => 0,
                'max' => 100,
            ],
        ];
        $this->supported_formats = ['jpeg', 'png', 'gif', 'tiff', 'bmp'];
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
            'url' => 'http://api.resmush.it/?qlty=' . $this->fields['quality_jpg']['value'],
            'post_fields' => [
                'files' => new CURLFile($src_path, mime_content_type($src_path), basename($src_path)),
            ],
            'timeout' => 10,
        ];
        $response = $this->curlRequest($data);
        if (!$this->errors) {
            if (isset($response['error'])) {
                $this->errors[] = 'error ' . $response['error'] .
                (isset($response['error_long']) ? ' - ' . $response['error_long'] : '');
            } elseif (!isset($response['dest'])) {
                $this->errors[] = 'no output';
            }
        }

        return !$this->errors ? $response['dest'] : false;
    }
}
