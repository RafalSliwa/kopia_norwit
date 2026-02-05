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
<div id="tag-generator-panel-menu" class="hidden">
    <form action="" class="tag-generator-panel bootstrap" data-id="menu">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type = 'menu'}
                    <tr>
                        <th scope="row">Field type</th>
                        <td>
                            <fieldset>
                                <label><input type="checkbox" name="required"/>{l s='Required field' mod='ets_cfultimate'}</label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-menu-name">{l s='Name' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-menu-name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Options' mod='ets_cfultimate'}</th>
                        <td>
                            <fieldset>
                                {include "./values.tpl" element='menu' input='textarea'}
                                <p class="description">{l s='One option per line. It also allows you to set custom label, custom value and default value following this structure: label|value:default' mod='ets_cfultimate'}</p>
                                <label><input type="checkbox" name="multiple" class="option"/> {l s='Allow multiple selections' mod='ets_cfultimate'}</label>
                                <label><input type="checkbox" name="include_blank" class="option"/> {l s='Insert a blank item as the first option' mod='ets_cfultimate'}</label>
                            </fieldset>
                        </td>
                    </tr>
                    {include file="./desc.tpl" input_type='menu'}
                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-menu-id">{l s='Id attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-menu-id"/></td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tag-generator-panel-menu-class">{l s='Class attribute' mod='ets_cfultimate'}</label>
                        </th>
                        <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-menu-class"/></td>
                    </tr>

                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <div class="ets_cfu_input_menu ets_cfu_input"
                 data-type="menu"
                 data-required="0"
                 data-name=""
                 data-multiple="0"
                 data-include_blank="0"
                 data-id=""
                 data-class=""
                 data-order="0">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_menu.png'|escape:'html':'UTF-8'}" alt="{l s='Dropdown selections' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('content') default = 'label'}
                    <p class="ets_cfu_help_block">{l s='Dropdown selections' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='select'}
        </div>
    </form>
</div>