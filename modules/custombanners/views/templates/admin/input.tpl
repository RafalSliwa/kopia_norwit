{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if $field.type == 'img'}
    {if !$value}{$value = []}{/if}
    <div class="img-holder{if !empty($value.name)} has-img{/if}" data-field="{$f_key|escape:'html':'UTF-8'}">
        <div class="img-uploader">
            <i class="icon-file-image-o"></i>
            {l s='Drag your image here, or' mod='custombanners'}
            <a href="#" class="img-browse">{l s='browse' mod='custombanners'}</a>
            <input type="file" class="img-file" name="banner_{$f_key|escape:'html':'UTF-8'}_{$id_lang|intval}" style="display:none;">
            {foreach $value as $n => $v}
                {if !isset($field.subfields[$n])}
                    <input type="hidden" name="{$name|escape:'html':'UTF-8'}[{$n|escape:'html':'UTF-8'}]" value="{$v|escape:'html':'UTF-8'}" class="hidden-img-value" data-key="{$n|escape:'html':'UTF-8'}">
                {/if}
            {/foreach}
        </div>
        <div class="img-data">
            {if !empty($value.name)}{$preview_src = $cb->getBannerImgSrc($value.name)}{else}{$preview_src = ''}{/if}
            {include file="./img-preview.tpl" src = $preview_src configurable = !empty($field.subfields)}
            {if isset($value.w) && isset($value.h)}
                <div class="img-info">
                    {$value.w|intval}x{$value.h|intval} px | {$value.b|escape:'html':'UTF-8'}
                    {if !empty($value.o)} <span class="o-data">({l s='%s compressed' mod='custombanners' sprintf=[$value.o]})</span>{/if}
                </div>
            {/if}
            {if !empty($field.subfields)}
                <div class="img-settings clearfix">
                    {foreach $field.subfields as $n => $sf}
                        {if $n == 'custom_file_name' && !empty($value.name)}{$sf.locked_overlay = $value.name}{/if}
                        {if !empty($value[$n])}{$sf.value = $value[$n]}{/if}
                        {include file="./form-group.tpl"
                            name="$name[$n]"
                            field=$sf
                            group_class='img-form-group clearfix'
                            label_class='control-label col-lg-2'
                            input_wrapper_class='col-lg-10'
                            input_class='visible-img-value'
                        }
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
{else if $field.type == 'link'}
    <select name="{$name|escape:'html':'UTF-8'}[type]" class="col-lg-3 linkTypeSelector">
        {foreach $field.selector key=k item=type}
            <option value="{$k|escape:'html':'UTF-8'}"{if $value && $value.type == $k} selected{/if}>{$type|escape:'html':'UTF-8'}</option>
        {/foreach}
    </select>
    <div class="input-group link-type col-lg-9" data-type="{if $value && $value.type}{$value.type|escape:'html':'UTF-8'}{else}custom{/if}">
        <span class="input-group-addon">
            <span class="any">{l s='Any URL' mod='custombanners'}</span>
            <span class="by_id">
                <span class="label-tooltip" data-toggle="tooltip" title="{l s='Just add the ID of selected resource. For example: 10' mod='custombanners'}">
                    <i class="icon-info-circle"></i>
                </span>
                {l s='ID' mod='custombanners'}
            </span>
        </span>
        <input type="text" name="{$name|escape:'html':'UTF-8'}[href]" value="{if $value && $value.href}{$value.href|escape:'html':'UTF-8'}{/if}">
        <span class="input-group-addon">
            <label class="label-checkbox">
                <input type="checkbox" name="{$name|escape:'html':'UTF-8'}[_blank]" value="1"{if $value && isset($value._blank)} checked="checked"{/if}>
                {l s='new window' mod='custombanners'}
            </label>
        </span>
    </div>
{else if $field.type == 'html'}
    <textarea class="mce" name="{$name|escape:'html':'UTF-8'}">{$value}{* can not be escaped *}</textarea>
{else if $field.type == 'exceptions'}
    {foreach $field.selectors as $key => $selector}
        {if !$value}{$value = []}{/if}
        {if !isset($value[$key]['type'])}{$value[$key]['type'] = '0'}{/if}
        {if !isset($value[$key]['ids'])}{$value[$key]['ids'] = ''}{/if}
        <div class="exceptions-block {$key|escape:'html':'UTF-8'}{if $value[$key]['type']} has-ids{/if}">
            <select name="{$name|escape:'html':'UTF-8'}[{$key|escape:'html':'UTF-8'}][type]" class="exc {$key|escape:'html':'UTF-8'}">
                {if $banner.hook_name == 'displayHome' && $key == 'page'}
                    <option value="0">{l s='Only on homepage (hook displayHome)' mod='custombanners'}</option>
                {else}
                    {foreach $selector as $k => $type}
                        {$selected = $value[$key]['type'] == $k}
                        <option value="{$k|escape:'html':'UTF-8'}"{if $selected} selected{/if}>{$type|escape:'html':'UTF-8'}</option>
                    {/foreach}
                {/if}
            </select>
            <div class="input-group exc-ids">
                <span class="input-group-addon">
                    <span class="label-tooltip" data-toggle="tooltip" title="{l s='For example: 11, 15, 18' mod='custombanners'}">
                        {$show_exclude_txt = $value[$key]['type'] && Tools::substr($value[$key]['type'], -4) == '_all'}
                        <span class="include-ids-txt{if $show_exclude_txt} hidden{/if}">{l s='IDs' mod='custombanners'}</span>
                        <span class="exclude-ids-txt{if !$show_exclude_txt} hidden{/if}">{l s='Except IDs' mod='custombanners'}</span>
                    </span>
                </span>
                <input type="text" name="{$name|escape:'html':'UTF-8'}[{$key|escape:'html':'UTF-8'}][ids]" value="{$value[$key]['ids']|escape:'html':'UTF-8'}" class="ids">
            </div>
        </div>
    {/foreach}
{else if $f_key == 'css_class'}
    <div class="input-group">
        <input type="text" name="{$name|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}">
        <span class="input-group-addon act show-classes">
            {l s='Predefined classes' mod='custombanners'}
            <i class="icon-angle-down"></i>
        </span>
    </div>
{else}
    <input type="text" name="{$name|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" class="{if $field.type == 'date'} datepicker{/if}">
{/if}
{* since 3.0.0 *}
