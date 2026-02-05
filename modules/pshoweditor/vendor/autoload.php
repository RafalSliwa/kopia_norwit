<?php

if (PHP_VERSION_ID >= 70100 && function_exists('ioncube_loader_version')) {
    require_once dirname(__FILE__) . "/autoload_.php";
    return;
}

require_once dirname(__FILE__) . "/functions.php";
require_once dirname(__FILE__) . "/classes.php";

spl_autoload_register(function ($classFullName) {
    if (class_exists($classFullName, false)) {
        return;
    }

    if (
        stripos($classFullName, 'Prestashow\\') !== 0
        && stripos($classFullName, 'PShow') !== 0
    ) {
        return;
    }

    $className = explode('\\', $classFullName);
    $className = end($className);

    if ($className === 'AbstractModule' || $className === 'Module') {
        class_alias(__AbstractModule::class, $classFullName);
        return;
    }

    if (
        $className === 'AbstractAdminController'
        || substr($className, -10) == 'Controller'
    ) {
        if (class_exists('__AbstractAdminController')) {
            class_alias(__AbstractAdminController::class, $classFullName);
            return;
        }
    }

    @class_alias(__GenericClass::class, $classFullName);
});
