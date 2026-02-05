{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    SeoSA    <885588@bk.ru>
* @copyright 2012-2024 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<div class="default-settings clearfix">

    <div class="row">

        <label class="col-xs-12 col-sm-6 control-label text-left">
            <h2 class="mr-1 mb-0 mt-0 float-left">
                {l s='Default settings' mod='sitemappro'}:
            </h2>
        </label>

        {if $type_object == 'product'}
            <div class="col-xs-12 col-sm-6">
                <div class="clearfix">
                    <label class="control-label margin-right" style="text-align: left">{l s='Use on default settings category?' mod='sitemappro'}</label>
                    <div class="switch prestashop-switch fixed-width-lg margin-right">
                        {foreach [1,0] as $value}
                            <input type="radio" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][is_on_default_setting_category]" value="{$value|escape:'quotes':'UTF-8'}"
                                    {if $value == 1} id="is_on_default_setting_category_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} id="is_on_default_setting_category_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}
                                    {if $value == 1}
                                        {if $default_settings["{$type_object}"]['is_on_default_setting_category']}checked="checked"{/if}
                                    {else}
                                        {if !$default_settings["{$type_object}"]['is_on_default_setting_category']}checked="checked"{/if}
                                    {/if}
                            />
                            <label {if $value == 1} for="is_on_default_setting_category_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} for="is_on_default_setting_category_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}>
                                {if $value == 1}{l s='Yes' mod='sitemappro'}{else}{l s='No' mod='sitemappro'}{/if}
                            </label>
                        {/foreach}
                        <a class="slide-button btn"></a>
                    </div>
                </div>
            </div>
        {/if}

        {if $type_object == 'category'}
            <div class="col-xs-12 col-sm-6">
                <div class="clearfix">
                    <label class="control-label">{l s='Define product only by default category' mod='sitemappro'}?
                    </label>
                    <span class="switch prestashop-switch fixed-width-lg">
                {foreach [1,0] as $value}
                    <input type="radio"
                           name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][default_category]"
                           value="{$value|escape:'quotes':'UTF-8'}"
                           {if $value == 1}
                               id="default_category_on-{$type_object|escape:'htmlall':'UTF-8'}"
                               {if $default_settings["{$type_object}"]['default_category']}checked="checked"{/if}
                           {else}
                               id="default_category_off-{$type_object|escape:'htmlall':'UTF-8'}"
                               {if !$default_settings["{$type_object}"]['default_category']}checked="checked"{/if}
                           {/if}
                    />
                    <label {if $value == 1}
                        for="default_category_on-{$type_object}" {else}
                        for="default_category_off-{$type_object}" {/if}>
                        {if $value == 1}{l s='Yes' mod='sitemappro'}
                        {else} {l s='No' mod='sitemappro'}{/if}
                    </label>
                {/foreach}
                    <a class="slide-button btn"></a>
            </span>
                </div>
            </div>
        {/if}
    </div>

    <div class="row">

        {if $type_object != 'product'}
            <div class="col-xs-6 col-sm-6">
                <div class="row clearfix margin-top">
                    <label class="col-xs-6 col-sm-6 control-label">{l s='Export category' mod='sitemappro'}</label>
                    <div class="col-xs-6 col-sm-6">
                        <div class="switch prestashop-switch fixed-width-lg margin-right">
                            {foreach [1,0] as $value}
                                <input type="radio" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][is_export]" value="{$value|escape:'quotes':'UTF-8'}"
                                        {if $value == 1} id="disable_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} id="disable_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}
                                        {if $value == 1}
                                            {if $default_settings["{$type_object}"]['is_export']}checked="checked"{/if}
                                        {else}
                                            {if !$default_settings["{$type_object}"]['is_export']}checked="checked"{/if}
                                        {/if}
                                />
                                <label {if $value == 1} for="disable_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} for="disable_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}>
                                    {if $value == 1}{l s='Yes' mod='sitemappro'}{else}{l s='No' mod='sitemappro'}{/if}
                                </label>
                            {/foreach}
                            <a class="slide-button btn"></a>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <div class="col-xs-6 col-sm-6">
            <div class=" clearfix margin-top row">
                <label class="col-xs-6 col-sm-6 control-label">{l s='Export changefreq' mod='sitemappro'}</label>
                <div class="col-xs-6 col-sm-6 ">
                    <div class=" switch prestashop-switch fixed-width-lg">
                        {foreach [1,0] as $value}
                            <input type="radio" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][is_changefreq]" value="{$value|escape:'quotes':'UTF-8'}"
                                    {if $value == 1} id="is_changefreq_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} id="is_changefreq_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}
                                    {if $value == 1}
                                        {if $default_settings["{$type_object}"]['is_changefreq']}checked="checked"{/if}
                                    {else}
                                        {if !$default_settings["{$type_object}"]['is_changefreq']}checked="checked"{/if}
                                    {/if}
                            />
                            <label {if $value == 1} for="is_changefreq_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} for="is_changefreq_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}>
                                {if $value == 1}{l s='Yes' mod='sitemappro'}{else}{l s='No' mod='sitemappro'}{/if}
                            </label>
                        {/foreach}
                        <a class="slide-button btn"></a>
                    </div>
                </div>
            </div>

        </div>

        {if $type_object == 'product'}
            <div class="col-xs-6 col-sm-6">
                <div class="row clearfix margin-top">
                    <label class="col-xs-6 col-sm-6 control-label">{l s='Export product?' mod='sitemappro'}</label>
                    <div class="col-xs-6 col-sm-6">
                        <div class="switch prestashop-switch margin-right fixed-width-lg">
                            {foreach [1,0] as $value}
                                <input type="radio" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][is_on_default_setting_export_product]" value="{$value|escape:'quotes':'UTF-8'}"
                                        {if $value == 1} id="is_on_default_setting_export_product_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} id="is_on_default_setting_export_product_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}
                                        {if $value == 1}
                                            {if $default_settings["{$type_object}"]['is_on_default_setting_export_product']}checked="checked"{/if}
                                        {else}
                                            {if !$default_settings["{$type_object}"]['is_on_default_setting_export_product']}checked="checked"{/if}
                                        {/if}
                                />
                                <label {if $value == 1} for="is_on_default_setting_export_product_on-{$type_object|escape:'htmlall':'UTF-8'}" {else} for="is_on_default_setting_export_product_off-{$type_object|escape:'htmlall':'UTF-8'}" {/if}>
                                    {if $value == 1}{l s='Yes' mod='sitemappro'}{else}{l s='No' mod='sitemappro'}{/if}
                                </label>
                            {/foreach}
                            <a class="slide-button btn"></a>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        {if $type_object != 'product'}
            <div class="col-xs-6 col-sm-6">

                <div class="row margin-top">
                    <label class="col-xs-6 col-sm-6 control-label priority-block_label">{l s='Priority' mod='sitemappro'}</label>
                    <select class="col-xs-6 col-sm-6 form-control priority-block_select margin-right" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][priority]">
                        {foreach from=$priorities item=priority}
                            <option {if $default_settings["{$type_object}"]['priority'] == $priority.id}selected{/if} value="{$priority.id|escape:'quotes':'UTF-8'}">{$priority.id|escape:'quotes':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>

            </div>
        {/if}

        {if $type_object == 'product'}
            <div class="col-xs-6 col-sm-6">

                <div class="row margin-top">
                    <label class="col-xs-6 col-sm-6 control-label priority-block_label">{l s='Priority' mod='sitemappro'}</label>
                    <select class="col-xs-6 col-sm-6 form-control priority-block_select margin-right" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][priority]">
                        {foreach from=$priorities item=priority}
                            <option {if $default_settings["{$type_object}"]['priority'] == $priority.id}selected{/if} value="{$priority.id|escape:'quotes':'UTF-8'}">{$priority.id|escape:'quotes':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>

            </div>
        {/if}

        <div class="col-xs-6 col-sm-6">

            <div class="row margin-top">
                <label class="col-xs-6 col-sm-6 control-label changefreq-block_label">{l s='Changefreq' mod='sitemappro'}</label>
                <div class="col-xs-6 col-sm-6 ">
                    <select class="form-control changefreq-block_select margin-right" name="default_settings[{$type_object|escape:'htmlall':'UTF-8'}][changefreq]">
                        {foreach from=$changefreqs item=changefreq}
                            <option {if $default_settings["{$type_object}"]['changefreq'] == $changefreq.id}selected{/if} value="{$changefreq.id|escape:'quotes':'UTF-8'}">{$changefreq.name|escape:'quotes':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

        </div>
    </div>

</div>