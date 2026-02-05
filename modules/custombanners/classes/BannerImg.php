<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'OptimizerModel.php';

class BannerImg extends CustomBanners
{
    public $max_upload_size = 5242880; // 5MB

    public $l_specific = 'BannerImg';

    public $img_data_cache = [];

    public static $allowed_mime_types = [
        'image/gif',
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/x-png',
        'image/webp',
    ];

    public static $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png', 'webp'];

    public function includeOptimizationInfo(&$content)
    {
        foreach ($this->img_fields as $k) {
            if (!empty($content[$k]['name'])) {
                $img_name = $content[$k]['name'];
                if (!isset($this->img_data_cache[$img_name])) {
                    $orig_size = (int) filesize($this->getPath($img_name, true));
                    $optimized_size = (int) filesize($this->getPath($img_name));
                    $this->img_data_cache[$img_name]['b'] = $this->formatBytes($optimized_size);
                    if ($c_rate = $this->getCompressionRate($orig_size, $optimized_size)) {
                        $this->img_data_cache[$img_name]['o'] = $c_rate . '%';
                    }
                }
                $content[$k] += $this->img_data_cache[$img_name];
            }
        }
    }

    public function getConfigurableFields()
    {
        return [
            'custom_file_name' => [
                'label' => $this->l('File name'),
                'type' => 'text',
                'locked_overlay' => $this->l('Generated automatically'),
            ],
            'alt' => [
                'label' => $this->l('Alt text'),
                'type' => 'text',
            ],
            'title' => [
                'label' => $this->l('Title'),
                'tooltip' => $this->l('Displayed on hover'),
                'type' => 'text',
            ],
        ];
    }

    public function getCompressionRate($orig_size, $optimized_size, $allow_negative = false)
    {
        $rate = $orig_size ? round((($orig_size - $optimized_size) / $orig_size) * 100, 2) : 0;

        return $allow_negative ? $rate : max([0, $rate]);
    }

    public function prepareImgDataForSaving(&$data_multilang)
    {
        $ret = ['uploaded' => [], 'delete_if_unused' => Tools::getValue('imgs_to_delete', [])];
        $img_array = array_fill_keys(['name', 'alt', 'title', 'w', 'h'], '');
        foreach ($this->prepareImageFields($data_multilang) as $id_lang => $img_fields) {
            foreach ($img_fields as $k => $img) {
                if (!empty($img['outdated_file_name'])) {
                    $ret['delete_if_unused'][] = $img['outdated_file_name'];
                }
                if (!empty($img['to_delete'])) {
                    if (isset($img['name'])) {
                        $ret['delete_if_unused'][] = $img['name'];
                    }
                    unset($data_multilang[$id_lang][$k]);
                    continue;
                } elseif (!empty($img['file_to_upload'])) {
                    $file_key = md5(json_encode($img['file_to_upload']));
                    if (!isset($ret['uploaded'][$file_key])) {
                        $ret['uploaded'][$file_key] = $this->upload($img['file_to_upload'], $img['custom_file_name']);
                    }
                    $img += $ret['uploaded'][$file_key];
                } elseif ($img['custom_file_name']) {
                    $this->copy($img['name'], $img['custom_file_name']);
                    $ret['delete_if_unused'][] = $img['name'];
                    $img['name'] = $img['custom_file_name'];
                }
                $data_multilang[$id_lang][$k] = array_intersect_key($img, $img_array);
            }
        }

        return $ret;
    }

