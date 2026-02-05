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
{assign var='condition' value=$contact_form->condition|json_decode:true}
{if isset($condition.if) && $condition.if|count > 0}
<script type="text/javascript">
    ETS_CFU_CONDITION_FF_{$contact_form->id|intval} ={$condition.fields_form nofilter};
    ETS_CFU_CONDITION_IF_{$contact_form->id|intval} ={$condition.if|json_encode nofilter};
    ETS_CFU_CONDITION_OP_{$contact_form->id|intval} ={$condition.operator|json_encode nofilter};
    ETS_CFU_CONDITION_VL_{$contact_form->id|intval} ={$condition.value|json_encode nofilter};
    ETS_CFU_CONDITION_DO_{$contact_form->id|intval} ={$condition.do|json_encode nofilter};
    ETS_CFU_CONDITION_FI_{$contact_form->id|intval} ={$condition.fields|json_encode nofilter};
</script>
{/if}
{if $displayHook|trim != 'floating'}
    {if $open_form_by_button}
        {assign var="button" value=$contact_form->properties.button}
        {if isset($button.hover_color) && $button.hover_color|trim !== '' || isset($button.background_hover_color) && $button.background_hover_color !== ''}
            <style type="text/css">
                #open_form_by_button_{$contact_form->unit_tag|cat:':hover'|escape:'html':'UTF-8'}{
                    {if isset($button.hover_color) && $button.hover_color|trim !== ''}color: {$button.hover_color|escape:'html':'UTF-8'} !important;fill: {$button.hover_color|escape:'html':'UTF-8'} !important;{/if}
                    {if isset($button.background_hover_color) && $button.background_hover_color !== ''}background-color: {$button.background_hover_color|escape:'html':'UTF-8'} !important;{/if}
                }
            </style>
        {/if}
        <span id="open_form_by_button_{$contact_form->unit_tag|escape:'html':'UTF-8'}" class="ctf_click_open_contactform7 btn btn-primary{if $button.label|trim == ''} no-label{/if} no_floating_form"
              style="{if isset($button.background_color) && $button.background_color|trim !== ''}background-color: {$button.background_color|escape:'html':'UTF-8'};{/if}{if isset($button.text_color) && $button.text_color|trim !== ''}color: {$button.text_color|escape:'html':'UTF-8'};fill: {$button.text_color|escape:'html':'UTF-8'};{/if}"
              data-id="{$contact_form->unit_tag|escape:'html':'UTF-8'}">
                {if isset($button.icon_enabled) && $button.icon_enabled}
                    {if isset($button.icon_custom_file) && $button.icon_custom_file !== ''}
                        <img src="{$button.icon_custom_file nofilter}" style="max-width: 40px;max-height: 40px;">
                    {elseif isset($button.icon_custom) && $button.icon_custom !== ''}
                        {$button.icon_custom nofilter}
                    {else}
                        <svg class="w_14 h_14" enable-background="new 0 0 512 512" height="14" viewBox="0 0 512 512" width="14" xmlns="http://www.w3.org/2000/svg"><g><path d="m494.5 60.514h-477c-9.649 0-17.5 7.851-17.5 17.5v259.93c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-199.403h482v295.446c0 1.378-1.121 2.5-2.5 2.5h-477c-1.379 0-2.5-1.122-2.5-2.5v-61.52c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v61.52c0 9.649 7.851 17.5 17.5 17.5h477c9.649 0 17.5-7.851 17.5-17.5v-355.973c0-9.65-7.851-17.5-17.5-17.5zm-479.5 63.027v-45.527c0-1.378 1.121-2.5 2.5-2.5h477c1.379 0 2.5 1.122 2.5 2.5v45.527z"/><path d="m47.546 92.027h-.113c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.113c4.143 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5z"/><path d="m70.188 92.027h-.112c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.112c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m92.832 92.027h-.112c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.112c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m464.567 92.027h-13.956c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h13.956c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m99.338 179.684h92.44c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-92.44c-9.649 0-17.5 7.851-17.5 17.5v211.996c0 9.649 7.851 17.5 17.5 17.5h313.324c9.649 0 17.5-7.851 17.5-17.5v-89.21c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v89.21c0 1.378-1.121 2.5-2.5 2.5h-313.324c-1.379 0-2.5-1.122-2.5-2.5v-143.167h318.324v19.434c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-88.264c0-9.649-7.851-17.5-17.5-17.5h-186.361c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h186.361c1.379 0 2.5 1.122 2.5 2.5v53.83h-318.324v-53.83c0-1.378 1.121-2.499 2.5-2.499z"/><path d="m360.168 222.4c1.465 1.464 3.385 2.197 5.304 2.197s3.839-.732 5.304-2.197l3.944-3.944 3.944 3.944c1.465 1.464 3.385 2.197 5.304 2.197s3.839-.732 5.304-2.197c2.929-2.929 2.929-7.678 0-10.606l-3.945-3.945 3.945-3.945c2.929-2.929 2.929-7.678 0-10.606-2.93-2.929-7.678-2.929-10.607 0l-3.944 3.944-3.944-3.944c-2.93-2.929-7.678-2.929-10.607 0s-2.929 7.678 0 10.606l3.945 3.945-3.945 3.945c-2.931 2.928-2.931 7.677-.002 10.606z"/><path d="m136.5 284.833h36c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-36c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5z"/><path d="m371.501 310.955c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.131-3.372-7.468-7.499-7.468z"/><path d="m371.501 334.478c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.132-3.372-7.468-7.499-7.468z"/><path d="m371.501 358c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.132-3.372-7.468-7.499-7.468z"/></g></svg>
                    {/if}
                {/if}
            {$button.label|escape:'html':'UTF-8'}
        </span>
        <div class="ctf-popup-wapper" id="ctf-popup-wapper-{$contact_form->unit_tag|escape:'html':'UTF-8'}">
            <div class="ctf-popup-table">
                <div class="ctf-popup-tablecell">
                    <div class="ctf-popup-content">
                        <div class="ctf_close_popup">close</div>
                        {/if}
                        {if !$form_elements|trim}
                            <p class="ets_cfu_alert alert alert-warning">{l s='Contact form is empty' mod='ets_cfultimate'}</p>
                        {else}
                            <div role="form" class="wpcfu{if $displayHook} hook{/if}" id="{$contact_form->unit_tag|escape:'html':'UTF-8'}" dir="ltr" data-id="{$contact_form->id|intval}">
                                <form class="ets-cfu-form" action="{$link->getModuleLink('ets_cfultimate','submit')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" autocomplete="false" novalidate="novalidate" data-id="{$contact_form->id|intval}">
                                    {if $displayHook}<h3>{$contact_form->title|escape:'html':'UTF-8'}</h3>{/if}
                                    <input type="hidden" name="_wpcfu" value="{$contact_form->id|intval}"/>
                                    <input type="hidden" name="_ets_cfu_version" value="5.0.1"/>
                                    <input type="hidden" name="_ets_cfu_locale" value="en_US"/>
                                    <input type="hidden" name="_ets_cfu_unit_tag" value="wpcfu-{$contact_form->unit_tag|escape:'html':'UTF-8'}"/>
                                    <input type="hidden" name="form_unit_tag" value="{$contact_form->form_unit_tag|escape:'html':'UTF-8'}" />
                                    <input type="hidden" name="_ets_cfu_container_post" value="{$contact_form->id|intval}"/>
                                    {if isset($ets_cfu_product) && $ets_cfu_product}
                                        <input type="hidden" name="_ets_cfu_product_id" value="{$ets_cfu_product.id|intval}"/>
                                        <div class="ets_cfu_product">
                                            <a href="{$ets_cfu_product.link nofilter}">
                                                {if isset($ets_cfu_product.cover) && $ets_cfu_product.cover|trim !== ''}
                                                    <img src="{$ets_cfu_product.cover nofilter}">
                                                {/if}
                                                {$ets_cfu_product.name|escape:'html':'UTF-8'}
                                            </a>
                                        </div>
                                    {/if}
                                    {$form_elements nofilter}
                                    <div class="wpcfu-response-output wpcfu-display-none" id="wpcfu-response-output"></div>
                                </form>
                                <div class="clearfix">&nbsp;</div>
                            </div>
                        {/if}
                        {if $open_form_by_button}
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/if}

