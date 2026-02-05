<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Retro extends CustomBanners
{
    public $x = []; // quick cache

    public function prepareDataForImport($data, $exctracted_contents_dir)
    {
        if (!isset($data['version'])) {
            $data['version'] = 0;
        }
        if ($data['version'] < 290 && !empty($data['cb_hook_settings'])) {
            foreach ($data['cb_hook_settings'] as $id_shop => $rows) {
                if (isset($rows['hook_name'])) { // fix incorrectly exported exceptions data
                    $data['cb_hook_settings'][$id_shop] = [$rows['hook_name'] => $rows];
                }
            }
        }
        if ($data['version'] < 295) {
            foreach ($this->customCode('getTypes') as $type) {
                $possible_retro_path = $exctracted_contents_dir . $type . '/shopid_shop_default.' . $type;
                if (is_file($possible_retro_path)) {
                    $base_name = basename($this->customCode('getFilePath', ['type' => $type]));
                    rename($possible_retro_path, $exctracted_contents_dir . $base_name);
                }
            }
        }
        if ($data['version'] < 298 && !empty($data['custombanners'])) {
            foreach ($data['custombanners'] as $id_shop => $banners) {
                foreach ($banners as $id_banner => $multilang_data) {
                    foreach ($multilang_data as $lang_iso => $banner_data) {
                        $this->fillRequiredFields($banner_data);
                        if ($data['version'] < 297) {
                            $this->updateImgDataIfRequired(
                                $banner_data['content'],
                                $exctracted_contents_dir . 'img/',
                                $data['version']
                            );
                        }
                        $data['custombanners'][$id_shop][$id_banner][$lang_iso] = $banner_data;
                    }
                }
            }
        }
        if ($data['version'] < 299) {
            $modern_rows = ['main' => [], 'lang' => []];
            $lang_iso_id = array_column(Language::getLanguages(false), 'id_lang', 'iso_code');
            $lang_id_iso = array_flip($lang_iso_id);
            $all_shop_ids = Shop::getShops(false, null, true);
            foreach ($all_shop_ids as $id_shop) {
                $shop_banners = isset($data['custombanners'][$id_shop]) ?
                $data['custombanners'][$id_shop] : $data['custombanners']['ID_SHOP_DEFAULT'];
                foreach ($shop_banners as $id_banner => $multilang_data) {
                    foreach ($lang_iso_id as $iso_code => $id_lang) {
                        $retro_row = isset($multilang_data[$iso_code]) ?
                        $multilang_data[$iso_code] : $multilang_data['LANG_ISO_DEFAULT'];
                        $retro_row['id_lang'] = $id_lang;
                        $retro_row['id_shop'] = $id_shop;
                        $this->toModernRows($retro_row, $modern_rows);
                    }
                }
            }
            $data['cb'] = $modern_rows['main'];
            foreach ($modern_rows['lang'] as $row) {
                $iso = $lang_id_iso[$row['id_lang']];
                $data['cb_lang'][$row['id_shop']][$row['id_banner']][$iso] = $row;
            }
            unset($data['custombanners']);
            if (!empty($data['hook_module'])) {
                foreach (array_keys($data['hook_module']) as $id_shop) {
                    unset($data['hook_module'][$id_shop]['displayBackOfficeHeader']);
                }
            }
        }

        return $data;
    }

    public function fillRequiredFields(&$banner_data)
    {
        // column positions will be adjusted in BannersData.php -> $verified_row
        foreach (['publish_from', 'publish_to'] as $key) {
            if (!isset($banner_data[$key])) {
                $banner_data[$key] = '';
            }
        }
        foreach (['active_tablet', 'active_mobile'] as $key) {
            if (!isset($banner_data[$key])) {
                $banner_data[$key] = $banner_data['active'];
            }
        }
    }

    public function updateImgDataIfRequired(&$encoded_content, $custom_img_dir = false, $version = 0)
    {
        $updated = false;
        $content = json_decode($encoded_content, true);
        $img_dir = $custom_img_dir ?: $this->img_dir_local;
        foreach ($this->img_fields as $img_field) {
            if ($version < 297 && !empty($content[$img_field])) {
                if (is_array($content[$img_field])) {
                    unset($content[$img_field]['b']);
                    $updated = true;
                } elseif (is_string($content[$img_field])) {
                    $content[$img_field] = ['name' => $content[$img_field]] +
                    $this->img()->getDimensions($img_dir . $content[$img_field]);
                    $updated = true;
                }
            }
        }
        $encoded_content = json_encode($content);

        return $updated;
    }

    public function prepareOriginalImageFiles()
    {
        foreach (glob($this->img()->getPath('*')) as $img_path) {
            Tools::copy($img_path, $this->img()->getPath(basename($img_path), true));
        }
    }

    public function checkMagicQuotes()
    {
        if (_PS_MAGIC_QUOTES_GPC_) {
            parent::$errors[] = 'Please turn OFF "magic quotes" in your server configuration. ' .
            'This directive is deprecated since 2009 (when PHP 5.3.0 was released)';
        }
    }

    public function toModernRows($retro_row, &$modern_rows)
    {
        $id_banner = $retro_row['id_banner'];
        $content = json_decode($retro_row['content'], true);
        if (!isset($modern_rows['main'][$id_banner])) {
            $main_row = $this->fillData('main_row', $retro_row);
            if (!empty($content['exceptions'])) {
                $main_row['exceptions'] = json_encode($content['exceptions']);
            }
            if (!empty($content['class'])) {
                $main_row['css_class'] = $content['class'];
            }
            $main_row['label'] = 'Banner ' . $retro_row['id_banner'];
            $modern_rows['main'][$id_banner] = $main_row;
        }
        if (isset($content['title'])) {
            if (isset($content['img'])) {
                $content['img']['title'] = $content['title'];
            }
            $content['ac_title'] = $content['title'];
            if ($this->isDefaultLang($retro_row['id_lang'], $retro_row['id_shop'])) {
                $modern_rows['main'][$id_banner]['label'] = $content['title'];
            }
        }
        $lang_row = $this->fillData('lang_row', $retro_row);
        $lang_row['content'] = json_encode(array_filter($this->fillData('content', $content)));
        $modern_rows['lang'][] = $lang_row;
    }

    public function fillData($type, $data)
    {
        if (!isset($this->x['main_arrays'])) {
            $this->x['main_arrays'] = [
                'main_row' => array_fill_keys($this->getColumns('main'), ''),
                'lang_row' => array_fill_keys($this->getColumns('lang'), ''),
                'content' => [],
            ];
            foreach ($this->getBannerFields() as $key => $field) {
                if (!empty($field['multilang'])) {
                    $this->x['main_arrays']['content'][$key] = '';
                }
            }
        }
        $main_array = isset($this->x['main_arrays'][$type]) ? $this->x['main_arrays'][$type] : [];

        return $this->fillArray($main_array, $data);
    }

    public function isDefaultLang($id_lang, $id_shop)
    {
        if (!isset($this->x['l_default'][$id_shop])) {
            $this->x['l_default'][$id_shop] = Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop);
        }

        return $id_lang == $this->x['l_default'][$id_shop];
    }
}
