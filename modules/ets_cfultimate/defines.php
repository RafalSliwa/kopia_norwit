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

class Ets_cfudefines
{
    public static $_hooks = array(
        'actionOutputHTMLBefore',
        'contactFormUltimateTopBlock',
        'displayBackOfficeHeader',
        'displayContactFormUltimate',
        'displayHeader',
        'displayHome',
        'moduleRoutes',
        'displayNav2',
        'displayNav',
        'displayTop',
        'displayLeftColumn',
        'displayFooter',
        'displayRightColumn',
        'displayProductAdditionalInfo',
        'displayFooterProduct',
        'displayAfterProductThumbs',
        'displayRightColumnProduct',
        'displayLeftColumnProduct',
        'displayShoppingCartFooter',
        'displayCustomerAccountForm',
        'displayCustomerLoginFormAfter',
        'displayBackOfficeFooter'
    );
    public static $instance;
    public $_config_fields;
    public $_email_fields;
    public $_ip_black_list;
    public $contact_fields;
    public $_inputs = array();
    public $_tabs = array();
    public $module;
    public $smarty;
    public $groups;
    public $context;

    public function __construct($module = null)
    {
        if (!(is_object($module)) || !$module) {
            $module = Module::getInstanceByName(_ETS_MODULE_);
        }
        $this->module = $module;
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty;
        }
        $this->groups = Group::getGroups($this->context->language->id, true);
    }

    public static function getInstance($module = null)
    {
        if (!(isset(self::$instance))) {
            self::$instance = new Ets_cfudefines($module);
        }
        return self::$instance;
    }

    public function getContactFields($mailchimp_audience = [])
    {
        if (!(isset($this->contact_fields)) || !$this->contact_fields) {
            $datProvider = ETS_CFU_Data_Provider::getInstance();
            $svg_icons = array(
                'fa-envelope' => array(
                    'id' => 'fa-envelope',
                    'icon' => $datProvider->getIcons('fa-envelope'),
                ),
                'fa-envelope-o' => array(
                    'id' => 'fa-envelope-o',
                    'icon' => $datProvider->getIcons('fa-envelope-o'),
                ),
                'fa-address-card-o' => array(
                    'id' => 'fa-address-card-o',
                    'icon' => $datProvider->getIcons('fa-address-card-o'),
                ),
                'fa-address-card' => array(
                    'id' => 'fa-address-card',
                    'icon' => $datProvider->getIcons('fa-address-card'),
                ),
                'fa-calendar' => array(
                    'id' => 'fa-calendar',
                    'icon' => $datProvider->getIcons('fa-calendar'),
                ),
                'fa-info' => array(
                    'id' => 'fa-info',
                    'icon' => $datProvider->getIcons('fa-info'),
                ),
                'fa-info-circle' => array(
                    'id' => 'fa-info-circle',
                    'icon' => $datProvider->getIcons('fa-info-circle'),
                ),
                'fa-list-ul' => array(
                    'id' => 'fa-list-ul',
                    'icon' => $datProvider->getIcons('fa-list-ul'),
                ),
                'fa-question-circle' => array(
                    'id' => 'fa-question-circle',
                    'icon' => $datProvider->getIcons('fa-question-circle'),
                ),
                'fa-question' => array(
                    'id' => 'fa-question',
                    'icon' => $datProvider->getIcons('fa-question'),
                )
            );
            $switch_values = array(
                array(
                    'id' => 'on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                ),
                array(
                    'id' => 'off',
                    'value' => 0,
                    'label' => $this->l('No')
                )
            );
            $mailchimp_audience_list = [];
            $mailchimp_audience_list[] = array(
                'id' => '--',
                'label' => $this->l('--')
            );
            if ($mailchimp_audience) {
                $mailchimp_audience_list = array_merge($mailchimp_audience_list, $mailchimp_audience);
            }
            $this->contact_fields = array(
                'form' => array(
                    'legend' => array(
                        'title' => Tools::getValue('id_contact') ? $this->l('Edit contact form') : $this->l('Add contact form'),
                        'icon' => Tools::getValue('id_contact') ? 'icon-pencil-square-o' : 'icon-pencil-square-o'
                    ),
                    'input' => array(
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Form editor'),
                            'name' => 'short_code',
                            'lang' => true,
                            'id' => 'wpcfu-form',
                            'class' => 'wpcfu-form ',
                            'form_group_class' => 'form_group_contact form short_code',
                            'validate' => 'isString',
                        ),
                        array(
                            'label' => $this->l('Render form'),
                            'type' => 'textarea',
                            'name' => 'render_form',
                            'class' => 'hide',
                            'form_group_class' => 'form_group_contact form short_code',
                            'validate' => 'isString',
                        ),
                        array(
                            'label' => $this->l('Logic conditions'),
                            'type' => 'hidden',
                            'name' => 'condition',
                            'class' => 'hide',
                            'form_group_class' => 'form_group_contact form condition',
                            'validate' => 'isString',
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Activate contact form'),
                            'name' => 'active',
                            'values' => $switch_values,
                            'default' => 1,
                            'form_group_class' => 'form_group_contact general_settings',
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'save_message',
                            'label' => $this->l('Save messages'),
                            'values' => $switch_values,
                            'default' => 1,
                            'form_group_class' => 'form_group_contact general_settings',
                            'desc' => $this->l('Save customer messages to "Messages" tab.'),
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'save_attachments',
                            'label' => $this->l('Save attachments'),
                            'values' => $switch_values,
                            'desc' => $this->l('Save attached files on your server, you can download the files in "Messages" tab. Enable this option is useful but it will take some of your hosting disk space to store the files. You can set this to "No" if it is not necessary for saving files on server because the files will be also sent to your email inbox'),
                            'default' => 1,
                            'form_group_class' => 'form_group_contact general_settings general_settings4',
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'star_message',
                            'label' => $this->l('Mark messages from this contact form as "Star message"'),
                            'values' => $switch_values,
                            'default' => 0,
                            'form_group_class' => 'form_group_contact general_settings general_settings4',
                            'desc' => $this->l('Highlight messages sent from this contact form in the "Messages" tab by a yellow star'),
                        ),
                        array(
                            'type' => 'group',
                            'label' => $this->l('Give access to customer group'),
                            'name' => 'group_access',
                            'values' => $this->groups,
                            'col' => '6',
                            'is_id' => (int)Tools::getValue('id_contact') ? true : false,
                            'custommer_access' => ETS_CFU_Contact::getGroupAccessById((int)Tools::getValue('id_contact')),
                            'form_group_class' => 'form_group_contact general_settings form_hook'
                        ),
                        array(
                            'type' => 'checkbox',
                            'name' => 'hook',
                            'label' => $this->l('Available display position (default Prestashop hooks)'),
                            'values' => array(
                                'query' => array(
                                    array(
                                        'name' => $this->l('Header - top navigation'),
                                        'val' => 'nav_top',
                                        'id' => 'nav_top',
                                    ),
                                    array(
                                        'name' => $this->l('Header - main header'),
                                        'val' => 'header',
                                        'id' => 'header',
                                    ),
                                    array(
                                        'name' => $this->l('Top'),
                                        'val' => 'displayTop',
                                        'id' => 'displayTop',
                                    ),
                                    array(
                                        'name' => $this->l('Home'),
                                        'val' => 'home',
                                        'id' => 'home',
                                    ),
                                    array(
                                        'name' => $this->l('Left column'),
                                        'val' => 'left_column',
                                        'id' => 'left_column',
                                    ),
                                    array(
                                        'name' => $this->l('Right column'),
                                        'val' => 'right_column',
                                        'id' => 'right_column',
                                    ),
                                    array(
                                        'name' => $this->l('Footer'),
                                        'val' => 'footer_page',
                                        'id' => 'footer_page',
                                    ),
                                    array(
                                        'name' => $this->l('Product page - below product images'),
                                        'val' => 'product_thumbs',
                                        'id' => 'product_thumbs',
                                    ),
                                    array(
                                        'name' => $this->l('Product page - Footer'),
                                        'val' => 'product_footer',
                                        'id' => 'product_footer',
                                    ),
                                    array(
                                        'name' => $this->l('Checkout page'),
                                        'val' => 'checkout_page',
                                        'id' => 'checkout_page',
                                    ),
                                    array(
                                        'name' => $this->l('Login page'),
                                        'val' => 'login_page',
                                        'id' => 'login_page',
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('Besides using short code, custom hook and a separated page to display the contact form, you can also display contact form on default Prestashop pre-defined hooks'),
                            'form_group_class' => 'form_group_contact general_settings form_hook ' . (!Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE') ? 'hidden' : '')
                        ),

                        array(
                            'type' => 'switch',
                            'name' => 'open_form_by_button',
                            'label' => $this->l('Open form by button'),
                            'values' => $switch_values,
                            'form_group_class' => 'form_group_contact general_settings',
                            'desc' => $this->l('Display a button (the form is hidden initially), when customer click on the button, it will open the form via a popup'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Button label'),
                            'name' => 'button_label',
                            'lang' => true,
                            'default' => $this->l('Open contact form'),
                            'default_origin' => 'Open contact form',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact general_settings open_form button_label',
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Button text color'),
                            'name' => 'button_text_color',
                            'default' => '#ffffff',
                            'validate' => 'isColor',
                            'form_group_class' => 'form_group_contact general_settings open_form button_text_color',
                            'ref' => 'open_form_by_button',
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Button background color'),
                            'name' => 'button_background_color',
                            'default' => '#2fb5d2',
                            'validate' => 'isColor',
                            'form_group_class' => 'form_group_contact general_settings open_form button_background_color',
                            'ref' => 'open_form_by_button',
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Button text hover color'),
                            'name' => 'button_hover_color',
                            'default' => '#ffffff',
                            'validate' => 'isColor',
                            'form_group_class' => 'form_group_contact general_settings open_form button_hover_color',
                            'ref' => 'open_form_by_button',
                        ),
                        array(
                            'type' => 'color',
                            'validate' => 'isColor',
                            'label' => $this->l('Button background hover color'),
                            'name' => 'button_background_hover_color',
                            'default' => '#2592a9',
                            'form_group_class' => 'form_group_contact general_settings open_form button_background_hover_color',
                            'ref' => 'open_form_by_button',
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'button_icon_enabled',
                            'label' => $this->l('Display an icon with button'),
                            'values' => $switch_values,
                            'default' => 0,
                            'form_group_class' => 'form_group_contact general_settings open_form button_icon_enabled',
                            'desc' => $this->l('An icon will be displayed with "Open form" button'),
                        ),
                        array(
                            'type' => 'icon',
                            'label' => $this->l('Select an icon'),
                            'name' => 'button_icon_custom',
                            'icons' => $svg_icons,
                            'default' => 'fa-envelope',
                            'form_group_class' => 'form_group_contact general_settings open_form button_icon_custom',
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Upload a custom icon'),
                            'name' => 'button_icon_custom_file',
                            'default' => '#2592a9',
                            'base_uri' => _PS_IMG_ . $this->module->name . DIRECTORY_SEPARATOR,
                            'base_url' => _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR,
                            'form_group_class' => 'form_group_contact general_settings open_form button_icon_custom_file',
                            'desc' => sprintf($this->l('Acceptable formats: jpg, jpeg, png, gif, webp. Limit: %s'), ETS_CFU_Tools::formatBytes(ETS_CFU_Tools::getPostMaxSizeBytes())),
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'button_popup_enabled',
                            'label' => $this->l('Floating button'),
                            'values' => $switch_values,
                            'form_group_class' => 'form_group_contact general_settings',
                            'desc' => $this->l('Display a floating button (the form is hidden initially), when customer clicks on the floating button, it will open the contact form'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Floating button position'),
                            'name' => 'button_popup_position',
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => 'middle_right',
                                        'label' => $this->l('Vertical: middle right'),
                                    ),
                                    array(
                                        'id' => 'bottom_right',
                                        'label' => $this->l('Horizontal: bottom right'),
                                    ),
                                    array(
                                        'id' => 'middle_left',
                                        'label' => $this->l('Vertical: middle left'),
                                    ),
                                    array(
                                        'id' => 'bottom_left',
                                        'label' => $this->l('Horizontal: bottom left'),
                                    )
                                ),
                                'id' => 'id',
                                'name' => 'label',
                            ),
                            'default' => 'bottom_right',
                            'form_group_class' => 'form_group_contact general_settings floating button_popup_position',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Left margin'),
                            'name' => 'button_popup_left',
                            'suffix' => $this->l('px'),
                            'validate' => 'isUnsignedInt',
                            'desc' => $this->l('Space between floating button and the left-end of your web page.'),
                            'form_group_class' => 'form_group_contact general_settings floating button_popup_left',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Right margin'),
                            'name' => 'button_popup_right',
                            'suffix' => $this->l('px'),
                            'validate' => 'isUnsignedInt',
                            'desc' => $this->l('Space between floating button and the right-end of your web page.'),
                            'form_group_class' => 'form_group_contact general_settings floating button_popup_right',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Top margin'),
                            'name' => 'button_popup_top',
                            'suffix' => $this->l('px'),
                            'validate' => 'isUnsignedInt',
                            'desc' => $this->l('Space between floating button and the top-end of your web page.'),
                            'form_group_class' => 'form_group_contact general_settings floating button_popup_top',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Bottom margin'),
                            'name' => 'button_popup_bottom',
                            'suffix' => $this->l('px'),
                            'validate' => 'isUnsignedInt',
                            'desc' => $this->l('Space between floating button and the bottom-end of your web page.'),
                            'form_group_class' => 'form_group_contact general_settings floating button_popup_bottom',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Button label'),
                            'name' => 'floating_label',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default' => $this->l('Open contact form'),
                            'default_origin' => 'Open contact form',
                            'form_group_class' => 'form_group_contact general_settings floating floating_label',
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Button text color'),
                            'name' => 'floating_text_color',
                            'default' => '#ffffff',
                            'validate' => 'isColor',
                            'form_group_class' => 'form_group_contact general_settings floating floating_text_color',
                            'ref' => 'button_popup_enabled',
                        ),
                        array(
                            'type' => 'color',
                            'validate' => 'isColor',
                            'label' => $this->l('Button background color'),
                            'name' => 'floating_background_color',
                            'default' => '#2fb5d2',
                            'form_group_class' => 'form_group_contact general_settings floating floating_background_color',
                            'ref' => 'button_popup_enabled',
                        ),
                        array(
                            'type' => 'color',
                            'validate' => 'isColor',
                            'label' => $this->l('Button text hover color'),
                            'name' => 'floating_hover_color',
                            'default' => '#ffffff',
                            'form_group_class' => 'form_group_contact general_settings floating floating_hover_color',
                            'ref' => 'button_popup_enabled',
                        ),
                        array(
                            'type' => 'color',
                            'validate' => 'isColor',
                            'label' => $this->l('Button background hover color'),
                            'name' => 'floating_background_hover_color',
                            'default' => '#2592a9',
                            'form_group_class' => 'form_group_contact general_settings floating floating_background_hover_color',
                            'ref' => 'button_popup_enabled',
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'floating_icon_enabled',
                            'label' => $this->l('Display an icon for button'),
                            'values' => $switch_values,
                            'default' => 0,
                            'form_group_class' => 'form_group_contact general_settings floating floating_icon_enabled',
                            'desc' => $this->l('An icon will be displayed before "Open contact form" button'),
                        ),
                        array(
                            'type' => 'icon',
                            'label' => $this->l('Select an icon'),
                            'name' => 'floating_icon_custom',
                            'icons' => $svg_icons,
                            'default' => 'fa-envelope',
                            'form_group_class' => 'form_group_contact general_settings floating floating_icon_custom',
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Upload a custom icon'),
                            'name' => 'floating_icon_custom_file',
                            'default' => '#2592a9',
                            'base_uri' => _PS_IMG_ . $this->module->name . DIRECTORY_SEPARATOR,
                            'base_url' => _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR,
                            'form_group_class' => 'form_group_contact general_settings floating floating_icon_custom_file',
                            'desc' => sprintf($this->l('Acceptable formats: jpg, jpeg, png, gif, webp. Limit: %s'), ETS_CFU_Tools::formatBytes(ETS_CFU_Tools::getPostMaxSizeBytes())),
                        ),
                        array(
                            'type' => 'switch',
                            'name' => 'enable_form_page',
                            'label' => $this->l('Enable separate form page'),
                            'values' => $switch_values,
                            'default' => 1,
                            'form_group_class' => 'form_group_contact seo',
                            'desc' => $this->l('Besides displaying the form using short code, custom hook and default Prestashop hooks, you can also create a specific web page to display the form'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Form title'),
                            'name' => 'title',
                            'required' => true,
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'class' => 'title_form',
                            'form_group_class' => 'form_group_contact seo seo3',
                            'default' => '',
                            'default_origin' => '',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Contact form alias'),
                            'name' => 'title_alias',
                            'lang' => true,
                            'class' => 'alias_form',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact seo seo3',
                            'default' => '',
                            'default_origin' => '',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Meta title'),
                            'name' => 'meta_title',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact seo seo3',
                        ),
                        array(
                            'type' => 'tags',
                            'label' => $this->l('Meta keywords'),
                            'name' => 'meta_keyword',
                            'lang' => true,
                            'form_group_class' => 'form_group_contact seo seo3',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Meta description'),
                            'name' => 'meta_description',
                            'lang' => true,
                            'form_group_class' => 'form_group_contact seo seo3',
                            'validate' => 'isCleanHtml',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('To'),
                            'name' => 'email_to',
                            'multi' => true,
                            'show_btn_add' => true,
                            'class' => 'hide',
                            'required' => true,
                            'form_group_class' => 'form_group_contact mail mail1',
                            'default' => Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>',
                            'validate' => 'isCleanHtml',
                            'desc' => $this->l('Enter email addresses of people who will receive this email'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('BCC'),
                            'name' => 'bcc',
                            'multi' => true,
                            'show_btn_add' => true,
                            'class' => 'hide',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail1',
                            'desc' => $this->l('When you place email addresses in the BCC field of a message, those addresses are invisible to the recipients of the email.'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('From'),
                            'name' => 'email_from',
                            'multi' => true,
                            'show_btn_add' => false,
                            'class' => 'hide',
                            'mail_tag' => true,
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail1',
                            'desc' => $this->l('This should be an authorized email address. Normally it is your shop SMTP email (if your website is enabled with SMTP) or an email associated with your website domain name (if your website uses default Mail() function to send emails. Leave blank to get default setting)'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Subject'),
                            'name' => 'subject',
                            'lang' => true,
                            'required' => true,
                            'validate' => 'isMailSubject',
                            'form_group_class' => 'form_group_contact mail mail1',
                            'default' => $this->l('Your email subject'),
                            'default_origin' => 'Your email subject',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Reply to'),
                            'name' => 'additional_headers',
                            'multi' => true,
                            'show_btn_add' => false,
                            'class' => 'hide',
                            'mail_tag' => true,
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail1',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Message body'),
                            'name' => 'message_body',
                            'lang' => true,
                            'autoload_rte' => true,
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail1 message_body',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('File attachments'),
                            'name' => 'file_attachments',
                            'file_attachment' => 'true',
                            'class' => 'hide file_attachment',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail1 attach',
                            'desc' => $this->l('*Note: You need to enter respective mail-tags for the file form-tags used in the "Form editor" into this field in order to receive the files via email as well as "Messages" tab. See more details about mail-tag in the documentation of this module.'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable auto responder'),
                            'name' => 'use_email2',
                            'values' => $switch_values,
                            'desc' => $this->l('Auto responder is an additional email sent to anyone you want when customer submits a contact form. It\'s often used to send a confirmation email to customer when they successfully submit a contact form'),
                            'form_group_class' => 'form_group_contact mail mail2',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('To'),
                            'name' => 'email_to2',
                            'multi' => true,
                            'show_btn_add' => true,
                            'mail_tag' => true,
                            'class' => 'hide',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2',
                            'desc' => $this->l('Enter email addresses of people who will receive this email'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('BCC'),
                            'name' => 'bcc2',
                            'multi' => true,
                            'show_btn_add' => true,
                            'class' => 'hide',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2',
                            'desc' => $this->l('When you place email addresses in the BCC field of a message, those addresses are invisible to the recipients of the email.'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('From'),
                            'name' => 'email_from2',
                            'multi' => true,
                            'show_btn_add' => false,
                            'class' => 'hide',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2',
                            'desc' => $this->l('This should be an authorized email address. Normally it is your shop SMTP email (if your website is enabled with SMTP) or an email associated with your website domain name (if your website uses default Mail() function to send emails. Leave blank to get default setting)'),
                            'default' => Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Subject'),
                            'name' => 'subject2',
                            'lang' => true,
                            'required' => true,
                            'validate' => 'isMailSubject',
                            'form_group_class' => 'form_group_contact mail mail2',
                            'default' => $this->l('Your email has been sent'),
                            'default_origin' => 'Your email has been sent',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Reply to'),
                            'name' => 'additional_headers2',
                            'multi' => true,
                            'show_btn_add' => false,
                            'class' => 'hide',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2',
                            'default' => Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Message body'),
                            'name' => 'message_body2',
                            'lang' => true,
                            'autoload_rte' => true,
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2 message_body',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('File attachments'),
                            'name' => 'file_attachments2',
                            'file_attachment' => 'true',
                            'class' => 'hide file_attachment',
                            'validate' => 'isCleanHtml',
                            'form_group_class' => 'form_group_contact mail mail2 attach',
                            'desc' => $this->l('*Note: You need to enter respective mail-tags for the file form-tags used in the "Form editor" into this field in order to receive the files via email. See more details about mail-tag in the documentation of this module.'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Notification message when contact form was sent successfully'),
                            'name' => 'message_mail_sent_ok',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'Thank you for your message. It has been sent.',
                            'default' => $this->l('Thank you for your message. It has been sent.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Notification message when contact form failed to send'),
                            'name' => 'message_mail_sent_ng',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'There was an error while trying to send your message. Please try again later.',
                            'default' => $this->l('There was an error while trying to send your message. Please try again later.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Validation errors occurred'),
                            'name' => 'message_validation_error',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'One or more fields have an error. Please check and try again.',
                            'default' => $this->l('One or more fields have an error. Please check and try again.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Submission was referred as spam'),
                            'name' => 'message_spam',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'There was an error while trying to send your message. Please try again later.',
                            'default' => $this->l('There was an error while trying to send your message. Please try again later.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('There are terms that the sender must accept'),
                            'name' => 'message_accept_terms',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'You must accept the terms and conditions before sending your message.',
                            'default' => $this->l('You must accept the terms and conditions before sending your message.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('There is a field that the sender must fill in'),
                            'name' => 'message_invalid_required',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The field is required.',
                            'default' => $this->l('The field is required.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('There is a field with input value that is longer than the maximum allowed length'),
                            'name' => 'message_invalid_too_long',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The field is too long.',
                            'default' => $this->l('The field is too long.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('There is a field with input value that is shorter than the minimum allowed length'),
                            'name' => 'message_invalid_too_short',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The field is too short.',
                            'default' => $this->l('The field is too short.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Date format that the sender entered is invalid'),
                            'name' => 'message_invalid_date',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The date format is incorrect.',
                            'default' => $this->l('The date format is incorrect.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('The date sender entered is earlier than minimum limit'),
                            'name' => 'message_date_too_early',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The date is before the earliest one allowed.',
                            'default' => $this->l('The date is before the earliest one allowed.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('The date sender entered is later than maximum limit'),
                            'name' => 'message_date_too_late',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The date is after the latest one allowed.',
                            'default' => $this->l('The date is after the latest one allowed.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Uploading a file failed due to some unknown reasons'),
                            'name' => 'message_upload_failed',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'There was an unknown error while uploading the file.',
                            'default' => $this->l('There was an unknown error while uploading the file.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Invalid upload file type'),
                            'name' => 'message_upload_file_type_invalid',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'You are not allowed to upload files of this type.',
                            'default' => $this->l('You are not allowed to upload files of this type.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Sender does not enter the correct answer to the quiz'),
                            'name' => 'message_quiz_answer_not_correct',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The answer to the quiz is incorrect.',
                            'default' => $this->l('The answer to the quiz is incorrect.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Uploaded file is too large'),
                            'name' => 'message_upload_file_too_large',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The file is too big.',
                            'default' => $this->l('The file is too big.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Uploading a file failed due to PHP error'),
                            'name' => 'message_upload_failed_php_error',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'There was an error while uploading the file.',
                            'default' => $this->l('There was an error while uploading the file.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Number format that the sender entered is invalid'),
                            'name' => 'message_invalid_number',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The number format is invalid.',
                            'default' => $this->l('The number format is invalid.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('The number sender entered is smaller than minimum limit'),
                            'name' => 'message_number_too_small',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The number is smaller than the minimum allowed.',
                            'default' => $this->l('The number is smaller than the minimum allowed.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('The number sender entered is larger than maximum limit'),
                            'name' => 'message_number_too_large',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The number is larger than the maximum allowed',
                            'default' => $this->l('The number is larger than the maximum allowed'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Email address that the sender entered is invalid'),
                            'name' => 'message_invalid_email',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The e-mail address entered is invalid.',
                            'default' => $this->l('The e-mail address entered is invalid.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('URL that the sender entered is invalid'),
                            'name' => 'message_invalid_url',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The URL is invalid.',
                            'default' => $this->l('The URL is invalid.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Telephone number that the sender entered is invalid'),
                            'name' => 'message_invalid_tel',
                            'lang' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'The telephone number is invalid.',
                            'default' => $this->l('The telephone number is invalid.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Message IP is in blacklist'),
                            'name' => 'message_ip_black_list',
                            'lang' => true,
                            'default_origin' => 'You are not allowed to submit this form. Please contact webmaster for more information.',
                            'default' => $this->l('You are not allowed to submit this form. Please contact webmaster for more information.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Message Email is in blacklist'),
                            'name' => 'message_email_black_list',
                            'lang' => true,
                            'default_origin' => 'Your email is blocked. Contact webmaster for more info.',
                            'default' => $this->l('Your email is blocked. Contact webmaster for more info.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Captcha entered is invalid'),
                            'name' => 'message_captcha_not_match',
                            'lang' => true,
                            'default_origin' => 'Your entered code is incorrect.',
                            'default' => $this->l('Your entered code is incorrect.'),
                            'form_group_class' => 'form_group_contact message',
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Display "Thank you" page after form submission'),
                            'name' => 'thank_you_active',
                            'values' => $switch_values,
                            'default' => 0,
                            'form_group_class' => 'form_group_contact thank_you thank_you_active',
                        ),
                        array(
                            'label' => $this->l('"Thank you" page'),
                            'type' => 'select',
                            'name' => 'thank_you_page',
                            'options' => array(
                                'query' => array(
                                    array(
                                        'name' => $this->l('Default page'),
                                        'thank_page' => 'thank_page_default',
                                    ),
                                    array(
                                        'name' => $this->l('Custom URL'),
                                        'thank_page' => 'thank_page_url',
                                    ),
                                ),
                                'id' => 'thank_page',
                                'name' => 'name',
                            ),
                            'default' => 'thank_page_default',
                            'form_group_class' => 'form_group_contact thank_you thank_you_page',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Title'),
                            'name' => 'thank_you_page_title',
                            'lang' => true,
                            'required' => true,
                            'class' => 'title_tk_page',
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                            'default' => $this->l('Thanks for submitting the form'),
                            'default_origin' => 'Thanks for submitting the form',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => 'Page alias',
                            'lang' => true,
                            'name' => 'thank_you_alias',
                            'class' => 'alias_tk_page',
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                            'default' => $this->l('thanks-for-submitting-the-form'),
                            'default_origin' => 'thanks-for-submitting-the-form',
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->l('Content'),
                            'name' => 'thank_you_message',
                            'class' => 'rte',
                            'lang' => true,
                            'required' => true,
                            'autoload_rte' => true,
                            'validate' => 'isCleanHtml',
                            'default_origin' => 'Thank you for contacting us. This message is to confirm that you have successfully submitted the contact form. We\'ll get back to you shortly.',
                            'default' => $this->l('Thank you for contacting us. This message is to confirm that you have successfully submitted the contact form. We\'ll get back to you shortly.'),
                            'form_group_class' => 'form_group_contact thank_you thank_you_message',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Custom URL'),
                            'name' => 'thank_you_url',
                            'lang' => true,
                            'required' => true,
                            'placeholder' => $this->l('https://example.com/thank-you.html'),
                            'default' => '',
                            'desc' => $this->l('Customer will be redirected to this URL after submitting the form successfully'),
                            'form_group_class' => 'form_group_contact thank_you thank_you_url',
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Mailchimp synchronization'),
                            'name' => 'mailchimp_enabled',
                            'values' => $switch_values,
                            'default' => 0,
                            'desc' => $this->l('By enabling this option, you can sync your subscriber audience to your Mailchimp account'),
                            'form_group_class' => 'form_group_contact mailchimp mailchimp_enabled',
                        ),
                        array(
                            'type' => 'text',
                            'validate' => 'isCleanHtml',
                            'label' => $this->l('Mailchimp API key'),
                            'name' => 'mailchimp_api_key',
                            'required' => true,
                            'col' => 4,
                            'form_group_class' => 'form_group_contact mailchimp mailchimp_api_key',
                        ),
                        //Audience
                        array(
                            'type' => 'select',
                            'label' => $this->l('Synchronize with this Mailchimp audience'),
                            'name' => 'mailchimp_audience',
                            'options' => array(
                                'query' => $mailchimp_audience_list,
                                'id' => 'id',
                                'name' => 'label',
                            ),
                            'form_group_class' => 'form_group_contact mailchimp mailchimp_audience' . (empty($mailchimp_audience) ? ' hide' : ''),
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Mapping data'),
                            'name' => 'mailchimp_mapping_data',
                            'form_group_class' => 'form_group_contact mailchimp mailchimp_mapping_data',
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                    'buttons' => array(
                        array(
                            'id' => 'backListContact',
                            'href' => defined('_PS_ADMIN_DIR_') ? 'index.php?controller=AdminContactFormUltimateContactForm&token=' . Tools::getAdminTokenLite('AdminContactFormUltimateContactForm') : '#',
                            'icon' => 'process-icon-cancel',
                            'class' => 'pull-left',
                            'title' => $this->l('Cancel'),
                        )
                    )
                ),
            );
        }
        return $this->contact_fields;
    }

    public function getFields($fields)
    {
        switch ($fields) {
            case 'config':
                if (!(isset($this->_config_fields)) || !$this->_config_fields) {
                    $this->_config_fields = array(
                        'form' => array(
                            'legend' => array(
                                'title' => $this->l('Integration'),
                                'icon' => 'icon-cogs'
                            ),
                            'id_form' => 'ets_cfu_module_form_integration',
                            'input' => array(
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_ENABLE_RECAPTCHA',
                                    'label' => $this->l('Enable reCAPTCHA'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_RECAPTCHA_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_RECAPTCHA_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 0,
                                    'form_group_class' => 'form_group_contact google',
                                ),
                                array(
                                    'type' => 'radio',
                                    'label' => $this->l('ReCaptcha type'),
                                    'name' => 'ETS_CFU_RECAPTCHA_TYPE',
                                    'required' => true,
                                    'form_group_class' => 'form_group_contact google google2',
                                    'values' => array(
                                        array(
                                            'id' => 'id_recaptcha_v2',
                                            'value' => 'v2',
                                            'label' => $this->l('reCaptcha v2'),
                                        ),
                                        array(
                                            'id' => 'id_recaptcha_v3',
                                            'value' => 'v3',
                                            'label' => $this->l('reCaptcha v3'),
                                        ),
                                    ),
                                    'default' => 'v2'
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Site key'),
                                    'name' => 'ETS_CFU_SITE_KEY',
                                    'required' => true,
                                    'validate' => 'isCleanHtml',
                                    'form_group_class' => 'form_group_contact google google2 capv2',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Secret key'),
                                    'name' => 'ETS_CFU_SECRET_KEY',
                                    'required' => true,
                                    'validate' => 'isCleanHtml',
                                    'form_group_class' => 'form_group_contact google google2 capv2',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Site key v3'),
                                    'name' => 'ETS_CFU_SITE_KEY_V3',
                                    'required' => true,
                                    'validate' => 'isCleanHtml',
                                    'form_group_class' => 'form_group_contact google google3 capv3',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Secret key v3'),
                                    'name' => 'ETS_CFU_SECRET_KEY_V3',
                                    'required' => true,
                                    'validate' => 'isCleanHtml',
                                    'form_group_class' => 'form_group_contact google google3 capv3',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Score'),
                                    'name' => 'ETS_CFU_SCORE_CAPTCHA_V3',
                                    'required' => true,
                                    'validate' => 'isCleanHtml',
	                                'default' => '0.5',
                                    'form_group_class' => 'form_group_contact google google3 capv3',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Form title'),
                                    'name' => 'ETS_CFU_CONTACT_ALIAS',
                                    'required' => true,
                                    'lang' => true,
                                    'validate' => 'isLinkRewrite',
                                    'default' => 'contact',
                                    'form_group_class' => 'form_group_contact other_setting',
                                ),
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_URL_SUFFIX',
                                    'label' => $this->l('Use URL suffix'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_URL_SUFFIX_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_URL_SUFFIX_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 0,
                                    'form_group_class' => 'form_group_contact other_setting',
                                    'desc' => $this->l('Add ".html" to the end of form page URL. Set this to "Yes" if your product pages are ended with ".html". Set this to "No", if product pages are NOT ended with ".html"'),
                                ),
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_REMOVE_ID',
                                    'label' => $this->l('Remove ID from contact form URL'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_REMOVE_ID_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_REMOVE_ID_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 1,
                                    'form_group_class' => 'form_group_contact other_setting',
                                ),
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_ENABLE_TMCE',
                                    'label' => $this->l('Enable TinyMCE editor'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_TMCE_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_TMCE_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 1,
                                    'form_group_class' => 'form_group_contact other_setting',
                                    'desc' => $this->l('Set this to "Yes" will allow you to enable rich text editor for textarea fields when compiling contact forms'),
                                ),
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_ENABLE_HOOK_SHORTCODE',
                                    'label' => $this->l('Enable shortcode for contact form and display contact form in PrestaShop hook'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_HOOK_SHORTCODE_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_HOOK_SHORTCODE_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 0,
                                    'form_group_class' => 'form_group_contact other_setting',
                                ),
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_CACHE_ENABLED',
                                    'label' => $this->l('Enable cache'),
                                    'desc' => $this->l('The module uses PrestaShop Smarty Cache, so please make sure that PrestaShop Smarty Cache is enabled to use this feature'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_CACHE_ENABLED_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_CACHE_ENABLED_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 0,
                                    'form_group_class' => 'form_group_contact other_setting',
                                ),
                                array(
                                    'type' => 'text',
                                    'label' => $this->l('Cache life time'),
                                    'name' => 'ETS_CFU_CACHE_LIFETIME',
                                    'required' => true,
                                    'validate' => 'isUnsignedInt',
                                    'default' => '24',
                                    'suffix' => $this->l('hour(s)'),
                                    'col' => 4,
                                    'form_group_class' => 'form_group_contact other_setting',
                                ),
                            ),
                            'submit' => array(
                                'title' => $this->l('Save'),
                            )
                        ),
                    );
                }
                return $this->_config_fields;
            case 'email':
                if (!(isset($this->_email_fields)) || !$this->_email_fields) {
                    $this->_email_fields = array(
                        'form' => array(
                            'legend' => array(
                                'title' => $this->l('Email template'),
                                'icon' => 'icon-file-text-o'
                            ),
                            'id_form' => 'ets_cfu_module_form_email_template',
                            'input' => array(
                                array(
                                    'type' => 'switch',
                                    'name' => 'ETS_CFU_ENABLE_TEMPLATE',
                                    'label' => $this->l('Enable email template'),
                                    'values' => array(
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_TEMPLATE_on',
                                            'value' => 1,
                                            'label' => $this->l('Yes')
                                        ),
                                        array(
                                            'id' => 'ETS_CFU_ENABLE_TEMPLATE_off',
                                            'value' => 0,
                                            'label' => $this->l('No')
                                        )
                                    ),
                                    'default' => 1,
                                    'form_group_class' => 'template',
                                    'desc' => $this->l('Disable this option if you would like to send simple email without HTML/CSS styles'),
                                ),
                                array(
                                    'type' => 'textarea',
                                    'label' => $this->l('Mail to admin'),
                                    'name' => 'ETS_CFU_EMAIL_TEMPLATE_ADMIN',
                                    'lang' => true,
                                    'required' => true,
                                    'autoload_rte' => true,
                                    'default' => $this->display('mail_template.tpl'),
                                    'form_group_class' => 'template template2',
                                    'validate' => 'isCleanHtml',
                                    'desc' => $this->l('Available shortcodes:') . ETS_CFU_Tools::displayText('{shop_name}', 'span') . ',' . ETS_CFU_Tools::displayText('{shop_name}', 'span') . ',' . ETS_CFU_Tools::displayText('{shop_logo}', 'span') . ',' . ETS_CFU_Tools::displayText('{message_content}', 'span') . ',' . ETS_CFU_Tools::displayText('{shop_url}', 'span'),
                                ),
                                array(
                                    'type' => 'textarea',
                                    'label' => $this->l('Auto responder'),
                                    'name' => 'ETS_CFU_EMAIL_TEMPLATE_CUSTOMER',
                                    'lang' => true,
                                    'required' => true,
                                    'autoload_rte' => true,
                                    'form_group_class' => 'template template2',
                                    'default' => $this->display('mail_template2.tpl'),
                                    'validate' => 'isCleanHtml',
                                    'desc' => $this->l('Available short codes:') . ETS_CFU_Tools::displayText('{shop_name}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{shop_logo}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{message_content}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{shop_url}', 'span', ['title' => $this->l('Click to Copy')]),
                                ),
                                array(
                                    'type' => 'textarea',
                                    'label' => $this->l('Reply email template'),
                                    'name' => 'ETS_CFU_EMAIL_REPLY_TEMPLATE',
                                    'lang' => true,
                                    'required' => true,
                                    'autoload_rte' => true,
                                    'form_group_class' => 'template template2',
                                    'default' => $this->display('mail_template_reply.tpl'),
                                    'validate' => 'isCleanHtml',
                                    'desc' => $this->l('Available short codes:') . ETS_CFU_Tools::displayText('{shop_name}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{shop_logo}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{message_content}', 'span', ['title' => $this->l('Click to Copy')]) . ',' . ETS_CFU_Tools::displayText('{shop_url}', 'span', ['title' => $this->l('Click to Copy')]),
                                ),
                            ),
                            'submit' => array(
                                'title' => $this->l('Save'),
                            )
                        ),
                    );
                }
                return $this->_email_fields;
            case 'ip_black_list':
                if (!(isset($this->_ip_black_list)) || !$this->_ip_black_list) {
                    $this->_ip_black_list = array(
                        'form' => array(
                            'legend' => array(
                                'title' => $this->l('IP') . " & " . $this->l('Email blacklist'),
                                'icon' => 'icon-user-times'
                            ),
                            'id_form' => 'ets_cfu_module_form_ip_black_list',
                            'input' => array(
                                array(
                                    'type' => 'textarea',
                                    'name' => 'ETS_CFU_IP_BLACK_LIST',
                                    'label' => $this->l('IP blacklist (IPs to block)'),
                                    'desc' => $this->l('Enter exact IP or IP pattern using "*", each IP/IP pattern on a line. For example: 69.89.31.226, 69.89.31.*, *.226, etc.'),
                                    'form_group_class' => 'form_group_contact black_list',
                                    'validate' => 'isCleanHtml',
                                ),
                                array(
                                    'type' => 'textarea',
                                    'name' => 'ETS_CFU_EMAIL_BLACK_LIST',
                                    'label' => $this->l('Email blacklist (emails to block)'),
                                    'desc' => $this->l('Enter exact email address or email pattern using "*", each email/email pattern on a line. For example: example@mail.ru,*@mail.ru, *@qq.com, etc.'),
                                    'form_group_class' => 'form_group_contact black_list',
                                    'validate' => 'isCleanHtml',
                                ),
                                array(
                                    'type' => 'textarea',
                                    'name' => 'ETS_CFU_REGEX_FILTER_SPAM_EMAIL',
                                    'label' => $this->l('Regex code to filter spam email'),
                                    'desc' => $this->l('Enter regex code to filter spam email. Each regex code on a line. For example:'),
                                    'form_group_class' => 'form_group_contact black_list',
                                    'validate' => 'isCleanHtml',
                                ),
                                array(
                                    'type' => 'textarea',
                                    'name' => 'ETS_CFU_REGEX_FILTER_SPAM_CONTENT',
                                    'label' => $this->l('Regex code to filter spam content'),
                                    'desc' => $this->l('Enter regex code to filter spam content. Each regex code on a line. For example:'),
                                    'form_group_class' => 'form_group_contact black_list',
                                    'validate' => 'isCleanHtml',
                                ),
                            ),
                            'submit' => array(
                                'title' => $this->l('Save'),
                            )
                        )
                    );
                }
                return $this->_ip_black_list;
            case 'contact':
                return $this->getContactFields();
            case 'inputs':
                if (!(isset($this->_inputs)) || !$this->_inputs) {
                    $this->_inputs = array(
                        'tex' => array(
                            'id' => 'text',
                            'label' => $this->l('Text'),
                        ),
                        'tea' => array(
                            'id' => 'textarea',
                            'label' => $this->l('Textarea'),
                        ),
                        'ema' => array(
                            'id' => 'email',
                            'label' => $this->l('Email'),
                        ),
                        'pas' => array(
                            'id' => 'password',
                            'label' => $this->l('Password'),
                        ),
                        'tel' => array(
                            'id' => 'tel',
                            'label' => $this->l('Phone'),
                        ),
                        'url' => array(
                            'id' => 'url',
                            'label' => $this->l('URL'),
                        ),
                        'num' => array(
                            'id' => 'number',
                            'label' => $this->l('Number'),
                        ),
                        'dat' => array(
                            'id' => 'date',
                            'label' => $this->l('Date'),
                        ),
                        'fil' => array(
                            'id' => 'file',
                            'label' => $this->l('File'),
                        ),
                        'ref' => array(
                            'id' => 'referrence',
                            'label' => $this->l('Order reference'),
                        ),
                        'men' => array(
                            'id' => 'menu',
                            'label' => $this->l('Dropdown selections'),
                        ),
                        'che' => array(
                            'id' => 'checkbox',
                            'label' => $this->l('Checkboxes'),
                        ),
                        'rad' => array(
                            'id' => 'radio',
                            'label' => $this->l('Radio buttons'),
                        ),
                        'htm' => array(
                            'id' => 'html',
                            'label' => $this->l('HTML'),
                        ),
                        'qui' => array(
                            'id' => 'quiz',
                            'label' => $this->l('Quiz'),
                        ),
                        'acc' => array(
                            'id' => 'acceptance',
                            'label' => $this->l('Acceptance'),
                        ),
                        'rec' => array(
                            'id' => 'recaptcha',
                            'label' => $this->l('ReCaptcha'),
                            'enabled' => (int)Configuration::get('ETS_CFU_ENABLE_RECAPTCHA')
                        ),
                        'cap' => array(
                            'id' => 'captcha',
                            'label' => $this->l('Captcha'),
                        ),
                        'sub' => array(
                            'id' => 'submit',
                            'label' => $this->l('Submit'),
                        ),
                    );
                }
                return $this->_inputs;
            case 'tabs' :
                if (!(isset($this->_tabs)) || !$this->_tabs) {
                    $this->_tabs = array(
                        array(
                            'class_name' => 'AdminContactFormUltimateDashboard',
                            'tab_name' => $this->l('Contact dashboard'),
                            'icon' => 'icon icon-home',
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateContactForm',
                            'tab_name' => $this->l('Contact forms'),
                            'icon' => 'icon icon-envelope-o',
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateMessage',
                            'tab_name' => $this->l('Messages'),
                            'icon' => 'icon icon-comments',
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateStatistics',
                            'tab_name' => $this->l('Statistics'),
                            'icon' => 'icon icon-line-chart',
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateIpBlacklist',
                            'tab_name' => $this->l('IP and Email blacklist'),
                            'icon' => 'icon icon-user-times',
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateSetting',
                            'tab_name' => $this->l('Setting'),
                            'icon' => 'icon icon-cog',
                            'children' => array(
                                array(
                                    'class_name' => 'AdminContactFormUltimateEmail',
                                    'tab_name' => $this->l('Email templates'),
                                    'icon' => 'icon icon-file-text-o',
                                    'active' => 1
                                ),
                                array(
                                    'class_name' => 'AdminContactFormUltimateImportExport',
                                    'tab_name' => $this->l('Import/Export'),
                                    'icon' => 'icon icon-exchange',
                                    'active' => 1
                                ),
                                array(
                                    'class_name' => 'AdminContactFormUltimateIntegration',
                                    'tab_name' => $this->l('Integration'),
                                    'icon' => 'icon icon-cogs',
                                    'active' => 1
                                ),
                            ),
                            'active' => 1
                        ),
                        array(
                            'class_name' => 'AdminContactFormUltimateDownload',
                            'tab_name' => $this->l('File download'),
                            'icon' => 'icon icon-download',
                            'active' => 0
                        ),
                    );
                }
                return $this->_tabs;
        }
    }

    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function display($template)
    {
        if (!$this->module)
            return;
        return $this->module->display($this->module->getLocalPath(), $template);
    }

    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $this->context->shop->domain . $this->context->shop->getBaseURI();
    }

}