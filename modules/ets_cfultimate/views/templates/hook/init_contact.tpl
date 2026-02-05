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
{assign var="is_multi_lang" value=$languages|count > 1}
{if $type == 'rf'}
    <div class="ets_cfu_box style3" data-type="style3" data-order="0">
        <div class="ets_cfu_btn">
            <span class="ets_cfu_btn_edit" title="{l s='Edit row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg> {l s='Edit row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_copy" title="{l s='Duplicate row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg> {l s='Duplicate row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_delete" title="{l s='Delete row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_drag_drop" title="{l s='Drag drop row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg> {l s='Drag drop row' mod='ets_cfultimate'}</span>
        </div>
        <div class="ets_cfu_row">
            <div class="ets_cfu_col col1" data-col="col1">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_text ets_cfu_input" data-type="text" data-required="1"
                         data-name="text-652" data-placeholder="" data-id="" data-class="" data-mailtag="1"
                         data-default="user_first_name">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_text.png" alt="Input text">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_label_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>First Name</span>
                                    <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">First Name*[text* text-652 default:user_full_name]</span>
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>{/foreach}
                                {else}
                                    <span class="ets_cfu_label_{$id_lang_default|intval}">First Name</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">First Name*[text* text-652 default:user_full_name]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>{/if}
                            </div>
                            <p class="ets_cfu_help_block">Text</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}">
                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s1">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ets_cfu_col col2" data-col="col2">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_text ets_cfu_input" data-type="text" data-required="1" data-name="text-85"
                         data-placeholder="" data-id="" data-class="" data-mailtag="0" data-default="user_last_name">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_text.png" alt="Input text">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_label_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>Last Name</span>
                                    <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">Last Name*[text* text-85 default:user_last_name]</span>
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>
                                {/foreach}
                                {else}
                                    <span class="ets_cfu_label_{$id_lang_default|intval}">Last Name</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">Last Name*[text* text-85 default:user_last_name]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>
                                {/if}
                            </div>
                            <p class="ets_cfu_help_block">Text</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s2">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ets_cfu_box style3" data-type="style3" data-order="0">
        <div class="ets_cfu_btn">
            <span class="ets_cfu_btn_edit" title="{l s='Edit row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg> {l s='Edit row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_copy" title="{l s='Duplicate row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg> {l s='Duplicate row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_delete" title="{l s='Delete row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_drag_drop" title="{l s='Drag drop row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg> {l s='Drag drop row' mod='ets_cfultimate'}</span>
        </div>
        <div class="ets_cfu_row">
            <div class="ets_cfu_col col1" data-col="col1">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_email ets_cfu_input" data-type="email" data-required="1"
                         data-name="email-668" data-placeholder="" data-id="" data-class="" data-mailtag="1">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_email.png"
                                 alt="{l s='Input email' mod='ets_cfultimate'}">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_label_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>Email</span>
                                <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">
                                        Email*[email* email-668 default:user_email]</span>{/foreach}
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>
                                {else}
                                    <span class="ets_cfu_label_{$id_lang_default|intval}">Email</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">Email*[email* email-668 default:user_email]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>
                                {/if}
                            </div>
                            <p class="ets_cfu_help_block">{l s='Email' mod='ets_cfultimate'}</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s3">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ets_cfu_col col2" data-col="col2">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_tel ets_cfu_input" data-type="tel" data-required="0" data-name="tel-534"
                         data-placeholder="" data-id="" data-class="">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_tel.png"
                                 alt="{l s='Input telephone' mod='ets_cfultimate'}">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_label_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>Phone</span>
                                    <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">Phone[tel tel-534]</span>
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>
                                {/foreach}
                                {else}
                                    <span class="ets_cfu_label_{$id_lang_default|intval}">Phone</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">Phone[tel tel-534]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>
                                {/if}
                            </div>
                            <p class="ets_cfu_help_block">{l s='Telephone' mod='ets_cfultimate'}</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s4">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ets_cfu_box style1" data-type="style1" data-order="0">
        <div class="ets_cfu_btn">
            <span class="ets_cfu_btn_edit" title="{l s='Edit row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg> {l s='Edit row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_copy" title="{l s='Duplicate row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg> {l s='Duplicate row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_delete" title="{l s='Delete row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_drag_drop" title="{l s='Drag drop row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg> {l s='Drag drop row' mod='ets_cfultimate'}</span>
        </div>
        <div class="ets_cfu_row">
            <div class="ets_cfu_col col1" data-col="col1">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_textarea ets_cfu_input" data-type="textarea" data-required="1"
                         data-name="textarea-261" data-placeholder="" data-id="" data-class="">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_textarea.png"
                                 alt="{l s='Textarea' mod='ets_cfultimate'}">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_label_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>Message</span>
                                    <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">Message*[textarea* textarea-261]</span>
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>
                                {/foreach}
                                {else}
                                    <span class="ets_cfu_label_{$id_lang_default|intval}">Message</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">Message*[textarea* textarea-261]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>
                                {/if}
                            </div>
                            <p class="ets_cfu_help_block">{l s='Textarea' mod='ets_cfultimate'}</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s5">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span class="ets_cfu_title_box">{l s='1 column in a row' mod='ets_cfultimate'}</span>
    </div>
    <div class="ets_cfu_box style1" data-type="style1" data-order="0">
        <div class="ets_cfu_btn">
            <span class="ets_cfu_btn_edit" title="{l s='Edit row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg> {l s='Edit row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_copy" title="{l s='Duplicate row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg> {l s='Duplicate row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_delete" title="{l s='Delete row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete row' mod='ets_cfultimate'}</span>
            <span class="ets_cfu_btn_drag_drop" title="{l s='Drag drop row' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg> {l s='Drag drop row' mod='ets_cfultimate'}</span>
        </div>
        <div class="ets_cfu_row">
            <div class="ets_cfu_col col1" data-col="col1">
                <div class="ets_cfu_col_box ui-sortable">
                    <span class="ets_cfu_add_input"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>&nbsp;{l s='Add input field' mod='ets_cfultimate'}</span>
                    <div class="ets_cfu_input_submit ets_cfu_input" data-type="submit" data-required="0"
                         data-name="submit-992" data-placeholder="" data-id="" data-class="">
                        <div class="ets_cfu_input_generator">
                            <img src="{$icon_link|escape:'html':'UTF-8'}ip_submit.png"
                                 alt="{l s='Submit' mod='ets_cfultimate'}">
                            <div class="ets_cfu_form_data">
                                {if $is_multi_lang}{foreach from=$languages item='lang'}
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}"{if $lang.id_lang != $id_lang_default} style="display:none;"{/if}>Send</span>
                                    <span class="ets_cfu_short_code_{$lang.id_lang|intval}" style="display:none;">[submit submit-992 "Send"]</span>
                                    <span class="ets_cfu_values_{$lang.id_lang|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$lang.id_lang|intval}" style="display:none;"></span>
                                {/foreach}
                                {else}
                                    <span class="ets_cfu_values_{$id_lang_default|intval}">Send</span>
                                    <span class="ets_cfu_short_code_{$id_lang_default|intval}" style="display:none;">[submit submit-992 "Send"]</span>
                                    <span class="ets_cfu_values_{$id_lang_default|intval}" style="display:none;"></span>
                                    <span class="ets_cfu_desc_{$id_lang_default|intval}" style="display:none;"></span>
                                {/if}
                            </div>
                            <p class="ets_cfu_help_block">{l s='Submit' mod='ets_cfultimate'}</p>
                        </div>
                        <div class="ets_cfu_btn_input">
                            <span class="settings_icon" title="{l s='Settings' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                            <div class="settings_icon_content s6">
                                <span class="ets_cfu_btn_edit_input" title="{l s='Edit input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>&nbsp;{l s='Edit input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_copy_input" title="{l s='Duplicate input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg>&nbsp;{l s='Duplicate input' mod='ets_cfultimate'}</span>
                                <span class="ets_cfu_btn_delete_input" title="{l s='Delete input' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>&nbsp;{l s='Delete input' mod='ets_cfultimate'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span class="ets_cfu_title_box">{l s='1 column in a row' mod='ets_cfultimate'}</span>
    </div>
{elseif $type == 'sc'}
    <div class="ets_cfu_wrapper">
        <div class="ets_cfu_box style3">
            <div class="ets_cfu_col col1">
                <div class="ets_cfu_input_text ets_cfu_input">
                    <label>First Name*[text* text-652 default:user_full_name]</label></div>
            </div>
            <div class="ets_cfu_col col2">
                <div class="ets_cfu_input_text ets_cfu_input">
                    <label>Last Name*[text* text-85 default:user_last_name]</label>
                </div>
            </div>
        </div>
        <div class="ets_cfu_box style3">
            <div class="ets_cfu_col col1">
                <div class="ets_cfu_input_email ets_cfu_input"><label>Email*[email* email-668
                        default:user_email]</label></div>
            </div>
            <div class="ets_cfu_col col2">
                <div class="ets_cfu_input_tel ets_cfu_input"><label>Phone[tel tel-534]</label></div>
            </div>
        </div>
        <div class="ets_cfu_box style1">
            <div class="ets_cfu_col col1">
                <div class="ets_cfu_input_textarea ets_cfu_input"><label>Message*[textarea* textarea-261]</label></div>
            </div>
        </div>
        <div class="ets_cfu_box style1">
            <div class="ets_cfu_col col1">
                <div class="ets_cfu_input_submit ets_cfu_input">[submit submit-992 "Send"]</div>
            </div>
        </div>
    </div>
{elseif $type == 'msg'}
    <p>Full name: [text-652] [text-85]</p>
    <p>Email: [email-668]</p>
    <p>Phone number: [tel-534]</p>
    <p>Message: [textarea-261]</p>
{/if}