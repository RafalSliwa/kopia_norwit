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
<div class="hidden" id="tag-generator-panel-captcha">
    <form data-id="captcha" class="tag-generator-panel bootstrap" action="">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type='captcha'}
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-captcha">{l s='Name' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-captcha"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Theme' mod='ets_cfultimate'}</th>
                        <td>
                            <div class="ets_cfu_input_groups form-group row">
                                <label>
                                    <input type="radio" checked="checked" value="basic" id="tag-generator-panel-captcha-theme-basic" class="option default" name="theme"/>{l s='Basic' mod='ets_cfultimate'}
                                </label>
                                <label>
                                    <input type="radio" value="colorful" id="tag-generator-panel-captcha-theme-colorful" class="option" name="theme"/>{l s='Colorful' mod='ets_cfultimate'}
                                </label>
                            </div>
                        </td>
                    </tr>
                    {include file="./desc.tpl" input_type='captcha'}
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-captcha-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" id="tag-generator-panel-captcha-id" class="idvalue oneline option" name="id"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-captcha-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" id="tag-generator-panel-captcha-class" class="classvalue oneline option" name="class"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_captcha ets_cfu_input"
                 data-type="captcha"
                 data-name=""
                 data-theme="basic"
                 data-id=""
                 data-class="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_captcha.png'|escape:'html':'UTF-8'}" alt="{l s='Input captcha' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('values', 'content') default = 'label'}
                    <p class="ets_cfu_help_block">{l s='Captcha' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type = 'captcha'}
        </div>
    </form>
</div>