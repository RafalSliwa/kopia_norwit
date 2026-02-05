<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ETS_CFU_Pagination
{
    public $total = 0;
    public $page = 1;
    public $limit = 20;
    public $num_links = 10;
    public $url = '';
    public $text = 'Showing {start} to {end} of {total} ({pages} Page(s))';
    public $text_first = '<<';
    public $text_last = '>>';
    public $text_next = '>';
    public $text_prev = '<';
    public $style_links = 'links';
    public $style_results = 'results';
    public $alias;
    public $friendly;
    public $controller;

    public function __construct()
    {
        $this->alias = Configuration::get('YBC_BLOG_ALIAS');
        $this->friendly = (int)Configuration::get('YBC_BLOG_FRIENDLY_URL') && (int)Configuration::get('PS_REWRITING_SETTINGS');
        $this->text_first = ETS_CFU_Tools::displayText('|&lt;', 'span');
        $this->text_last = ETS_CFU_Tools::displayText('&gt;|', 'span');
        $this->text_next = ETS_CFU_Tools::displayText('&gt;', 'span');
        $this->text_prev = ETS_CFU_Tools::displayText('&lt;', 'span');
    }

    public function render()
    {

        $total = $this->total;
        if ($total <= 1)
            return false;
        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!(int)$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $output = '';

        if ($page > 1) {
            $output .= ' ' . ETS_CFU_Tools::displayText($this->text_first, 'a', ['class' => 'frist', 'href' => $this->replacePage(1)]) . ' ' . ETS_CFU_Tools::displayText($this->text_prev, 'a', ['class' => 'prev', 'href' => $this->replacePage($page - 1)]);
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }
            }

            if ($start > 1) {
                $output .= ' .... ';
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= ' ' . ETS_CFU_Tools::displayText($i, 'b');
                } else {
                    $output .= ' ' . ETS_CFU_Tools::displayText($i, 'a', ['href' => $this->replacePage($i)]);
                }
            }

            if ($end < $num_pages) {
                $output .= ' .... ';
            }
        }

        if ($page < $num_pages) {
            $output .= ' ' . ETS_CFU_Tools::displayText($this->text_next, 'a', ['class' => 'next', 'href' => $this->replacePage($page + 1)]) . ' ' . ETS_CFU_Tools::displayText($this->text_last, 'a', ['class' => 'last', 'href' => $this->replacePage($num_pages)]);
        }

        $find = array(
            '{start}',
            '{end}',
            '{total}',
            '{pages}'
        );

        $replace = array(
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            $num_pages
        );

        return ($output ? ETS_CFU_Tools::displayText($output, 'div', ['class' => $this->style_links]) : '') . ETS_CFU_Tools::displayText(str_replace($find, $replace, $this->text), 'div', ['class' => $this->style_results]);
    }

    public function render2()
    {

        $total = $this->total;
        if ($total <= 1)
            return false;
        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!(int)$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $output = '';

        if ($page > 1) {
            $output .= ' ' . ETS_CFU_Tools::displayText($this->text_first, 'a', ['class' => 'frist', 'href' => $this->replacePage(1)]) . ' ' . ETS_CFU_Tools::displayText($this->text_prev, 'a', ['class' => 'pref', 'href' => $this->replacePage($page - 1)]);
        }

        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }
            }

            if ($start > 1) {
                $output .= ' .... ';
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= ' ' . ETS_CFU_Tools::displayText($i, 'b');
                } else {
                    $output .= ' ' . ETS_CFU_Tools::displayText($i, 'a', ['href' => $this->replacePage($i)]);
                }
            }

            if ($end < $num_pages) {
                $output .= ' .... ';
            }
        }

        if ($page < $num_pages) {
            $output .= ' ' . ETS_CFU_Tools::displayText($this->text_next, 'a', ['class' => 'next', 'href' => $this->replacePage($page + 1)]) . ' ' . ETS_CFU_Tools::displayText($this->text_last, 'a', ['class' => 'last', 'href' => $this->replacePage($num_pages)]);
        }

        $find = array(
            '{start}',
            '{end}',
            '{total}',
            '{pages}'
        );

        $replace = array(
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            $num_pages
        );

        return ETS_CFU_Tools::displayText(str_replace($find, $replace, $this->text), 'div', ['class' => $this->style_results]) . ($output ? ETS_CFU_Tools::displayText($output, 'div', ['class' => $this->style_links]) : '');
    }

    public function replacePage($page)
    {
        if ($page > 1)
            return str_replace('_page_', $page, $this->url);
        elseif ($this->friendly && $this->alias && $this->controller != 'AdminModules')
            return str_replace('/_page_', '', $this->url);
        else
            return str_replace('_page_', $page, $this->url);
    }
}

?>