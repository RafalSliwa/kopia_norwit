<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

if (!defined('_PS_VERSION_')) {exit;}

include_once(dirname(__FILE__) . '/../../jprestaspeedpack.php');

class AdminPageCacheMemcacheTestController extends ModuleAdminController
{
    public $php_self = null;

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        header('Access-Control-Allow-Origin: *');

        parent::initContent();

        $host = Tools::getValue('host', '');
        $port = (int)Tools::getValue('port', '');
        $memcache = new PageCacheCacheMemcache($host, $port);
        $result = array(
            'host' => $host,
            'port' => $port,
            'status' => $memcache->isConnected() ? 1 : 0,
            'comments' => $memcache->isConnected() ? 'Server version ' . $memcache->getVersion() : error_get_last()['message']
        );
        die(json_encode($result));
    }
}
