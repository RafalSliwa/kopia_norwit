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
<tr>
    <th scope="row"><label for="tag-generator-panel-{$input_type|escape:'html':'UTF-8'}-label">{l s='Label' mod='ets_cfultimate'}</label></th>
    <td>
        {if $is_multi_lang}
        <div class="ets_cfu_input_groups row">
            {/if}
            {foreach $languages as $language}
                {if $is_multi_lang}
                    <div class="translatable-field lang-{$language.id_lang|intval}"{if $is_multi_lang} data-lang="{$language.id_lang|intval}"{/if}{if $language.id_lang != $defaultFormLanguage} style="display:none"{/if}>
                    <div class="col-lg-9">
                {/if}
                <input id="tag-generator-panel-{$input_type|escape:'html':'UTF-8'}-label" data-unique="label" class="cfu-label is-multi-lang" type="text" name="label_{if $is_multi_lang}{$language.id_lang|intval}{else}{$defaultFormLanguage|intval}{/if}"/>
                {if $is_multi_lang}
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                            {$language.iso_code|escape:'quotes':'UTF-8'}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            {foreach from=$languages item=language}
                                <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                            {/foreach}
                        </ul>
                    </div>
                    </div>
                {/if}
            {/foreach}
            {if $is_multi_lang}
        </div>
        {/if}
    </td>
</tr>