    public function prepareImageFields($data_multilang)
    {
        $img_lang_source = Tools::getValue('img_lang_source', []);
        $ready_img_fields = [];
        foreach ($data_multilang as $id_lang => $content) {
            foreach ($this->img_fields as $k) {
                $id_lang_source = isset($img_lang_source[$k]) ? $img_lang_source[$k] : $id_lang;
                $img = isset($data_multilang[$id_lang_source][$k]) ? $data_multilang[$id_lang_source][$k] : [];
                if (isset($_FILES['banner_' . $k . '_' . $id_lang_source])) {
                    $img['file_to_upload'] = $_FILES['banner_' . $k . '_' . $id_lang_source];
                    $img['path'] = $img['file_to_upload']['tmp_name'];
                } elseif (!empty($img['name'])) {
                    $img['path'] = $this->getPath($img['name']);
                }
                if (!empty($img['path']) && file_exists($img['path'])) {
                    if ($k == 'img_hover') {
                        if (empty($ready_img_fields[$id_lang]['img']['path'])) {
                            $img['to_delete'] = 1;
                        } else {
                            $img_size = $this->getDimensions($ready_img_fields[$id_lang]['img']['path']);
                            $img_hover_size = $this->getDimensions($img['path']);
                            if ($img_hover_size['w'] != $img_size['w'] || $img_hover_size['h'] != $img_size['h']) {
                                $this->throwError(sprintf(
                                    'Hover image (%1$s) should be the same size as main image: %2$s px',
                                    Language::getIsoById($id_lang),
                                    $img_size['w'] . 'x' . $img_size['h']
                                ));
                            }
                        }
                    }
                    if (!empty($img['custom_file_name']) && empty($img['to_delete'])) {
                        if (!empty($img['name']) && $img['name'] == $img['custom_file_name']) {
                            $img['custom_file_name'] = '';
                        } elseif (file_exists($this->getPath($img['custom_file_name']))) {
                            $this->throwError(sprintf(
                                'Image with this name already exists: "%1$s"',
                                $img['custom_file_name']
                            ));
                        } else {
                            $required_ext = false;
                            if (!empty($img['file_to_upload'])) {
                                $required_ext = $this->getExt($img['file_to_upload']['name']);
                            } elseif (!empty($img['name'])) {
                                $required_ext = $this->getExt($img['name']);
                            }
                            if ($required_ext && $this->getExt($img['custom_file_name']) != $required_ext) {
                                $this->throwError(sprintf(
                                    'Please use extension %1$s in File name (%2$s)',
                                    '".' . $required_ext . '"',
                                    Language::getIsoById($id_lang)
                                ));
                            }
                        }
                    }
                } else {
                    $img['to_delete'] = 1;
                }
                if ($id_lang_source != $id_lang && isset($content[$k]['name'])) {
                    $img['outdated_file_name'] = $content[$k]['name'];
                }
                $ready_img_fields[$id_lang][$k] = $img;
            }
        }

        return $ready_img_fields;
    }

    public function upload($file, $custom_file_name = '')
    {
        $result = ['name' => $custom_file_name ?: $this->getNewFilename($this->getExt($file['name']))];
        $img_file_path = '';
        $img_file_path_orig = $this->getPath($result['name'], true);
        if (!empty($file['tmp_name'])) {
            if ($error = $this->validateUpload($file)) {
                $this->throwError($error);
            }
            if (!move_uploaded_file($file['tmp_name'], $img_file_path_orig)) { // move orig file as-is
                $this->throwError('Error on uploading the image');
            }
            if ($this->optimizer('process', ['img_name' => $result['name']])) {
                $img_file_path = $this->getPath($result['name']);
                if ($this->optimizer('model')->getImageFormat($img_file_path) == 'webp') {
                    $result['webp_fallback'] = $this->webP()->addFallback($img_file_path);
                }
            }
        } else {
            $result['name'] = '';
        }

        return $result + $this->getDimensions($img_file_path);
    }

    public function getNewFilename($ext)
    {
        do {
            $string = sha1(microtime());
            $filename = substr($string . $string, rand(0, strlen($string)), 10) . '.' . $ext;
        } while (file_exists($this->getPath($filename)));

        return $filename;
    }

    public function prepareContentForDuplication($encoded_content)
    {
        $content = json_decode($encoded_content, true);
        if (!isset($this->duplicated_images)) {
            $this->duplicated_images = [];
        }
        foreach ($this->img_fields as $k) {
            if (!empty($content[$k]['name'])) {
                $orig_file_name = $content[$k]['name'];
                if (!isset($this->duplicated_images[$orig_file_name])) {
                    $ext = $this->getExt($orig_file_name);
                    $new_file_name = $this->duplicated_images[$orig_file_name] = $this->getNewFilename($ext);
                    $this->img()->copy($orig_file_name, $new_file_name);
                }
                $content[$k]['name'] = $this->duplicated_images[$orig_file_name];
            }
        }

        return json_encode($content);
    }

    public function extractImageNames($rows, $include_fallbacks = false)
    {
        $used_images = [];
        foreach ($rows as $row) {
            $content = json_decode($row['content'], true);
            foreach ($this->img_fields as $k) {
                if (!empty($content[$k]['name'])) {
                    $used_images[$content[$k]['name']] = $content[$k]['name'];
                    if ($include_fallbacks && !empty($content[$k]['webp_fallback'])) {
                        $fallback_img_name = $content[$k]['name'] . '.' . $content[$k]['webp_fallback'];
                        $used_images[$fallback_img_name] = $fallback_img_name;
                    }
                }
            }
        }

        return $used_images;
    }

