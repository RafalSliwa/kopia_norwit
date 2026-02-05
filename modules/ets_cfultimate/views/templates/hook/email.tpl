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
<div id="tag-generator-panel-email" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="email">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type='email'}
                    <tr>
                        <th scope="row">{l s='Field type' mod='ets_cfultimate'}</th>
                        <td>
                            <label><input type="checkbox" name="required"/>&nbsp;{l s='Required field' mod='ets_cfultimate'}</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-email-name">{l s='Name' mod='ets_cfultimate'}</label></th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-email-name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>{l s='Default value' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            {include "./values.tpl" element='email'}
                            <label>
                                <input type="checkbox" name="placeholder" class="option"/> {l s='Use this text as the placeholder of the field' mod='ets_cfultimate'}
                            </label>
                            <label>
                                <input type="checkbox" name="default" class="default" value="user_email"/>{l s='Use customer email as default value' mod='ets_cfultimate'}
                            </label>
                        </td>
                    </tr>
                    {include file="./desc.tpl" input_type='email'}
                    <tr>
                        <th scope="row">
                            <label>{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="id" class="idvalue oneline option"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="class" class="classvalue oneline option"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <label><input type="checkbox" name="mailtag" class="mail-tag email"/>{l s='Is contact email' mod='ets_cfultimate'}</label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_email ets_cfu_input"
                 data-type="email"
                 data-required="0"
                 data-name=""
                 data-placeholder=""
                 data-id=""
                 data-class=""
                 data-mailtag="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_email.png'|escape:'html':'UTF-8'}" alt="{l s='Input email' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('content') default = 'label'}
                    <p class="ets_cfu_help_block">{l s='Email' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type = 'email'}
        </div>
    </form>
</div>