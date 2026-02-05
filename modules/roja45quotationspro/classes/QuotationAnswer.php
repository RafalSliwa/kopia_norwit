<?php
/**
 * QuotationAnswer.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationAnswer
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationAnswer.
 *
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationAnswer extends ObjectModel
{
    public static $PDF = 1;
    public static $MAIL = 2;
    public static $OLD = 3;
    public static $HTML_TEMPLATE = 11;
    public static $TEXT_TEMPLATE = 12;

    public $name;
    public $subject;
    public $path;
    public $type;
    public $custom_css;
    public $template;
    public $enabled;

    private $template_content;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_answer',
        'primary' => 'id_roja45_quotation_answer',
        'multilang' => true,
        'fields' => array(
            'type' => array(
                'type' => self::TYPE_INT
            ),
            'custom_css' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'required' => false
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true
            ),
            'subject' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false
            ),
            'template' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => false
            ),
            'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public static function getQuotationAnswers()
    {
        $cache_id = 'QuotationStatus::getQuotationAnswers';
        if (!Cache::isStored($cache_id)) {
            $sql = '
			SELECT qa.id_roja45_quotation_answer as id, qa.name as name
			FROM `'._DB_PREFIX_.'roja45_quotationspro_answer` qa
			ORDER BY qa.id_roja45_quotation_answer ASC';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }

    public static function getPDFTemplates($id_lang)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        $cache_id = 'QuotationStatus::getQuotationAnswers::PDF::'.$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('roja45_quotationspro_answer', 'qa');
            $sql->leftJoin(
                'roja45_quotationspro_answer_lang',
                'qal',
                'qa.id_roja45_quotation_answer = qal.id_roja45_quotation_answer'
            );
            $sql->where('qa.type =' . (int) QuotationAnswer::$PDF);
            $sql->where('qal.id_lang =' . (int) $id_lang);
            $sql->orderBy('qa.id_roja45_quotation_answer ASC');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }

    public static function getMailTemplates($id_lang)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        $cache_id = 'QuotationStatus::getQuotationAnswers::EMAIL::'.$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('roja45_quotationspro_answer', 'qa');
            $sql->leftJoin(
                'roja45_quotationspro_answer_lang',
                'qal',
                'qa.id_roja45_quotation_answer = qal.id_roja45_quotation_answer'
            );
            $sql->where('qa.type =' . (int) QuotationAnswer::$MAIL);
            $sql->where('qal.id_lang =' . (int) $id_lang);
            $sql->orderBy('qa.id_roja45_quotation_answer ASC');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }

    public static function getOldTemplates($id_lang)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        $cache_id = 'QuotationStatus::getQuotationAnswers::EMAIL::'.$id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('roja45_quotationspro_answer', 'qa');
            $sql->leftJoin(
                'roja45_quotationspro_answer_lang',
                'qal',
                'qa.id_roja45_quotation_answer = qal.id_roja45_quotation_answer'
            );
            $sql->where('qa.type =' . (int) QuotationAnswer::$OLD);
            $sql->where('qal.id_lang =' . (int) $id_lang);
            $sql->orderBy('qa.id_roja45_quotation_answer ASC');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }

    public function renderHTMLTemplate($custom_object, $smarty)
    {
        $html_template = $this->getTemplatePath(QuotationAnswer::$HTML_TEMPLATE);
        $tpl = $smarty->createTemplate(
            $html_template
        );
        $tpl->assign($custom_object);
        $this->template_content = $tpl->fetch();
    }

    public function getTemplateHeader()
    {
        if (!$this->template_content) {
            $template_path = $this->getTemplate($this->template);
            $this->template_content = Tools::file_get_contents(
                $template_path . $this->template . '.tpl'
            );
        }
        if ($start = strpos($this->template_content, '<!-- BEGIN HEADER -->')) {
            $end = strpos($this->template_content, '<!-- END HEADER -->') + 19;
            $header = substr($this->template_content, $start, $end-$start);
            return $header;
        }
    }

    public function getTemplateContent()
    {
        if (!$this->template_content) {
            $template_path = $this->getTemplate($this->template);
            $this->template_content = Tools::file_get_contents(
                $template_path . $this->template . '.tpl'
            );
        }

        if ($start = strpos($this->template_content, '<!-- END HEADER -->') + 19) {
            $end = strpos($this->template_content, '<!-- BEGIN FOOTER -->');
            $content = substr($this->template_content, $start, $end-$start);
            return $content;
        }
    }

    public function getTemplateFooter()
    {
        if (!$this->template_content) {
            $template_path = $this->getTemplate($this->template);
            $this->template_content = Tools::file_get_contents(
                $template_path . $this->template . '.tpl'
            );
        }

        if ($start = strpos($this->template_content, '<!-- BEGIN FOOTER -->')) {
            $end = strpos($this->template_content, '<!-- END FOOTER -->') + 19;
            $footer = substr($this->template_content, $start, $end-$start);
            return $footer;
        }
    }

    public function isRemovable()
    {
        return true;
    }

    public function delete()
    {
        foreach (Language::getLanguages(true) as $lang) {
            $template = _PS_ROOT_DIR_.
                '/modules/roja45quotationspro/views/templates/admin/custom/'.
                $this->template[$lang['id_lang']];
            if (file_exists($template.'.tpl')) {
                unlink($template.'.tpl');
            }
            if (file_exists($template.'-txt.tpl')) {
                unlink($template.'-txt.tpl');
            }
        }

        return parent::delete();
    }

    public function duplicate()
    {
        /** @var QuotationAnswer $duplicate */
        $duplicate = parent::duplicateObject();
        if ((int)Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach (Language::getLanguages(true) as $lang) {
                $template = _PS_ROOT_DIR_.
                    '/modules/roja45quotationspro/views/templates/admin/custom/'.
                    $duplicate->template[$lang['id_lang']];
                if (file_exists($template.'.tpl')) {
                    if (!copy($template.'.tpl', $template.'_COPY.tpl')) {
                        return false;
                    }
                }
                if (file_exists($template.'-txt.tpl')) {
                    if (!copy($template.'-txt.tpl', $template.'_COPY-txt.tpl')) {
                        return false;
                    }
                }
                $duplicate->name[$lang['id_lang']] = $duplicate->name[$lang['id_lang']].' COPY';
                $duplicate->subject[$lang['id_lang']] = $duplicate->subject[$lang['id_lang']];
                $duplicate->template[$lang['id_lang']] = $duplicate->template[$lang['id_lang']].'_COPY';
            }
        } else {
            $template = $this->getTemplate($this->template) . $this->template . '.tpl';
            $new_template = $this->getTemplate($this->template) . $this->template . '_COPY.tpl';
            if (file_exists($template)) {
                if (!copy($template, $new_template)) {
                    return false;
                }
            }
            $duplicate->template = $this->template . '_COPY';
        }

        $duplicate->save();
        return $duplicate;
    }

    public function getTemplate($template)
    {
        $theme_path = _PS_THEME_DIR_;
        $template_path = false;
        if (file_exists($theme_path.'modules/roja45quotationspro/views/templates/admin/custom/' . $template .'.tpl')) {
            $template_path = $theme_path.'modules/roja45quotationspro/views/templates/admin/custom/';
        } elseif (file_exists(_PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/'.$template.'.tpl')) {
            $template_path = _PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/';
        }

        return $template_path;
    }

    public function getDefaultVars($id_lang, $id_shop)
    {
        $shop_name = Configuration::get('PS_SHOP_NAME', null, null, $id_shop);

        if (Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = Configuration::get(
                'PS_LOGO_INVOICE',
                null,
                null,
                (int) Shop::getContextShopID()
            );
        } elseif (Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = Configuration::get(
                'PS_LOGO_MAIL',
                null,
                null,
                (int) Shop::getContextShopID()
            );
        }  elseif (Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = Configuration::get(
                'PS_LOGO',
                null,
                null,
                (int) Shop::getContextShopID()
            );
        } else {
            $logo = '';
        }

        $width = 0;
        $height = 0;
        if (!empty($logo)) {
            list($width, $height) = getimagesize(_PS_IMG_DIR_ . $logo);
        }
        $logo = Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_ . $logo;

        // Limit the height of the logo for the PDF render
        $maximum_height = 100;
        if ($height > $maximum_height) {
            $ratio = $maximum_height / $height;
            $height *= $ratio;
            $width *= $ratio;
        }

        $addressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
        $shop_address = '';
        $shop_address_line = '';
        $shop_state = '';
        $shop_country = '';
        $shop_address_obj = Context::getContext()->shop->getAddress();
        if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
            $shop_address_line = AddressFormat::generateAddress(
                $shop_address_obj,
                array(),
                '-',
                ' '
            );
            $shop_address = AddressFormat::generateAddress(
                $shop_address_obj,
                $addressPatternRules,
                '<br/>',
                ' '
            );
            if ($shop_address_obj->id_state) {
                $shop_state = new State($shop_address_obj->id_state, $id_lang);
                $shop_state = $shop_state->name;
            }
            if ($shop_address_obj->id_country) {
                $shop_country = new Country($shop_address_obj->id_country, $id_lang);
                $shop_country = $shop_country->name;
            }
        }

        $data = array(
            'logo_path' => $logo,
            'img_ps_dir' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'shop_name' => $shop_name,
            'shop_logo' => $logo,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, (int) $id_shop),
            'shop_address1' => $shop_address_obj->address1,
            'shop_address2' => $shop_address_obj->address2,
            'shop_city' => $shop_address_obj->city,
            'shop_postcode' => $shop_address_obj->postcode,
            'shop_state' => $shop_state,
            'shop_country' => $shop_country,
            'shop_address' => $shop_address,
            'shop_address_line' => $shop_address_line,
            'shop_url' => Context::getContext()->link->getPageLink(
                'index',
                true,
                Context::getContext()->language->id,
                null,
                false,
                Context::getContext()->shop->id
            ),
            'width_logo' => $width,
            'height_logo' => $height
        );
        return $data;
    }

    public static function getTemplates()
    {
        $templates_to_use = Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES');
        $array = array();


        if ((int)Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach (Language::getLanguages(false) as $language) {
                $mail_templates = self::getMailTemplates($language['id_lang']);
                $iso_code = $language['iso_code'];

                $template_path =
                    _PS_ROOT_DIR_.
                    '/modules/roja45quotationspro/views/templates/admin/custom/';
                foreach ($mail_templates as $template) {
                    $array[$iso_code][] = array(
                        'id' => $template['id_roja45_quotation_answer'],
                        'name' => $template['name'],
                        'template' => $template['template'],
                        'folder' => $template_path,
                        'type' => QuotationAnswer::$MAIL
                    );
                }
            }
        } else {
            foreach (Language::getLanguages(false) as $language) {
                $mail_templates = self::getOldTemplates($language['id_lang']);
                $iso_code = $language['iso_code'];

                $template_path =
                    _PS_ROOT_DIR_.
                    '/modules/roja45quotationspro/views/templates/admin/custom/';
                foreach ($mail_templates as $template) {
                    $array[$iso_code][] = array(
                        'id' => $template['id_roja45_quotation_answer'],
                        'name' => $template['name'],
                        'template' => $template['template'],
                        'folder' => $template_path,
                        'type' => QuotationAnswer::$OLD
                    );
                }
            }
        }

        return $array;
    }

    public static function processTemplate($template, $custom_data)
    {
        if ($lastPos = strpos($template, '<!-- foreach', 0)) {
            $template = self::processTokens($template, 0, $custom_data);
        }

        foreach ($custom_data as $key => $data) {
            if (!is_array($data) && !is_object($data)) {
                $template = str_replace('@@'.$key.'@@', $data, $template);
            }
        }
        return $template;
    }

    public static function processRecursiveTemplate($template, $custom_data)
    {
        $lastPos = strpos($template, '<!-- foreach', 0);
        $continue = true;
        while ($continue) {
            $template = self::processTemplate($template, $custom_data);
            if (!$lastPos = strpos($template, '<!-- foreach', 0)) {
                $continue = false;
            }
        }
        return $template;
    }

    private static function processTokens($template, $offset, $custom_object)
    {
        $start_tag_len = strlen('<!-- foreach  -->');
        $end_tag_len = strlen('<!-- end foreach  -->');

        $start_tag = strpos($template, '<!-- foreach', $offset);
        $end_tag = strpos($template, ' -->', $start_tag)-($start_tag);
        $var = trim(substr($template, $start_tag+strlen('<!-- foreach '), $end_tag-+strlen('<!-- foreach ')));
        $var_len = strlen($var);

        $block_start = $start_tag + $var_len + $start_tag_len;
        $block_end = strpos($template, '<!-- end foreach ' . $var, $start_tag);
        $block = substr($template, $block_start, $block_end-$block_start);

        $nextEnd = strpos($block, '<!-- end foreach', 0);
        $nextFor = strpos($block, '<!-- foreach', 0);
        if ($custom_object && isset($custom_object[$var])) {
            foreach ($custom_object[$var] as $row) {
                if ($nextFor) {
                    $replace = self::processTokens($block, 0, $row);
                    $replace = self::replaceTokens($replace, $row);
                } else {
                    $replace = self::replaceTokens($block, $row);
                }

                $template = substr_replace(
                    $template,
                    $replace,
                    $block_end+$end_tag_len+$var_len,
                    0
                );
            }
            $template = self::deleteTokens($template, $var);
            return $template;
        } else {
            $template = substr_replace(
                $template,
                '',
                $start_tag,
                ($block_end+$end_tag_len+$var_len) - $start_tag
            );
            $template = self::deleteTokens($template, $var);
            return $template;
        }
    }

    private static function replaceTokens($template, $data)
    {
        $replacement = '';
        $replacement .= preg_replace_callback(
            '/@@([^@@]*)@@/',
            function ($matches) use ($data) {
                if (isset($data[$matches[1]])) {
                    return $data[$matches[1]];
                }
            },
            $template
        );
        return $replacement;
    }

    private static function deleteTokens($template, $identifier)
    {
        $matches = array();
        $has_tokens = true;
        while ($has_tokens) {
            preg_match('/<!-- foreach '.$identifier.'/', $template, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches)) {
                $end_tag_pos = strpos(
                    $template,
                    '<!-- end foreach ' . $identifier,
                    $matches[0][1]
                ) + strlen('<!-- end foreach  -->') + strlen($identifier);
                $template = substr_replace(
                    $template,
                    '',
                    $matches[0][1],
                    $end_tag_pos - $matches[0][1]
                );
            } else {
                $has_tokens = false;
            }
        }
        return $template;
    }

    public static function getImageTag($image)
    {
        $url = Context::getContext()->link->getMediaLink($image);

        $dimensions = '';
        if (file_exists(_PS_ROOT_DIR_.$image)) {
            $width = 0;
            $height = 0;
            list($width, $height) = getimagesize(_PS_ROOT_DIR_.$image);
            $dimensions = 'width="' . $width . '" height="'.$height.'"';
        }

        $image_tag = '<img class="img-responsive" src="'.$url.'"/>';
        return $image_tag;
    }

    /**
     * @param $quotation RojaQuotation
     * @param null $context Context
     * @return array
     * @throws SmartyException
     */
    public function compileTemplate($template_vars, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $default_data = $this->getDefaultVars(
            $context->language->id,
            (int) $context->shop->id
        );
        $template_vars = array_merge(
            $template_vars,
            $default_data
        );

        $template_path = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('customer_request_form');
        $tpl = $context->smarty->createTemplate(
            $template_path
        );
        $tpl->assign($template_vars);
        $customer_form = $tpl->fetch();

        $customer_form_txt = \Soundasleep\Html2Text::convert(
            $customer_form,
            [
                'ignore_errors' => true
            ]
        );

        $template_vars['customer_form'] = $customer_form;
        $template_vars['customer_form_text'] = $customer_form_txt;

        $html_template = $this->getTemplatePath(self::$HTML_TEMPLATE);
        $text_template = $this->getTemplatePath(self::$TEXT_TEMPLATE);

        $tpl = $context->smarty->createTemplate(
            $html_template
        );
        $tpl->assign(
            $template_vars
        );
        $message_content_html = $tpl->fetch();
        $mail_template_path = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('mail_template');

        /**
         * file_get_contents being used as Tools::file_get_contents returns a different answer and causes an error
         */
        $contents = file_get_contents($mail_template_path);
        $contents = str_replace(
            '{email_css}',
            file_get_contents(_PS_ROOT_DIR_.'/modules/roja45quotationspro/views/css/email-styles.css'),
            $contents
        );
        $contents = str_replace(
            '{email_body}',
            $message_content_html,
            $contents
        );
        $summary_html = QuotationAnswer::processRecursiveTemplate($contents, $template_vars);

        $tpl = $context->smarty->createTemplate(
            $text_template
        );
        $tpl->assign(
            $template_vars
        );
        $message_content_text = $tpl->fetch();
        $mail_template_path = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('mail_template_txt');
        $contents = file_get_contents($mail_template_path);
        $contents = str_replace(
            '{email_css}',
            file_get_contents(_PS_ROOT_DIR_.'/modules/roja45quotationspro/views/css/email-styles.css'),
            $contents
        );
        $contents = str_replace(
            '{email_body}',
            $message_content_text,
            $contents
        );
        $summary_text = QuotationAnswer::processRecursiveTemplate($contents, $template_vars);

        return array(
            '{content_html}' => $summary_html,
            '{content_txt}' => $summary_text
        );
    }

    protected function getLogo($id_shop)
    {
        $invoiceLogo = Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
        if ($invoiceLogo && file_exists(_PS_IMG_DIR_ . $invoiceLogo)) {
            return $invoiceLogo;
        }

        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            return $logo;
        }

        return null;
    }

    public function getTemplatePath($type)
    {
        if ($type == self::$HTML_TEMPLATE) {
            if (file_exists(_PS_THEME_DIR_.'modules/roja45quotationspro/views/templates/admin/custom/'.$this->template.'.tpl')) {
                $template_path = _PS_THEME_DIR_.'modules/roja45quotationspro/views/templates/admin/custom/';
            } elseif (file_exists(_PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/'.$this->template.'.tpl')) {
                $template_path = _PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/';
            }

            $html_template = $template_path.$this->template.'.tpl';
            if (!file_exists($html_template)) {
                $en_template = Tools::substr($this->template, 0, Tools::strlen($this->template)-2).'en';
                $html_template = $template_path.$en_template.'.tpl';
            }
            return $html_template;
        }

        if ($type == self::$TEXT_TEMPLATE) {
            if (file_exists(_PS_THEME_DIR_.'modules/roja45quotationspro/views/templates/admin/custom/'.$this->template.'-txt.tpl')) {
                $template_path = _PS_THEME_DIR_.'modules/roja45quotationspro/views/templates/admin/custom/';
            } elseif (file_exists(_PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/'.$this->template.'-txt.tpl')) {
                $template_path = _PS_ROOT_DIR_.'/modules/roja45quotationspro/views/templates/admin/custom/';
            }
            $text_template = $template_path.$this->template.'-txt.tpl';
            if (!file_exists($text_template)) {
                $en_template = Tools::substr($this->template, 0, Tools::strlen($this->template)-2).'en';
                $text_template = $template_path.$en_template.'.tpl';
            }
            return $text_template;
        }
    }
}
