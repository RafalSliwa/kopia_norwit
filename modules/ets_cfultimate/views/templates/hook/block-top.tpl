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
<script type="text/javascript">
    var ets_cfu_default_lang = {$ets_cfu_default_lang|intval};
    var ets_cfu_is_updating = {$ets_cfu_is_updating|intval};
    var PS_ALLOW_ACCENTED_CHARS_URL = true;
    var detele_confirm = "{l s='Do you want to delete?' mod='ets_cfultimate'}";
    var ets_cfu_msg_email_required = "{l s='Email is required.' mod='ets_cfultimate'}";
    var ets_cfu_msg_email_invalid = "{l s='Email %s is invalid.' mod='ets_cfultimate'}";
    var ets_cfu_msg_email_exist = "{l s='This email is exist. Please enter another email address.' mod='ets_cfultimate'}";
    var ets_cfu_label_delete = "{l s='Delete' mod='ets_cfultimate'}";
    var ets_cfu_copy_msg = "{l s='Copied' mod='ets_cfultimate'}";
    var ets_cfu_delete_msg = "{l s='Do you want to delete this item?' mod='ets_cfultimate'}";
    var ets_cfu_btn_back_label = "{l s='Back' mod='ets_cfultimate'}";
    var ets_cfu_btn_close_label = "{l s='Close' mod='ets_cfultimate'}";
    var ets_cfu_add_input_field = "{l s='Add input field:' mod='ets_cfultimate'}";
    var ets_cfu_edit_input_field = "{l s='Edit input field:' mod='ets_cfultimate'}";
    var ets_cfu_add_row_title = "{l s='Add row' mod='ets_cfultimate'}";
    var ets_cfu_edit_row_title = "{l s='Edit row' mod='ets_cfultimate'}";
    var ets_cfu_languages = {$languages|json_encode};
</script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}contact_form7_admin.js"></script>
<div class="cfu-top-menu">
    <ul>
        <li class="AdminContactFormUltimateDashboard{if $controller=='AdminContactFormUltimateDashboard'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateDashboard',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1856 992v-832q0-13-9.5-22.5t-22.5-9.5h-1600q-13 0-22.5 9.5t-9.5 22.5v832q0 13 9.5 22.5t22.5 9.5h1600q13 0 22.5-9.5t9.5-22.5zm128-832v1088q0 66-47 113t-113 47h-544q0 37 16 77.5t32 71 16 43.5q0 26-19 45t-45 19h-512q-26 0-45-19t-19-45q0-14 16-44t32-70 16-78h-544q-66 0-113-47t-47-113v-1088q0-66 47-113t113-47h1600q66 0 113 47t47 113z"/></svg> {l s='Dashboard' mod='ets_cfultimate'}
            </a>
        </li>
        <li class="AdminContactFormUltimateContactForm{if $controller=='AdminContactFormUltimateContactForm' || isset($smarty.request.etsCfuEditContact) || isset($smarty.request.etsCfuAddContact)} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 1504v-768q-32 36-69 66-268 206-426 338-51 43-83 67t-86.5 48.5-102.5 24.5h-2q-48 0-102.5-24.5t-86.5-48.5-83-67q-158-132-426-338-37-30-69-66v768q0 13 9.5 22.5t22.5 9.5h1472q13 0 22.5-9.5t9.5-22.5zm0-1051v-24.5l-.5-13-3-12.5-5.5-9-9-7.5-14-2.5h-1472q-13 0-22.5 9.5t-9.5 22.5q0 168 147 284 193 152 401 317 6 5 35 29.5t46 37.5 44.5 31.5 50.5 27.5 43 9h2q20 0 43-9t50.5-27.5 44.5-31.5 46-37.5 35-29.5q208-165 401-317 54-43 100.5-115.5t46.5-131.5zm128-37v1088q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-1088q0-66 47-113t113-47h1472q66 0 113 47t47 113z"/></svg> {l s='Contact forms' mod='ets_cfultimate'}
            </a>
        </li>
        <li class="AdminContactFormUltimateMessage{if $controller=='AdminContactFormUltimateMessage'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 768q0 139-94 257t-256.5 186.5-353.5 68.5q-86 0-176-16-124 88-278 128-36 9-86 16h-3q-11 0-20.5-8t-11.5-21q-1-3-1-6.5t.5-6.5 2-6l2.5-5 3.5-5.5 4-5 4.5-5 4-4.5q5-6 23-25t26-29.5 22.5-29 25-38.5 20.5-44q-124-72-195-177t-71-224q0-139 94-257t256.5-186.5 353.5-68.5 353.5 68.5 256.5 186.5 94 257zm384 256q0 120-71 224.5t-195 176.5q10 24 20.5 44t25 38.5 22.5 29 26 29.5 23 25q1 1 4 4.5t4.5 5 4 5 3.5 5.5l2.5 5 2 6 .5 6.5-1 6.5q-3 14-13 22t-22 7q-50-7-86-16-154-40-278-128-90 16-176 16-271 0-472-132 58 4 88 4 161 0 309-45t264-129q125-92 192-212t67-254q0-77-23-152 129 71 204 178t75 230z"/></svg> {l s='Messages' mod='ets_cfultimate'}&nbsp;
                <span class="count_messages {if !$count_messages}hide{/if}">{$count_messages|intval}</span>
            </a>
        </li>
        <li class="AdminContactFormUltimateStatistics{if $controller=='AdminContactFormUltimateStatistics'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateStatistics',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M2048 1536v128h-2048v-1536h128v1408h1920zm-384-1024l256 896h-1664v-576l448-576 576 576z"/></svg> {l s='Statistics' mod='ets_cfultimate'}
            </a>
        </li>
        <li class="AdminContactFormUltimateIpBlacklist{if $controller=='AdminContactFormUltimateIpBlacklist'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateIpBlacklist', true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 896q-159 0-271.5-112.5t-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5-112.5 271.5-271.5 112.5zm1077 320l249 249q9 9 9 23 0 13-9 22l-136 136q-9 9-22 9-14 0-23-9l-249-249-249 249q-9 9-23 9-13 0-22-9l-136-136q-9-9-9-22 0-14 9-23l249-249-249-249q-9-9-9-23 0-13 9-22l136-136q9-9 22-9 14 0 23 9l249 249 249-249q9-9 23-9 13 0 22 9l136 136q9 9 9 22 0 14-9 23zm-498 0l-181 181q-37 37-37 91 0 53 37 90l83 83q-21 3-44 3h-874q-121 0-194-69t-73-190q0-53 3.5-103.5t14-109 26.5-108.5 43-97.5 62-81 85.5-53.5 111.5-20q19 0 39 17 154 122 319 122t319-122q20-17 39-17 28 0 57 6-28 27-41 50t-13 56q0 54 37 91z"/></svg> {l s='IP & Email blacklist' mod='ets_cfultimate'}
            </a>
        </li>
        <li class="AdminContactFormUltimateEmail{if $controller=='AdminContactFormUltimateEmail' ||  $controller=='AdminContactFormUltimateImportExport' || $controller=='AdminContactFormUltimateIntegration'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateEmail',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg> {l s='Settings' mod='ets_cfultimate'}
            </a>
        </li>
        <li class="AdminContactFormUltimateHelp{if $controller=='AdminContactFormUltimateHelp'} active{/if}">
            <a href="{$link->getAdminLink('AdminContactFormUltimateHelp',true)|escape:'html':'UTF-8'}">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg> {l s='Help' mod='ets_cfultimate'}
            </a>
        </li>
    </ul>
</div>
<div class="cfu-top-menu-height"></div>
