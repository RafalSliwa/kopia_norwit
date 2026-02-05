<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class BannersData extends CustomBanners
{
    public function prepareDemoContent($parent_obj_id)
    {
        if ($demo_file_path = $this->getDemoFilePath()) {
            $this->id = $parent_obj_id; // $this->id is empty during installation
            $this->import($demo_file_path);
        }

        return true;
    }

    public function getDemoFilePath()
    {
        $demo_file_path = $this->local_path . 'defaults/data-custom.zip';
        if (!file_exists($demo_file_path)) {
            $demo_file_path = $this->local_path . 'defaults/data' . ($this->is_16 ? '-16' : '') . '.zip';
        }

        return file_exists($demo_file_path) ? $demo_file_path : false;
    }

    public function export($to_directory = '', $archive_name = '')
    {
        if (!class_exists('ZipArchive')) {
            self::$errors[] = $this->l('Ask your hosting provider to install zip extension');

            return;
        }
        $lang_id_iso = array_column(Language::getLanguages(false), 'iso_code', 'id_lang');
        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $tables_to_export = [
            'cb',
            'cb_lang',
            'cb_hook_settings',
            'cb_wrapper_settings',
            'cb_optimizer',
            'hook_module',
        ];
        $export_data = $export_images = [];
        foreach ($tables_to_export as $table_name) {
            $data_from_db = $this->db->executeS('SELECT * FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`');
            $table_data = [];
            switch ($table_name) {
                case 'cb':
                    foreach ($data_from_db as $d) {
                        $table_data[$d['id_banner']] = $d;
                    }
                    break;
                case 'cb_lang':
                    foreach ($data_from_db as $d) {
                        if (!$d['content']) {
                            continue;
                        }
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $iso = $d['id_lang'] == $id_lang_default ? 'LANG_ISO_DEFAULT' : $lang_id_iso[$d['id_lang']];
                        // if ($id_shop != 'ID_SHOP_DEFAULT' || $iso != 'LANG_ISO_DEFAULT') continue;
                        $table_data[$id_shop][$d['id_banner']][$iso] = $d;
                    }
                    $export_images = $this->img()->extractImageNames($data_from_db, true);
                    break;
                case 'cb_hook_settings':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        // if ($id_shop != 'ID_SHOP_DEFAULT') continue;
                        $table_data[$id_shop][$d['hook_name']] = $d;
                    }
                    break;
                case 'cb_optimizer':
                    foreach ($data_from_db as $d) {
                        if (!empty($d['active'])) {
                            $table_data[] = $d;
                        }
                    }
                    break;
                case 'hook_module':
                    foreach ($data_from_db as $d) {
                        if ($d['id_module'] != $this->id) {
                            continue;
                        }
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        // if ($id_shop != 'ID_SHOP_DEFAULT') continue;
                        $hook_name = Hook::getNameByid($d['id_hook']);
                        $table_data[$id_shop][$hook_name] = $d['position'];
                    }
                    break;
                default:
                    $table_data = $data_from_db;
                    break;
            }
            $export_data[$table_name] = $table_data;
        }
        $export_data['slider_library'] = $this->sliderLibrary('getData');
        $export_data['version'] = str_replace('.', '', $this->version);
        $tmp_zip_file = tempnam($this->getTmpDir(), 'zip');
        $zip = new ZipArchive();
        $zip->open($tmp_zip_file, ZipArchive::OVERWRITE);
        $zip->addFromString('data.txt', json_encode($export_data));
        foreach ($export_images as $img_name) {
            foreach (['', 'orig/'] as $dir) {
                $file_path = $this->img()->getPath($img_name, (bool) $dir);
                if (is_file($file_path)) {
                    $zip->addFile($file_path, 'img/' . $dir . $img_name);
                }
            }
        }
        foreach ($this->customCode('getTypes') as $code_type) {
            $file_path = $this->customCode('getFilePath', ['type' => $code_type]);
            if (is_file($file_path)) {
                $zip->addFile($file_path, basename($file_path));
            }
        }
        $zip->close();
        if (!$archive_name || !Validate::isFileName($archive_name) || substr($archive_name, -4) != '.zip') {
            $archive_name = 'backup-' . date('d-m-Y') . '.zip';
        }
        $file_path = '';
        if ($to_directory) {
            if (is_writable($to_directory)) {
                $file_path = rtrim($to_directory, '/') . '/' . $archive_name;
                rename($tmp_zip_file, $file_path);
            }
        } else {
            header('Content-Type: application/zip');
            header('Content-Length: ' . filesize($tmp_zip_file));
            header('Content-Disposition: attachment; filename="' . $archive_name . '"');
            readfile($tmp_zip_file);
            unlink($tmp_zip_file);
        }

        return $file_path;
    }

    public function import($zip_file = false)
    {
        $tmp_zip_file = $this->getTmpDir() . 'uploaded.zip';
        if (!$zip_file) {
            if (!isset($_FILES['zipped_banners_data'])) {
                return $this->clearFilesAndSetError($this->l('File not uploaded'));
            }
            $uploaded_file = $_FILES['zipped_banners_data'];
            $accepted_types = [
                'application/zip',
                'application/x-zip-compressed',
                'multipart/x-zip',
                'application/x-compressed',
            ];
            if (!in_array($uploaded_file['type'], $accepted_types)) {
                return $this->clearFilesAndSetError($this->l('Please upload a valid zip file'));
            }
            if (!move_uploaded_file($uploaded_file['tmp_name'], $tmp_zip_file)) {
                return $this->clearFilesAndSetError($this->failed_txt);
            }
        } else {
            Tools::copy($zip_file, $tmp_zip_file);
        }
        if (!$exctracted_contents_dir = $this->extractZipToTemporaryDirectory($tmp_zip_file)) {
            return $this->clearFilesAndSetError($this->l('An error occured while unzipping archive'));
        }
        if (!file_exists($exctracted_contents_dir . 'data.txt')) {
            return $this->clearFilesAndSetError($this->l('This is not a valid backup file'));
        }
        $imported_data = json_decode(Tools::file_get_contents($exctracted_contents_dir . 'data.txt'), true);
        $imported_data = $this->retro()->prepareDataForImport($imported_data, $exctracted_contents_dir);
        $lang_iso_id = array_column(Language::getLanguages(false), 'id_lang', 'iso_code');
        $tables_to_fill = $banner_ids = $import_images = $hooks_data = [];
        $all_shop_ids = Shop::getShops(false, null, true);
        foreach ($all_shop_ids as $id_shop) {
            if (isset($imported_data['cb_lang'][$id_shop])) {
                $shop_banners = $imported_data['cb_lang'][$id_shop];
            } else {
                $shop_banners = $imported_data['cb_lang']['ID_SHOP_DEFAULT'];
            }
            $shop_banners = array_intersect_key($shop_banners, $imported_data['cb']);
            foreach ($shop_banners as $id_banner => $banner_multilang) {
                $banner_ids[$id_banner] = $id_banner;
                foreach ($lang_iso_id as $iso => $id_lang) {
                    if (isset($banner_multilang[$iso])) {
                        $banner_data = $banner_multilang[$iso];
                    } else {
                        $banner_data = $banner_multilang['LANG_ISO_DEFAULT'];
                    }
                    $banner_data['id_shop'] = $id_shop;
                    $banner_data['id_lang'] = $id_lang;
                    $tables_to_fill['cb_lang'][] = $banner_data;
                    $import_images += $this->img()->extractImageNames([$banner_data], true);
                }
            }
            if ($imported_data['cb_hook_settings']) {
                if (isset($imported_data['cb_hook_settings'][$id_shop])) {
                    $settings_rows = $imported_data['cb_hook_settings'][$id_shop];
                } else {
                    $settings_rows = $imported_data['cb_hook_settings']['ID_SHOP_DEFAULT'];
                }
                foreach ($settings_rows as $row) {
                    $row['id_shop'] = $id_shop;
                    $tables_to_fill['cb_hook_settings'][] = $row;
                }
            }
            // hooks & positions
            if ($imported_data['hook_module']) {
                if (isset($imported_data['hook_module'][$id_shop])) {
                    $hooks_data[$id_shop] = $imported_data['hook_module'][$id_shop];
                } else {
                    $hooks_data[$id_shop] = $imported_data['hook_module']['ID_SHOP_DEFAULT'];
                }
            }
        }
        $tables_to_fill['cb'] = array_intersect_key($imported_data['cb'], $banner_ids);
        foreach (['cb_wrapper_settings', 'cb_optimizer'] as $table_name) {
            if (!empty($imported_data[$table_name])) {
                $tables_to_fill[$table_name] = $imported_data[$table_name];
            }
        }
        $sql = [];
        foreach ($tables_to_fill as $table_name => $rows_to_insert) {
            $db_columns = array_column($this->db->executeS('
                SHOW COLUMNS FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`
            '), 'Field');
            $sql[] = 'DELETE FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`';
            $rows = [];
            foreach ($rows_to_insert as $row) {
                $verified_row = [];
                foreach ($db_columns as $col_name) {
                    if (!isset($row[$col_name])) {
                        $err = $this->l('This file can not be used for import.') . ' ' .
                        $this->l('Reason: Database tables don\'t match (%s).');

                        return $this->throwError(sprintf($err, _DB_PREFIX_ . $table_name));
                    } else {
                        $allow_html = $col_name == 'content';
                        $verified_row[$col_name] = pSQL($row[$col_name], $allow_html);
                    }
                }
                $rows[] = '(\'' . implode('\', \'', $verified_row) . '\')';
            }
            if (!$rows || !$db_columns) {
                continue;
            }
            $sql[] = '
                INSERT INTO `' . _DB_PREFIX_ . bqSQL($table_name) . '`
                (`' . implode('`, `', array_map('bqSQL', $db_columns)) . '`)
                VALUES ' . implode(', ', $rows) . '
            ';
        }
        if (!$sql) {
            return $this->clearFilesAndSetError($this->l('Nothing to import'));
        }
        if ($imported = $this->runSql($sql)) {
            $this->updateAllWrappersSettings();
            if (!$slider_lib = $this->sliderLibrary('detectExternal')) {
                $slider_lib = isset($imported_data['slider_library']) ? $imported_data['slider_library'] : [];
            }
            $this->sliderLibrary('updateData', $slider_lib);
            $this->img()->deleteAll();
            foreach ($import_images as $img_name) {
                foreach (['', 'orig/'] as $dir) {
                    $src_path = $exctracted_contents_dir . 'img/' . $dir . $img_name;
                    $dst_path = $this->img()->getPath($img_name, (bool) $dir);
                    if (is_file($src_path) && is_writable(dirname($dst_path))) {
                        Tools::copy($src_path, $dst_path);
                    }
                }
            }
            if ($imported_data['version'] < 297) {
                $this->retro()->prepareOriginalImageFiles();
            }
            foreach ($this->customCode('getTypes') as $type) {
                $dst_path = $this->customCode('getFilePath', ['type' => $type]);
                if (is_writable(dirname($dst_path))) {
                    $src_path = $exctracted_contents_dir . basename($dst_path);
                    if (is_file($src_path)) {
                        Tools::copy($src_path, $dst_path);
                    } elseif (is_file($dst_path)) {
                        unlink($dst_path);
                    }
                }
            }
            $this->recursiveRemove($this->getTmpDir(), true);
            // save original shop context, because it will be changed while setting up hooks
            $original_shop_context = Shop::getContext();
            $original_shop_context_id = null;
            if ($original_shop_context == Shop::CONTEXT_GROUP) {
                $original_shop_context_id = $this->context->shop->id_shop_group;
            } elseif ($original_shop_context == Shop::CONTEXT_SHOP) {
                $original_shop_context_id = $this->context->shop->id;
            }
            foreach ($hooks_data as $id_shop => $hook_list) {
                foreach ($hook_list as $hook_name => $cb_position) {
                    if ($id_shop != $this->context->shop->id) {
                        Cache::clean('hook_module_list');
                        Shop::setContext(Shop::CONTEXT_SHOP, $id_shop);
                    }
                    $id_hook = Hook::getIdByName($hook_name);
                    $this->registerHook($hook_name, [$id_shop]);
                    $this->updatePosition($id_hook, 0, $cb_position);
                }
            }
            Shop::setContext($original_shop_context, $original_shop_context_id);
            Media::clearCache();
        }

        return $imported;
    }

    public function extractZipToTemporaryDirectory($zip_file, $dir_path = '')
    {
        $dir_path = $dir_path ?: $this->getTmpDir() . 'uploaded_extracted/';

        return Tools::ZipExtract($zip_file, $dir_path) ? $dir_path : false;
    }

    public function getTmpDir()
    {
        return $this->local_path . 'tmp/';
    }
}
