<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WebP
{
    public function addFallback($webp_file_path)
    {
        $image_resource = imagecreatefromwebp($webp_file_path);
        imagejpeg($image_resource, $webp_file_path . '.jpg', 80);
        imagedestroy($image_resource);

        return 'jpg';
    }

    public function fallbackRequired($img_name)
    {
        return Tools::substr($img_name, -5) === '.webp' ? 'jpg' : '';
    }

    public function isFallback($img_name)
    {
        return Tools::substr($img_name, -9) === '.webp.jpg';
    }
}
