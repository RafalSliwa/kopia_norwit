<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class XmlfeedsApiModuleFrontController
 *
 * /index.php?fc=module&module=xmlfeeds&controller=api
 * /module/xmlfeeds/api
 */
class XmlfeedsApiModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        require_once(dirname(__FILE__).'/../../api/xml.php');
    }
}
