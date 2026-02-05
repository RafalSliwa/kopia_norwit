{*
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
*}
{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.name == 'categories_tree'}
        <div class="panel">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                        <input type="checkbox" id="checkAllCheckbox">
                        </th>
                        <th>
                            <span class="title_box">
                                {l s='ID' mod='addtocartbar'}
                            </span>
                        </th>
                        <th>
                            <span class="title_box">
                                {l s='Name' mod='addtocartbar'}
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {if !isset($categories) || empty($categories)}
                        <tr>
                            <td>{l s='No Category found.' mod='addtocartbar'}</td>
                        </tr>
                    {else}
                        {foreach from=$categories item=category}
                            <tr>
                                <td>
                                    <input type="checkbox" name="categoryBox[]" value="{$category.id_category|escape:'htmlall':'UTF-8'}"
                                        {if isset($selected_cat) && in_array($category.id_category|escape:'htmlall':'UTF-8', $selected_cat)}
                                        checked="checked" {/if} />
                                </td>
                                <td>
                                    {$category.id_category|escape:'htmlall':'UTF-8'}
                                </td>
                                <td>
                                    {$category.name|escape:'htmlall':'UTF-8'}
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </tbody>
            </table>
        </div>
    {/if}
    {if $input.name == 'STARTING_RESOLUTION'}
        <div class="list-filter">
            <label for="RESOLUTION_ENDING">{l s='Starting Resolution:' mod='addtocartbar'}</label>
            <input type="number" pattern="[0-9]*" id="STARTING_RESOLUTION" name="STARTING_RESOLUTION"
                value="{$fields_value.STARTING_RESOLUTION|escape:'htmlall':'UTF-8'}" min="200" max="5000"
                class="input fixed-width-xl form-control" placeholder="{l s='Enter a number' mod='addtocartbar'}"
                oninput="STARTING_RESOLUTION.value = STARTING_RESOLUTION.value">
        </div>
        <br>
        <div class="list-filter">
            <label for="RESOLUTION_ENDING">{l s='Ending Resolution:' mod='addtocartbar'}</label>
            <input type="number" pattern="[0-9]*" id="RESOLUTION_ENDING" name="RESOLUTION_ENDING"
                value="{$fields_value.RESOLUTION_ENDING|escape:'htmlall':'UTF-8'}" min="200" max="5000"
                class="input fixed-width-xl form-control" placeholder="{l s='Enter a number' mod='addtocartbar'}"
                oninput="RESOLUTION_ENDING.value = RESOLUTION_ENDING.value">
        </div>
    {/if}
    {if $input.name == 'STICKY_BODY_PAD'}
        <input type="range" name="STICKY_BODY_PAD" id="STICKY_BODY_PAD"
            value="{$fields_value.STICKY_BODY_PAD|escape:'htmlall':'UTF-8'}" min="1" max="300" class="input fixed-width-md"
            oninput="BODY_PAD.value = STICKY_BODY_PAD.value">
        <output name="BODY_PAD" id="BODY_PAD">{$fields_value.STICKY_BODY_PAD|escape:'htmlall':'UTF-8'}</output>
    {/if}
    {if $input.name == 'STICKY_BORDER_RADIUS'}
        <input type="range" name="STICKY_BORDER_RADIUS" id="STICKY_BORDER_RADIUS"
            value="{$fields_value.STICKY_BORDER_RADIUS|escape:'htmlall':'UTF-8'}" min="1" max="50" class="input fixed-width-md"
            oninput="BORDER_WIDTH.value = STICKY_BORDER_RADIUS.value">
        <output name="BORDER_WIDTH" id="BORDER_WIDTH">{$fields_value.STICKY_BORDER_RADIUS|escape:'htmlall':'UTF-8'}</output>
    {/if}
    {if $input.name == 'STICKY_BAR_HEIGHT'}
        <input type="range" name="STICKY_BAR_HEIGHT" id="STICKY_BAR_HEIGHT"
            value="{$fields_value.STICKY_BAR_HEIGHT|escape:'htmlall':'UTF-8'}" min="100" max="500" class="input fixed-width-md"
            oninput="STICKY_HEIGHT.value = STICKY_BAR_HEIGHT.value">
        <output name="STICKY_HEIGHT" id="STICKY_HEIGHT">{$fields_value.STICKY_BAR_HEIGHT|escape:'htmlall':'UTF-8'}</output>
    {/if}
    {if $input.name == 'STICKY_EXC_PRODUCTS'}
        <div class="form-group col-lg-12" id="pquote_product_list" style="display: block">
            <br />
            <label class="control-label col-lg-4 col-md-pull-3">{l s='Find products to include' mod='addtocartbar'}</label>
            <div class="col-lg-8 col-md-pull-3">
                <div class="col-lg-12 placeholder_holder product-holder-margin">
                    <input type="text" placeholder="{l s='Example' mod='addtocartbar'}: Blue XL shirt"
                        onkeyup="getRelProducts(this);" />
                    <div id="rel_holder"></div>
                    <div id="rel_holder_temp">
                        <ul>
                            {if (!empty($products))}
                                {foreach from=$products item=product}
                                    <li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
                                        <div class="media-left"><img
                                                src="{Context::getContext()->link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
                                                class="media-object image"></div>
                                        <div class="media-body media-middle"><span
                                                class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i
                                                onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input
                                            type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="STICKY_EXC_PRODUCTS[]">
                                    </li>
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <p class="help-block selectproduct-description">
            {l s='Choose to include Products on which you want to show the sticky add to cart bar.' mod='addtocartbar'}
        </p>
    {/if}
    {$smarty.block.parent}
{/block}