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
<div id="tag-generator-panel-submit" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="submit">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-submit-values">{l s='Label' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            {include "./values.tpl" element='submit'}
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-submit-name">{l s='Name' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-submit-name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tag-generator-panel-submit-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-submit-id"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tag-generator-panel-submit-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-submit-class"/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_submit ets_cfu_input"
                 data-type="submit"
                 data-required="0"
                 data-name=""
                 data-placeholder=""
                 data-id=""
                 data-class="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_submit.png'|escape:'html':'UTF-8'}" alt="{l s='Submit' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('label', 'desc', 'content') default = 'values'}
                    <p class="ets_cfu_help_block">{l s='Submit' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='submit'}
        </div>
    </form>
</div>