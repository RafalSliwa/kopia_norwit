<?php

class AdminExportCategoriesController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'export_link' => $this->context->link->getAdminLink('AdminExportCategories') . '&export_categories=1',
            'mod' => $this->module,
        ));

        $this->setTemplate('export_categories.tpl');

    }

    public function postProcess()
    {
        if (Tools::isSubmit('export_categories')) {
            $this->exportCategoriesToCSV();
        }
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


    private function sanitizeFileName($filename)
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
    }

    private function displayTestMessage()
    {
        return '<h1>Test</h1>';
    }

}
