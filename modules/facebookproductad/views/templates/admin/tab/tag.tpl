{*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
<div id="fpa bt_advanced-tag" class="col-xs-12 bootstrap">
    <form class="form-horizontal" method="post" id="bt_form-advanced-tag" name="bt_form-advanced-tag">
        <h1 class="text-center mb-3">{l s='Tag attribution table' mod='facebookproductad'}</h1>
        <hr />
        <div class="alert alert-warning col-xs-12">
            <p>{l s='WARNING : before starting, please note that the categories displayed below are the DEFAULT categories of your products. So, make sure that your products are correctly assigned to the right default category.' mod='facebookproductad'}</p>
        </div>
        <span class="mt-3"></span>
        <hr />

        {if !empty($useGender) && $currentTagHandle == 'gender'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more, read the following FAQs:' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/274" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about adult tags' mod='facebookproductad'}</a>&nbsp;&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/270" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about gender tags' mod='facebookproductad'}</a>&nbsp;&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/271" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about age group tags' mod='facebookproductad'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='facebookproductad'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='facebookproductad'}</option>
                        <option value="product_data" {if $useGenderProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='facebookproductad'}</option>
                    </select>
                </div>
            </div>
        {/if}

        {if !empty($useAgegroup) && $currentTagHandle == 'agegroup'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more,' mod='facebookproductad'}&nbsp;
                    <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/271" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='read our FAQ.' mod='facebookproductad'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='facebookproductad'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='facebookproductad'}</option>
                        <option value="product_data" {if $useAgeGroupProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='facebookproductad'}</option>
                    </select>
                </div>
            </div>
        {/if}

        {if !empty($useAdult) && $currentTagHandle == 'adult'}
            <div class="card bg-light shadow-lg rounded border border-dark mb-3 mt-3 p-2">

                <div class="span alert alert-info mb-2">
                    {l s='Select how you want to assign the tag values to your products. Choose the first option to assign the same tag value to all the products in a given category. Choose the second option if the products in a category do not necessarily have the same tag value. In this case, you must first define a product feature corresponding to the tag, set the right feature value for each product to be exported and then come back here to select the corresponding feature for each category. To learn more,' mod='facebookproductad'}&nbsp;
                    <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/274" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='read our FAQ.' mod='facebookproductad'}</a>
                </div>

                <div class="form-group px-5 py-3 ">
                    <label for="set_tag_mode">{l s='Select the tag attribution mode:' mod='facebookproductad'}</label>
                    <select class="form-control" class="set_tag_mode" name="set_tag_mode" id="set_tag_mode">
                        <option value="bulk">{l s='Assign the same tag value to all products in a category' mod='facebookproductad'}</option>
                        <option value="product_data" {if $useAdultProduct == 1} selected {/if}>{l s='Use the values of a feature for each category' mod='facebookproductad'}</option>
                    </select>
                </div>
            </div>
        {/if}

        <div class="card bg-light shadow-lg rounded border border-dark hide">
            <div class="form-group px-5 py-3">
                <label for="set_tag">{l s='Select which type of tags you want to set :' mod='facebookproductad'}</label>
                <select class="form-control" class="set_tag" name="set_tag" id="set_tag">
                    {if !empty($useMaterial)}
                        <option value="material">{l s='Set product material tags' mod='facebookproductad'}</option>
                    {/if}
                    {if !empty($usePattern)}
                        <option value="pattern">{l s='Set product pattern tags' mod='facebookproductad'}</option>
                    {/if}
                    {if !empty($useGender)}
                        <option value="gender">{l s='Set product gender tags' mod='facebookproductad'}</option>
                    {/if}
                    {if !empty($useAgegroup)}
                        <option value="agegroup">{l s='Set product age group tags' mod='facebookproductad'}</option>
                    {/if}
                    {if !empty($useAdult)}
                        <option value="adult">{l s='Set product for adults only tags' mod='facebookproductad'}</option>
                    {/if}
                </select>
            </div>
        </div>

        <div class="bulk-actions">
            <div class="card shadow-sm" id="bulk_action_material">
                <p class="card-text text-center mt-3">{l s='Set MATERIAL tags : for each product default category, if available, you will have to indicate the feature that defines the material of the products that are in this category.' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <select name="set_material_bulk_action" class="set_material_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.material', $('.set_material_bulk_action').val());">{l s='Set for all categories' mod='facebookproductad'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.material', 0);">{l s='Reset' mod='facebookproductad'}
                </p>
            </div>

            <div class="card" id="bulk_action_pattern">
                <p class="card-text text-center mt-3">{l s='Set PATTERN tags : for each product default category, if available, you will have to indicate the feature that defines the pattern of the products that are in this category.' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <select name="set_pattern_bulk_action mb-3" class="set_pattern_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.pattern', $('.set_pattern_bulk_action').val());">{l s='Set for all categories' mod='facebookproductad'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.pattern', 0);">{l s='Reset' mod='facebookproductad'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_adult">
                <p class="card-text text-center mt-3 text-center">{l s='Set AGE GROUP tags : for each product default category, select, in the drop and down menu, the age group for which the products in the category are intended. To assign the same tag to all categories, click on one of the buttons below.' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'adult');">{l s='Adults' mod='facebookproductad'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'all ages');">{l s='All ages' mod='facebookproductad'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'teen');">{l s='Teens' mod='facebookproductad'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'kids');">{l s='Kids' mod='facebookproductad'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'toddler');">{l s='Toddlers' mod='facebookproductad'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'infant');">{l s='Infants' mod='facebookproductad'}</span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.agegroup', 'newborn');">{l s='Newborns' mod='facebookproductad'}</span>
                    - <span class="btn btn-warning btn-lg" onclick="oFpa.doSet('.agegroup', 0);">{l s='Reset' mod='facebookproductad'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_adult_product">
                <p class="card-text text-center mt-3 text-center">{l s='Set AGE GROUP tags : for each product default category, if available, you will have to indicate the feature that defines the age group for which each product in the category is intended. To assign the same feature to all categories, click "Set for all categories".' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <select name="set_adult_bulk_action mb-3" class="set_adult_bulk_action">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.agegroup_product', $('.set_adult_bulk_action').val());">{l s='Set for all categories' mod='facebookproductad'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.agegroup_product', 0);">{l s='Reset' mod='facebookproductad'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_gender">
                <p class="card-text text-center mt-3 text-center">{l s='Set GENDER tags : for each product default category, select, in the drop and down menu, the gender for which the products in the category are intended. To assign the same tag to all categories, click on one of the buttons below.' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.gender', 'male');">{l s='Men (male)' mod='facebookproductad'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.gender', 'female');">{l s='Women (female)' mod='facebookproductad'} </span>
                    - <span class="btn btn-info btn-lg" onclick="oFpa.doSet('.gender', 'unisex');">{l s='Unisex' mod='facebookproductad'} </span>
                    - <span class="btn btn-warning btn-lg" onclick="oFpa.doSet('.gender', 0);">{l s='Reset' mod='facebookproductad'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_gender_product">
                <p class="card-text text-center mt-3">{l s='Set GENDER tags : for each product default category, if available, you will have to indicate the feature that defines the gender for which each product in the category is intended. To assign the same feature to all categories, click "Set for all categories".' mod='facebookproductad'}</p>
                <p class="text-center mt-3">
                    <select name="bulk_action_gender_product mb-3" class="bulk_action_gender_product">
                        {foreach from=$aFeatures item=feature}
                            <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                        {/foreach}
                    </select>
                </p>
                <p class="text-center mt-3">
                    <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.gender_product', $('.bulk_action_gender_product').val());">{l s='Set for all categories' mod='facebookproductad'}</span>
                    - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.gender_product', 0);">{l s='Reset' mod='facebookproductad'}</span>
                </p>
            </div>

            <div class="card" id="bulk_action_tagadult">
                <div class="card-body">
                    <p class="card-text text-center">{l s='Set ADULT tags : for each product default category, if the products of the category are for adult only, select the \"true\" value in the drop and down menu. To assign the tag "true" to all categories, click "Set for all categories".' mod='facebookproductad'}</p>
                    <p class="text-center mt-3">
                        <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.adult', 'true');">{l s='Set for all categories' mod='facebookproductad'}</span>
                        - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.adult', 0);">{l s='Reset' mod='facebookproductad'}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="card" id="bulk_action_tagadult_product">
            <p class="card-text text-center">{l s='Set ADULT tags : for each product default category, if available, you will have to select the feature that indicates whether the products in this category are for adults only or not. To assign the same feature to all categories, click "Set for all categories".' mod='facebookproductad'}</p>
            <p class="text-center mt-3">
                <select name="bulk_action_tagadult_product mb-3" class="bulk_action_tagadult_product">
                    {foreach from=$aFeatures item=feature}
                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'html'}</option>
                    {/foreach}
                </select>
            </p>
            <p class="text-center mt-3">
                <span class="btn btn-lg btn-success" onclick="oFpa.doSet('.tagadult_product', $('.bulk_action_tagadult_product').val());">{l s='Set for all categories' mod='facebookproductad'}</span>
                - <span class="btn btn-lg btn-warning" onclick="oFpa.doSet('.tagadult_product', 0);">{l s='Reset' mod='facebookproductad'}</span>
            </p>
        </div>



        {if !empty($success)}
            <div class="col-xs-12 alert alert-success text-center mt-3" id="sucess_message">
                {l s='Settings updated' mod='facebookproductad'}
            </div>
        {/if}

        {if !empty($error)}
            <div class="col-xs-12 alert alert-danger text-center mt-3" id="error_message">
                {l s='An error occurred while assigning the tags' mod='facebookproductad'}
            </div>
        {/if}

        <input type="hidden" name="sUseTag" value="{$useTag|escape:'htmlall':'UTF-8'}" id="default_tag" />
        {if isset($token) && $token}
            <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
        {/if}

        <table class="table">
            <thead>
                <th class="bt_tr_header text-center"><b>{l s='Shop category' mod='facebookproductad'}</b></th>
                <th class="bt_tr_header text-center"><b>{l s='Tag' mod='facebookproductad'}</b></th>
            </thead>
            {foreach from=$aShopCategories item=cat}
                <tr>
                    <td class="label_tag_categories_value text-center font-weight-bold text-uppercase">{$cat.path|escape:'quotes':'UTF-8'}</td>
                    <td>
                        <div class="value_material">
                            <div class="col-xs-12">
                                <select name="material[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="material">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.material == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_pattern">
                            <div class="col-xs-12">
                                <select name="pattern[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="pattern">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.pattern == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_agegroup">
                            <div class="col-xs-12">
                                <select class="agegroup" name="agegroup[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="agegroup{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if $cat.agegroup=="0"} selected{/if}>--</option>
                                    <option value="adult" {if $cat.agegroup=="adult"} selected{/if}>{l s='Adults' mod='facebookproductad'}</option>
                                    <option value="all ages" {if $cat.agegroup=="all"} selected{/if}>{l s='All ages' mod='facebookproductad'}</option>
                                    <option value="teen" {if $cat.agegroup=="teen"} selected{/if}>{l s='Teens' mod='facebookproductad'}</option>
                                    <option value="kids" {if $cat.agegroup=="kids"} selected{/if}>{l s='Kids' mod='facebookproductad'}</option>
                                    <option value="toddler" {if $cat.agegroup=="toddler"} selected{/if}>{l s='Toddlers' mod='facebookproductad'}</option>
                                    <option value="infant" {if $cat.agegroup=="infant"} selected{/if}>{l s='Infants' mod='facebookproductad'}</option>
                                    <option value="newborn" {if $cat.agegroup=="newborn"} selected{/if}>{l s='Newborns' mod='facebookproductad'}</option>
                                </select>
                            </div>

                            <select name="agegroup_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="agegroup_product">
                                <option value="0">-----</option>
                                {foreach from=$aFeatures item=feature}
                                    <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.agegroup_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="value_gender">
                            <div class="col-xs-12">
                                <select class="gender" name="gender[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="gender{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if $cat.gender=="0"} selected{/if}>--</option>
                                    <option value="male" {if $cat.gender=="male"} selected{/if}>{l s='Men (male)' mod='facebookproductad'}</option>
                                    <option value="female" {if $cat.gender=="female"} selected{/if}>{l s='Women (female)' mod='facebookproductad'}</option>
                                    <option value="unisex" {if $cat.gender=="unisex"} selected{/if}>{l s='Unisex' mod='facebookproductad'}</option>
                                </select>

                                <select name="gender_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="gender_product">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.gender_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="value_tagadult">
                            <div class="col-xs-12">
                                <select class="adult" name="adult[{$cat.id_category|escape:'htmlall':'UTF-8'}]" id="adult{$cat.id_category|escape:'htmlall':'UTF-8'}">
                                    <option value="0" {if $cat.adult=="0"} selected{/if}>--</option>
                                    <option value="true" {if $cat.adult=="true"} selected{/if}>true</option>
                                </select>

                                <select name="adult_product[{$cat.id_category|escape:'htmlall':'UTF-8'}]" class="tagadult_product">
                                    <option value="0">-----</option>
                                    {foreach from=$aFeatures item=feature}
                                        <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $cat.adult_product == $feature.id_feature} selected {/if}>{$feature.name|escape:'html'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>

        <div class="navbar navbar-default navbar-fixed-bottom shadow px-3 py-3 border border-dark">
            <p class="pull-right">
                <button class="btn btn-primary btn-lg text-center" type="submit" name="save_btn">{l s='Save' mod='facebookproductad'}</button>
                <a class="btn btn-default btn-lg" href="{$moduleUrl|escape:'htmlall':'UTF-8'}">{l s='Go back to module configuration' mod='facebookproductad'}</a>
            </p>
        </div>
    </form>
</div>

<script type="text/javascript">
    // instantiate object
    var oFpa = oFpa || new Fpa('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oFpaFeatureByCat = oFpaFeatureByCat || new FpaFeatureByCat('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oFpaFeedList = oFpaFeedList || new FpaFeedList('{$sModuleName|escape:'htmlall':'UTF-8'}');
    var oFpaLabel = oFpaLabel || new FpaCustomLabel('{$sModuleName|escape:'htmlall':'UTF-8'}');
</script>