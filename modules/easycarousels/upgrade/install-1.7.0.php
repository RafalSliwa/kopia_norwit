<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_7_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $files_to_remove = [
        _PS_MODULE_DIR_ . $module_obj->name . '/views/templates/admin/exceptions-settings-form.tpl',
    ];
    foreach ($files_to_remove as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // update db
    $has_general_settings = false;
    $columns = $module_obj->db->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'easycarousels');
    foreach ($columns as $c) {
        if ($c['Field'] == 'general_settings') {
            $has_general_settings = true;
        }
    }
    if ($has_general_settings) {
        $current_carousels = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
        foreach ($current_carousels as $c) {
            $general_settings = json_decode($c['general_settings'], true);
            $owl_settings = json_decode($c['owl_settings'], true);
            $current_settings = [];
            foreach ($owl_settings as $k => $s) {
                $current_settings['carousel'][$k] = $s;
            }
            if (isset($general_settings['rows'])) {
                $current_settings['carousel']['r'] = $general_settings['rows'];
            }
            if (isset($general_settings['items_in_carousel'])) {
                $current_settings['carousel']['total'] = $general_settings['items_in_carousel'];
            }
            foreach ($general_settings as $k => $s) {
                if (strpos($k, 'show_') !== false) {
                    $current_settings['tpl'][str_replace('show_', '', $k)] = $s;
                }
            }
            if (isset($general_settings['image_type'])) {
                $current_settings['tpl']['image_type'] = $general_settings['image_type'];
            }
            if (isset($general_settings['custom_class'])) {
                $current_settings['tpl']['custom_class'] = $general_settings['custom_class'];
            }

            $special_fields = [
                'id_feature' => 'id_feature',
                'cat_ids' => 'cat_ids',
                'id_m' => 'id_manufacturer',
                'id_s' => 'id_supplier',
            ];
            foreach ($special_fields as $prev_key => $new_key) {
                if (isset($general_settings[$prev_key])) {
                    $current_settings['special'][$new_key] = $general_settings[$prev_key];
                }
            }

            foreach (['php', 'tpl', 'carousel', 'special'] as $type) {
                foreach ($module_obj->getFields($type) as $name => $field) {
                    if (!isset($current_settings[$type][$name])) {
                        $current_settings[$type][$name] = $field['value'];
                    }
                }
            }
            $current_settings = json_encode($current_settings);
            $module_obj->db->execute('
                UPDATE ' . _DB_PREFIX_ . 'easycarousels
                SET general_settings = \'' . pSQL($current_settings) . '\'
                WHERE id_carousel = ' . (int) $c['id_carousel'] . ' AND id_shop =  ' . (int) $c['id_shop'] . '
            ');
        }
    }

    $new_columns = [
        'group_in_tabs' => ['in_tabs', 'tinyint(1) NOT NULL DEFAULT 1'],
        'carousel_type' => ['type', 'varchar(128) NOT NULL'],
        'general_settings' => ['settings', 'text NOT NULL'],
    ];
    foreach ($columns as $c) {
        $c_name = $c['Field'];
        if (isset($new_columns[$c_name])) {
            $new_c = $new_columns[$c_name];
            $module_obj->db->execute('
                ALTER TABLE ' . _DB_PREFIX_ . 'easycarousels
                CHANGE `' . bqSQL($c_name) . '` `' . bqSQL($new_c[0]) . '` ' . pSQL($new_c[1]) . '
            ');
        } elseif ($c_name == 'owl_settings') {
            $module_obj->db->execute('ALTER TABLE ' . _DB_PREFIX_ . 'easycarousels DROP COLUMN owl_settings');
        }
    }
    $module_obj->db->execute('ALTER TABLE ' . _DB_PREFIX_ . 'easycarousels ADD INDEX (in_tabs)');
    $module_obj->db->execute('DROP INDEX group_in_tabs ON ' . _DB_PREFIX_ . 'easycarousels');

    // $module_obj->addOverride('Product'); // processed in 2.7.6
    // Tools::generateIndex();

    return true;
}
