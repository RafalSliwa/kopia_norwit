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

class CronManager
{
    public function getCurrentChunkNo($feedId)
    {
        $list = $this->getAllChunks();

        return !empty($list[$feedId]) ? $list[$feedId] : 0;
    }

    public function updateChunkNo($feedId, $productsTotal)
    {
        $list = $this->getAllChunks();
        $list[$feedId] = $productsTotal;

        return ConfigurationBlx::update('BLMOD_XML_FEED_CRON_CHUNKS', json_encode($list));
    }

    public function getAllChunks()
    {
        $list = ConfigurationBlx::get('BLMOD_XML_FEED_CRON_CHUNKS');

        if (empty($list)) {
            return [];
        }

        return json_decode($list, true);
    }

    public function resetChunkFile($feedId)
    {
        $feedMeta = new FeedMeta();
        $feedMetaValues = $feedMeta->getFeedMeta($feedId);

        $this->updateChunkNo($feedId, 0);

        $cronFileName = !empty($feedMetaValues['cron_file_name']) ? $feedMetaValues['cron_file_name'].'.xml' : 'feed_'.$feedId.'.xml';
        $filePath = _PS_ROOT_DIR_.'/modules/xmlfeeds/xml_files/chunk_'.$cronFileName;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
