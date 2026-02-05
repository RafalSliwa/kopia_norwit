<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Sitemap
{
    private $writer;
    private $include_links = false;
    private $domain;
    private $path;
    public $filename = 'sitemap';
    private $current_item = 0;
    private $current_sitemap = 0;

    const EXT = '.xml';
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    const SCHEMA_IMAGE = 'http://www.google.com/schemas/sitemap-image/1.1';
    const DEFAULT_PRIORITY = 0.5;
    const ITEM_PER_SITEMAP = 10000;
    const SEPERATOR = '-';
    const INDEX_SUFFIX = 'index';

    public function __construct($domain)
    {
        $this->setDomain($domain);
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    private function getDomain()
    {
        return $this->domain;
    }

    public function setIncludeLinks($value)
    {
        $this->include_links = $value;
        return $this;
    }

    private function getIncludeLinks()
    {
        return $this->include_links;
    }

    private function getWriter()
    {
        return $this->writer;
    }

    private function setWriter(XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    private function getCurrentItem()
    {
        return $this->current_item;
    }

    private function incCurrentItem()
    {
        $this->current_item = $this->current_item + 1;
    }

    private function getCurrentSitemap()
    {
        return $this->current_sitemap;
    }

    private function incCurrentSitemap()
    {
        $this->current_sitemap = $this->current_sitemap + 1;
    }

    private function startSitemap($only = 0)
    {
        if ($only == 1) {
            $name_text = '-only-product';
        } elseif ($only == 2) {
            $name_text = '-only-category';
        } else {
            $name_text = '';
        }

        $this->setWriter(new XMLWriter());
        if ($this->getCurrentSitemap()) {
            $this->getWriter()->openURI($this->getPath() . $this->getFilename() . $name_text . self::SEPERATOR . $this->getCurrentSitemap() . self::EXT);
        } else {
            $this->getWriter()->openURI($this->getPath() . $this->getFilename() . $name_text . self::EXT);
        }
        $this->getWriter()->startDocument('1.0', 'UTF-8');
        $this->getWriter()->setIndent(true);
        $this->getWriter()->startElement('urlset');
        $this->getWriter()->writeAttribute('xmlns', self::SCHEMA);
        $this->getWriter()->writeAttribute('xmlns:image', self::SCHEMA_IMAGE);
        if ($this->getIncludeLinks()) {
            $this->getWriter()->writeAttribute('xmlns:xhtml', 'https://www.w3.org/1999/xhtml');
        }
    }

    public function addItem($loc, $priority = self::DEFAULT_PRIORITY, $changefreq = null, $lastmod = null, $images =
    [], $links = [], $without_domain = false, $page_qty = 0, $only = 0)
    {
        if ($this->getCurrentItem() == 0) {
            if ($this->getWriter() instanceof XMLWriter) {
                $this->endSitemap();
            }
            $this->startSitemap($only);
            $this->incCurrentSitemap();
        } elseif ($page_qty == 0) {
            if ($this->getWriter() instanceof XMLWriter) {
                $this->endSitemap();
            }
            $this->startSitemap($only);
            $this->incCurrentSitemap();
        }
        $this->incCurrentItem();
        $this->getWriter()->startElement('url');
        $this->getWriter()->writeElement('loc', (!$without_domain ? $this->getDomain() : '') . $loc);
        $this->getWriter()->writeElement('priority', $priority);
        if ($changefreq) {
            $this->getWriter()->writeElement('changefreq', $changefreq);
        }
        if ($lastmod) {
            $this->getWriter()->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
        }
        if (count($links) && $this->getIncludeLinks()) {
            foreach ($links as $lang => $link) {
                $this->getWriter()->startElement('xhtml:link');
                $this->getWriter()->writeAttribute('rel', 'alternate');
                $this->getWriter()->writeAttribute('hreflang', $lang);
                $this->getWriter()->writeAttribute('href', $this->getDomain() . $link);
                $this->getWriter()->endElement();
            }
        }

        if (is_array($images) && count($images)) {
            foreach ($images as $image) {
                $params = ['loc', 'caption', 'geo_location', 'title', 'license'];
                $this->getWriter()->startElement('image:image');
                foreach ($params as $param) {
                    if (isset($image[$param])) {
                        $this->getWriter()->writeElement('image:' . $param, $image[$param]);
                    }
                }
                $this->getWriter()->endElement();
            }
        }

        $this->getWriter()->endElement();
        return $this;
    }

    private function getLastModifiedDate($date)
    {
        if (ctype_digit($date)) {
            return date('Y-m-d', $date);
        } else {
            $date = strtotime($date);
            return date('Y-m-d', $date);
        }
    }

    private function endSitemap()
    {
        if (!$this->getWriter()) {
            $this->startSitemap();
        }
        $this->getWriter()->endElement();
        $this->getWriter()->endDocument();
    }

    public function createSitemapIndex($loc, $lastmod = 'Today', $only = 0)
    {
        if ($only == 1) {
            $name_text = '-only-product';
        } elseif ($only == 2) {
            $name_text = '-only-category';
        } else {
            $name_text = '';
        }
        $this->endSitemap();
        $indexwriter = new XMLWriter();
        $indexwriter->openURI($this->getPath() . $this->getFilename() . $name_text . self::SEPERATOR . self::INDEX_SUFFIX . self::EXT);
        $indexwriter->startDocument('1.0', 'UTF-8');
        $indexwriter->setIndent(true);
        $indexwriter->startElement('sitemapindex');
        $indexwriter->writeAttribute('xmlns', self::SCHEMA);
        $indexwriter->writeAttribute('xmlns:image', self::SCHEMA_IMAGE);
        $indexwriter->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        for ($index = 0; $index < $this->getCurrentSitemap(); ++$index) {
            $indexwriter->startElement('sitemap');
            $indexwriter->writeElement(
                'loc',
                $loc . $this->getFilename() . $name_text . ($index ? self::SEPERATOR . $index : '') . self::EXT
            );
            $indexwriter->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
            $indexwriter->endElement();
        }
        $indexwriter->endElement();
        $indexwriter->endDocument();
    }
}
