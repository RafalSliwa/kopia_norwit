<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class RelatedOverrides
{
    public function __construct($module)
    {
        $this->module = $module;
        $module_path = _PS_MODULE_DIR_ . $this->module->name . '/';
        $this->custom_dir = $module_path . 'override_files/';
        $this->native_dir = $module_path . 'override/';
    }

    public function getData($extended = false)
    {
        $data = [];
        foreach (Tools::scandir($this->custom_dir, 'php', '', true) as $file) {
            $class_name = basename($file, '.php');
            if ($class_name != 'index') {
                $path = $this->getPath($class_name);
                $installed = $this->isInstalled($path);
                $data[$class_name] = [
                    'class_name' => $class_name,
                    'path' => $path,
                    'installed' => $this->isInstalled($path),
                    'note' => $this->getNote($class_name, $installed),
                ];
            }
        }

        return $data;
    }

    public function getNote($class_name, $installed)
    {
        $note = '';
        switch ($class_name) {
            case 'Product':
                if ($this->module->accessoriesDisplayed()) {
                    $note = 'Required to replace native accessories block on product page';
                } else {
                    $note = 'Not required';
                    if ($installed === true) {
                        $note .= '. You can safely uninstall it';
                    }
                }
                break;
        }

        return $note;
    }

    public function getPath($class_name)
    {
        if (empty($this->ps_autoload)) {
            $this->ps_autoload = PrestaShopAutoload::getInstance();
        }

        return $this->ps_autoload->getClassPath($class_name . 'Core');
    }

    public function isInstalled($file_path)
    {
        $shop_override_path = _PS_OVERRIDE_DIR_ . $file_path;
        $module_override_path = $this->custom_dir . $file_path;
        $methods_to_override = $already_overriden = [];
        if (file_exists($module_override_path)) {
            $lines = file($module_override_path);
            foreach ($lines as $line) {
                if (Tools::substr(trim($line), 0, 6) == 'public') { // NOTE: works only for public functions
                    $key = trim(current(explode('(', $line)));
                    $methods_to_override[$key] = 0;
                }
            }
        }
        $name_length = Tools::strlen($this->module->name);
        if (file_exists($shop_override_path)) {
            $lines = file($shop_override_path);
            foreach ($lines as $i => $line) {
                if (Tools::substr(trim($line), 0, 6) == 'public') {
                    $key = trim(current(explode('(', $line)));
                    if (isset($methods_to_override[$key])) {
                        unset($methods_to_override[$key]);
                        // check comments above the overriden method
                        if (!isset($lines[$i - 4])
                            || Tools::substr(trim($lines[$i - 4]), -$name_length) !== $this->module->name) {
                            $key = explode('function ', $key);
                            if (isset($key[1])) {
                                $already_overriden[] = $key[1] . '()';
                            }
                        }
                    }
                }
            }
        }
        $result = (bool) !$methods_to_override;
        if ($already_overriden) {
            $result = implode(', ', $already_overriden);
        }

        return $result;
    }

    public function process($action, $class_name)
    {
        if ($result = in_array($action, ['addOverride', 'removeOverride'])) {
            $file_path = $this->getPath($class_name);
            $custom_path = $this->custom_dir . $file_path;
            $tmp_native_path = $this->native_dir . $file_path;
            if ($result &= file_exists($custom_path)) {
                if ($result &= is_writable(dirname($tmp_native_path))) {
                    try {
                        $code = Tools::file_get_contents($custom_path);
                        $code = str_replace("if (!defined('_PS_VERSION_')) {\n    exit;\n}\n\n", '', $code);
                        file_put_contents($tmp_native_path, $code); // copy to /override/ dir for processing natively
                        $result &= $this->module->$action($class_name);
                    } catch (Exception $e) {
                        $result = $e->getMessage();
                    }
                    unlink($tmp_native_path);
                } else {
                    $dir_name = str_replace(_PS_ROOT_DIR_, '', dirname($tmp_native_path)) . '/';
                    $result = 'Make sure the following directory is writable: ' . $dir_name;
                }
            }
        }

        return $result;
    }
}
