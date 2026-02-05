<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class BasicOptimizer extends OptimizerModel
{
    public function __construct($saved_data = [])
    {
        $this->id = 1;
        $this->label = 'Basic';
        $this->fields = [
            'quality_jpg' => [
                'type' => 'text',
                'value' => '80',
                'validate' => 'isInt',
                'min' => 0,
                'max' => 100,
            ],
        ];
        $this->supported_formats = ['jpeg', 'png'];
        $this->applySavedData($saved_data);
    }

    public function processSupportedFormat($src_path, $dst_path)
    {
        $img = imagecreatefromstring(Tools::file_get_contents($src_path));
        switch ($this->getImageFormat($src_path)) {
            case 'png':
                imagealphablending($img, false);
                imagesavealpha($img, true);
                $success = imagepng($img, $dst_path, 9); // smallest filesize, same quality, longer processing
                break;
            default: // jpg, jpeg
                imageinterlace($img, 1); // / make it PROGRESSIVE
                $success = imagejpeg($img, $dst_path, (int) $this->fields['quality_jpg']['value']);
                break;
        }
        imagedestroy($img);
        chmod($dst_path, 0664);

        return $success;
    }
}
