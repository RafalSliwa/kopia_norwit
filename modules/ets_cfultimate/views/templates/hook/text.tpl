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
<div id="tag-generator-panel-text" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="text">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                        {include file="./input_label.tpl" input_type='text'}
                        <tr>
                            <th scope="row">{l s='Field type' mod='ets_cfultimate'}</th>
                            <td>
                                <label><input type="checkbox" name="required"/>{l s='Required field' mod='ets_cfultimate'}</label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-text-name">{l s='Name' mod='ets_cfultimate'}</label>
                            </th>
                            <td>
                                <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-text-name"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-text-values">{l s='Default value' mod='ets_cfultimate'}</label>
                            </th>
                            <td>
                                <div class="row form-group">
                                <div class="ets_cfu_field_set">
                                    <div class="col-lg-9">
                                        {include "./values.tpl" element='text'}
                                    </div>
                                    <div class="col-lg-3">
                                        <select name="default" class="default">
                                            <option value="">{l s='-- Custom --' mod='ets_cfultimate'}</option>
                                            <option value="user_first_name">{l s='Default user Firstname' mod='ets_cfultimate'}</option>
                                            <option value="user_last_name">{l s='Default user Lastname' mod='ets_cfultimate'}</option>
                                            <option value="user_full_name">{l s='Default user Fullname' mod='ets_cfultimate'}</option>
                                            <option value="user_email ">{l s='Default user Email' mod='ets_cfultimate'}</option>
                                            <option value="user_phone ">{l s='Default user Phone Number' mod='ets_cfultimate'}</option>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-lg-12"><input type="checkbox" name="placeholder" class="option"/>{l s='Use this text as the placeholder of the field' mod='ets_cfultimate'}</label>
                                </div>
                            </td>
                        </tr>
                        {include file="./desc.tpl" input_type='text'}
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-text-id">{l s='Max character' mod='ets_cfultimate'}</label>
                            </th>
                            <td>
                                <input type="number" name="maxlength" class="option numeric" id="tag-generator-panel-text-maxlength"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-text-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                            </th>
                            <td>
                                <input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-text-id"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="tag-generator-panel-text-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                            </th>
                            <td>
                                <input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-text-class"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"></th>
                            <td>
                                <label><input type="checkbox" name="mailtag" class="mail-tag text"/>{l s='Is contact name' mod='ets_cfultimate'}</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_text ets_cfu_input"
                 data-type="text"
                 data-required="0"
                 data-name=""
                 data-placeholder=""
                 data-id=""
                 data-class=""
                 data-mailtag="0">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_text.png'|escape:'html':'UTF-8'}" alt="{l s='Input text' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('content') default = 'label'}
                    <p class="ets_cfu_help_block">{l s='Text' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='text'}
        </div>
    </form>
</div>