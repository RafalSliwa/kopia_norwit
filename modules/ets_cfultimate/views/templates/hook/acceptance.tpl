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
<div id="tag-generator-panel-acceptance" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="acceptance">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type='acceptance'}
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-acceptance-name">{l s='Name' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-acceptance-name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-acceptance-content">{l s='Condition' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            {include "./values.tpl" element='acceptance' input_name='content'}
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Options' mod='ets_cfultimate'}</th>
                        <td>
                            <fieldset>
                                <label><input type="checkbox" name="default" class="option default"/> {l s='Make this checkbox be checked by default' mod='ets_cfultimate'}</label>
                            </fieldset>
                        </td>
                    </tr>
                    {include file="./desc.tpl" input_type='acceptance'}
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-acceptance-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-acceptance-id"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-acceptance-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-acceptance-class"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_acceptance ets_cfu_input"
                 data-type="acceptance"
                 data-name=""
                 data-invert="0"
                 data-default="0"
                 data-id=""
                 data-class="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_acceptance.png'|escape:'html':'UTF-8'}" alt="{l s='Acceptance' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('values') default = 'label'}
                    <p class="ets_cfu_help_block">{l s='Acceptance' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='acceptance'}
        </div>
    </form>
</div>