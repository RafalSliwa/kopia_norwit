<?php
/*
 * MIT License
 *
 * Copyright (c) 2025 norwit
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * @author   norwit
 * @license  MIT
 * @version  1.3.0
 * @package  autocomplete_stock
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Autocomplete_Stock extends Module
{
    public function __construct()
    {
        $this->name = 'autocomplete_stock';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Autocomplete by stock');
        $this->description = $this->l('Product search and sorting by stock. Displaying the product category and brand.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        $uploadDir = _PS_MODULE_DIR_ . $this->name . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        // Harden uploads directory
        @file_put_contents($uploadDir.'.htaccess', "Options -Indexes\n<FilesMatch \"\\.(php|phar|phtml)$\">\nDeny from all\n</FilesMatch>\n");
        @file_put_contents($uploadDir.'index.php', "<?php\n// Silence is golden.\n");

        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->installCustomTable()
            && Configuration::updateValue('ASTOCK_SHOW_CATEGORIES', 1)
            && Configuration::updateValue('ASTOCK_CATEGORIES_LIMIT', 5)
            && Configuration::updateValue('ASTOCK_SHOW_MANUFACTURERS', 1)
            && Configuration::updateValue('ASTOCK_MANUFACTURERS_LIMIT', 5)
            && Configuration::updateValue('ASTOCK_PRODUCTS_LIMIT', 10)
            && Configuration::updateValue('ASTOCK_ONLY_AVAILABLE', 0)
            && Configuration::updateValue('ASTOCK_MIN_CHARS', 1)
            && Configuration::updateValue('ASTOCK_DEBOUNCE_MS', 150)
            && Configuration::updateValue('ASTOCK_IMAGE_TYPE', 'small_default');
    }

    public function uninstall()
    {
        Configuration::deleteByName('ASTOCK_SHOW_CATEGORIES');
        Configuration::deleteByName('ASTOCK_CATEGORIES_LIMIT');
        Configuration::deleteByName('ASTOCK_SHOW_MANUFACTURERS');
        Configuration::deleteByName('ASTOCK_MANUFACTURERS_LIMIT');
        Configuration::deleteByName('ASTOCK_PRODUCTS_LIMIT');
        Configuration::deleteByName('ASTOCK_ONLY_AVAILABLE');
        Configuration::deleteByName('ASTOCK_MIN_CHARS');
        Configuration::deleteByName('ASTOCK_DEBOUNCE_MS');
        Configuration::deleteByName('ASTOCK_IMAGE_TYPE');

        $this->uninstallCustomTable();

        return parent::uninstall();
    }

    /**
     * Build back-office config URL for this module including token.
     */
    private function getModuleConfigUrl(array $extra = [])
    {
        $base = AdminController::$currentIndex
            . '&configure=' . urlencode($this->name)
            . '&token=' . Tools::getAdminTokenLite('AdminModules');

        foreach ($extra as $k => $v) {
            $base .= '&' . urlencode($k) . '=' . urlencode((string)$v);
        }
        return $base;
    }

    private function installCustomTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'autocomplete_stock_custom` (
            `id_entry` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `link` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `alt` VARCHAR(255) DEFAULT NULL,
            `position` INT DEFAULT 0,
            `date_start` DATETIME DEFAULT NULL,
            `date_end` DATETIME DEFAULT NULL,
            `id_category` INT UNSIGNED DEFAULT NULL,
            PRIMARY KEY (`id_entry`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4;';
        $ok = Db::getInstance()->execute($sql);
        if ($ok) {
            // w razie starszej wersji bez kolumny — dopnij
            $this->ensureCustomInfra();
        }
        return $ok;
    }

    private function uninstallCustomTable()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'autocomplete_stock_custom`';
        return Db::getInstance()->execute($sql);
    }

    /**
     * Auto-heal: ensure custom table and uploads dir exist for already installed modules.
     * Also ensure missing columns are added (id_category).
     */
    private function ensureCustomInfra(): void
    {
        // Table exists?
        $table = _DB_PREFIX_.'autocomplete_stock_custom';
        $exists = Db::getInstance()->executeS('SHOW TABLES LIKE "'.pSQL($table).'"');
        if (empty($exists)) {
            $this->installCustomTable();
        } else {
            // ensure id_category column
            $cols = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.bqSQL($table).'` LIKE "id_category"');
            if (empty($cols)) {
                @Db::getInstance()->execute('ALTER TABLE `'.bqSQL($table).'` ADD `id_category` INT UNSIGNED DEFAULT NULL AFTER `date_end`');
            }
        }

        // Uploads dir exists & is writable?
        $uploadDir = _PS_MODULE_DIR_.$this->name.'/uploads/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!is_writable($uploadDir)) {
            @chmod($uploadDir, 0755);
        }
        // Re-harden if missing files
        if (!file_exists($uploadDir.'.htaccess')) {
            @file_put_contents($uploadDir.'.htaccess', "Options -Indexes\n<FilesMatch \"\\.(php|phar|phtml)$\">\nDeny from all\n</FilesMatch>\n");
        }
        if (!file_exists($uploadDir.'index.php')) {
            @file_put_contents($uploadDir.'index.php', "<?php\n// Silence is golden.\n");
        }
    }

    public function hookDisplayHeader()
    {
        if (empty($this->context->controller)) {
            return;
        }

        Media::addJsDef([
            'autocomplete_stock_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', [], true),
            'autocomplete_stock_cfg' => [
                'showCategories'    => (bool) Configuration::get('ASTOCK_SHOW_CATEGORIES'),
                'categoriesLimit'   => (int) Configuration::get('ASTOCK_CATEGORIES_LIMIT'),
                'showManufacturers' => (bool) Configuration::get('ASTOCK_SHOW_MANUFACTURERS'),
                'manufacturersLimit'=> (int) Configuration::get('ASTOCK_MANUFACTURERS_LIMIT'),
                'productsLimit'     => (int) Configuration::get('ASTOCK_PRODUCTS_LIMIT'),
                'onlyAvailable'     => (bool) Configuration::get('ASTOCK_ONLY_AVAILABLE'),
                'minChars'          => (int) Configuration::get('ASTOCK_MIN_CHARS'),
                'debounce'          => (int) Configuration::get('ASTOCK_DEBOUNCE_MS'),
                'imageType'         => (string) Configuration::get('ASTOCK_IMAGE_TYPE'),
                'i18n' => [
                    'categories'    => $this->l('Categories'),
                    'products'      => $this->l('Products'),
                    'manufacturers' => $this->l('Brands'),
                ],
            ],
        ]);

        $this->context->controller->registerJavascript(
            'modules-'.$this->name.'-enhanced',
            'modules/'.$this->name.'/views/js/autocomplete_enhanced.js',
            ['position' => 'bottom', 'priority' => 200]
        );
        $this->context->controller->registerStylesheet(
            'modules-'.$this->name.'-enhanced',
            'modules/'.$this->name.'/views/css/autocomplete_enhanced.css',
            ['media' => 'all', 'priority' => 200]
        );
        $this->context->controller->addJqueryUI('ui.autocomplete');
    }

    private function getProductImageTypeOptions()
    {
        $options = [];
        $types = ImageType::getImagesTypes('products');
        if (is_array($types)) {
            foreach ($types as $t) {
                $name = (string) $t['name'];
                $label = $name;
                if (!empty($t['width']) && !empty($t['height'])) {
                    $label .= sprintf(' (%dx%d)', $t['width'], $t['height']);
                }
                $options[] = ['id' => $name, 'name' => $label];
            }
        }

        if (empty($options)) {
            $options[] = ['id' => 'small_default', 'name' => 'small_default'];
        }

        $current = (string) Configuration::get('ASTOCK_IMAGE_TYPE') ?: 'small_default';
        $exists = false;
        foreach ($options as $opt) {
            if ($opt['id'] === $current) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            array_unshift($options, ['id' => $current, 'name' => $current.' (custom)']);
        }

        return $options;
    }

    /**
     * Allow absolute URLs or clean relative paths starting with "/"
     */
    private function isValidLink($link)
    {
        if (Validate::isUrl($link)) {
            return true;
        }
        if (Tools::substr($link, 0, 1) === '/' && Validate::isCleanHtml($link)) {
            return true;
        }
        return false;
    }

    /**
     * Build flat list of categories for select (indented).
     */
    private function getCategoryOptionsList(): array
    {
        $idLang = (int)$this->context->language->id;
        $idShop = (int)$this->context->shop->id;

        // all categories for shop/lang
        $rows = Db::getInstance()->executeS('
            SELECT c.id_category, cl.name, c.level_depth
            FROM '._DB_PREFIX_.'category c
            INNER JOIN '._DB_PREFIX_.'category_shop cs ON cs.id_category = c.id_category AND cs.id_shop = '.(int)$idShop.'
            INNER JOIN '._DB_PREFIX_.'category_lang cl ON cl.id_category = c.id_category AND cl.id_lang='.(int)$idLang.' AND cl.id_shop='.(int)$idShop.'
            WHERE c.active = 1
            ORDER BY c.level_depth ASC, cl.name ASC
        ');

        $homeId = (int) Configuration::get('PS_HOME_CATEGORY');
        $root   = Category::getRootCategory();
        $rootId = $root ? (int) $root->id : 0;

        $out = [];
        foreach ((array)$rows as $r) {
            $cid = (int)$r['id_category'];
            if ($cid === $homeId || $cid === $rootId) {
                continue;
            }
            $depth = (int)$r['level_depth'];
            $label = str_repeat('—', max(0, $depth - 1)) . ' ' . $r['name'];
            $out[] = ['id' => $cid, 'name' => $label];
        }
        return $out;
    }

    /**
     * Resolve category names by IDs with static cache.
     */
    private function resolveCategoryName(int $idCategory): string
    {
        static $cache = [];
        if ($idCategory <= 0) {
            return '';
        }
        if (isset($cache[$idCategory])) {
            return $cache[$idCategory];
        }
        $row = Db::getInstance()->getRow('
            SELECT cl.name FROM '._DB_PREFIX_.'category_lang cl
            INNER JOIN '._DB_PREFIX_.'category_shop cs ON cs.id_category=cl.id_category AND cs.id_shop='.(int)$this->context->shop->id.'
            WHERE cl.id_category='.(int)$idCategory.' AND cl.id_lang='.(int)$this->context->language->id.'
        ');
        $name = is_array($row) && isset($row['name']) ? (string)$row['name'] : '';
        $cache[$idCategory] = $name;
        return $name;
    }

    public function getContent()
    {
        $output = '';
        if ($msg = (string)$this->context->cookie->__get('autostock_notice')) {
            $output .= $this->displayConfirmation($msg);
            $this->context->cookie->__unset('autostock_notice');
        }

        // Auto-heal: ensure table/column + uploads dir
        $this->ensureCustomInfra();

        // Optional CSRF hardening for POST actions
        if (Tools::isSubmit('delete_custom_entry') || Tools::isSubmit('submit_custom_entry') || Tools::isSubmit('submit_update_entry')) {
            if (Tools::getValue('token') !== Tools::getAdminTokenLite('AdminModules')) {
                return $this->displayError($this->l('Invalid token.'));
            }
        }

        // DELETE
        if (Tools::isSubmit('delete_custom_entry')) {
            $idEntry = (int)Tools::getValue('id_entry');
            $entry = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'autocomplete_stock_custom` WHERE id_entry = '.(int)$idEntry);
            if ($entry && !empty($entry['image'])) {
                $imagePath = _PS_MODULE_DIR_.$this->name.'/uploads/'.$entry['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            Db::getInstance()->delete('autocomplete_stock_custom', 'id_entry = '.(int)$idEntry);

            // komunikat + redirect, aby wyczyścić GET (edit_custom_entry)
            $this->context->cookie->__set('autostock_notice', $this->l('Entry deleted.'));
            Tools::redirectAdmin($this->getModuleConfigUrl());
        }

        // ADD
        if (Tools::isSubmit('submit_custom_entry')) {
            $title = Tools::getValue('custom_title');
            $link  = Tools::getValue('custom_link');
            $alt   = Tools::getValue('custom_alt');
            $idCat = (int)Tools::getValue('id_category');

            $dateStart = Tools::getValue('custom_date_start');
            $dateEnd   = Tools::getValue('custom_date_end');
            $dateStartSQL = ($dateStart) ? date('Y-m-d H:i:00', strtotime($dateStart)) : null;
            $dateEndSQL   = ($dateEnd)   ? date('Y-m-d H:i:00', strtotime($dateEnd))   : null;

            if (empty($link)) {
                    $output .= $this->displayError($this->l('Link is required.'));
                } elseif (($title !== '' && !Validate::isCleanHtml($title)) || !$this->isValidLink($link)) {
                    $output .= $this->displayError($this->l('Invalid data.'));
                } else {
                // Upload with validation
                $imageName = '';
                if (isset($_FILES['custom_image']) && is_uploaded_file($_FILES['custom_image']['tmp_name'])) {
                    $error = ImageManager::validateUpload(
                        $_FILES['custom_image'],
                        (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024
                    );
                    if ($error) {
                        $output .= $this->displayError($error);
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm();
                    }

                    $ext = Tools::strtolower(pathinfo($_FILES['custom_image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];
                    if (!in_array($ext, $allowed, true)) {
                        $output .= $this->displayError($this->l('Invalid image extension. Allowed: jpg, jpeg, png, gif, webp.'));
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm();
                    }

                    $imageName = sha1(uniqid('', true)).'.'.$ext;
                    $uploadDir = _PS_MODULE_DIR_.$this->name.'/uploads/';
                    if (!@move_uploaded_file($_FILES['custom_image']['tmp_name'], $uploadDir.$imageName)) {
                        $output .= $this->displayError($this->l('Failed to save uploaded image.'));
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm();
                    }
                }

                Db::getInstance()->insert('autocomplete_stock_custom', [
                    'title'      => pSQL($title),
                    'link'       => pSQL($link),
                    'alt'        => pSQL($alt),
                    'image'      => pSQL($imageName),
                    'position'   => 0,
                    'date_start' => $dateStartSQL,
                    'date_end'   => $dateEndSQL,
                    'id_category'=> $idCat ?: null,
                ]);

                $output .= $this->displayConfirmation($this->l('Custom entry added.'));
            }
        }

        // UPDATE (edit submit)
        if (Tools::isSubmit('submit_update_entry')) {
            $idEntry    = (int)Tools::getValue('id_entry');
            $title      = Tools::getValue('custom_title');
            $link       = Tools::getValue('custom_link');
            $alt        = Tools::getValue('custom_alt');
            $idCat      = (int)Tools::getValue('id_category');
            $dateStart  = Tools::getValue('custom_date_start');
            $dateEnd    = Tools::getValue('custom_date_end');

            $dateStartSQL = ($dateStart) ? date('Y-m-d H:i:00', strtotime($dateStart)) : null;
            $dateEndSQL   = ($dateEnd)   ? date('Y-m-d H:i:00', strtotime($dateEnd))   : null;

            $current = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'autocomplete_stock_custom` WHERE id_entry='.(int)$idEntry);

            if (!$current) {
                $output .= $this->displayError($this->l('Entry not found.'));
             } elseif (empty($link)) {
                $output .= $this->displayError($this->l('Link is required.'));
            } elseif (($title !== '' && !Validate::isCleanHtml($title)) || !$this->isValidLink($link)) {
                $output .= $this->displayError($this->l('Invalid data.'));
            } else {
                // handle optional new image with validation
                $newImageName = $current['image'];
                if (isset($_FILES['custom_image']) && is_uploaded_file($_FILES['custom_image']['tmp_name'])) {
                    $error = ImageManager::validateUpload(
                        $_FILES['custom_image'],
                        (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024
                    );
                    if ($error) {
                        $output .= $this->displayError($error);
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm($current);
                    }

                    $ext = Tools::strtolower(pathinfo($_FILES['custom_image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];
                    if (!in_array($ext, $allowed, true)) {
                        $output .= $this->displayError($this->l('Invalid image extension. Allowed: jpg, jpeg, png, gif, webp.'));
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm($current);
                    }

                    $uploadDir = _PS_MODULE_DIR_.$this->name.'/uploads/';
                    $tmpName = sha1(uniqid('', true)).'.'.$ext;
                    if (!@move_uploaded_file($_FILES['custom_image']['tmp_name'], $uploadDir.$tmpName)) {
                        $output .= $this->displayError($this->l('Failed to save uploaded image.'));
                        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm($current);
                    }

                    // remove old after new saved
                    if (!empty($current['image'])) {
                        $old = $uploadDir.$current['image'];
                        if (file_exists($old)) { @unlink($old); }
                    }
                    $newImageName = $tmpName;
                }

                Db::getInstance()->update('autocomplete_stock_custom', [
                    'title'      => pSQL($title),
                    'link'       => pSQL($link),
                    'alt'        => pSQL($alt),
                    'image'      => pSQL($newImageName),
                    'date_start' => $dateStartSQL,
                    'date_end'   => $dateEndSQL,
                    'id_category'=> $idCat ?: null,
                ], 'id_entry='.(int)$idEntry);

                $output .= $this->displayConfirmation($this->l('Custom entry updated.'));
            }
        }

        // if GET param edit is present, fetch entry for prefilled form
        $entryToEdit = null;
        if (Tools::getIsset('edit_custom_entry')) {
            $id = (int)Tools::getValue('edit_custom_entry');
            $row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'autocomplete_stock_custom` WHERE id_entry='.(int)$id);
            $entryToEdit = is_array($row) ? $row : null; // <- kluczowe: false => null
        }

        return $output . $this->renderSettingsForm() . $this->renderCustomEntriesForm($entryToEdit);
    }

    private function renderSettingsForm()
    {
        $out = '';

        if (Tools::isSubmit('submit_autocomplete_stock')) {
            $showCats = (int) Tools::getValue('ASTOCK_SHOW_CATEGORIES', 1);
            $cLimit   = max(0, (int) Tools::getValue('ASTOCK_CATEGORIES_LIMIT', 5));
            $showMan  = (int) Tools::getValue('ASTOCK_SHOW_MANUFACTURERS', 1);
            $mLimit   = max(0, (int) Tools::getValue('ASTOCK_MANUFACTURERS_LIMIT', 5));
            $pLimit   = max(1, (int) Tools::getValue('ASTOCK_PRODUCTS_LIMIT', 10));
            $onlyAv   = (int) Tools::getValue('ASTOCK_ONLY_AVAILABLE', 0);
            $minChars = max(1, (int) Tools::getValue('ASTOCK_MIN_CHARS', 1));
            $debounce = max(0, (int) Tools::getValue('ASTOCK_DEBOUNCE_MS', 150));
            $imgType  = (string) Tools::getValue('ASTOCK_IMAGE_TYPE', 'small_default');

            $validOptions = $this->getProductImageTypeOptions();
            $validIds = array_column($validOptions, 'id');
            if (!in_array($imgType, $validIds, true)) {
                $imgType = 'small_default';
            }

            Configuration::updateValue('ASTOCK_SHOW_CATEGORIES', $showCats);
            Configuration::updateValue('ASTOCK_CATEGORIES_LIMIT', $cLimit);
            Configuration::updateValue('ASTOCK_SHOW_MANUFACTURERS', $showMan);
            Configuration::updateValue('ASTOCK_MANUFACTURERS_LIMIT', $mLimit);
            Configuration::updateValue('ASTOCK_PRODUCTS_LIMIT', $pLimit);
            Configuration::updateValue('ASTOCK_ONLY_AVAILABLE', $onlyAv);
            Configuration::updateValue('ASTOCK_MIN_CHARS', $minChars);
            Configuration::updateValue('ASTOCK_DEBOUNCE_MS', $debounce);
            Configuration::updateValue('ASTOCK_IMAGE_TYPE', pSQL($imgType));

            $out .= $this->displayConfirmation($this->l('Settings saved.'));
        }

        $imageTypeOptions = $this->getProductImageTypeOptions();

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->name;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_autocomplete_stock';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->default_form_language = (int)$this->context->language->id;

        $fields_form = [
            'form' => [
                'legend' => ['title' => $this->l('Autocomplete settings')],
                'input' => [
                    ['type'=>'switch','label'=>$this->l('Show categories'),'name'=>'ASTOCK_SHOW_CATEGORIES',
                     'values'=>[['id'=>'on','value'=>1,'label'=>$this->l('Yes')],['id'=>'off','value'=>0,'label'=>$this->l('No')]]],
                    ['type'=>'text','label'=>$this->l('Categories limit'),'name'=>'ASTOCK_CATEGORIES_LIMIT','col'=>2],
                    ['type'=>'switch','label'=>$this->l('Show brands'),'name'=>'ASTOCK_SHOW_MANUFACTURERS',
                     'values'=>[['id'=>'on','value'=>1,'label'=>$this->l('Yes')],['id'=>'off','value'=>0,'label'=>$this->l('No')]]],
                    ['type'=>'text','label'=>$this->l('Brands limit'),'name'=>'ASTOCK_MANUFACTURERS_LIMIT','col'=>2],
                    ['type'=>'text','label'=>$this->l('Products limit'),'name'=>'ASTOCK_PRODUCTS_LIMIT','col'=>2],
                    ['type'=>'switch','label'=>$this->l('Only available (>0)'),'name'=>'ASTOCK_ONLY_AVAILABLE',
                     'values'=>[['id'=>'on','value'=>1,'label'=>$this->l('Yes')],['id'=>'off','value'=>0,'label'=>$this->l('No')]]],
                    ['type'=>'text','label'=>$this->l('Min chars to trigger'),'name'=>'ASTOCK_MIN_CHARS','col'=>2],
                    ['type'=>'text','label'=>$this->l('Debounce (ms)'),'name'=>'ASTOCK_DEBOUNCE_MS','col'=>2],
                    ['type'=>'select','label'=>$this->l('Image type'),'name'=>'ASTOCK_IMAGE_TYPE',
                     'options'=> ['query' => $imageTypeOptions, 'id'=>'id', 'name'=>'name']],
                ],
                'submit' => ['title' => $this->l('Save')],
            ],
        ];

        $helper->fields_value = [
            'ASTOCK_SHOW_CATEGORIES' => (int)Configuration::get('ASTOCK_SHOW_CATEGORIES'),
            'ASTOCK_CATEGORIES_LIMIT'=> (int)Configuration::get('ASTOCK_CATEGORIES_LIMIT'),
            'ASTOCK_SHOW_MANUFACTURERS'=> (int)Configuration::get('ASTOCK_SHOW_MANUFACTURERS'),
            'ASTOCK_MANUFACTURERS_LIMIT'=> (int)Configuration::get('ASTOCK_MANUFACTURERS_LIMIT'),
            'ASTOCK_PRODUCTS_LIMIT' => (int)Configuration::get('ASTOCK_PRODUCTS_LIMIT'),
            'ASTOCK_ONLY_AVAILABLE'=> (int)Configuration::get('ASTOCK_ONLY_AVAILABLE'),
            'ASTOCK_MIN_CHARS'     => (int)Configuration::get('ASTOCK_MIN_CHARS'),
            'ASTOCK_DEBOUNCE_MS'   => (int)Configuration::get('ASTOCK_DEBOUNCE_MS'),
            'ASTOCK_IMAGE_TYPE'    => Configuration::get('ASTOCK_IMAGE_TYPE'),
        ];

        return $out . $helper->generateForm([$fields_form]);
    }

    private function renderCustomEntriesForm(?array $entryToEdit = null)
    {
        $isEdit = (bool)$entryToEdit;

        // prefill values (edit) or empty (add)
        $valTitle = $isEdit ? $entryToEdit['title'] : '';
        $valLink  = $isEdit ? $entryToEdit['link']  : '';
        $valAlt   = $isEdit ? $entryToEdit['alt']   : '';
        $valCatId = $isEdit ? (int)$entryToEdit['id_category'] : 0;
        // convert SQL datetimes -> datetime-local (Y-m-d\TH:i)
        $valStart = ($isEdit && !empty($entryToEdit['date_start'])) ? date('Y-m-d\TH:i', strtotime($entryToEdit['date_start'])) : '';
        $valEnd   = ($isEdit && !empty($entryToEdit['date_end']))   ? date('Y-m-d\TH:i', strtotime($entryToEdit['date_end']))   : '';
        $imgUrl   = ($isEdit && !empty($entryToEdit['image'])) ? $this->_path.'uploads/'.rawurlencode($entryToEdit['image']) : '';

        $entries = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'autocomplete_stock_custom` ORDER BY id_entry DESC');

        // categories for select
        $catOptions = $this->getCategoryOptionsList();
        $optionsHtml = '';
        foreach ($catOptions as $opt) {
            $optionsHtml .= '<option value="'.(int)$opt['id'].'"'.($valCatId===(int)$opt['id']?' selected':'').'>'
                .htmlspecialchars($opt['name'], ENT_QUOTES, 'UTF-8').'</option>';
        }

        // —— FORM PANEL (ADD or EDIT) ——
        $html = '
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-picture"></i> '.($isEdit ? $this->l('Edit custom entry') : $this->l('Add custom entry')).'
            </div>

            <div class="panel-body">
                <form method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Title').'</label>
                                <div class="col-lg-9">
                                   <input type="text" name="custom_title" class="form-control" value="" placeholder="'.$this->l('Optional').'">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Link').'</label>
                                <div class="col-lg-9">
                                    <input type="url" name="custom_link" class="form-control" value="'.htmlspecialchars($valLink, ENT_QUOTES, 'UTF-8').'" required placeholder="https://... or /relative-url">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Alt text').'</label>
                                <div class="col-lg-9">
                                    <input type="text" name="custom_alt" class="form-control" value="'.htmlspecialchars($valAlt, ENT_QUOTES, 'UTF-8').'" placeholder="'.$this->l('Optional').'">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Assign to category').'</label>
                                <div class="col-lg-9">
                                    <select name="id_category" class="form-control">
                                        <option value="0">'.$this->l('— None —').'</option>
                                        '.$optionsHtml.'
                                    </select>
                                    <p class="help-block">'.$this->l('Optional: display this custom entry as recommended for the selected category.').'</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">';

        if ($isEdit && $imgUrl) {
            $html .= '
                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Current image').'</label>
                                <div class="col-lg-9">
                                    <img src="'.$imgUrl.'" style="max-width:140px;max-height:100px;border-radius:4px;">
                                    <p class="help-block">'.$this->l('Upload a new image to replace the current one (optional).').'</p>
                                </div>
                            </div>';
        }

        $html .= '
                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Image').'</label>
                                <div class="col-lg-9">
                                    <input type="file" name="custom_image" accept="image/*" '.($isEdit ? '' : 'required').'>
                                    <p class="help-block">'.$this->l('Recommended: JPG/PNG, ~300×200px.').'</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('Start date').'</label>
                                <div class="col-lg-9">
                                    <input type="datetime-local" name="custom_date_start" class="form-control" value="'.htmlspecialchars($valStart, ENT_QUOTES, 'UTF-8').'">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">'.$this->l('End date').'</label>
                                <div class="col-lg-9">
                                    <input type="datetime-local" name="custom_date_end" class="form-control" value="'.htmlspecialchars($valEnd, ENT_QUOTES, 'UTF-8').'">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        '.($isEdit ? '
                            <input type="hidden" name="id_entry" value="'.(int)$entryToEdit['id_entry'].'">
                            <button type="submit" name="submit_update_entry" class="btn btn-primary">
                                <i class="icon-save"></i> '.$this->l('Save changes').'
                            </button>
                            <a href="'.htmlspecialchars($this->getModuleConfigUrl(), ENT_QUOTES, 'UTF-8').'" class="btn btn-default">
                                <i class="icon-remove"></i> '.$this->l('Cancel').'
                            </a>
                        ' : '
                            <button type="submit" name="submit_custom_entry" class="btn btn-primary">
                                <i class="icon-plus"></i> '.$this->l('Add entry').'
                            </button>
                        ').'
                    </div>

                    <input type="hidden" name="configure" value="'.htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8').'">
                    <input type="hidden" name="token" value="'.Tools::getAdminTokenLite('AdminModules').'">
                </form>
            </div>
        </div>';

        // —— LIST PANEL ——
        $html .= '
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-list"></i> '.$this->l('Existing entries').'
            </div>
            <div class="panel-body">';

        if ($entries) {
            // pre-fetch category names for displayed entries
            $catIds = [];
            foreach ($entries as $e) {
                if (!empty($e['id_category'])) {
                    $catIds[(int)$e['id_category']] = true;
                }
            }
            $catNames = [];
            if (!empty($catIds)) {
                $ids = implode(',', array_map('intval', array_keys($catIds)));
                $rows = Db::getInstance()->executeS('
                    SELECT cl.id_category, cl.name
                    FROM '._DB_PREFIX_.'category_lang cl
                    INNER JOIN '._DB_PREFIX_.'category_shop cs ON cs.id_category=cl.id_category AND cs.id_shop='.(int)$this->context->shop->id.'
                    WHERE cl.id_lang='.(int)$this->context->language->id.' AND cl.id_category IN ('.$ids.')
                ');
                foreach ((array)$rows as $r) {
                    $catNames[(int)$r['id_category']] = $r['name'];
                }
            }

            $html .= '<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:100px;">'.$this->l('Image').'</th>
                            <th>'.$this->l('Title & Link').'</th>
                            <th style="width:220px;">'.$this->l('Category').'</th>
                            <th style="width:260px;">'.$this->l('Active range').'</th>
                            <th style="width:120px;">'.$this->l('Status').'</th>
                            <th style="width:180px;">'.$this->l('Actions').'</th>
                        </tr>
                    </thead>
                    <tbody>';

            $now = date('Y-m-d H:i:s');

            foreach ($entries as $entry) {
                $imgUrlRow = !empty($entry['image']) ? $this->_path.'uploads/'.rawurlencode($entry['image']) : '';
                $range = ($entry['date_start'] ? $entry['date_start'] : '—').' &rarr; '.($entry['date_end'] ? $entry['date_end'] : '—');

                $isActive =
                    (empty($entry['date_start']) || $entry['date_start'] <= $now) &&
                    (empty($entry['date_end'])   || $entry['date_end']   >= $now);

                $badge = $isActive
                    ? '<span class="label label-success"><i class="icon-check"></i> '.$this->l('Active').'</span>'
                    : '<span class="label label-default"><i class="icon-time"></i> '.$this->l('Inactive').'</span>';

                $editUrl = $this->getModuleConfigUrl(['edit_custom_entry' => (int)$entry['id_entry']]);

                $catName = '';
                if (!empty($entry['id_category'])) {
                    $cid = (int)$entry['id_category'];
                    $catName = isset($catNames[$cid]) ? $catNames[$cid] : $this->resolveCategoryName($cid);
                }

                $html .= '<tr>
                    <td>'.($imgUrlRow ? '<img src="'.$imgUrlRow.'" style="max-width:90px;max-height:70px;border-radius:4px;">' : '<span class="text-muted">—</span>').'</td>
                    <td>
                        <div><strong>'.htmlspecialchars($entry['title'], ENT_QUOTES, 'UTF-8').'</strong></div>
                        <div><a href="'.htmlspecialchars($entry['link'], ENT_QUOTES, 'UTF-8').'" target="_blank">'.htmlspecialchars($entry['link'], ENT_QUOTES, 'UTF-8').'</a></div>
                    </td>
                    <td>'.($catName ? htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">—</span>').'</td>
                    <td>'.$range.'</td>
                    <td>'.$badge.'</td>
                    <td>
                        <a href="'.htmlspecialchars($editUrl, ENT_QUOTES, 'UTF-8').'" class="btn btn-default btn-sm">
                            <i class="icon-pencil"></i> '.$this->l('Edit').'
                        </a>
                        <form method="post" style="display:inline" onsubmit="return confirm(\''.$this->l('Are you sure?').'\')">
                            <input type="hidden" name="id_entry" value="'.(int)$entry['id_entry'].'">
                            <input type="hidden" name="configure" value="'.htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8').'">
                            <input type="hidden" name="token" value="'.Tools::getAdminTokenLite('AdminModules').'">
                            <button type="submit" name="delete_custom_entry" class="btn btn-danger btn-sm">
                                <i class="icon-trash"></i> '.$this->l('Delete').'
                            </button>
                        </form>
                    </td>
                </tr>';
            }

            $html .= '</tbody></table></div>';
        } else {
            $html .= '<p class="text-muted"><i class="icon-info-circle"></i> '.$this->l('No custom entries yet. Add your first one above.').'</p>';
        }

        $html .= '</div></div>';

        return $html;
    }
}