    public function validateUpload($file)
    {
        $error = '';
        if ($file['error']) {
            $error = sprintf($this->l('Error while uploading image. Code: %s', $this->l_specific), $file['error']);
        } elseif ($file['size'] > $this->max_upload_size) {
            $error = $this->l('Image is too large', $this->l_specific);
        } elseif (!ImageManager::isRealImage($file['tmp_name'], $file['type'], self::$allowed_mime_types)
            || !ImageManager::isCorrectImageFileExt($file['name'], self::$allowed_file_extensions)
            || preg_match('/\%00/', $file['name'])) {
            $error = $this->l('Image format not recognized', $this->l_specific);
        }

        return $error;
    }

    public function copy($img_name, $new_img_name)
    {
        foreach ([true, false] as $orig) {
            $src_path = $this->getPath($img_name, $orig);
            if (is_file($src_path)) {
                $dst_path = $this->getPath($new_img_name, $orig);
                Tools::copy($src_path, $dst_path);
                if (!$orig && ($ext = $this->webP()->fallbackRequired($img_name))) {
                    if (is_file($src_path . '.' . $ext)) {
                        Tools::copy($src_path . '.' . $ext, $dst_path . '.' . $ext);
                    }
                }
            }
        }
    }

    public function delete($img_name)
    {
        Tools::deleteFile($this->getPath($img_name));
        Tools::deleteFile($this->getPath($img_name, true));
        if ($ext = $this->webP()->fallbackRequired($img_name)) {
            Tools::deleteFile($this->getPath($img_name . '.' . $ext));
        }
    }

    public function deleteAll()
    {
        return $this->recursiveRemove($this->getPath('', false), true);
    }

    public function deleteIfUnused($to_delete)
    {
        foreach (array_unique($to_delete) as $img_name) {
            if (!$this->imageIsUsed($img_name)) {
                $this->delete($img_name);
            }
        }
    }

