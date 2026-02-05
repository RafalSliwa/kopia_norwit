<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-2024 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
require_once _PS_MODULE_DIR_ . 'seoredirect/seoredirect.php';
require_once _PS_MODULE_DIR_ . 'seoredirect/models/seoRedirectList.php';

class AdminSeoRedirectImportController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->className = 'Configuration';
        $this->table = 'configuration';
        parent::__construct();
    }

    public function insert_seoredirect($array)
    {
        $seoRedirectList = new seoRedirectList();
        $seoRedirectList->old = trim($array['old']);
        $seoRedirectList->new = trim($array['new']);
        $seoRedirectList->redirect_type = (isset($array['redirect_type']) ? $array['redirect_type']:'301');
        $seoRedirectList->active = 1;
        $seoRedirectList->regexp = (isset($array['regexp']) ? trim($array['regexp']):0);
        $seoRedirectList->wildcard = (isset($array['wildcard']) ? trim($array['wildcard']):0);
        $seoRedirectList->id_shop = Context::getContext()->shop->id;
        $seoRedirectList->add();
    }

    public function initContent()
    {
        $output = '';
        if (Tools::isSubmit('upload_csv'))
        {
            $plik_tmp = $_FILES['upload_csv']['tmp_name'];
            $plik_nazwa = $_FILES['upload_csv']['name'];
            $plik_rozmiar = $_FILES['upload_csv']['size'];
            if (is_uploaded_file($plik_tmp))
            {
                $date = date("Y-m-d-h-i-s");
                if (move_uploaded_file($plik_tmp, '..' . '/modules/seoredirect/' . "$date.csv"))
                {
                }
            }
        }
        if (Tools::isSubmit('save_voucher_settings'))
        {
            Configuration::updateValue('IV_ROW_DELIMITER', "{$_POST['iv_row_delimiter']}");
            Configuration::updateValue('IV_COL_DELIMITER', "{$_POST['iv_col_delimiter']}");
        }
        if (Tools::isSubmit('delete_csv_file'))
        {
            if (file_exists("../modules/seoredirect/" . $_POST['fcsv']))
            {
                unlink("../modules/seoredirect/" . $_POST['fcsv']);
            }
        }
        if (Tools::isSubmit('importcsv'))
        {
            $file = file_get_contents("../modules/seoredirect/{$_POST['importfile']}");
            $exp = null;
            if (Configuration::get('IV_ROW_DELIMITER') == '\n')
            {
                $exp = explode("\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r')
            {
                $exp = explode("\r", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r\n')
            {
                $exp = explode("\r\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\n\r')
            {
                $exp = explode("\r\n", $file);
            }
            $rows = '<table class="table table-bordered" ><form action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="filename" value="' . $_POST['importfile'] . '"/>';
            if (count($exp) > 0)
            {
                foreach ($exp as $key => $value)
                {
                    $first = "1";
                    $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                    $rows .= "<tr><td>add</td>";
                    foreach ($exprow as $id => $val)
                    {
                        $rows .= "<td>" . $this->generateselect($id) . "</td>";
                    }
                    $rows .= "</tr>";
                    if ($first == 1)
                    {
                        break;
                    }
                }
            }
            if (count($exp) > 0)
            {
                foreach ($exp as $key => $value)
                {
                    if (strlen($value) > 1)
                    {
                        $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                        $rows .= "<tr>";
                        $rows .= "<td><input type=\"checkbox\" checked=\"yes\" value=\"1\" name=\"add$key\"></td>";
                        foreach ($exprow as $id => $val)
                        {
                            $rows .= "<td>$val</td>";
                        }
                        $rows .= "</tr>";
                    }
                }
            }
            $rows .= "</table>";

            $rows.='
            <div class="panel-footer">
                <button type="submit" value="1" id="seor_form_submit_btn" name="submit_vouchers" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> '.$this->module->getTranslator()->trans('Import', [], 'Modules.Seoredirect.Import').'
                </button>
            </div>';
            $rows.='</form>';
            $output .= "<div class=\"bootstrap\" ><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->module->getTranslator()->trans('Loaded to import', [], 'Modules.Seoredirect.Import') . "</div></div>";
            $output .= '<div>
                <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            	<fieldset style="">
                <div class="alert alert-info">
                    '.$this->module->getTranslator()->trans('Old URL can be absolute path like: ', [], 'Modules.Seoredirect.Import').' <strong>http://'.$_SERVER['HTTP_HOST'].'/example/5-product.html</strong> '.$this->module->getTranslator()->trans('or relative path like: ', [], 'Modules.Seoredirect.Import').'<strong>/example/5-product.html</strong>
                </div>
                ' . $rows . '
                </fieldset>
                </form>
            </div>';
            $before_output ="<div class=\"panel\">";
            $before_output.= '<div class="panel-heading"><i class="icon-wrench"></i> '.$this->module->getTranslator()->trans('import file: ', [], 'Modules.Seoredirect.Import') . $_POST['importfile']."</div>";
            $after_output ="</div>";
            $this->context->smarty->assign('form_import', $before_output.$output.$after_output);
        }
        if (Tools::isSubmit('submit_vouchers'))
        {
            $file = file_get_contents("../modules/seoredirect/{$_POST['filename']}");
            if (Configuration::get('IV_ROW_DELIMITER') == '\n')
            {
                $exp = explode("\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r')
            {
                $exp = explode("\r", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r\n')
            {
                $exp = explode("\r\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\n\r')
            {
                $exp = explode("\r\n", $file);
            }
            $columns = "";
            foreach ($exp as $key => $value)
            {
                $first = 1;
                $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                foreach ($exprow as $id => $val)
                {
                    ${"col" . $id} = $_POST["col" . "$id"];
                    if (!(${"col" . $id} == "skip"))
                    {
                        $columns .= ${"col" . $id} . ",";
                    }
                }
                if ($first == 1)
                {
                    break;
                }
            }
            $columns = substr($columns, 0, -1);
            foreach ($exp as $key => $value)
            {
                if (isset($_POST["add" . "$key"]))
                {
                    $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                    $values = "";
                    foreach ($exprow as $id => $val)
                    {
                        ${"col" . $id} = $_POST["col" . "$id"];
                        if (!(${"col" . $id} == "skip"))
                        {
                            $values .= "'$val',";
                            $array_values[${"col" . "$id"}] = $val;
                        }
                    }
                    $values = substr($values, 0, -1);
                    $this->insert_seoredirect($array_values);
                }
            }
            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->module->getTranslator()->trans('added to database', [], 'Modules.Seoredirect.Import') . "</div></div>";
        }
        $form_settings = '
            <div class="alert alert-info">
            '.$this->module->getTranslator()->trans('In a comma-separated values file (CSV) the data items are separated using special characters called delimiters. Delimiters are used to distinct new lines (row delimiter) and values inside dataset (column delimiter)', [], 'Modules.Seoredirect.Import').'
            </div>
                <label class="">' . $this->module->getTranslator()->trans('ROW delimiter', [], 'Modules.Seoredirect.Import') . '</label>
            	<div class="margin-form">
            	<input type="text" name="iv_row_delimiter" value="' . Configuration::get('IV_ROW_DELIMITER') . '">
                <p class="clear">' . $this->module->getTranslator()->trans('setup the char for row delimiter (line)', [], 'Modules.Seoredirect.Import') . '<br/>\n ' . $this->module->getTranslator()->trans('new line in windows', [], 'Modules.Seoredirect.Import') . '<br/>\r ' . $this->module->getTranslator()->trans('new line in unix', [], 'Modules.Seoredirect.Import') . '</p>
            	</div>
                <label class="">' . $this->module->getTranslator()->trans('COLUMN delimiter', [], 'Modules.Seoredirect.Import') . '</label>
            	<div class="margin-form">
            	<input type="text" name="iv_col_delimiter" value="' . Configuration::get('IV_COL_DELIMITER') . '">
                <p class="clear">' .
            $this->module->getTranslator()->trans('setup the char for column delimiter (variables in line)', [], 'Modules.Seoredirect.Import')
            . '. ' .
            $this->module->getTranslator()->trans('Usually comma (,) or semicolon (;)', [], 'Modules.Seoredirect.Import') . '</p>
            	</div>';
        $this->context->smarty->assign('form_settings', $form_settings);

        $module = new seoredirect();
        if ($module->psversion() != 5)
        {
            $this->initPageHeaderToolbar();
        }
        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'lite_display' => false,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => true,
            'page_header_toolbar_title' => true,
            'title' => 'Import redirections from CSV file',
            'toolbar_btn' => [],
            'psver' => $module->psversion(1),
        ));
        $this->content = $this->displayForm();
        $this->context->smarty->assign(array('content' => $this->content));
    }

    public function generateselect($colid)
    {
        $form = '
        <SELECT name="col' . $colid . '" style="font-size:12px; max-width:200px;">
        <option value="skip">skip column</option>
        <option value="old">' . $this->module->getTranslator()->trans('old URL', [], 'Modules.Seoredirect.Import') . '</option>
        <option value="new">' . $this->module->getTranslator()->trans('new URL', [], 'Modules.Seoredirect.Import') . '</option>
        <option value="redirect_type">' . $this->module->getTranslator()->trans('redirect type', [], 'Modules.Seoredirect.Import') . '</option>
        <option value="regexp">' . $this->module->getTranslator()->trans('Regexp', [], 'Modules.Seoredirect.Import') . '</option>
        <option value="wildcard">' . $this->module->getTranslator()->trans('Wildcard', [], 'Modules.Seoredirect.Import') . '</option>
        </SELECT>';
        return $form;
    }

    public function getCsvFiles()
    {
        $dir = opendir('..' . '/modules/seoredirect/');
        $count = 0;
        while (false !== ($file = readdir($dir)))
        {
            if (($file == ".") || ($file == ".."))
            {
            }
            else
            {
                if (preg_match('@(.*)\.(csv)@i', $file))
                {
                    $filesarray[$count]['name'] = $file;
                    $count++;
                }
            }
        }
        $csvfiles = "";
        if (isset($filesarray))
        {
            if (count($filesarray) > 0)
            {
                foreach ($filesarray as $key => $value)
                {
                    $csvfiles = $csvfiles . '<div style="text-align:center; display:inline-block; padding:5px 10px; background:#FFF; border:1px solid #c0c0c0; margin-right:10px;"><a href="' . _PS_BASE_URL_ . _MODULE_DIR_ . 'seoredirect/' . $value['name'] . '" target="_blank" style="margin-bottom:10px; display:block; "><strong>' . $value['name'] . '</strong></a><form action="' . $_SERVER['REQUEST_URI'] . '" method="post" enctype="multipart/form-data"><input type="hidden" name="importfile" value="' . $value['name'] . '"><input type="submit" value="' . $this->module->getTranslator()->trans('Import to database', [], 'Modules.Seoredirect.Import') . '" name="importcsv" class="button"/></form><form action="' . $_SERVER['REQUEST_URI'] . '" style="margin-top:10px" method="post" enctype="multipart/form-data"><input type="hidden" name="fcsv" value="' . $value['name'] . '"><input type="submit" value="' . $this->module->getTranslator()->trans('Delete', [], 'Modules.Seoredirect.Import') . '" name="delete_csv_file" class="button"/></form></div>';
                }
            }
            else
            {
                $csvfiles = $this->module->getTranslator()->trans('No Files', [], 'Modules.Seoredirect.Import');
            }
        }
        else
        {
            $csvfiles = $this->module->getTranslator()->trans('No Files', [], 'Modules.Seoredirect.Import');
        }
        return $csvfiles;
    }

    public function displayForm()
    {
        $csvfiles = $this->getCsvFiles();
        $this->context->smarty->assign('csvfiles', $csvfiles);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/adminSeoRedirectImport.tpl');
    }
}