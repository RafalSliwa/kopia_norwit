<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Pkol_eksport_kategorii_presta extends Module
{
    public function __construct()
    {
        $this->name = 'pkol_eksport_kategorii_presta';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PKO Leasing';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
        
        $this->controllers = array('AdminExportCategories');
        $this->displayName = $this->l('Eksport kategorii');
        $this->description = $this->l('Moduł do eksportu kategorii do pliku z PrestaShop.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHookIfNotExists('displayBackOfficeHeader') || !$this->addAdminTab()) {
            return false;
        }
        return true;
    }

    private function registerHookIfNotExists($hook_name)
    {
        if (!Hook::getIdByName($hook_name)) {
            return $this->registerHook($hook_name);
        }
        return true;
    }

    public function uninstall()
    {
        if (!$this->removeAdminTab() || !parent::uninstall()) {
            return false;
        }
        return true;
    }

    private function addAdminTab()
    {
        try {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminExportCategories';
            $tab->name = array();

            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = 'Eksport kategorii';
            }

            $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
            $tab->module = $this->name;

            if (!$tab->add()) {
                PrestaShopLogger::addLog('Błąd podczas dodawania zakładki.', 3);
                return false;
            }

            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog('Błąd podczas dodawania zakładki: ' . $e->getMessage(), 3);
            return false;
        }
    }

    private function removeAdminTab()
    {
        try {
            $id_tab = Tab::getIdFromClassName('AdminExportCategories');
            if ($id_tab) {
                $tab = new Tab($id_tab);
                if (!$tab->delete()) {
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog('Błąd podczas usuwania zakładki: ' . $e->getMessage(), 3);
            return false;
        }
    }

    public function getContent()
    {
        $token = Tools::getAdminTokenLite('AdminModules');

        if (Tools::isSubmit('export_categories')) {
            $this->exportCategoriesToCSV();
        }

        $html = '<h2>' . $this->displayName . '</h2>';
        $html .= '<p>' . $this->description . '</p>';
        $html .= '
        <form action="' . AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . $token . '" method="post">
            <button type="submit" name="export_categories" class="btn btn-primary">' . $this->l('Eksportuj kategorie do CSV') . '</button>
        </form>';

        return $html;
    }

    private function exportCategoriesToCSV()
    {
        $id_lang = Context::getContext()->language->id;
        $categories = Category::getCategories(false, false, false);
        $current_datetime = date('YmdHis');
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $filename = 'Kategorie_' . $this->sanitizeFileName($shop_name) . '_' . $current_datetime . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename=' . $filename);

        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID kategorii', 'Nazwa kategorii', 'ID nadrzędnej kategorii', 'Nazwa nadrzędnej kategorii', 'Poziom zagnieżdżenia'), ';');

        foreach ($categories as $category) {
            $category_object = new Category($category['id_category'], $id_lang);
            $category_name = $category_object->name;

            $parent_category_object = new Category($category_object->id_parent, $id_lang);
            $parent_category_name = $parent_category_object->name;
            $parent_category_id = $category_object->id_parent;

            $level_depth = $category_object->level_depth;

            fputcsv($output, array(
                $category['id_category'],
                $category_name,
                $parent_category_id,
                $parent_category_name,
                $level_depth
            ), ';');
        }

        fclose($output);
        exit;
    }


    public function sanitizeFileName($filename)
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
    }
}
