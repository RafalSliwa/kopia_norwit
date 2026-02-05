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
{assign var="is_multi_lang" value=($languages|count > 1)}
<div class="hidden" id="tag-generator-panel-recaptcha">
    {if (Configuration::get('ETS_CFU_SITE_KEY') && Configuration::get('ETS_CFU_SECRET_KEY')) || (Configuration::get('ETS_CFU_SITE_KEY_V3') && Configuration::get('ETS_CFU_SECRET_KEY_V3')) }
        <form data-id="recaptcha" class="tag-generator-panel bootstrap" action="">
            <div class="control-box">
                <fieldset>
                    <table class="form-table">
                        <tbody>
                        {include file="./input_label.tpl" input_type='recaptcha'}
                        {if isset($re_captcha_v3) && !$re_captcha_v3}
                        <tr>
                            <th scope="row">{l s='Size' mod='ets_cfultimate'}</th>
                            <td>
                                <div class="ets_cfu_input_groups form-group row">
                                    <label>
                                        <input type="radio" checked="checked" value="normal" id="tag-generator-panel-recaptcha-size-normal" class="option default" name="size"/> {l s='Normal' mod='ets_cfultimate'}
                                    </label>
                                    <label>
                                        <input type="radio" value="compact" id="tag-generator-panel-recaptcha-size-compact" class="option" name="size"/> {l s='Compact' mod='ets_cfultimate'}
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{l s='Theme' mod='ets_cfultimate'}</th>
                            <td>
                                <div class="ets_cfu_input_groups form-group row">
                                    <label>
                                        <input type="radio" checked="checked" value="light" id="tag-generator-panel-recaptcha-theme-light" class="option default" name="theme"/>
                                        {l s='Light' mod='ets_cfultimate'}
                                    </label>
                                    <label>
                                        <input type="radio" value="dark" id="tag-generator-panel-recaptcha-theme-dark" class="option" name="theme"/>
                                        {l s='Dark' mod='ets_cfultimate'}
                                    </label>
                                </div>
                            </td>
                        </tr>{/if}
                        {include file="./desc.tpl" input_type='recaptcha'}
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-recaptcha-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                            </th>
                            <td><input type="text" id="tag-generator-panel-recaptcha-id" class="idvalue oneline option" name="id"/></td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-recaptcha-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                            </th>
                            <td><input type="text" id="tag-generator-panel-recaptcha-class" class="classvalue oneline option" name="class"/></td>
                        </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div class="insert-box">
                <div class="ets_cfu_input_recaptcha ets_cfu_input"
                     data-type="recaptcha"
                     data-name=""
                     data-theme="light"
                     data-size="normal"
                     data-id=""
                     data-class="">
                    <div class="ets_cfu_input_generator">
                        <img src="{$img_dir|cat:'ip_recaptcha.png'|escape:'html':'UTF-8'}" alt="{l s='ReCaptcha' mod='ets_cfultimate'}" />
                        {include file="./form_data.tpl" excludes = array('values', 'content') default='label'}
                        <p class="ets_cfu_help_block">{l s='ReCaptcha' mod='ets_cfultimate'}</p>
                    </div>
                    {include file="./buttons.tpl"}
                </div>
                {include file="./input_tag.tpl" input_type='recaptcha'}
            </div>
        </form>
    {else}
        <p>{l s='Config reCaptcha' mod='ets_cfultimate'} <a href="{$link->getAdminLink('AdminContactFormUltimateIntegration',true)|escape:'html':'UTF-8'}">{l s='click here' mod='ets_cfultimate'}</a>
        </p>
    {/if}
</div>