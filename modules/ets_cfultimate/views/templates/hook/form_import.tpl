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
{hook h='contactFormUltimateTopBlock'}
<div class="ets_cfu_errors">
    {if isset($errors) && $errors}{$errors nofilter}{/if}
</div>
{assign var='controller' value = $smarty.get.controller}
{assign var="wrapTab" value=($controller == 'AdminContactFormUltimateEmail' || $controller == 'AdminContactFormUltimateImportExport' || $controller == 'AdminContactFormUltimateIntegration')}
<div class="ets_cfu_wrapper">
    <div class="ets_cfu_group_wrapper">
        <h3 class="ets_cfu_title">{l s='Settings' mod='ets_cfultimate'}</h3>
        <ul class="ets_cfu_menu_left">
            <li{if $controller=='AdminContactFormUltimateEmail'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormUltimateEmail',true)|escape:'html':'UTF-8'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1596 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528v-1024h-416q-40 0-68-28t-28-68v-416h-768v1536h1280zm-1024-864q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"/></svg> {l s='Email templates' mod='ets_cfultimate'}</a></li>
            <li{if $controller=='AdminContactFormUltimateImportExport'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormUltimateImportExport',true)|escape:'html':'UTF-8'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1184v192q0 13-9.5 22.5t-22.5 9.5h-1376v192q0 13-9.5 22.5t-22.5 9.5q-12 0-24-10l-319-320q-9-9-9-22 0-14 9-23l320-320q9-9 23-9 13 0 22.5 9.5t9.5 22.5v192h1376q13 0 22.5 9.5t9.5 22.5zm0-544q0 14-9 23l-320 320q-9 9-23 9-13 0-22.5-9.5t-9.5-22.5v-192h-1376q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1376v-192q0-14 9-23t23-9q12 0 24 10l319 319q9 9 9 23z"/></svg> {l s='Import/Export' mod='ets_cfultimate'}</a></li>
            <li{if $controller=='AdminContactFormUltimateIntegration'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormUltimateIntegration',true)|escape:'html':'UTF-8'}"><svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm768 512q0-52-38-90t-90-38-90 38-38 90q0 53 37.5 90.5t90.5 37.5 90.5-37.5 37.5-90.5zm0-1024q0-52-38-90t-90-38-90 38-38 90q0 53 37.5 90.5t90.5 37.5 90.5-37.5 37.5-90.5zm-384 421v185q0 10-7 19.5t-16 10.5l-155 24q-11 35-32 76 34 48 90 115 7 11 7 20 0 12-7 19-23 30-82.5 89.5t-78.5 59.5q-11 0-21-7l-115-90q-37 19-77 31-11 108-23 155-7 24-30 24h-186q-11 0-20-7.5t-10-17.5l-23-153q-34-10-75-31l-118 89q-7 7-20 7-11 0-21-8-144-133-144-160 0-9 7-19 10-14 41-53t47-61q-23-44-35-82l-152-24q-10-1-17-9.5t-7-19.5v-185q0-10 7-19.5t16-10.5l155-24q11-35 32-76-34-48-90-115-7-11-7-20 0-12 7-20 22-30 82-89t79-59q11 0 21 7l115 90q34-18 77-32 11-108 23-154 7-24 30-24h186q11 0 20 7.5t10 17.5l23 153q34 10 75 31l118-89q8-7 20-7 11 0 21 8 144 133 144 160 0 8-7 19-12 16-42 54t-45 60q23 48 34 82l152 23q10 2 17 10.5t7 19.5zm640 533v140q0 16-149 31-12 27-30 52 51 113 51 138 0 4-4 7-122 71-124 71-8 0-46-47t-52-68q-20 2-30 2t-30-2q-14 21-52 68t-46 47q-2 0-124-71-4-3-4-7 0-25 51-138-18-25-30-52-149-15-149-31v-140q0-16 149-31 13-29 30-52-51-113-51-138 0-4 4-7 4-2 35-20t59-34 30-16q8 0 46 46.5t52 67.5q20-2 30-2t30 2q51-71 92-112l6-2q4 0 124 70 4 3 4 7 0 25-51 138 17 23 30 52 149 15 149 31zm0-1024v140q0 16-149 31-12 27-30 52 51 113 51 138 0 4-4 7-122 71-124 71-8 0-46-47t-52-68q-20 2-30 2t-30-2q-14 21-52 68t-46 47q-2 0-124-71-4-3-4-7 0-25 51-138-18-25-30-52-149-15-149-31v-140q0-16 149-31 13-29 30-52-51-113-51-138 0-4 4-7 4-2 35-20t59-34 30-16q8 0 46 46.5t52 67.5q20-2 30-2t30 2q51-71 92-112l6-2q4 0 124 70 4 3 4 7 0 25-51 138 17 23 30 52 149 15 149 31z"/></svg> {l s='Integration' mod='ets_cfultimate'}</a></li>
        </ul>
        <div class="ets_cfu_setting_wrapper">
            <div class="cfu-content-block">
                <form id="module_form" class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post"
                      action="">
                    <div id="fieldset_0" class="panel">
                        <div class="panel-heading"><i class="icon-exchange"></i>&nbsp;
                            {l s='Import/export' mod='ets_cfultimate'}</div>
                        <div class="form-wrapper">

                                    <div class="form-group export_import">
                                        <div class="ctf_export_form_content">
                                            <div class="ctf_export_option">
                                                <div class="export_title">{l s='Export contact forms' mod='ets_cfultimate'}</div>
                                                <p>{l s='Export form configurations of all contact forms of the current shop that you are viewing' mod='ets_cfultimate'}</p>
                                                <a href="{$link->getAdminlink('AdminModules',true)|escape:'html':'UTF-8'}&configure=ets_cfultimate&tab_module=front_office_features&module_name=ets_cfultimate&etsCfuExportContactForm=1"
                                                   class="btn btn-default mm_export_menu">
                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h465l135 136q58 56 136 56t136-56l136-136h464q40 0 68 28t28 68zm-325-569q17 41-14 70l-448 448q-18 19-45 19t-45-19l-448-448q-31-29-14-70 17-39 59-39h256v-448q0-26 19-45t45-19h256q26 0 45 19t19 45v448h256q42 0 59 39z"/></svg> {l s='Export contact forms' mod='ets_cfultimate'}
                                                </a>
                                            </div>
                                            <div class="ctf_import_option">
                                                <div class="export_title">{l s='Import contact forms' mod='ets_cfultimate'}</div>
                                                <p>{l s='Import contact forms to the current shop that you are viewing for quick configuration. This is useful when you want to migrate contact forms between websites' mod='ets_cfultimate'}</p>
                                                <div class="ctf_import_option_updata">
                                                    <label for="contactformdata">{l s='Data file' mod='ets_cfultimate'}</label>
                                                    <input type="file" name="contactformdata" id="contactformdata"/>
                                                </div>
                                                <div class="cft_import_option_clean">
                                                    <input type="checkbox" name="importdeletebefore" id="importdeletebefore" value="1"/>
                                                    <label for="importdeletebefore">{l s='Delete all contact forms before importing' mod='ets_cfultimate'}</label>
                                                </div>
                                                <div class="cft_import_option_clean">
                                                    <input type="checkbox" name="importoverride" id="importoverride" value="1"/>
                                                    <label for="importoverride">{l s='Override all forms with the same IDs' mod='ets_cfultimate'}</label>
                                                </div>
                                                <div class="cft_import_option_button">
                                                    <input type="hidden" value="1" name="importContactform"/>
                                                    <div class="etsCfuImportContactSubmit">
                                                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 960v448q0 26-19 45t-45 19-45-19l-144-144-332 332q-10 10-23 10t-23-10l-114-114q-10-10-10-23t10-23l332-332-144-144q-19-19-19-45t19-45 45-19h448q26 0 45 19t19 45zm755-672q0 13-10 23l-332 332 144 144q19 19 19 45t-19 45-45 19h-448q-26 0-45-19t-19-45v-448q0-26 19-45t45-19 45 19l144 144 332-332q10-10 23-10t23 10l114 114q10 10 10 23z"/></svg>
                                                        <input type="submit" class="btn btn-default cft_import_menu"
                                                               name="etsCfuImportContactSubmit"
                                                               value="{l s='Import contact forms' mod='ets_cfultimate'}"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>