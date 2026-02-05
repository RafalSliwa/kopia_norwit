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
<div class="form-wrapper form-wrapper-edit-category">
    {if $type_input=='checkbox'}
        <div id="ps_categoryTags" class="pstaggerTagsWrapper ps_categoryTags" style="{if $categories}display: block;{else}display: none;{/if}">
            {if $categories}
                {foreach from= $categories item='category'}
                    <span class="pstaggerTag">
                        <span data-id="{$category.id_category|intval}" title="{$category.name|escape:'html':'UTF-8'}"> {$category.name|escape:'html':'UTF-8'}</span>
                        <a class="pstaggerClosingCross" href="#" data-id="{$category.id_category|intval}">x</a>
                    </span>
                {/foreach}
            {/if}
        </div>
    {/if}
    <div class="categories-tree-actions js-categories-tree-actions">
        <span class="form-control-label categories-tree-expand" id="categories-tree-expand" style="display: none;"><i class="material-icons">expand_more</i>{l s='Expand' mod='ets_productmanager'}</span>
        <span class="form-control-label categories-tree-reduce" id="categories-tree-reduce"><i class="material-icons">expand_less</i>{l s='Collapse' mod='ets_productmanager'}</span>
    </div>
    <div >
        <ul class="category-tree">
            {if !$backend}
                <li class="form-control-label text-right main-category">{l s='Main category' mod='ets_productmanager'}</li>
            {/if}
            {if $blockCategTree}
                {if !in_array($blockCategTree[0].id_category,$disabled_categories)}
                <li style="list-style: none;">
                    <div class="checkbox {if $blockCategTree[0].children} has-child{/if}">
                        <span>
                            {if $displayInput}
                                <input class="category" name="{if $type_input=='radio'}{$name|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}[]{/if}" value="{$blockCategTree[0].id_category|intval}"{if in_array($blockCategTree[0].id_category,$selected_categories)} checked="checked"{/if}{if in_array($blockCategTree[0].id_category,$disabled_categories)} disabled="disabled"{/if} {if $type_input=='radio'}type="radio"{else}type="checkbox"{/if} />
                            {/if}
                            <span class="label">{$blockCategTree[0].name|escape:'html':'UTF-8'}</span>
                            {if !$backend}
                                <input class="default-category" value="{$blockCategTree[0].id_category|intval}" name="id_category_default" type="radio" {if in_array($blockCategTree[0].id_category,$disabled_categories)} disabled="disabled"{/if}{if in_array($blockCategTree[0].id_category,$selected_categories)} checked="checked"{/if} />
                            {/if}
                            {if !$displayInput}
                                (ID: {$blockCategTree[0].id_category|intval})
                            {/if}
                        </span>
                    </div>
                {/if}
                    {if $blockCategTree[0].children}
                        {if !in_array($blockCategTree[0].id_category,$disabled_categories)}
              		        <ul class="children">
                        {/if}
                            {foreach from=$blockCategTree[0].children item=child name=blockCategTree}
                                {if $smarty.foreach.blockCategTree.last}
                        			{include file="$branche_tpl_path_input" node=$child last='true'}
                        		{else}
                        			{include file="$branche_tpl_path_input" node=$child}
                        		{/if}
                                
                        	{/foreach}
                        {if !in_array($blockCategTree[0].id_category,$disabled_categories)}
                        </ul>
                        {/if}
                    {/if}
                {if !in_array($blockCategTree[0].id_category,$disabled_categories)}
                </li>
                {/if}
            {/if}
        </ul>
    </div>
</div>