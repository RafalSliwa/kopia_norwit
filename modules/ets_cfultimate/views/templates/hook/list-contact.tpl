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
{if $ETS_CFU_ENABLE_TMCE}
    <script type="text/javascript">
        var ad = '';
        var iso = 'en';
        var file_not_found = '';
        var pathCSS = '{$smarty.const._THEME_CSS_DIR_ nofilter}';
    </script>
    <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}tiny_mce/tiny_mce.js"></script>
    {if $is_ps15}
        <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}tinymce.inc.js"></script>
    {else}
        <script src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}admin/tinymce.inc.js"></script>
    {/if}
{/if}
{if isset($okimport)&& $okimport}
    <div class="bootstrap">
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            {l s='Contact form imported successfully.' mod='ets_cfultimate'}
        </div>
    </div>
{/if}
{hook h='contactFormUltimateTopBlock'}
<script type="text/javascript">
    var text_update_position = '{l s='Successful update' mod='ets_cfultimate'}';
    {if isset($is_ps15) && $is_ps15}
    $(document).on('click', '.dropdown-toggle', function () {
        $(this).closest('.btn-group').toggleClass('open');
    });
    {/if}
</script>
<div class="cfu-content-block">
    <form id="form-contact" class="form-horizontal clearfix products-catalog"
          action="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}" method="post">
        <input id="submitFilterContact" type="hidden" value="0" name="submitFilterContact"/>
        <input type="hidden" value="1" name="page"/>
        <input type="hidden" value="50" name="selected_pagination"/>
        <div class="panel col-lg-12">
            <div class="panel-heading">
                {l s='Contact forms' mod='ets_cfultimate'}
                <span class="badge">{count($ets_cfu_contacts)|intval}</span>
                <span class="panel-heading-action">
                    <a id="desc-contactform-new" class="list-toolbar-btn" title="{l s='Add new' mod='ets_cfultimate'}"
                       href="{$url_module|escape:'html':'UTF-8'}&etsCfuAddContact=1">
                        <span title="{l s='Add new' mod='ets_cfultimate'}" class="label-tooltip" data-placement="top"
                              data-html="true" data-original-title="{l s='Add new' mod='ets_cfultimate'}"
                              data-toggle="tooltip" title="">
                            <i class="process-icon-new"></i>
                        </span>
                    </a>
                 </span>
            </div>
            <div class="table-responsive-row clearfix">
                <table id="table-contact" class="table contact">
                    <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-xs text-center ctf_id">
                                <span class="title_box">
                                    {l s='ID' mod='ets_cfultimate'}
                                    <a {if $sort=='id_contact' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=id_contact&sort_type=desc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                    <a {if $sort=='id_contact' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=id_contact&sort_type=asc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></a>
                                </span>
                        </th>
                        <th class="ctf_title">
                                <span class="title_box">
                                    {l s='Title' mod='ets_cfultimate'}
                                    <a {if $sort=='title' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=title&sort_type=desc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                    <a {if $sort=='title' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=title&sort_type=asc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></a>
                                </span>
                        </th>
                        {if !isset($showShortcodeHook) || (isset($showShortcodeHook)  && $showShortcodeHook)}
                            <th class="ctf_shortcode">
                                    <span class="title_box">
                                        {l s='Short code' mod='ets_cfultimate'}
                                    </span>
                            </th>
                        {/if}
                        <th class="ct_form_url">
                                <span class="title_box">
                                    {l s='Form URL' mod='ets_cfultimate'}
                                </span>
                        </th>
                        <th class="ct_form_views">
                                <span class="title_box">
                                    {l s='Views' mod='ets_cfultimate'}
                                </span>
                        </th>
                        <th class="ctf_sort">
                                <span class="title_box">
                                    {l s='Sort order' mod='ets_cfultimate'}
                                    <a {if $sort=='position' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=position&sort_type=desc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                    <a {if $sort=='position' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=position&sort_type=asc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></a>
                                </span>
                        </th>
                        <th class="ctf_message">
                                <span class="title_box">
                                    {l s='Save message' mod='ets_cfultimate'}
                                    <a {if $sort=='save_message' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=save_message&sort_type=desc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                    <a {if $sort=='save_message' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=save_message&sort_type=asc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></a>
                                </span>
                        </th>
                        <th class="ctf_active">
                                <span class="title_box">
                                    {l s='Active' mod='ets_cfultimate'}
                                    <a {if $sort=='active' && $sort_type=='desc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=active&sort_type=desc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                    <a {if $sort=='active' && $sort_type=='asc'} class="active"{/if} href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}&sort=active&sort_type=asc{$filter_params nofilter}"><svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></a>
                                </span>
                        </th>
                        <th class="ctf_action">
                                <span class="title_box">
                                    {l s='Action' mod='ets_cfultimate'}
                                </span>
                        </th>
                    </tr>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-xs text-center ctf_id">
                                <span class="title_box">
                                    <input class="form-control" name="id_contact" style="width:60px"
                                           value="{if isset($values_submit.id_contact)}{$values_submit.id_contact|escape:'html':'UTF-8'}{/if}"/>
                                </span>
                        </th>
                        <th class="ctf_title">
                                <span class="title_box">
                                    <input class="form-control" name="contact_title" style="width:150px"
                                           value="{if isset($values_submit.contact_title)}{$values_submit.contact_title|escape:'html':'UTF-8'}{/if}"/>
                                </span>
                        </th>
                        {if !isset($showShortcodeHook) || (isset($showShortcodeHook)  && $showShortcodeHook)}
                            <th class="">

                            </th>
                        {/if}
                        <th class="ct_form_url">
                        </th>
                        <th class="">

                        </th>
                        <th>
                        </th>
                        <th class="ctf_message">
                                <span class="title_box">
                                    <select class="form-control" name="save_message">
                                        <option value="">---</option>
                                        <option value="1" {if isset($values_submit.save_message) && $values_submit.hook==1} selected="selected"{/if}>{l s='Yes' mod='ets_cfultimate'}</option>
                                        <option value="0" {if isset($values_submit.save_message) && $values_submit.hook==0} selected="selected"{/if}>{l s='No' mod='ets_cfultimate'}</option>
                                    </select>
                                </span>
                        </th>
                        <th class="ctf_active">
                            <select class="form-control" name="active_contact">
                                <option value="">---</option>
                                <option value="1" {if isset($values_submit.active_contact) && $values_submit.active_contact==1} selected="selected"{/if}>{l s='Yes' mod='ets_cfultimate'}</option>
                                <option value="0" {if isset($values_submit.active_contact) && $values_submit.active_contact==0} selected="selected"{/if}>{l s='No' mod='ets_cfultimate'}</option>
                            </select>
                        </th>
                        <th class="ctf_action">
                                <span class="pull-right">
                                    <button id="submitFilterButtonContact" class="btn btn-default"
                                            name="submitFilterButtonContact" type="submit">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                                        {l s='Search' mod='ets_cfultimate'}
                                    </button>
                                    {if isset($filter)&& $filter}
                                        <a class="btn btn-warning"
                                           href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}">
                                            <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 1408l336-384h-768l-336 384h768zm1013-1077q15 34 9.5 71.5t-30.5 65.5l-896 1024q-38 44-96 44h-768q-38 0-69.5-20.5t-47.5-54.5q-15-34-9.5-71.5t30.5-65.5l896-1024q38-44 96-44h768q38 0 69.5 20.5t47.5 54.5z"/></svg>
                                            {l s='Reset' mod='ets_cfultimate'}
                                        </a>
                                    {/if}
                                </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="list-contactform">
                    {if $ets_cfu_contacts}
                        {foreach from=$ets_cfu_contacts item='contact'}
                            <tr id="formcontact_{$contact.id_contact|intval}">
                                <td class="ctf_id text-center">{$contact.id_contact|intval}</td>
                                <td class="ctf_title">{$contact.title|escape:'html':'UTF-8'}</td>
                                {if !isset($showShortcodeHook) || (isset($showShortcodeHook)  && $showShortcodeHook)}
                                    <td class="ctf_shortcode">
                                        <div class="short-code">
                                            <input title="{l s='Click to copy' mod='ets_cfultimate'}" class="ctf-short-code"
                                                   type="text" value='[contact-form-7 id="{$contact.id_contact|intval}"]'/>
                                            <span class="text-copy">{l s='Copied' mod='ets_cfultimate'}</span>
                                        </div>
                                    </td>
                                {/if}
                                <td class="ct_form_url">
                                    {if $contact.enable_form_page}
                                        <a href="{$contact.link|escape:'html':'UTF-8'}"
                                           target="_blank">{$contact.link|escape:'html':'UTF-8'}</a>
                                    {else}
                                        {l s='Form page is disabled' mod='ets_cfultimate'}
                                    {/if}
                                </td>
                                <td class="ct_view text-center">
                                    {$contact.count_views|intval}
                                </td>
                                <td class="ctf_sort text-center {if $sort=='position' && $sort_type=='asc'}pointer dragHandle center{/if}">
                                    <div class="dragGroup">
                                        <span class="positions">{($contact.position+1)|intval}</span>
                                    </div>
                                </td>
                                <td class="text-center ctf_message">
                                    {if $contact.save_message}
                                        <a title="{l s='Click to disable' mod='ets_cfultimate'}"
                                           href="{$url_module|escape:'html':'UTF-8'}&etsCfuSaveMessageUpdate=0&id_contact={$contact.id_contact|intval}">
                                            <i class="material-icons action-enabled">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                                            </i>
                                        </a>
                                        {if $contact.count_message}
                                            <a title="{l s='View messages' mod='ets_cfultimate'}"
                                               href="{$link->getAdminLink('AdminContactFormUltimateMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}"
                                               class="">
                                                ({$contact.count_message|intval})
                                            </a>
                                        {/if}
                                    {else}
                                        <a title="{l s='Click to enable' mod='ets_cfultimate'}"
                                           href="{$url_module|escape:'html':'UTF-8'}&etsCfuSaveMessageUpdate=1&id_contact={$contact.id_contact|intval}">
                                            <i class="material-icons action-disabled">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                            </i>
                                        </a>
                                        {if $contact.count_message}
                                            <a href="{$link->getAdminLink('AdminContactFormUltimateMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}"
                                               class="">
                                                ({$contact.count_message|intval})
                                            </a>
                                        {/if}
                                    {/if}
                                </td>
                                <td class="text-center ctf_active">
                                    {if $contact.active}
                                        <a title="{l s='Click to disable' mod='ets_cfultimate'}"
                                        href="{$url_module|escape:'html':'UTF-8'}&etsCfuActiveUpdate=0&id_contact={$contact.id_contact|intval}">
                                            <i class="material-icons action-enabled">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                                            </i>
                                        </a>
                                    {else}
                                        <a title="{l s='Click to enable' mod='ets_cfultimate'}"
                                        href="{$url_module|escape:'html':'UTF-8'}&etsCfuActiveUpdate=1&id_contact={$contact.id_contact|intval}">
                                            <i class="material-icons action-disabled">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                            </i>
                                        </a>
                                    {/if}
                                </td>
                                <td class="text-center ctf_action">
                                    <div class="btn-group-action">
                                        <div class="btn-group">
                                            <a class="btn tooltip-link product-edit" title=""
                                               href="{$url_module|escape:'html':'UTF-8'}&etsCfuEditContact=1&id_contact={$contact.id_contact|intval}"
                                               title="{l s='Edit' mod='ets_cfultimate'}">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg> {l s='Edit' mod='ets_cfultimate'}
                                            </a>
                                            <a class="btn btn-link dropdown-toggle dropdown-toggle-split product-edit"
                                               aria-expanded="false" aria-haspopup="true" data-toggle="dropdown"> <svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></a>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 style="position: absolute; transform: translate3d(-164px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                {if $contact.enable_form_page}
                                                    <a href="{Ets_CfUltimate::getLinkContactForm($contact.id_contact|intval)|escape:'html':'UTF-8'}"
                                                       class="dropdown-item product-edit" target="_blank"
                                                       >
                                                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M555 1335l78-141q-87-63-136-159t-49-203q0-121 61-225-229 117-381 353 167 258 427 375zm389-759q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm363-191q0 7-1 9-106 189-316 567t-315 566l-49 89q-10 16-28 16-12 0-134-70-16-10-16-28 0-12 44-87-143-65-263.5-173t-208.5-245q-20-31-20-69t20-69q153-235 380-371t496-136q89 0 180 17l54-97q10-16 28-16 5 0 18 6t31 15.5 33 18.5 31.5 18.5 19.5 11.5q16 10 16 27zm37 447q0 139-79 253.5t-209 164.5l280-502q8 45 8 84zm448 128q0 35-20 69-39 64-109 145-150 172-347.5 267t-419.5 95l74-132q212-18 392.5-137t301.5-307q-115-179-282-294l63-112q95 64 182.5 153t144.5 184q20 34 20 69z"/></svg>
                                                        {l s='View form' mod='ets_cfultimate'}
                                                    </a>
                                                {/if}
                                                <a href="{$link->getAdminLink('AdminContactFormUltimateMessage')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}"
                                                   class="dropdown-item product-edit"
                                                   >
                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 768q0 139-94 257t-256.5 186.5-353.5 68.5q-86 0-176-16-124 88-278 128-36 9-86 16h-3q-11 0-20.5-8t-11.5-21q-1-3-1-6.5t.5-6.5 2-6l2.5-5 3.5-5.5 4-5 4.5-5 4-4.5q5-6 23-25t26-29.5 22.5-29 25-38.5 20.5-44q-124-72-195-177t-71-224q0-139 94-257t256.5-186.5 353.5-68.5 353.5 68.5 256.5 186.5 94 257zm384 256q0 120-71 224.5t-195 176.5q10 24 20.5 44t25 38.5 22.5 29 26 29.5 23 25q1 1 4 4.5t4.5 5 4 5 3.5 5.5l2.5 5 2 6 .5 6.5-1 6.5q-3 14-13 22t-22 7q-50-7-86-16-154-40-278-128-90 16-176 16-271 0-472-132 58 4 88 4 161 0 309-45t264-129q125-92 192-212t67-254q0-77-23-152 129 71 204 178t75 230z"/></svg>
                                                    {l s='Messages' mod='ets_cfultimate'}
                                                </a>
                                                <a href="{$link->getAdminLink('AdminContactFormUltimateStatistics')|escape:'html':'UTF-8'}&id_contact={$contact.id_contact|intval}"
                                                   class="dropdown-item product-edit"
                                                   >
                                                    <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M2048 1536v128h-2048v-1536h128v1408h1920zm-128-1248v435q0 21-19.5 29.5t-35.5-7.5l-121-121-633 633q-10 10-23 10t-23-10l-233-233-416 416-192-192 585-585q10-10 23-10t23 10l233 233 464-464-121-121q-16-16-7.5-35.5t29.5-19.5h435q14 0 23 9t9 23z"/></svg>
                                                    {l s='Statistics' mod='ets_cfultimate'}
                                                </a>
                                                <a href="{$url_module|escape:'html':'UTF-8'}&etsCfuDuplicateContact=1&id_contact={$contact.id_contact|intval}"
                                                   class="dropdown-item message-duplidate product-edit"
                                                   >
                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>
                                                    {l s='Duplicate' mod='ets_cfultimate'}
                                                </a>
                                                <a href="{$url_module|escape:'html':'UTF-8'}&etsCfuDeleteContact=1&id_contact={$contact.id_contact|intval}" class="dropdown-item message-delete product-edit">
                                                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete form' mod='ets_cfultimate'}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="10">
                                <p class="alert alert-warning">{l s='No contact forms available' mod='ets_cfultimate'}</p>
                            </td>
                        </tr>
                    {/if}
                    </tbody>
                </table>
                {$ets_cfu_pagination_text nofilter}
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($("table .datepicker").length > 0) {
                $("table .datepicker").datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                });
            }
        });
    </script>
</div>
<div class="ctf-popup-wapper-admin">
    <div class="fuc"></div>
    <div class="ctf-popup-tablecell">
        <div class="ctf-popup-content">
            <div class="ctf_close_popup">{l s='close' mod='ets_cfultimate'}</div>
            <div id="form-contact-preview">

            </div>
        </div>
    </div>
</div>