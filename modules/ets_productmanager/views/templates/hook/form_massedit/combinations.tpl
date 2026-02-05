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
{assign var='has_attributes' value=false}
{if $attributeGroups &&  is_array($attributeGroups)}
    {foreach from =$attributeGroups item='attributeGroup'}
        {if $attributeGroup.attributes}
            {assign var='has_attributes' value=true}
        {/if}
    {/foreach}
{/if}
<div class="form-group">
    <div class="ets_pmn_combination_left {if $has_attributes}col-lg-9{else}col-lg-12{/if}"> 
        <div class="alert alert-info">
            <p class="alert-text">
                {l s='Combinations are the different variations of a product, with attributes like its size, weight or color taking different values. To create a combination, you need to create your product attributes first. Go to Catalog > Attributes & Features for this!' mod='ets_productmanager'}
            </p>
        </div>
        <div id="attributes-generator">
            {if $has_attributes}
                <div class="row">
                    <div class="col-xs-12 col-lg-12">
                        <fieldset class="form-group">
                            <div class="tokenfield form-control">
                                
                            </div>
                        </fieldset>
                    </div>
                </div>
            {/if}
        </div>
        <div class="combinations-list">
        </div>
    </div>
    <div class="ets_pmn_combination_right col-lg-3">
        {if $has_attributes}
            <div id="attributes-list">
                {foreach from=$attributeGroups item='attributeGroup'}
                    {if $attributeGroup.attributes}
                        <div class="attribute-group{if $attributeGroup.is_color_group} attribute-group-colors{/if}">
                            <a  class="attribute-group-name" data-toggle="collapse" aria-expanded="true" href="#attribute-group-{$attributeGroup.id_attribute_group|intval}"> {$attributeGroup.name|escape:'html':'UTF-8'} </a>
                            <div id="attribute-group-{$attributeGroup.id_attribute_group|intval}" class="attributes show collapse in" aria-expanded="true">
                                <div class="attributes-overflow{if $attributeGroup.is_color_group} two-columns{/if}">
                                    {foreach from =$attributeGroup.attributes item='attribute'}
                                        <div class="attribute">
                                            <div class="ets_input_group">
                                                <input  name="attribute_options[{$attribute.id_attribute_group|intval}][{$attribute.id_attribute|intval}]" id="attribute-{$attribute.id_attribute|intval}" class="js-attribute-checkbox massedit-field" data-label="{$attributeGroup.name|escape:'html':'UTF-8'} : {$attribute.name|escape:'html':'UTF-8'}" data-value="{$attribute.id_attribute|intval}" data-group-id="{$attribute.id_attribute_group|intval}" type="checkbox" value="{$attribute.id_attribute|intval}" {if isset($selected_attributeGroups) && in_array($attribute.id_attribute,$selected_attributeGroups)} checked="checked"{/if} />
                                                <div class="ets_input_check"></div>
                                            </div>
                                            <label class="attribute-label" for="attribute-{$attribute.id_attribute|intval}">
                                                <span class="pretty-checkbox {if $attributeGroup.is_color_group}ets-item-color{/if} " {if $attributeGroup.is_color_group} {if $attribute.color} style="background-color:{$attribute.color|escape:'html':'UTF-8'}"{elseif isset($attribute.image) && $attribute.image} style="background-image: url('{$attribute.image|escape:'html':'UTF-8'}');"{/if}{/if}> {$attribute.name|escape:'html':'UTF-8'}</span>
                                            </label>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
        {/if}
    </div>
    <div class="clearfix"></div>
</div>
