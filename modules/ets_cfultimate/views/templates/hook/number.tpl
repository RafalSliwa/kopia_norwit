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
<div id="tag-generator-panel-number" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="number">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type='number'}
                    <tr>
                        <th scope="row">{l s='Field type' mod='ets_cfultimate'}</th>
                        <td>
                            <div class="ets_cfu_input_groups form-group row">
                                <select name="tagtype" class="cfu-tagtype">
                                    <option value="number" selected="selected">{l s='Spinbox' mod='ets_cfultimate'}</option>
                                    <option value="range">{l s='Slider' mod='ets_cfultimate'}</option>
                                </select>
                                <label><input type="checkbox" name="required"/>{l s='Required field' mod='ets_cfultimate'}</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-number-name">{l s='Name' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-number-name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-number-values">{l s='Default value' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            {include "./values.tpl" element='number'}
                            <label><input type="checkbox" name="placeholder" class="option"/> {l s='Use this text as the placeholder of the field' mod='ets_cfultimate'}</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Range' mod='ets_cfultimate'}</th>
                        <td>
                            <div class="ets_cfu_min_max">
                                <label>{l s='Min' mod='ets_cfultimate'} <input type="number" name="min" class="numeric option"/></label>
                                &ndash;
                                <label>{l s='Max' mod='ets_cfultimate'}<input type="number" name="max" class="numeric option"/></label>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-text-id">{l s='Step' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="number" name="step" class="option numeric" value="1" id="tag-generator-panel-text-step"/>
                        </td>
                    </tr>

                    {include file="./desc.tpl" input_type='number'}

                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-number-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-number-id"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-number-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-number-class"/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_number ets_cfu_input"
                 data-type="number"
                 data-tagtype="number"
                 data-required="0"
                 data-name=""
                 data-min=""
                 data-max=""
                 data-placeholder=""
                 data-id=""
                 data-class="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_number.png'|escape:'html':'UTF-8'}" alt="{l s='Input number' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('content') default='label'}
                    <p class="ets_cfu_help_block">{l s='Number' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='number'}
        </div>
    </form>
</div>