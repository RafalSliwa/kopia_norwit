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
<div class="hidden" id="tag-generator-panel-file">
    <form data-id="file" class="tag-generator-panel bootstrap" action="">
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                    {include file="./input_label.tpl" input_type='file'}
                    <tr>
                        <th scope="row">{l s='Field type' mod='ets_cfultimate'}</th>
                        <td>
                            <fieldset>
                                <label><input type="checkbox"
                                              name="required"/> {l s='Required field' mod='ets_cfultimate'}</label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label
                                    for="tag-generator-panel-file-name">{l s='Name' mod='ets_cfultimate'}</label></th>
                        <td><input type="text" id="tag-generator-panel-file-name" class="tg-name oneline" name="name"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label
                                    for="tag-generator-panel-file-limit">{l s='File size limit (MB)' mod='ets_cfultimate'}</label>
                        </th>
                        <td>
                            <input type="text" id="tag-generator-panel-file-limit" class="filesize oneline option" name="limit" data-max-filesize="{$max_upload_file|escape:'html':'UTF-8'}"/>
                            {if isset($max_upload_file) && $max_upload_file}
                                <span class="help-block">{l s='Maximum size : ' mod='ets_cfultimate'} {$max_upload_file|escape:'html':'UTF-8'} </span>
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tag-generator-panel-file-filetypes">{l s='Acceptable file types' mod='ets_cfultimate'}</label></th>
                        <td><input type="text" id="tag-generator-panel-file-filetypes" class="filetype oneline option" name="filetypes"/>
                            <span class="help-block">{l s='Eg: gif|png|jpg|jpeg' mod='ets_cfultimate'}</span>
                        </td>
                    </tr>
                    {include file="./desc.tpl" input_type='file'}
                    <tr>
                        <th scope="row"><label for="tag-generator-panel-file-id">{l s='Id attribute' mod='ets_cfultimate'}</label></th>
                        <td><input type="text" id="tag-generator-panel-file-id" class="idvalue oneline option" name="id"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tag-generator-panel-file-class">{l s='Class attribute' mod='ets_cfultimate'}</label></th>
                        <td><input type="text" id="tag-generator-panel-file-class" class="classvalue oneline option" name="class"/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <div class="ets_cfu_input_file ets_cfu_input"
                 data-type="file"
                 data-required="0"
                 data-name=""
                 data-limit=""
                 data-filetypes=""
                 data-id=""
                 data-class="">
                <div class="ets_cfu_input_generator">
                    <img src="{$img_dir|cat:'ip_file.png'|escape:'html':'UTF-8'}" alt="{l s='Input file' mod='ets_cfultimate'}" />
                    {include file="./form_data.tpl" excludes = array('values') default='label'}
                    <p class="ets_cfu_help_block">{l s='File' mod='ets_cfultimate'}</p>
                </div>
                {include file="./buttons.tpl"}
            </div>
            {include file="./input_tag.tpl" input_type='file'}
        </div>
    </form>
</div>