    public function imageIsUsed($img_name)
    {
        return $this->db->executeS('
            SELECT id_banner FROM ' . $this->sqlTable('_lang') . '
            WHERE content LIKE \'%"name":"' . pSQL($img_name) . '"%\'
        ');
    }

    public function getDimensions($img_file_path)
    {
        $size = ['w' => 0, 'h' => 0];
        if (file_exists($img_file_path)) {
            $px_size = getimagesize($img_file_path);
            $size = ['w' => $px_size[0], 'h' => $px_size[1]];
        }

        return $size;
    }

    public function getExt($file_path)
    {
        return pathinfo($file_path, PATHINFO_EXTENSION);
    }

    public function getPath($img_name, $orig = false)
    {
        return $this->img_dir_local . ($orig ? 'orig/' : '') . $img_name;
    }

    public function getAllImages($original = false)
    {
        return array_filter(glob($this->getPath('*', $original)), function ($file) {
            return is_file($file) && basename($file) != 'index.php';
        });
    }

    public function deleteUnused()
    {
        $processed = $deleted = [];
        $all_rows = $this->db->executeS('SELECT * FROM ' . $this->sqlTable('_lang'));
        $used_names = $this->extractImageNames($all_rows, true);
        foreach ([false, true] as $orig) {
            foreach ($this->getAllImages($orig) as $img) {
                $img_name = basename($img);
                if (!isset($processed[$img_name])) {
                    if (!isset($used_names[$img_name])) {
                        $deleted[$img_name] = 1;
                        $this->delete($img_name);
                    }
                    $processed[$img_name] = 1;
                }
            }
        }

        return $deleted;
    }

    public function formatBytes($size, $precision = 2)
    {
        if (!$size) {
            $result = '0B';
        } else {
            $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
            $base = log($size) / log(1024);
            $floor_base = floor($base);
            $result = round(1024 ** ($base - $floor_base), $precision) . $suffixes[$floor_base];
        }

        return $result;
    }

    public function optimizer($action, $params = [])
    {
        $ret = [];
        switch ($action) {
            case 'install':
                $ret = $this->db->execute('
                    CREATE TABLE IF NOT EXISTS ' . $this->sqlTable('_optimizer') . ' (
                        id int(10) unsigned NOT NULL,
                        name varchar(32) NOT NULL,
                        active tinyint(1) NOT NULL,
                        settings text NOT NULL,
                        PRIMARY KEY (id)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
                ');
                $this->optimizer('save', ['id' => '0-NoOptimizer', 'settings' => []]);
                break;
            case 'uninstall':
                $ret = $this->db->execute('DROP TABLE IF EXISTS ' . $this->sqlTable('_optimizer'));
                break;
            case 'save':
                $id = explode('-', $params['id']);
                if (count($id) == 2) {
                    $o_name = $id[1];
                    require_once $o_name . '.php';
                    $o_model = new $o_name();
                    if (!$ret['errors'] = $this->validateSettings($params['settings'], $o_model->fields)) {
                        $settings = json_encode($params['settings']);
                        $ret['saved'] = $settings && $this->runSql([
                            'UPDATE ' . $this->sqlTable('_optimizer') . ' SET active = 0',
                            'REPLACE INTO ' . $this->sqlTable('_optimizer') . ' VALUES
                            (' . (int) $id[0] . ', \'' . pSQL($id[1]) . '\', 1, \'' . pSQL($settings) . '\')',
                        ]);
                    }
                }
                break;
            case 'model':
                if (!isset($this->optimizer_model)) {
                    if ($o_data = $this->db->getRow('
                        SELECT * FROM ' . $this->sqlTable('_optimizer') . ' WHERE active = 1
                    ')) {
                        $o_name = $o_data['name'];
                        require_once $o_name . '.php';
                        $this->optimizer_model = new $o_name($o_data);
                    } else {
                        $this->optimizer_model = new OptimizerModel();
                    }
                }
                $ret = $this->optimizer_model;
                break;
            case 'getAllData':
                $ret['optimizers'] = $this->optimizer('getAvailable');
                $ret['images'] = $this->optimizer('getAvailableImagesData');
                break;
            case 'getAvailable':
                $all_saved_data = [];
                $db_data = $this->db->executeS('SELECT * FROM ' . $this->sqlTable('_optimizer'));
                foreach ($db_data as $d) {
                    $all_saved_data[$d['name']] = $d;
                }
                foreach (glob($this->local_path . 'classes/*Optimizer.php') as $file) {
                    require_once $file;
                    $o_name = basename($file, '.php');
                    $saved_data = isset($all_saved_data[$o_name]) ? $all_saved_data[$o_name] : [];
                    $o = new $o_name($saved_data);
                    foreach ($o->fields as $k => $f) {
                        $o->fields[$k]['label'] = $this->decodeTxt($k);
                        if (isset($f['min']) && isset($f['max'])) {
                            $o->fields[$k]['tooltip'] = $f['min'] . ' - ' . $f['max'];
                        }
                    }
                    $ret[$o->id . '-' . $o_name] = $o;
                }
                ksort($ret);
                break;
            case 'getAvailableImagesData':
                $images = $this->getAllImages(true);
                $ret['num'] = count($images);
                $ret['orig_size'] = $ret['compr_size'] = 0;
                foreach ($images as $orig_img) {
                    $compr_img = $this->getPath(basename($orig_img));
                    if (file_exists($compr_img)) {
                        $ret['orig_size'] += (int) filesize($orig_img);
                        $ret['compr_size'] += (int) filesize($compr_img);
                    }
                }
                $ret['compression'] = $this->getCompressionRate($ret['orig_size'], $ret['compr_size']);
                break;
            case 'process':
                foreach ($this->optimizer('model')->fields as $k => $field) {
                    if ($field['value'] == '') {
                        $err = sprintf($this->l('Please fill %s', $this->l_specific), $this->decodeTxt($k));
                        $this->throwError($err);
                    }
                }
                $src_path = $this->getPath($params['img_name'], true);
                $dst_path = $this->getPath($params['img_name']);
                $ret = $this->optimizer('model')->process($src_path, $dst_path);
                if ($ret !== true) {
                    $this->throwError($ret);
                }
                break;
        }

        return $ret;
    }

    public function webP()
    {
        if (!isset($this->webp_obj)) {
            require_once $this->local_path . 'classes/WebP.php';
            $this->webp_obj = new WebP();
        }

        return $this->webp_obj;
    }

    public function decodeTxt($txt)
    {
        $codes = [
            'quality_jpg' => $this->l('JPEG quality', $this->l_specific),
            'api_key' => $this->l('API Key', $this->l_specific),
        ];

        return isset($codes[$txt]) ? $codes[$txt] : $txt;
    }
}