{if $button_popup_enabled && $displayHook|trim == 'floating'}
    {assign var="floating" value=$contact_form->properties.floating}
    {if isset($floating.hover_color) && $floating.hover_color|trim !== '' || isset($floating.background_hover_color) && $floating.background_hover_color !== ''}
        <style type="text/css">
            #button_popup_enabled_{$contact_form->unit_tag|cat:':hover'|escape:'html':'UTF-8'}{
                {if isset($floating.hover_color) && $floating.hover_color|trim !== ''}color: {$floating.hover_color|escape:'html':'UTF-8'} !important;fill: {$floating.hover_color|escape:'html':'UTF-8'} !important;{/if}
                {if isset($floating.background_hover_color) && $floating.background_hover_color !== ''}background-color: {$floating.background_hover_color|escape:'html':'UTF-8'} !important;{/if}
            }
        </style>
    {/if}
    <span id="button_popup_enabled_{$contact_form->unit_tag|escape:'html':'UTF-8'}"
          class="ctf_click_open_contactform7{if $floating.label|trim == ''} no-label{/if} btn btn-primary ets-cfu-button-popup {$floating.popup_position|escape:'html':'UTF-8'}"
          style="{if isset($floating.background_color) && $floating.background_color|trim !== ''}background-color: {$floating.background_color|escape:'html':'UTF-8'};{/if}{if isset($floating.text_color) && $floating.text_color|trim !== ''}color: {$floating.text_color|escape:'html':'UTF-8'};fill: {$floating.text_color|escape:'html':'UTF-8'};{/if}{if $floating.popup_position == 'middle_right'}margin-right:{$floating.popup_right|intval}px;margin-top:{$floating.popup_top|intval}px;{elseif $floating.popup_position == 'bottom_right'}margin-right:{$floating.popup_right|intval}px;margin-bottom:{$floating.popup_bottom|intval}px;{elseif $floating.popup_position == 'middle_left'}margin-left:{$floating.popup_left|intval}px;margin-top:{$floating.popup_top|intval}px;{elseif $floating.popup_position == 'bottom_left'}margin-left:{$floating.popup_left|intval}px;margin-bottom:{$floating.popup_bottom|intval}px;{/if}"
          data-id="{$contact_form->unit_tag|escape:'html':'UTF-8'}"
    >
        {if isset($floating.icon_enabled) && $floating.icon_enabled}
            {if isset($floating.icon_custom_file) && $floating.icon_custom_file !== ''}
                <img src="{$floating.icon_custom_file nofilter}" style="max-width: 40px;max-height: 40px;">
            {elseif isset($floating.icon_custom) && $floating.icon_custom !== ''}
                {$floating.icon_custom nofilter}
            {else}
                <svg class="w_14 h_14" enable-background="new 0 0 512 512" height="14" viewBox="0 0 512 512" width="14" xmlns="http://www.w3.org/2000/svg"><g><path d="m494.5 60.514h-477c-9.649 0-17.5 7.851-17.5 17.5v259.93c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-199.403h482v295.446c0 1.378-1.121 2.5-2.5 2.5h-477c-1.379 0-2.5-1.122-2.5-2.5v-61.52c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v61.52c0 9.649 7.851 17.5 17.5 17.5h477c9.649 0 17.5-7.851 17.5-17.5v-355.973c0-9.65-7.851-17.5-17.5-17.5zm-479.5 63.027v-45.527c0-1.378 1.121-2.5 2.5-2.5h477c1.379 0 2.5 1.122 2.5 2.5v45.527z"/><path d="m47.546 92.027h-.113c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.113c4.143 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5z"/><path d="m70.188 92.027h-.112c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.112c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m92.832 92.027h-.112c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h.112c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m464.567 92.027h-13.956c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h13.956c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z"/><path d="m99.338 179.684h92.44c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-92.44c-9.649 0-17.5 7.851-17.5 17.5v211.996c0 9.649 7.851 17.5 17.5 17.5h313.324c9.649 0 17.5-7.851 17.5-17.5v-89.21c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v89.21c0 1.378-1.121 2.5-2.5 2.5h-313.324c-1.379 0-2.5-1.122-2.5-2.5v-143.167h318.324v19.434c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-88.264c0-9.649-7.851-17.5-17.5-17.5h-186.361c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h186.361c1.379 0 2.5 1.122 2.5 2.5v53.83h-318.324v-53.83c0-1.378 1.121-2.499 2.5-2.499z"/><path d="m360.168 222.4c1.465 1.464 3.385 2.197 5.304 2.197s3.839-.732 5.304-2.197l3.944-3.944 3.944 3.944c1.465 1.464 3.385 2.197 5.304 2.197s3.839-.732 5.304-2.197c2.929-2.929 2.929-7.678 0-10.606l-3.945-3.945 3.945-3.945c2.929-2.929 2.929-7.678 0-10.606-2.93-2.929-7.678-2.929-10.607 0l-3.944 3.944-3.944-3.944c-2.93-2.929-7.678-2.929-10.607 0s-2.929 7.678 0 10.606l3.945 3.945-3.945 3.945c-2.931 2.928-2.931 7.677-.002 10.606z"/><path d="m136.5 284.833h36c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-36c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5z"/><path d="m371.501 310.955c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.131-3.372-7.468-7.499-7.468z"/><path d="m371.501 334.478c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.132-3.372-7.468-7.499-7.468z"/><path d="m371.501 358c-.011 0-.022 0-.033 0l-235 1c-4.142.018-7.485 3.39-7.468 7.532.018 4.132 3.372 7.468 7.499 7.468h.033l235-1c4.142-.018 7.485-3.39 7.468-7.532-.018-4.132-3.372-7.468-7.499-7.468z"/></g></svg>
            {/if}
        {/if}
        {$floating.label|escape:'html':'UTF-8'}
    </span>
    <div class="ctf-popup-wapper" id="ctf-popup-wapper-{$contact_form->unit_tag|escape:'html':'UTF-8'}">
        <div class="ctf-popup-table">
            <div class="ctf-popup-tablecell">
                <div class="ctf-popup-content">
                    <div class="ctf_close_popup">close</div>
                    {if !$form_elements|trim}
                        <p class="ets_cfu_alert alert alert-warning">{l s='Contact form is empty' mod='ets_cfultimate'}</p>
                    {else}
                        <div role="form" class="wpcfu{if $displayHook} hook{/if}" id="{$contact_form->unit_tag|escape:'html':'UTF-8'}" dir="ltr" data-id="{$contact_form->id|intval}">
                            <form class="ets-cfu-form" action="{$link->getModuleLink('ets_cfultimate','submit')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" autocomplete="false" novalidate="novalidate" data-id="{$contact_form->id|intval}">
                                {if $displayHook}<h3>{$contact_form->title|escape:'html':'UTF-8'}</h3>{/if}
                                <input type="hidden" name="_wpcfu" value="{$contact_form->id|intval}"/>
                                <input type="hidden" name="_ets_cfu_version" value="5.0.1"/>
                                <input type="hidden" name="_ets_cfu_locale" value="en_US"/>
                                <input type="hidden" name="_ets_cfu_unit_tag" value="wpcfu-{$contact_form->unit_tag|escape:'html':'UTF-8'}"/>
                                <input type="hidden" name="form_unit_tag" value="{$contact_form->form_unit_tag|escape:'html':'UTF-8'}" />
                                <input type="hidden" name="_ets_cfu_container_post" value="{$contact_form->id|intval}"/>
                                {if isset($ets_cfu_product) && $ets_cfu_product}
                                    <input type="hidden" name="_ets_cfu_product_id" value="{$ets_cfu_product.id|intval}"/>
                                    <div class="ets_cfu_product">
                                        <a href="{$ets_cfu_product.link nofilter}">
                                            {if isset($ets_cfu_product.cover) && $ets_cfu_product.cover|trim !== ''}
                                                <img src="{$ets_cfu_product.cover nofilter}" style="width: 45px;height:auto;vertical-align: middle;">
                                            {/if}
                                            {$ets_cfu_product.name|escape:'html':'UTF-8'}
                                        </a>
                                    </div>
                                {/if}
                                {$form_elements nofilter}
                                <div class="wpcfu-response-output wpcfu-display-none" id="wpcfu-response-output"></div>
                            </form>
                            <div class="clearfix">&nbsp;</div>
                        </div>
                    {/if}

                </div>
            </div>
        </div>
    </div>
{/if}