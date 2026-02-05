{*
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
*}
{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
{assign var="current_tab" value=""}
{if isset($smarty.request.current_tab) && $smarty.request.current_tab}
{assign var="current_tab" value=$smarty.request.current_tab}
{/if}
{assign var="current_tab_email" value=""}
{if isset($smarty.request.current_tab_email) && $smarty.request.current_tab_email}
{assign var="current_tab_email" value=$smarty.request.current_tab_email}
{/if}
<input type="hidden" name="current_tab" value="{$current_tab|escape:'html':'UTF-8'}" />
<input type="hidden" name="current_tab_email" value="{$current_tab_email|escape:'html':'UTF-8'}" />
{$smarty.block.parent}
{/block}

{block name="label"}
	{if $ps15}
        {if $input.name=='ETS_CFU_ENABLE_RECAPTCHA'}
            <div class="ets_form_tab_header">
                <span class="active" data-tab="other_setting"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg> {l s='Global settings' mod='ets_cfultimate'}</span>
                <span class="" data-tab="google"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 786h725q12 67 12 128 0 217-91 387.5t-259.5 266.5-386.5 96q-157 0-299-60.5t-245-163.5-163.5-245-60.5-299 60.5-299 163.5-245 245-163.5 299-60.5q300 0 515 201l-209 201q-123-119-306-119-129 0-238.5 65t-173.5 176.5-64 243.5 64 243.5 173.5 176.5 238.5 65q87 0 160-24t120-60 82-82 51.5-87 22.5-78h-436v-264z"/></svg> {l s='reCAPTCHA' mod='ets_cfultimate'}</span>
                <span class="" data-tab="black_list">{l s='IP & Email blacklist' mod='ets_cfultimate'}</span>
                <span class="more_tab"><span class="more_three_dots"></span></span>
            </div>
            <div class="form-group-wapper">
            <div class="form-group form_group_contact google">
                <div class="col-lg-3">&nbsp;</div>
                <div class="col-lg-9 ">
                    <p class="alert alert-info">
                        <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                        <a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">{l s='Google reCAPTCHA ' mod='ets_cfultimate'}</a>{l s='is a free service to protect your website from spam and abuse' mod='ets_cfultimate'}<br />
                        {l s='To use reCAPTCHA, you need to install an API key pair' mod='ets_cfultimate'}<br />
                    </p>
                </div>
            </div>
        {/if}
        {if $input.name=='title'}
        <div class="ets_form_tab_header">
                <span class="active" data-tab="form">{l s='Form' mod='ets_cfultimate'}</span>
                <span class="" data-tab="mail">{l s='Mail' mod='ets_cfultimate'}</span>
                <span class="" data-tab="message">{l s='Notifications' mod='ets_cfultimate'}</span>
                <span class="" data-tab="seo">{l s='Seo' mod='ets_cfultimate'}</span>
                <span class="" data-tab="general_settings">{l s='Settings' mod='ets_cfultimate'}</span>
                <span class="more_tab"><span class="more_three_dots"></span></span>
        </div>
        <div class="form-group-wapper">
        {/if}
        {if $input.name=='email_to2'}
            <div class="form-group form_group_contact mail mail2">
                <div class="col-lg-3">&nbsp;</div>
                <div class="col-lg-9">
                    <p class="alert alert-info">
                        <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                        {l s='You can edit the mail template here. For details, see' mod='ets_cfultimate'} <a target="_blank" href="{$link_basic|escape:'html':'UTF-8'}/modules/ets_cfultimate/help/index.html#!/create">{l s='Create your first contact form' mod='ets_cfultimate'}</a>.<br />
                        {l s='In the following fields, you can use these mail-tags such as' mod='ets_cfultimate'}:
                        <span class="ets_cfu_tag_shortcode">
                            <span class="mailtag code unused">[your-name]</span>
                            <span class="mailtag code used">[your-email]</span>
                            <span class="mailtag code used">[your-subject]</span>
                            <span class="mailtag code used">[your-message]</span>
                        </span>
                    </p>
                </div>
            </div>
        {/if}
        {if $input.name=='message_mail_sent_ok'}
            <div class="form-group form_group_contact message">
                <div class="col-lg-3">&nbsp;</div>
                <div class="col-lg-9">
                    <p class="alert alert-info">
                        <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                        {l s='You can edit messages used in various situations here.' mod='ets_cfultimate'}
                    </p>
                </div>
            </div>
        {/if}
        {if $input.name=='title' && isset($fields_value['id_contact']) && $fields_value['id_contact']}
            <div class="form-group form_group_contact form"> 
                <div class="col-lg-3"></div>
                  <div class="col-lg-9">
                       <p class="alert alert-info">
                       <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                        {if isset($fields_value['id_contact']) && $fields_value['id_contact'] && $fields_value['link_contact']}
                        {l s='Form URL:' mod='ets_cfultimate'} <a target="_blank" href="{$fields_value['link_contact']|escape:'html':'UTF-8'}">{$fields_value['link_contact']|escape:'html':'UTF-8'}</a><br />
                        {if !isset($enable_hook_shortcode) || !$enable_hook_shortcode}
                            {l s='You can use shortcode or custom hook to display the contact form.' mod='ets_cfultimate'}&nbsp;<a target="_blank" href="{$enable_hook_shortcode_link nofilter}">{l s='Configure here' mod='ets_cfultimate'}</a><br />
                        {/if}
                        {/if}
                        {if !isset($showShortcodeHook) || (isset($showShortcodeHook)  && $showShortcodeHook)}
                            {l s='Contact form shortcode: ' mod='ets_cfultimate'}<span title="{l s='Click to copy' mod='ets_cfultimate'}" style="position: relative;display: inline-block; vertical-align: middle;"><input type="text" class="ctf-short-code" value='[contact-form-7 id="{$fields_value['id_contact']|intval}"]'/><span class="text-copy">{l s='Copied' mod='ets_cfultimate'}</span></span><br/>
                            {l s='Copy the shortcode above, paste into anywhere on your product description, CMS page content, tpl files, etc. in order to display this contact form' mod='ets_cfultimate'}
                            <br/>
                            {l s='Besides using shortcode to display the contact form, you can also display the contact form using a custom hook. Copy this custom hook' mod='ets_cfultimate'}
                            <span title="{l s='Click to copy' mod='ets_cfultimate'}" style="position: relative;display: inline-block; vertical-align: middle;">
                            <input style="width: 305px ! important;" class="ctf-short-code" type="text" value='{literal}{hook h="displayContactFormUltimate" id="{/literal}{$fields_value.id_contact|intval}{literal}"}{/literal}' /><span class="text-copy">{l s='Copied' mod='ets_cfultimate'}</span></span>
                            {l s=', place into your template .tpl files where you want to display the contact form' mod='ets_cfultimate'}
                        {/if}
                       </p>
                  </div>
             </div>
        {/if}
        <div class="form-group {if isset($input.form_group_class)}{$input.form_group_class|escape:'html':'UTF-8'}{/if}">
    {/if}
    {$smarty.block.parent}
{/block}
{block name="field"}
    {$smarty.block.parent}
    {if $ps15}
        </div>
        {if $input.name=='ETS_CFU_ENABLE_HOOK_SHORTCODE'}
            <div class="form-group form_group_contact export_import">
                <div class="ctf_export_form_content">            
                    <div class="ctf_export_option">
                        <div class="export_title">{l s='Export contact forms' mod='ets_cfultimate'}</div>
                        <p>{l s='Export form configurations of all contact forms of the current shop that you are viewing' mod='ets_cfultimate'}</p>
                        <a target="_blank" href="{$link->getAdminlink('AdminModules',true)|escape:'html':'UTF-8'}&configure=ets_cfultimate&tab_module=front_office_features&module_name=ets_cfultimate&exportContactForm=1" class="btn btn-default mm_export_menu">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h465l135 136q58 56 136 56t136-56l136-136h464q40 0 68 28t28 68zm-325-569q17 41-14 70l-448 448q-18 19-45 19t-45-19l-448-448q-31-29-14-70 17-39 59-39h256v-448q0-26 19-45t45-19h256q26 0 45 19t19 45v448h256q42 0 59 39z"/></svg> {l s='Export contact forms' mod='ets_cfultimate'}
                        </a>
                    </div>                       
                    <div class="ctf_import_option">
                        <div class="export_title">{l s='Import contact forms' mod='ets_cfultimate'}</div>
                        <p>{l s='Import contact forms to the current shop that you are viewing for quick configuration. This is useful when you need to migrate contact forms between websites' mod='ets_cfultimate'}</p>
                            <div class="ctf_import_option_updata">
                                <label for="contactformdata">{l s='Data file' mod='ets_cfultimate'}</label>
                                <input type="file" name="contactformdata" id="contactformdata" />
                            </div>
                            <div class="cft_import_option_clean">
                                <input type="checkbox" name="importdeletebefore" id="importdeletebefore" value="1" />
                                <label for="importdeletebefore">{l s='Delete all contact forms before importing' mod='ets_cfultimate'}</label>
                            </div>
                            <div class="cft_import_option_clean">
                                <input type="checkbox" name="importoverride" id="importoverride" value="1" />
                                <label for="importoverride">{l s='Override all forms with the same IDs' mod='ets_cfultimate'}</label>
                            </div>
                            <div class="cft_import_option_button">
                                <input type="hidden" value="1" name="importContactform" />
                                <div class="cft_import_contact_submit">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 960v448q0 26-19 45t-45 19-45-19l-144-144-332 332q-10 10-23 10t-23-10l-114-114q-10-10-10-23t10-23l332-332-144-144q-19-19-19-45t19-45 45-19h448q26 0 45 19t19 45zm755-672q0 13-10 23l-332 332 144 144q19 19 19 45t-19 45-45 19h-448q-26 0-45-19t-19-45v-448q0-26 19-45t45-19 45 19l144 144 332-332q10-10 23-10t23 10l114 114q10 10 10 23z"/></svg>
                                    <input type="submit" class="btn btn-default cft_import_menu" name="cft_import_contact_submit" value="{l s='Import contact forms' mod='ets_cfultimate'}" />
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        {/if}
        {if $input.name=='message_captcha_not_match' || $input.name=='ETS_CFU_ENABLE_HOOK_SHORTCODE'}
            </div>
        {/if}
    {/if}
{/block}
{block name="input_row"}
{if $input.name == 'open_form_by_button' || $input.name == 'button_popup_enabled'}
<div class="form-group form_group_contact general_settings {$input.name|lower|escape:'html':'UTF-8'}{if $input.name|trim == 'open_form_by_button' && isset($enable_hook_shortcode) && !$enable_hook_shortcode} disabled_hook_shortcode{/if}">
    {if $input.name != 'button_popup_enabled'}
        <h4>{l s='Settings for "Open contact form" button' mod='ets_cfultimate'}</h4>
    {else}
        <h4>{l s='Settings for floating contact form button' mod='ets_cfultimate'}</h4>
    {/if}
{/if}

{if $input.name == 'active'}
    <div class="form-group form_group_contact general_settings">
        <div class="col-lg-3"></div>
        <div class="col-lg-9">
            <p class="ets_cfu_admin_integration_link alert alert-info">
            <i class="icon_alert">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
            </i> {l s='To enable shortcode feature for contact form and display contact form on PrestaShop hooks, please turn on "Enable shortcode for contact form and display contact form in PrestaShop hook" option on' mod='ets_cfultimate'} <a href="{$admin_integration_link nofilter}" target="_blank" rel="noreferrer noopener" >{l s='Setting page' mod='ets_cfultimate'}</a></p>
        </div>
    </div>
{/if}
{if $input.name=='ETS_CFU_ENABLE_RECAPTCHA'}
    <div class="ets_form_tab_header">
        <span class="{if $current_tab == 'other_setting'  || !$current_tab}active{/if}" data-tab="other_setting"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg> {l s='Global settings' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'google'}active{/if}" data-tab="google"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 786h725q12 67 12 128 0 217-91 387.5t-259.5 266.5-386.5 96q-157 0-299-60.5t-245-163.5-163.5-245-60.5-299 60.5-299 163.5-245 245-163.5 299-60.5q300 0 515 201l-209 201q-123-119-306-119-129 0-238.5 65t-173.5 176.5-64 243.5 64 243.5 173.5 176.5 238.5 65q87 0 160-24t120-60 82-82 51.5-87 22.5-78h-436v-264z"/></svg> {l s='reCAPTCHA' mod='ets_cfultimate'}</span>
        <span class="more_tab"><span class="more_three_dots"></span></span>
    </div>
    <div class="form-group-wapper">
    <div class="form-group form_group_contact google">
        <div class="col-lg-3">&nbsp;</div>
        <div class="col-lg-9 ">
            <p class="alert alert-info">
            <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                <a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">{l s='Google reCAPTCHA ' mod='ets_cfultimate'}</a>{l s='is a free service to protect your website from spam and abuse' mod='ets_cfultimate'}<br />
                {l s='To use reCAPTCHA, you need to install an API key pair' mod='ets_cfultimate'}<br />
            </p>
        </div>
    </div>
{/if}
{if $input.name=='short_code'}
{assign var='_svg_random' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M666 481q-60 92-137 273-22-45-37-72.5t-40.5-63.5-51-56.5-63-35-81.5-14.5h-224q-14 0-23-9t-9-23v-192q0-14 9-23t23-9h224q250 0 410 225zm1126 799q0 14-9 23l-320 320q-9 9-23 9-13 0-22.5-9.5t-9.5-22.5v-192q-32 0-85 .5t-81 1-73-1-71-5-64-10.5-63-18.5-58-28.5-59-40-55-53.5-56-69.5q59-93 136-273 22 45 37 72.5t40.5 63.5 51 56.5 63 35 81.5 14.5h256v-192q0-14 9-23t23-9q12 0 24 10l319 319q9 9 9 23zm0-896q0 14-9 23l-320 320q-9 9-23 9-13 0-22.5-9.5t-9.5-22.5v-192h-256q-48 0-87 15t-69 45-51 61.5-45 77.5q-32 62-78 171-29 66-49.5 111t-54 105-64 100-74 83-90 68.5-106.5 42-128 16.5h-224q-14 0-23-9t-9-23v-192q0-14 9-23t23-9h224q48 0 87-15t69-45 51-61.5 45-77.5q32-62 78-171 29-66 49.5-111t54-105 64-100 74-83 90-68.5 106.5-42 128-16.5h256v-192q0-14 9-23t23-9q12 0 24 10l319 319q9 9 9 23z"/></svg></i>'}
{assign var='_svg_bell' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M912 1696q0-16-16-16-59 0-101.5-42.5t-42.5-101.5q0-16-16-16t-16 16q0 73 51.5 124.5t124.5 51.5q16 0 16-16zm816-288q0 52-38 90t-90 38h-448q0 106-75 181t-181 75-181-75-75-181h-448q-52 0-90-38t-38-90q50-42 91-88t85-119.5 74.5-158.5 50-206 19.5-260q0-152 117-282.5t307-158.5q-8-19-8-39 0-40 28-68t68-28 68 28 28 68q0 20-8 39 190 28 307 158.5t117 282.5q0 139 19.5 260t50 206 74.5 158.5 85 119.5 91 88z"/></svg></i>'}
{assign var='_svg_newspaper_o' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 512h-384v384h384v-384zm128 640v128h-640v-128h640zm0-768v640h-640v-640h640zm640 768v128h-512v-128h512zm0-256v128h-512v-128h512zm0-256v128h-512v-128h512zm0-256v128h-512v-128h512zm-1536 960v-960h-128v960q0 26 19 45t45 19 45-19 19-45zm1664 0v-1088h-1536v1088q0 33-11 64h1483q26 0 45-19t19-45zm128-1216v1216q0 80-56 136t-136 56h-1664q-80 0-136-56t-56-136v-1088h256v-128h1792z"/></svg></i>'}
{assign var='_svg_flag' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M320 256q0 72-64 110v1266q0 13-9.5 22.5t-22.5 9.5h-64q-13 0-22.5-9.5t-9.5-22.5v-1266q-64-38-64-110 0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm1472 64v763q0 25-12.5 38.5t-39.5 27.5q-215 116-369 116-61 0-123.5-22t-108.5-48-115.5-48-142.5-22q-192 0-464 146-17 9-33 9-26 0-45-19t-19-45v-742q0-32 31-55 21-14 79-43 236-120 421-120 107 0 200 29t219 88q38 19 88 19 54 0 117.5-21t110-47 88-47 54.5-21q26 0 45 19t19 45z"/></svg></i>'}
{assign var='_svg_envelop' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg></i>'}
{assign var='_svg_links' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1520 1216q0-40-28-68l-208-208q-28-28-68-28-42 0-72 32 3 3 19 18.5t21.5 21.5 15 19 13 25.5 3.5 27.5q0 40-28 68t-68 28q-15 0-27.5-3.5t-25.5-13-19-15-21.5-21.5-18.5-19q-33 31-33 73 0 40 28 68l206 207q27 27 68 27 40 0 68-26l147-146q28-28 28-67zm-703-705q0-40-28-68l-206-207q-28-28-68-28-39 0-68 27l-147 146q-28 28-28 67 0 40 28 68l208 208q27 27 68 27 42 0 72-31-3-3-19-18.5t-21.5-21.5-15-19-13-25.5-3.5-27.5q0-40 28-68t68-28q15 0 27.5 3.5t25.5 13 19 15 21.5 21.5 18.5 19q33-31 33-73zm895 705q0 120-85 203l-147 146q-83 83-203 83-121 0-204-85l-206-207q-83-83-83-203 0-123 88-209l-88-88q-86 88-208 88-120 0-204-84l-208-208q-84-84-84-204t85-203l147-146q83-83 203-83 121 0 204 85l206 207q83 83 83 203 0 123-88 209l88 88q86-88 208-88 120 0 204 84l208 208q84 84 84 204z"/></svg></i>'}
{assign var='_svg_cog' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></i>'}
{assign var='_svg_refresh' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1639 1056q0 5-1 7-64 268-268 434.5t-478 166.5q-146 0-282.5-55t-243.5-157l-129 129q-19 19-45 19t-45-19-19-45v-448q0-26 19-45t45-19h448q26 0 45 19t19 45-19 45l-137 137q71 66 161 102t187 36q134 0 250-65t186-179q11-17 53-117 8-23 30-23h192q13 0 22.5 9.5t9.5 22.5zm25-800v448q0 26-19 45t-45 19h-448q-26 0-45-19t-19-45 19-45l138-138q-148-137-349-137-134 0-250 65t-186 179q-11 17-53 117-8 23-30 23h-199q-13 0-22.5-9.5t-9.5-22.5v-7q65-268 270-434.5t480-166.5q146 0 284 55.5t245 156.5l130-129q19-19 45-19t45 19 19 45z"/></svg></i>'}
    <div class="ets_form_tab_header">
        <span class="{if $current_tab == 'seo' || !$current_tab}active{/if}" data-tab="seo">{$_svg_links nofilter} {l s='Info' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'form'}active{/if}" data-tab="form">{$_svg_newspaper_o nofilter} {l s='Form' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'condition'}active{/if}" data-tab="condition">{$_svg_random nofilter} {l s='Logic conditions' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'mail'}active{/if}" data-tab="mail">{$_svg_envelop nofilter} {l s='Mail' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'message'}active{/if}" data-tab="message">{$_svg_bell nofilter} {l s='Notifications' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'thank_you'}active{/if}" data-tab="thank_you">{$_svg_flag nofilter} {l s='Thank you page' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'general_settings'}active{/if}" data-tab="general_settings">{$_svg_cog nofilter} {l s='Settings' mod='ets_cfultimate'}</span>
        <span class="{if $current_tab == 'mailchimp'}active{/if}" data-tab="mailchimp">{$_svg_refresh nofilter} {l s='Synchronization' mod='ets_cfultimate'}</span>
        <span class="more_tab"><span class="more_three_dots"></span></span>
    </div>
    <div class="form-group-wapper">
{/if}
{if $input.name=='email_to'}
    <div class="form-group form_group_contact mail menu">
        <ul class="ets_cfu_mail_menu">
            <li class="ets_cfu_item mail1 {if $current_tab_email == 'mail1' || !$current_tab_email}active{/if}" data-tab="mail1">
                <span>{l s='Email to admin' mod='ets_cfultimate'}</span>
            </li>
            <li class="ets_cfu_item mail2 {if $current_tab_email == 'mail2'}active{/if}" data-tab="mail2">
                <span>{l s='Auto responder' mod='ets_cfultimate'}</span>
            </li>
        </ul>
    </div>
    <div class="ets_cfu_block_short_code form_group_contact mail hide">
        <h3 class="ets_cfu_title">{l s='Available mail-tags' mod='ets_cfultimate'}</h3>
        <p class="ets_cfu_desc">{l s='Copy mail-tags below and paste into any configuration fields of the "Email to admin" and "Auto responder" to get form input value.' tags=['<br>','<span class="ets_cfu_example">'] mod='ets_cfultimate'}</p>
        <ul class="ets_cfu_block_ul" data-title="{l s='Click to copy' mod='ets_cfultimate'}"></ul>
    </div>
{/if}
{if $input.name=='message_mail_sent_ok'}
    <div class="form-group form_group_contact message">
        <div class="col-lg-3">&nbsp;</div>
        <div class="col-lg-9">
            <p class="alert alert-info">
            <i class="icon_alert">
                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        </i>
                {l s='You can edit notification messages used in various situations here.' mod='ets_cfultimate'}
            </p>
        </div>
    </div>
{/if}
{if $input.name == 'enable_form_page' && isset($fields_value['id_contact']) && $fields_value['id_contact']}
    <div class="form-group form_group_contact seo">
        <div class="col-lg-3"></div>
        <div class="col-lg-9">
            <p class="alert alert-info">
            <i class="icon_alert">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
            </i>
            {if isset($fields_value['id_contact']) && $fields_value['id_contact'] && $fields_value['link_contact']}
            {l s ='Form URL:' mod='ets_cfultimate'} <a target="_blank" href="{$fields_value['link_contact']|escape:'html':'UTF-8'}">{$fields_value['link_contact']|escape:'html':'UTF-8'}</a><br />
            {/if}
            {if !isset($enable_hook_shortcode) || !$enable_hook_shortcode}
                {l s='You can use shortcode or custom hook to display the contact form.' mod='ets_cfultimate'}&nbsp;<a target="_self" href="{$enable_hook_shortcode_link nofilter}">{l s='Configure here' mod='ets_cfultimate'}</a><br />
            {/if}
            {if !isset($showShortcodeHook) || (isset($showShortcodeHook)  && $showShortcodeHook)}
                {l s='Contact form shortcode: ' mod='ets_cfultimate'}<span title="{l s='Click to copy' mod='ets_cfultimate'}" style="position: relative;display: inline-block; vertical-align: middle;">
                    <input type="text" class="ctf-short-code" value='[contact-form-7 id="{$fields_value['id_contact']|intval}"]'/><span class="text-copy">{l s='Copied' mod='ets_cfultimate'}</span></span><br/>
                {l s='Copy the shortcode above, paste into anywhere on your product description, CMS page content, tpl files, etc. in order to display this contact form' mod='ets_cfultimate'}
                <br />
                {l s='Besides using shortcode to display the contact form, you can also display the contact form using a custom hook. Copy this custom hook' mod='ets_cfultimate'}
                <span title="{l s='Click to copy' mod='ets_cfultimate'}" style="position: relative;display: inline-block; vertical-align: middle;">
                <input style="width: 305px ! important;" class="ctf-short-code" type="text" value='{literal}{hook h="displayContactFormUltimate" id="{/literal}{$fields_value.id_contact|intval}{literal}"}{/literal}' /><span class="text-copy">{l s='Copied' mod='ets_cfultimate'}</span></span>
                {l s=', place into your template .tpl files where you want to display the contact form' mod='ets_cfultimate'}
            {/if}
            </p>
        </div>
    </div>
{/if}
{if $input.name=='short_code'}
    <div class="form-group form_group_contact form ets_cfu_add_contact" data-multi-lang="{$languages|count > 1|intval}" data-default-lang="{$defaultFormLanguage|intval}" style="display: none;">
        <div class="ets_cfu_add_form_contact">
            {assign var="is_render_form" value=(isset($fields_value.render_form) && $fields_value.render_form)}
            <div class="ets_cfu_add_form">
                {if $is_render_form}{$fields_value.render_form nofilter}{/if}
            </div>
            <div class="ets_cfu_form_empty contact-form"{if $is_render_form} style="display: none;"{/if}>
                <h4 class="ets_cfu_form_title">{l s='Contact form is blank' mod='ets_cfultimate'}</h4>
                <a class="ets_cfu_add_input first_item" href="javascript:void(0)">{l s='Add new input field/row to create your contact form' mod='ets_cfultimate'}</a>
            </div>
            <div class="ets_cfu_action">
               <button class="ets_cfu_add_input btn" name="ets_cfu_add_input btn"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</button>
               <button class="ets_cfu_add_row btn" name="ets_cfu_add_row btn"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 1344v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45z"/></svg>&nbsp;{l s='Add row' mod='ets_cfultimate'}</button>
            </div>
        </div>
        <div class="ets_cfu_form_popup">
            <div class="ets_cfu_table">
                <div class="ets_cfu_table_cell">
                    <div class="ets_cfu_wrapper">
                        <span class="ets_cfu_close_popup" title="{l s='Close' mod='ets_cfultimate'}">{l s='Close' mod='ets_cfultimate'}</span>
                        <div class="ets_cfu_form_load"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ets_cfu_form_group">
            <div class="ets_cfu_row_group">
                {include file="./rows.tpl"}
            </div>
            <div class="ets_cfu_input_group">
                {include file="./inputs.tpl"}
            </div>
        </div>
    </div>
{/if}
{if $input.name=='condition'}
    <div class="form-group form_group_contact condition ets_cfu_add_condition" style="display: none;">
        {assign var='condition' value=$fields_value.condition}
        {if isset($condition.fields_form)}{assign var="fields_form" value=$condition.fields_form}{else}{assign var="fields_form" value=[]}{/if}
        <input id="condition_fields_form" type="hidden" name="condition_fields_form" value="">
        <div class="ets_cfu_add_form_condition">
            <div class="ets_cfu_condition_form">
                <div class="ets_cfu_condition_item" data-id="0" style="display: none; !important;">
                    <div class="ets_cfu_condition_item_left ets1">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                 <label for="if_0" class="required">{l s='If' mod='ets_cfultimate'}</label>
                                <select id="if_0" name="if[0]" disabled></select>
                            </div>
                            <div class="col-lg-6">
                                <label for="operator_0">{l s='Is' mod='ets_cfultimate'}</label>
                                <select id="operator_0" name="operator[0]" disabled>
                                    {foreach from=$condition_operator key='ik1' item='option'}<option value="{$ik1|escape:'html':'UTF-8'}">{$option|escape:'html':'UTF-8'}</option>{/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12">
                                 <label for="value_0" class="required">{l s='Value' mod='ets_cfultimate'}</label>
                                 <div id="field_value_0"></div>
                             </div>
                        </div>
                        <div class="row form-group">
                            <div  class="col-lg-6">
                                <label for="do_0">{l s='Do' mod='ets_cfultimate'}</label>
                                <select id="do_0" name="do[0]" disabled>
                                    {foreach from=$condition_do key='ik2' item='do'}<option value="{$ik2|intval}">{$do|escape:'html':'UTF-8'}</option>{/foreach}
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="fields_0" class="required">{l s='Fields' mod='ets_cfultimate'}</label>
                                <select id="fields_0" name="fields[0][]" disabled></select>
                            </div>
                        </div>
                    </div>
                    <button class="ets_cfu_condition_remove btn btn-default" name="ets_cfu_condition_remove" type="button" title="{l s='Remove' mod='ets_cfultimate'}">
                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                    </button>
                </div>
                {if !empty($condition) && $condition.if|count > 0 && isset($fields_form) && fields_form}
                    {assign var='field_type' value=[]}{assign var='field_values' value=[]}
                    {foreach from=$condition.if key='id' item='item'}
                        <div class="ets_cfu_condition_item" data-id="{$id|escape:'html':'UTF-8'}">
                            <div class="ets_cfu_condition_item_left ets">
                                <div class="row form-group">
                                    <div class="col-lg-6">
                                        <label class="required" for="if_{$id|escape:'html':'UTF-8'}">{l s='If' mod='ets_cfultimate'}</label>
                                        <select id="if_{$id|escape:'html':'UTF-8'}" name="if[{$id|escape:'html':'UTF-8'}]">
                                            <option value="--" data-type="--">{l s='-- Select an item --' mod='ets_cfultimate'}</option>
                                            {assign var='loop' value=0}
                                            {foreach from=$fields_form item='field'}
                                                {if $loop == 0}
                                                    {assign var='field_type' value=$field.type}{assign var='field_values' value=$field.values}
                                                 {/if}
                                                <option value="{$field.name|escape:'html':'UTF-8'}" data-type="{$field.type|escape:'html':'UTF-8'}"{if $field.name == $item}{assign var='field_type' value=$field.type}{assign var='field_values' value=$field.values} selected{/if}>{$field.label|escape:'html':'UTF-8'}</option>
                                                {assign var='loop' value=$loop+1}
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="operator_{$id|escape:'html':'UTF-8'}">{l s='Is' mod='ets_cfultimate'}</label>
                                        <select id="operator_{$id|escape:'html':'UTF-8'}" name="operator[{$id|escape:'html':'UTF-8'}]">
                                            {foreach from=$condition_operator key='ik3' item='option'}<option value="{$ik3|escape:'html':'UTF-8'}"{if $condition.operator[$id]|intval == $ik3|intval} selected{/if}>{$option|escape:'html':'UTF-8'}</option>{/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12">
                                         <label class="required" for="value_{$id|escape:'html':'UTF-8'}">{l s='Value' mod='ets_cfultimate'}</label>
                                         <div id="field_value_{$id|escape:'html':'UTF-8'}">
                                            {if isset($field_type)}
                                                {if in_array($field_type|trim, ['checkbox', 'radio'])}
                                                    {if isset($field_values)}
                                                        {foreach from=$field_values item='item'}
                                                            {if $item.value|trim !== ''}
                                                                <label class="ets_cfu_condition_label_radio" for="value_{$id nofilter}_{$item.value nofilter}"><input id="value_{$id nofilter}_{$item.value nofilter}" type="{$field_type nofilter}" name="value[{$id nofilter}][]" value="{$item.value nofilter}"{if isset($condition.value[$id]) && in_array($item.value,$condition.value[$id])} checked{/if} />{$item.label nofilter}</label>
                                                            {/if}
                                                         {/foreach}
                                                    {/if}
                                                {elseif $field_type|trim=='menu'}
                                                    <select id="value_{$id|escape:'html':'UTF-8'}" name="value[{$id|escape:'html':'UTF-8'}]">
                                                        {if isset($field_values)}
                                                            {foreach from=$field_values item='item'}
                                                                {if $item.value|trim !== ''}
                                                                    <option value="{$item.value|escape:'html':'UTF-8'}"{if isset($condition.value[$id]) && $condition.value[$id]==$item.value} selected{/if}>{$item.label|escape:'html':'UTF-8'}</option>
                                                                {/if}
                                                            {/foreach}
                                                        {/if}
                                                    </select>
                                                {else}
                                                    <input type="text" id="value_{$id|escape:'html':'UTF-8'}" name="value[{$id|escape:'html':'UTF-8'}]"{if isset($condition.value[$id])} value="{$condition.value[$id]|escape:'html':'UTF-8'}"{/if}/>
                                                {/if}
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div  class="col-lg-6">
                                        <label for="do_{$id|escape:'html':'UTF-8'}">{l s='Do' mod='ets_cfultimate'}</label>
                                        <select id="do_{$id|escape:'html':'UTF-8'}" name="do[{$id|escape:'html':'UTF-8'}]">
                                            {foreach from=$condition_do key='ik4' item='do'}<option value="{$ik4|intval}"{if $condition.do[$id]|intval == $ik4|intval} selected{/if}>{$do|escape:'html':'UTF-8'}</option>{/foreach}
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="required" for="fields_{$id|escape:'html':'UTF-8'}">{l s='Fields' mod='ets_cfultimate'}</label>
                                        <select id="fields_{$id|escape:'html':'UTF-8'}" name="fields[{$id|escape:'html':'UTF-8'}][]"{if isset($condition.fields[$id]) && is_array($condition.fields[$id]) && $condition.fields[$id]|count > 1} multiple{/if}>
                                            <option value="-1" data-type="--">{l s='-- Select an item --' mod='ets_cfultimate'}</option>
                                            {foreach from=$fields_form item='field'}
                                                <option value="{$field.name|escape:'html':'UTF-8'}" data-type="{$field.type|escape:'html':'UTF-8'}"{if in_array($field.name, $condition.fields[$id])} selected{/if}{if $field.name == $item} disabled{/if}>{$field.label|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button class="ets_cfu_condition_remove btn btn-default" name="ets_cfu_condition_remove" type="button" title="{l s='Remove' mod='ets_cfultimate'}">
                                <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                            </button>
                        </div>
                    {/foreach}
                {/if}
            </div>
            <div class="ets_cfu_form_empty condition"{if !empty($fields_form)} style="display: none;"{/if}>
                <h4 class="ets_cfu_form_title">{l s='Condition form is blank' mod='ets_cfultimate'}</h4>
                <a class="ets_cfu_add_condition first_item" href="javascript:void(0)">{l s='Add new condition to your contact form' mod='ets_cfultimate'}</a>
            </div>
            <div class="ets_cfu_action condition"{if empty($fields_form)} style="display: none;"{/if}>
               <button class="ets_cfu_add_condition2 btn btn-primary" name="ets_cfu_add_condition2" type="button"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add condition field' mod='ets_cfultimate'}</button>
            </div>
        </div>
    </div>
{/if}
{if $input.name == 'mailchimp_mapping_data'}
    <div class="form-group form_group_contact mailchimp mailchimp_merge_fields{if !isset($mailchimp_merge_fields) || !$mailchimp_merge_fields} hide{/if}">
        <label class="control-label col-lg-4">{l s='Mapping data' mod='ets_cfultimate'}</label>
        <div class="col-lg-4">
            <table class="table table-merge-fields">
                <thead>
                    <tr>
                        <th>{l s='Field label' mod='ets_cfultimate'}</th>
                        <th>{l s='Field name' mod='ets_cfultimate'}</th>
                        <th>{l s='Mapping field' mod='ets_cfultimate'}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="display: none" data-name="[name]">
                        <td>[label]</td>
                        <td>[name]</td>
                        <td>
                            <select name="mailchimp_merge_field[[name]]" data-id="[name]" disabled>
                                <option value="-1">{l s='-- Do not import --' mod='ets_cfultimate'}</option>
                                {if isset($mailchimp_merge_fields) && $mailchimp_merge_fields}{foreach from=$mailchimp_merge_fields key='tagName' item='item'}
                                    <option value="{$tagName nofilter}" data-id="{$item.id nofilter}">{$item.name nofilter}</option>
                                {/foreach}{/if}
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="help-block">{l s='Mapping fields: Address 1, City, Zipcode, State, Country must be selected!' mod='ets_cfultimate'}</p>
        </div>
    </div>
{/if}
{if $input.name=='condition'}
<input type="hidden" name="condition" id="condition" value="">
{else}{$smarty.block.parent}{/if}
{if $input.name=='button_icon_custom_file' || $input.name=='floating_icon_custom_file'}
</div>
{/if}
{if $input.name=='mailchimp_mapping_data' || $input.name=='ETS_CFU_ENABLE_HOOK_SHORTCODE'}
    </div>
{/if}
{/block}

{block name="input"}
{if $input.type == 'switch'}
    {if isset($input.values) && $input.values}
    <span class="switch prestashop-switch fixed-width-lg">
        {foreach $input.values as $value}
        <input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if (isset($input.disabled) && $input.disabled) or (isset($value.disabled) && $value.disabled)} disabled="disabled"{/if}/>
        {strip}<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
            {if $value.value == 1}
                {l s='Yes' d='Admin.Global' mod='ets_cfultimate'}
            {else}
                {l s='No' d='Admin.Global' mod='ets_cfultimate'}
            {/if}
        </label>{/strip}
        {/foreach}
        <a class="slide-button btn"></a>
    </span>
    {/if}
{elseif isset($input.multi) && $input.multi}
    <ul class="ets_cfu_ul{if isset($input.mail_tag) && $input.mail_tag} mail-tag{/if} {$input.name|lower|escape:'html':'UTF-8'} form-group" data-ul="{$input.name|lower|escape:'html':'UTF-8'}">
        {assign var="ik" value=0}
        {assign var="key" value='multi_'|cat:$input.name}
        {assign var="end" value=-1}
        {if isset($fields_value.$key) && $fields_value.$key}
            {assign var="end" value=$fields_value.$key|count - 1}
            {foreach from=$fields_value.$key item='email'}
                {include file="./email.tpl" element = $email}
                {assign var="ik" value=$ik+1}
            {/foreach}
        {/if}
        {if (isset($input.show_btn_add) && $input.show_btn_add && $ik >= $end) || (empty($fields_value.$key) && $ik > $end)}{include file="./email.tpl"}{/if}
    </ul>
    {$smarty.block.parent}
{elseif $input.type == 'group'}
    {if count($input.values) && isset($input.values)}
    <div class="row">
        <div class="col-lg-6">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="fixed-width-xs">
                            <span class="title_box">
                                <input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, '{$input.name|escape:'html':'UTF-8'}[]', this.checked)" />
                            </span>
                        </th>
                        <th class="fixed-width-xs"><span class="title_box">{l s='ID' d='Admin.Global' mod='ets_cfultimate'}</span></th>
                        <th>
                            <span class="title_box">
                                {l s='Group name' mod='ets_cfultimate'}
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                {foreach $input.values as $key => $group}
                    <tr>
                        <td>
                            {assign var=id_checkbox value=$group['id_group']}
                            <input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" class="groupBox" id="{$id_checkbox|intval}" value="{$group['id_group']|escape:'html':'UTF-8'}" {if ! $input.is_id}checked="checked"{else}{if in_array($group['id_group'], $input.custommer_access)}checked="checked"{/if}{/if} />
                        </td>
                        <td>{$group['id_group']|escape:'html':'UTF-8'}</td>
                        <td>
                            <label for="{$id_checkbox|escape:'html':'UTF-8'}">{$group['name']|escape:'html':'UTF-8'}</label>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {else}
    <p>
        {l s='No group created' mod='ets_cfultimate'}
    </p>
    {/if}
{elseif $input.type == 'textarea' && $ps15}
    {if isset($input.lang) AND $input.lang && $languages|count > 1}
        <div class="translatable translatable-field">
            {foreach $languages as $language}
                <div class="lang_{$language.id_lang|intval}" id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                    <textarea cols="{$input.cols|escape:'html':'UTF-8'}" rows="{$input.rows|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{/if} {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}" >{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
                </div>
            {/foreach}
        </div>
    {else}
        <textarea name="{$input.name|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" cols="{$input.cols|escape:'html':'UTF-8'}" rows="{$input.rows|escape:'html':'UTF-8'}" {if isset($input.autoload_rte) && $input.autoload_rte}class="rte autoload_rte {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"{/if}>{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}</textarea>
    {/if}
{elseif $input.type == 'icon'}
    {if isset($input.icons) && $input.icons && isset($input.icons[$fields_value[$input.name]]) && $input.icons[$fields_value[$input.name]].icon !== ''}
        <span class="{$input.name|escape:'html':'UTF-8'}_selected">{$input.icons[$fields_value[$input.name]|trim].icon nofilter}</span>
    {/if}
    <input id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" type="text" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|trim nofilter}{/if}"/>
    {if isset($input.icons) && $input.icons}
        <ul class="{$input.name|escape:'html':'UTF-8'}_icons_ul">
            {foreach from=$input.icons key='id' item='icon'}
                <li class="ets_cfu_icon_li" data-id="{$id|escape:'html':'UTF-8'}">{$icon.icon nofilter}</li>
            {/foreach}
        </ul>
    {/if}
{else}
    {if $input.name!='mailchimp_api_key'}
        {$smarty.block.parent}
    {/if}
    {if $input.name == 'ETS_CFU_CACHE_LIFETIME'}
        <a class="ets_cfu_clear_cache" href="#">{l s='Clear all cache' mod='ets_cfultimate'}</a>
    {/if}
    {if $input.name=='mailchimp_api_key'}
        <div class="mailchimp_api_key_column">
            <div class="mailchimp_api_key_column_flex">
                {$smarty.block.parent}
                <a class="btn btn-default btn_check_api_key" href="{$check_apikey_link nofilter}">{l s='Check API key' mod='ets_cfultimate'}</a>
            </div>
            <p class="help-block">
                <a class="how_get_mailchimp" href="{$mailchimp_doc_link nofilter}" target="_blank">{l s='How to get Mailchimp API key?' mod='ets_cfultimate'}</a>
            </p>
            <a class="btn btn-primary btn_setup_mailchimp" href="{$setup_mailchimp_link nofilter}">{l s='Setup' mod='ets_cfultimate'}</a>
        </div>
    {/if}
    {if $input.type=='file' && $input.base_url && isset($fields_value[$input.name]) && $fields_value[$input.name]|trim != '' && file_exists($input.base_url|cat:$fields_value[$input.name])}
        <div class="ets_cfu_image_wrapper" data-id="{$input.name|lower|escape:'html':'UTF-8'}_delete">
            <img src="{$input.base_uri|cat:$fields_value[$input.name] nofilter}" style="max-width: 100px; max-height: 100px;">
            <span class="ets_cfu_delete_image"><svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></span>
        </div>
        <input type="hidden" name="{$input.name|lower|escape:'html':'UTF-8'}_delete" value="0">
    {/if}
{/if}
{if $input.name=='title_alias'&& isset($fields_value['id_contact']) && $fields_value['id_contact']}
    <div class="col-lg-9">
        {if $languages|count > 1}
            {foreach from=$languages item='language'}
                <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    <i class="ets_cfu_page_url">{l s='Form page url:' mod='ets_cfultimate'}</i>&nbsp;<a class="ets_cfu_page_url" target="_blank" href="{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,$language['id_lang']|intval)}">{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,$language['id_lang']|intval)}</a>
                </div>
            {/foreach}
        {else}
            <i class="ets_cfu_page_url">{l s='Form page url:' mod='ets_cfultimate'}</i>&nbsp;<a class="ets_cfu_page_url" target="_blank" href="{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval)}">{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval)}</a>
        {/if}
    </div>
{/if}
{if $input.name=='thank_you_alias'&& isset($fields_value['id_contact']) && $fields_value['id_contact']}
    <div class="col-lg-9">
        {if $languages|count > 1}
            {foreach from=$languages item='language'}
                <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    <i class="ets_cfu_page_url">{l s='Form page url:' mod='ets_cfultimate'}</i>&nbsp;<a class="ets_cfu_page_url" target="_blank" href="{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,$language['id_lang']|intval,'thank')}">{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,$language['id_lang']|intval,'thank')}</a>
                </div>
            {/foreach}
        {else}
            <i class="ets_cfu_page_url">{l s='Form page url:' mod='ets_cfultimate'}</i>&nbsp;<a class="ets_cfu_page_url" target="_blank" href="{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,0,'thank')}">{ets_cfultimate::getLinkContactForm($fields_value['id_contact']|intval,0,'thank')}</a>
        {/if}
    </div>
{/if}
{if $input.name == 'button_background_hover_color'}
    <button class="ets_cfu_reset_color btn btn-primary" name="ets_cfu_reset_color" type="button">
        <i class="ets_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1639 1056q0 5-1 7-64 268-268 434.5t-478 166.5q-146 0-282.5-55t-243.5-157l-129 129q-19 19-45 19t-45-19-19-45v-448q0-26 19-45t45-19h448q26 0 45 19t19 45-19 45l-137 137q71 66 161 102t187 36q134 0 250-65t186-179q11-17 53-117 8-23 30-23h192q13 0 22.5 9.5t9.5 22.5zm25-800v448q0 26-19 45t-45 19h-448q-26 0-45-19t-19-45 19-45l138-138q-148-137-349-137-134 0-250 65t-186 179q-11 17-53 117-8 23-30 23h-199q-13 0-22.5-9.5t-9.5-22.5v-7q65-268 270-434.5t480-166.5q146 0 284 55.5t245 156.5l130-129q19-19 45-19t45 19 19 45z"/></svg></i> {l s='Reset to default color' mod='ets_cfultimate'}
    </button>
{/if}
{if $input.name == 'floating_background_hover_color'}
    <button class="ets_cfu_floating_reset_color btn btn-primary" name="ets_cfu_floating_reset_color" type="button">
        <i class="ets_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1639 1056q0 5-1 7-64 268-268 434.5t-478 166.5q-146 0-282.5-55t-243.5-157l-129 129q-19 19-45 19t-45-19-19-45v-448q0-26 19-45t45-19h448q26 0 45 19t19 45-19 45l-137 137q71 66 161 102t187 36q134 0 250-65t186-179q11-17 53-117 8-23 30-23h192q13 0 22.5 9.5t9.5 22.5zm25-800v448q0 26-19 45t-45 19h-448q-26 0-45-19t-19-45 19-45l138-138q-148-137-349-137-134 0-250 65t-186 179q-11 17-53 117-8 23-30 23h-199q-13 0-22.5-9.5t-9.5-22.5v-7q65-268 270-434.5t480-166.5q146 0 284 55.5t245 156.5l130-129q19-19 45-19t45 19 19 45z"/></svg></i> {l s='Reset to default color' mod='ets_cfultimate'}
    </button>
{/if}
{/block}

{block name="legend"}
<div class="panel-heading">
	{if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
	{if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
	{if isset($field.title)}{$field.title|escape:'html':'UTF-8'}{/if}
    {if isset($field.new) && $field.new && (!isset($smarty.get.etsCfuEditContact) && !isset($smarty.get.etsCfuAddContact))}
        <span class="panel-heading-action">
            <a id="desc-contactform-new" class="list-toolbar-btn" href="{$field.new|escape:'html':'UTF-8'}" title="{l s='Add new' mod='ets_cfultimate'}">
                <span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Add new' mod='ets_cfultimate'}" data-html="true" data-placement="top" title="{l s='Add new' mod='ets_cfultimate'}">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
    {/if}
</div>
{/block}

{block name="description"}
	{if isset($input.desc) && !empty($input.desc)}
		<p class="help-block">
			{if is_array($input.desc)}
				{foreach $input.desc as $p}
					{if is_array($p)}
						<span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span>
					{else}
						{$p|escape:'html':'UTF-8'}
					{/if}
				{/foreach}
			{else}
				{$input.desc nofilter}
			{/if}
			{if $input.name == 'ETS_CFU_REGEX_FILTER_SPAM_EMAIL'}
                <p style="margin-bottom: 10px;"><span class="regex">{l s='1. Email contains a suspicious domain.' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/[a-z0-9._%+-]+@[a-z0-9.-]+\.(ru|cn|xyz|info|click|top|work|club|online|shop|buzz)\b/i' nofilter}</code></p>

                <p style="margin-bottom: 10px;"><span class="regex">{l s='2. Email address is suspiciously long or contains too many characters.' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/[a-z0-9._%+-]{30,}@/' nofilter}</code></p>

                <p style="margin-bottom: 10px;"><span class="regex">{l s='3. Email contains too many special characters (e.g., hyphens or underscores).' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/[a-z0-9._%+-]*[-_]{2,}[a-z0-9._%+-]*@/' nofilter}</code></p>

                <p style="margin-bottom: 10px;"><span class="regex">{l s='4. Email address starts with non-letter characters.' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/^[^a-z]+[a-z0-9._%+-]*@/' nofilter}</code></p>

                <p style="margin-bottom: 10px;"><span class="regex">{l s='5. Email contains an invalid domain.' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/@[a-z0-9.-]*\.[a-z]{1,1}\b/i' nofilter}</code></p>

                <p style="margin-bottom: 10px;"><span class="regex">{l s='6. Email is from a known disposable or spammy email provider.' mod='ets_cfultimate'}</span>
			    <br><code>{'/[a-z0-9._%+-]+@(freemail|spammail|junkmail|trashmail)\b/i' nofilter}</code></p>
			{/if}
			{if $input.name == 'ETS_CFU_REGEX_FILTER_SPAM_CONTENT'}
			    <p style="margin-bottom: 10px;"><span class="regex">{l s='1. Detect spam trigger words related to promotions or offers' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\b(free|credit|offer|win|winner|discount|cash|deal|prize|loan|bonus)\b/i' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='2. Detect excessive use of exclamation marks' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/!{2,}/' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='3. Detect long uppercase text (overuse of capital letters)' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\b[A-Z]{5,}\b/' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='4. Detect suspicious links (common spam domains)' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\bhttps?:\/\/[^\s]*\.(xyz|info|click|top|work|club|online|shop|buzz)\b/i' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='5. Detect unusual or unnecessary special characters' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/[^\x00-\x7F]/' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='6. Detect typical "click here" or "buy now" spam phrases' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\b(click here|buy now|order now|subscribe here)\b/i' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='7. Detect long sequences of digits or unusual characters' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/[\w]{12,}/' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='8. Detect excessive whitespace or blank spaces' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\s{4,}/' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='9. Detect fake email marketing links' mod='ets_cfultimate'}</span>
			    <br><code style="margin-bottom: 10px;">{'/\b(unsubscribe|remove me|opt out|this is not spam)\b/i' nofilter}</code></p>

			    <p style="margin-bottom: 10px;"><span class="regex">{l s='10. Detect suspicious email addresses (spammy domains)' mod='ets_cfultimate'}</span>
			    <br><code>{'/[a-z0-9._%+-]+@[a-z0-9.-]+\.(ru|cn|xyz|info|click|top|work|club|online|shop|buzz)\b/i' nofilter}</code></p>
            {/if}
		</p>
	{/if}
{/block}