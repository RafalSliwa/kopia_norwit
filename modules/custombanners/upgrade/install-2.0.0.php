<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_0_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $images_to_copy = glob(_PS_MODULE_DIR_ . '/' . $module_obj->name . '/img/uploads/*');
    foreach ($images_to_copy as $img_path) {
        if (basename($img_path) != 'index.php') {
            Tools::copy($img_path, $module_obj->img_dir_local . basename($img_path));
        }
    }
    $directories_to_remove = ['/css', '/js', '/img', '/views/templates/front'];
    foreach ($directories_to_remove as $dir_path) {
        $module_obj->recursiveRemove(_PS_MODULE_DIR_ . $module_obj->name . $dir_path);
    }
    $files_to_remove = ['/views/templates/admin/carousel-form.tpl'];
    foreach ($files_to_remove as $file_path) {
        if (file_exists(_PS_MODULE_DIR_ . $module_obj->name . $file_path)) {
            unlink(_PS_MODULE_DIR_ . $module_obj->name . $file_path);
        }
    }
    updateDB($module_obj->db);

    return true;
}

function updateDB($db_instance)
{
    $banners_data = $db_instance->executeS('
        SELECT * FROM ' . _DB_PREFIX_ . 'custombanners cb
        LEFT JOIN ' . _DB_PREFIX_ . 'custombanners_shop_lang cbsl ON cb.id_banner = cbsl.id_banner
    ');
    $rows = [];
    foreach ($banners_data as $b) {
        $row = [];
        $row['id_banner'] = (int) $b['id_banner'];
        $row['id_shop'] = (int) $b['id_shop'];
        $row['id_lang'] = (int) $b['id_lang'];
        $row['hook_name'] = '\'' . pSQL($b['hook_name']) . '\'';
        $row['position'] = (int) $b['position'];
        $row['active'] = (int) $b['active'];
        $row['in_carousel'] = 0;
        $content = json_decode($b['banner_content'], true);
        if ($b['banner_img_name']) {
            $content['img'] = $b['banner_img_name'];
        }
        if (isset($content['link']) && $content['link']) {
            $new_version_link = [
                'type' => 'custom',
                'href' => $content['link'],
            ];
            if (isset($content['target_blank'])) {
                $new_version_link['_blank'] = 1;
                unset($content['target_blank']);
            }
            $content['link'] = $new_version_link;
        }
        if (isset($content['in_carousel'])) {
            if ($content['in_carousel']) {
                $row['in_carousel'] = (int) $content['in_carousel'];
            }
            unset($content['in_carousel']);
        }
        $content = json_encode($content);
        $row['content'] = '\'' . pSQL($content, true) . '\'';
        $rows[] = '(' . implode(', ', $row) . ')';
    }
    $result = true;
    $result &= $db_instance->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'custombanners_tmp');
    $result &= $db_instance->execute('
        CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'custombanners_tmp (
                id_banner int(10) unsigned NOT NULL,
                id_shop int(10) unsigned NOT NULL,
                id_lang int(10) unsigned NOT NULL,
                hook_name varchar(64) NOT NULL,
                position int(10) NOT NULL,
                active tinyint(1) NOT NULL,
                in_carousel tinyint(1) NOT NULL,
                content text NOT NULL,
                PRIMARY KEY (id_banner, id_shop, id_lang),
                KEY hook_name (hook_name),
                KEY active (active)
              ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
    ');
    if ($rows) {
        $result &= $db_instance->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'custombanners_tmp VALUES ' . implode(', ', $rows) . '
        ');
    }
    $new_banners_data = $db_instance->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'custombanners_tmp');
    if (count($banners_data) == count($new_banners_data)) {
        $result &= $db_instance->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'custombanners');
        $result &= $db_instance->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'custombanners_shop_lang');
        $result &= $db_instance->execute('
            RENAME TABLE ' . _DB_PREFIX_ . 'custombanners_tmp TO ' . _DB_PREFIX_ . 'custombanners
        ');
    } else {
        $result = false;
    }

    return $result;
}
