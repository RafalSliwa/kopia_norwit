{**
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2025 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}
<div class="panel " id="extraTabsProDiv">
    <input type="hidden" name="submitted_tabs[]" value="extratabspro"/>
    {if isset($smarty.get.addtab)}
        <div class="card">
        <div class="card-header">{l s='Product Tabs' mod='extratabspro'} - {l s='Add new' mod='extratabspro'}</div>
        <div class="card-body">
        <a class="btn-success btn-sm button bt-icon btn btn-default " style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?extratabspro=1&updateproduct&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}">
            <i class="material-icons">arrow_back</i><span>{l s='back to list' mod='extratabspro'}</span>
        </a><br/><br/>
        <div class="separation"></div>
        <div name="addnew" id="EXTRAglobalsettings" action="{$bolink}?extratabspro=1&tabaddition&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}" method="POST">
            <input type="hidden" name="id_product" value="{Tools::getValue('id_product')}"/>
            <input type="hidden" name="action" value="addnew"/>
            <input type="hidden" name="employee_idlang" value="{$employee_idlang}"/>

            {* TAB NAME *}
            {* TAB NAME *}
            {* TAB NAME *}
            <div class="alert alert-info">

                <p class="alert-text">
                    {l s='Tab name' mod='extratabspro'}<br/>
                    {l s='Name of the tab appears also in shop front office as a clickable area to activate tab, or as a heading above the contents' mod='extratabspro'}
                </p>
            </div>

            <div class="form_block">
                {if $thismodule->psversion()==7 || $thismodule->psversion(0)==8}
                    {foreach Language::getLanguages(false) as $lang}
                        <div id="bhbptitle_{$lang['id_lang']}" class="bhbptitle_{$lang['id_lang']}" style="display: {if $lang['id_lang']==$employee_idlang}{else}none{/if}; margin-bottom:15px;">
                            <input type="text" class="form-control" id="title[{$lang['id_lang']}]" name="title[{$lang['id_lang']}]" value=""/>
                        </div>
                    {/foreach}
                    <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbptitle', "bhbptitle", true)|replace:'../':'../../../../'}</div>
                {/if}
            </div>

            <hr/>

            {* TAB INTERNAL NAME *}
            {* TAB INTERNAL NAME *}
            {* TAB INTERNAL NAME *}
            <div class="alert alert-info">

                <p class="alert-text">
                    {l s='Internal name' mod='extratabspro'}<br/>
                    {l s='Internal name of tab is for back office only. With unique names you can easily distinct the tabs on the list of available tabs. It does not appear on front office.' mod='extratabspro'}
                </p>
            </div>

            <div class="form_block">
                {if $thismodule->psversion()==7 || $thismodule->psversion(0)==8}
                    {foreach Language::getLanguages(false) as $lang}
                        <div id="bhbptitlein_{$lang['id_lang']}" class="bhbptitlein_{$lang['id_lang']}" style="display: {if $lang['id_lang']==$employee_idlang}{else}none{/if}; margin-bottom:15px;">
                            <input type="text" class="form-control" id="titlein[{$lang['id_lang']}]" name="titlein[{$lang['id_lang']}]" value=""/>
                        </div>
                    {/foreach}
                    <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbptitlein', "bhbptitlein", true)|replace:'../':'../../../../'}</div>
                {/if}
            </div>

            <hr/>
            {* TAB CONTENTS *}
            {* TAB CONTENTS *}
            {* TAB CONTENTS *}
            <div class="alert alert-info">

                <p class="alert-text">{l s='Tab contents' mod='extratabspro'}<br/>
                    {l s='Contents appears inside tabs. Contents you enter here are visible on your shop front office as a tabs body.' mod='extratabspro'}
                </p>
            </div>

            <div class="form_block">
                {foreach Language::getLanguages(false) as $lang}
                    <div id="bhbpbody_{$lang['id_lang']}" class="bhbpbody_{$lang['id_lang']}" style="float:left;  display: {if $lang['id_lang']==$employee_idlang}block{else}none{/if}; width:100%;">
                        <div style="display:block; clear:both; overflow:hidden; padding:10px; padding-bottom:5px; padding-left:0px;">
                            <a class="button bt-icon btn btn-default" style="cursor:pointer;" onclick="addClass('mbbody{$lang['id_lang']}');"><i class="material-icons">format_paint</i><span>Editor</span></a>
                            <a class="button bt-icon btn btn-default" style="cursor:pointer;" onclick="removeClass('mbbody{$lang['id_lang']}');"><i class="material-icons">code</i><span>Code</span></a>
                        </div>
                        <textarea class="{if $thismodule->psversion()==7}rte rtepro{/if}" id="mbbody{$lang['id_lang']}" name="mbbody[{$lang['id_lang']}]"></textarea>
                    </div>
                {/foreach}
                <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbpbody', "bhbpbody", true)|replace:'../':'../../../../'}</div>
            </div>

            <hr/>
            {* TAB CMS PAGE *}
            {* TAB CMS PAGE *}
            {* TAB CMS PAGE *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <select id="extratabspro_cms" name="extratabspro_cms" style="max-width:100px; margin-bottom:10px;">
                            <option value="0">
                                {l s='No' mod='extratabspro'}
                            </option>
                            <option value="1">
                                {l s='Yes' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Apply CMS page' mod='extratabspro'}<br/>{l s='This option applies CMS page contents to this tab. It will appear below contents you can define in rich text editor available above.' mod='extratabspro'}</p>
                </div>
                {l s='Select CMS page:' mod='extratabspro'}
                <select class="form-control" id="extratabspro_cms_body" name="extratabspro_cms_body" style="max-width:200px;">
                    <option value="0">{l s='- select -' mod='extratabspro'}</option>
                    {foreach CMS::getCMSPages($employee_idlang,null,false,$id_shop) AS $cms}
                        <option value="{$cms.id_cms}">
                            {$cms.meta_title}
                        </option>
                    {/foreach}
                </select>
            </div>


            <h2 class="tab" style="margin-top:40px;"><i class="icon-folder"></i> {l s='Tab visibility rules' mod='extratabspro'}</h2>
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_allshops" name="extratabspro_allshops" style="max-width:500px; margin-bottom:10px;">
                            <option value="0" >
                                {l s='Show in all shops' mod='extratabspro'}
                            </option>
                            <option value="{Context::getContext()->shop->id}" >
                                {l s='Show in shop that I currently manage' mod='extratabspro'}
                            </option>
                        </select>
                        <br/>
                        {l s='In multistore context you can create tab that will be visible in shop that you currently edit or in all shops' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_allconditions" name="extratabspro_allconditions" style="max-width:500px; margin-bottom:10px;">
                            <option value="0" >
                                {l s='To display tab viewed product pages must meet at least one active condition' mod='extratabspro'}
                            </option>
                            <option value="1" >
                                {l s='To display tab viewed product pages must meet all active conditions' mod='extratabspro'}
                            </option>
                        </select>
                        <br/>
                        {l s='Set the way of how module will check conditions for this tab' mod='extratabspro'}
                    </p>
                </div>
            </div>

            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <select id="extratabspro_everywhere" name="extratabspro_everywhere" style="max-width:100px; margin-bottom:10px;">
                            <option value="0">
                                {l s='No' mod='extratabspro'}
                            </option>
                            <option value="1">
                                {l s='Yes' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Display it everwhere' mod='extratabspro'}<br/>{l s='With this option (if enabled) you can display this tab on each product page' mod='extratabspro'}
                    </p>
                </div>
            </div>
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            <hr/>

            {* TAB SUPPLIERS SELECTION *}
            {* TAB SUPPLIERS SELECTION *}
            {* TAB SUPPLIERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type4" name="block_type4" value="1"/>
                        {l s='Global tab based on suppliers' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with selected suppliers' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products that are associated with selected suppliers (you can select suppliers below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for supplier' mod='extratabspro'}
                        <input type="text" name="search_supplier" class="ex_supplier form-control"/>
                        <div class="ex_search_supplier"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of suppliers' mod='extratabspro'}<br/>
                        <textarea name="suppliers_block" class="ex_suppliers_ids form-control"></textarea>
                    </div>
                </div>
            </div>

            <hr/>
            {* TAB CATEGORY SELECTION *}
            {* TAB CATEGORY SELECTION *}
            {* TAB CATEGORY SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type" name="block_type" value="2"/>
                        {l s='Global tab based on categories' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with selected categories' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products that are associated with selected categories (you can select categories below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for category' mod='extratabspro'}
                        <input type="text" name="search_categories" class="ex_search form-control"/>
                        <div class="ex_search_result"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of categories' mod='extratabspro'}<br/>
                        <textarea name="categories_block" class="ex_pr_ids form-control"></textarea>
                    </div>
                </div>
            </div>


            <hr/>
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type2" name="block_type2" value="1"/>
                        {l s='Global tab based on manufacturers' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with many manufacturers' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products pages that are associated with selected manufacturers (you can select manufacturers below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for manufacturer' mod='extratabspro'}
                        <input type="text" name="search_manufacturers" class="ex_search_manuf form-control"/>
                        <div class="ex_search_manufacturers"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of manufacturers' mod='extratabspro'}
                        <textarea name="manufacturers_block" class="ex_manufacturers_ids form-control"></textarea>
                    </div>
                </div>
            </div>

            <hr/>
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type3" name="block_type3" value="1"/>
                        {l s='Display this tab also on selected product pages' mod='extratabspro'}<br/>
                        {l s='If you will select this option - this tab will appear also on selected product pages' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this tab on many product pages. Define the products where it will appear below.' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for product' mod='extratabspro'}
                        <input type="text" name="search_product" class="ex_search_product form-control"/>
                        <div class="ex_search_products"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of products' mod='extratabspro'}
                        <textarea name="products_block" class="ex_products_ids form-control"></textarea>
                    </div>
                </div>
            </div>


            {* TAB STOCK *}
            {* TAB STOCK *}
            {* TAB STOCK *}
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_stock" name="extratabspro_stock" style="max-width:100px; margin-bottom:10px;">
                            <option value="0">
                                {l s='Show for all products (stock + out of stock)' mod='extratabspro'}
                            </option>
                            <option value="1">
                                {l s='Show for in-stock products only' mod='extratabspro'}
                            </option>
                            <option value="2">
                                {l s='Show for out-of-stock products only' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Visibility by stock' mod='extratabspro'}<br/>{l s='This option gives you possibility to decide about appearance of tab depending on product stock' mod='extratabspro'}
                    </p>
                </div>
            </div>
            {* TAB STOCK *}
            {* TAB STOCK *}
            {* TAB STOCK *}



            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select name="etab_feature">
                            <option value="0"
                            '>{l s='No' mod='extratabspro'}</option>
                            <option value="1"
                            '>{l s='Yes' mod='extratabspro'}</option>
                        </select>
                        {l s='Global tab based on association with features' mod='extratabspro'}<br/>
                        {l s='Option when enabled will display tab on product page only if viewed product is associated with at least one selected feature.' mod='extratabspro'}
                    </p>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{l s='Feature search' mod='extratabspro'}</span>
                            </div>
                            <input type="text" name="etab_feature_s" class="etab_feature_s form-control">
                        </div>
                    </div>
                    <div class="col-lg-12 etab_feature_s_result"></div>
                    <div class="col-lg-12 etab_feature_selected"></div>
                </div>
            </div>

            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="extratabspro_geoip" name="extratabspro_geoip" value="1"/>
                        {l s='Geolocation' mod='extratabspro'}<br/>
                        {l s='Enable this option if you want to display tab only for guests from selected countries' mod='extratabspro'}
                    </p>
                </div>
                <div class="alert alert-info">
                    <p class="alert-text">
                        {l s='Select countries for which you want to enable the tab' mod='extratabspro'}<br/>
                        {l s='In order to use Geolocation, please download ' mod='extratabspro'} <a href="http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz">{l s='this file' mod='extratabspro'}</a> {l s='and extract it (using Winrar or Gzip) into the /app/Resources/geoip/ directory.' mod='extratabspro'}<br/>
                        {l s='(You dont have to enable it, only extract the file)' mod='extratabspro'}
                    </p>
                </div>
                <div style="margin-top:20px;">
                    {$thismodule->countriesSelection()}
                </div>
            </div>
            <hr/>
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="extratabspro_for_groups" name="extratabspro_for_groups" value="1"/>
                        {l s='Groups of customers' mod='extratabspro'}<br/>
                        {l s='This option when enabled will display tab and its contents for customers associated with at least one group selected below' mod='extratabspro'}
                    </p>
                </div>
                <div class="alert alert-info">

                    <p class="alert-text">
                        {l s='Check groups of customers. These groups will have privileges to see the tab' mod='extratabspro'}<br/>
                    </p>
                </div>
                <div style="margin-top:20px;">
                    {$thismodule->groupsSelection()}
                </div>
            </div>
            <hr/>
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <strong>{l s='Display date & time' mod='extratabspro'}</strong><br/>
                        {l s='If you want you can display this tab only during specific date / time. Just set the conditions below' mod='extratabspro'}<br/>
                        {l s='Select what kind of date & time conditions you want to use and then just set the values with date/time picker tool' mod='extratabspro'}<br/>
                    </p>
                </div>

                <div class="form_block">
                    <div class="col-lg-6" style="float: left; display: inline-block;">
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">date_range</i></span>
                                </div>
                                <input id="extratabspro_datefrom" name="extratabspro_datefrom" type="text" class="form-control datepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <input type="checkbox" name="extratabspro_df" value="1"> {l s='Date from' mod='extratabspro'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">access_time</i></span>
                                </div>
                                <input id="extratabspro_timefrom" name="extratabspro_timefrom" type="text" class="form-control timepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text"><input type="checkbox" name="extratabspro_tf" value="1">
                                        {l s='Time from' mod='extratabspro'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="float: left; display: inline-block;">
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">date_range</i></span>
                                </div>
                                <input id="extratabspro_dateto" name="extratabspro_dateto" type="text" class="form-control datepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <input type="checkbox" name="extratabspro_dt" value="1"> {l s='Date to' mod='extratabspro'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">access_time</i></span>
                                </div>
                                <span class="input-group-addon"></span>
                                <input id="extratabspro_timeto" name="extratabspro_timeto" type="text" class="form-control timepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <input type="checkbox" name="extratabspro_tt" value="1"> {l s='Time to' mod='extratabspro'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div style="clear:both; display:block; text-align:center; margin-top:10px;">
                <a href="javascript:extratabsprosubmit();" class="btn-lg btn-primary button bt-icon btn btn-default extra button">
                    <i class="material-icons">add_circle</i> <span>{l s='Save' mod='extratabspro'}</span>
                </a>
            </div>
        </div>
        </div>
    </div>
    {elseif isset($smarty.get.editblock)}
        <div class="card">
        <div class="card-header">{l s='Product Tabs' mod='extratabspro'} - {l s='Edit block' mod='extratabspro'}</div>
        <div class="card-body">
        <a class="button bt-icon btn btn-default btn btn-success btn-sm" style="cursor:pointer;" style="cursor:pointer;" href="{$bolink}?extratabspro=1&updateproduct&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}">
            <i class="material-icons">arrow_back</i><span>{l s='back to list' mod='extratabspro'}</span>
        </a></br></br>
        <div class="separation"></div>
        <div id="EXTRAglobalsettings" action="?token={Tools::getValue('token')}&extratabspro=1&editblock={$extratabpro->id_tab}&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}" method="POST">
            <input type="hidden" name="id_tab" value="{$extratabpro->id_tab}"/>
            <input type="hidden" name="id_product" value="{Tools::getValue('id_product')}"/>
            <input type="hidden" name="action" value="updateblock"/>
            <input type="hidden" name="employee_idlang" value="{$employee_idlang}"/>

            {* TAB NAME *}
            {* TAB NAME *}
            {* TAB NAME *}
            <div class="alert alert-info">

                <p class="alert-text">
                    {l s='Tab name' mod='extratabspro'}<br/>
                    {l s='Name of the tab appears also in shop front office as a clickable area to activate tab, or as a heading above the contents' mod='extratabspro'}
                </p>
            </div>

            <div class="form_block">
                {if $thismodule->psversion()==7 || $thismodule->psversion(0)==8}
                    {foreach Language::getLanguages(false) as $lang}
                        <div id="bhbptitle_{$lang['id_lang']}" class="bhbptitle_{$lang['id_lang']}" style="display: {if $lang['id_lang']==$employee_idlang}{else}none{/if}; margin-bottom:15px;">
                            <input type="text" class="form-control" id="title[{$lang['id_lang']}]" name="title[{$lang['id_lang']}]" value="{if isset($extratabpro->name[$lang['id_lang']])}{$extratabpro->name[$lang['id_lang']]}{/if}"/>
                        </div>
                    {/foreach}
                    <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbptitle', "bhbptitle", true)|replace:'../':'../../../../'}</div>
                {/if}
            </div>

            <hr/>
            {* TAB INTERNAL NAME *}
            {* TAB INTERNAL NAME *}
            {* TAB INTERNAL NAME *}
            <div class="alert alert-info">

                <p class="alert-text">
                    {l s='Internal name' mod='extratabspro'}<br/>
                    {l s='Internal name of tab is for back office only. With unique names you can easily distinct the tabs on the list of available tabs. It does not appear on front office.' mod='extratabspro'}
                </p>
            </div>
            <div class="form_block">
                {if $thismodule->psversion()==7 || $thismodule->psversion(0)==8}
                    {foreach Language::getLanguages(false) as $lang}
                        <div id="bhbptitlein_{$lang['id_lang']}" class="bhbptitlein_{$lang['id_lang']}" style="display: {if $lang['id_lang']==$employee_idlang}{else}none{/if}; margin-bottom:15px;">
                            <input type="text" class="form-control" id="titlein[{$lang['id_lang']}]" name="titlein[{$lang['id_lang']}]" value="{if isset($extratabpro->internal_name[$lang['id_lang']])}{$extratabpro->internal_name[$lang['id_lang']]}{/if}"/>
                        </div>
                    {/foreach}
                    <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbptitlein', "bhbptitlein", true)|replace:'../':'../../../../'}</div>
                {/if}
            </div>


            <hr/>
            {* TAB CONTENTS *}
            {* TAB CONTENTS *}
            {* TAB CONTENTS *}
            <div class="alert alert-info">

                <p class="alert-text">{l s='Tab contents' mod='extratabspro'}<br/>
                    {l s='Contents appears inside tabs. Contents you enter here are visible on your shop front office as a tabs body.' mod='extratabspro'}
                </p>
            </div>

            <div class="form_block">
                {foreach Language::getLanguages(false) as $lang}
                    <div id="bhbpbody_{$lang['id_lang']}" class="bhbpbody_{$lang['id_lang']}" style="float:left;  display: {if $lang['id_lang']==$employee_idlang}block{else}none{/if}; width:100%;">
                        <div style="display:block; clear:both; overflow:hidden; padding:10px; padding-bottom:5px; padding-left:0px;">
                            <a class="button bt-icon btn btn-default" style="cursor:pointer;" onclick="addClass('mbbody{$lang['id_lang']}');"><i class="material-icons">format_paint</i><span>Editor</span></a>
                            <a class="button bt-icon btn btn-default" style="cursor:pointer;" onclick="removeClass('mbbody{$lang['id_lang']}');"><i class="material-icons">code</i><span>Code</span></a>
                        </div>
                        <textarea class="{if $thismodule->psversion()==7 || $thismodule->psversion(0)==8}rte rtepro{/if}" id="mbbody{$lang['id_lang']}" name="mbbody[{$lang['id_lang']}]">{if isset($extratabpro->body[$lang['id_lang']])}{$extratabpro->body[$lang['id_lang']]}{/if}</textarea>
                    </div>
                {/foreach}
                <div class="flags_block">{$thismodule->displayFlags(Language::getLanguages(false), $employee_idlang, 'bhbpbody', "bhbpbody", true)|replace:'../':'../../../../'}</div>
            </div>

            <hr/>
            {* TAB SAVE IN THIS CONTEXT *}
            {* TAB SAVE IN THIS CONTEXT *}
            {* TAB SAVE IN THIS CONTEXT *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text"><input name="save_only_for_this_product" type="checkbox" value="1" {if Extratabproextracontents::getBoolByProductAndTabId(Tools::getValue('id_product'),$extratabpro->id_tab)==true}checked{else}{/if}/> {l s='Save in this context only' mod='extratabspro'}<br/>
                        {l s='Select this option if you want to change contents of the tab only for this product. ' mod='extratabspro'}<br/><br/>
                        {if Extratabproextracontents::getBoolByProductAndTabId(Tools::getValue('id_product'),$extratabpro->id_tab)==true}
                            {l s='If you will disable this option - contents of this tab will be saved globally and changes will be visible on each product page that has association with this tab and there where this option is also unselected' mod='extratabspro'}
                        {else}
                            {l s='If this tab will be associated with other products, it will show there global contents (or other unique contents if it will be saved in those products context only).' mod='extratabspro'}
                        {/if}
                    </p>
                </div>
            </div>

            <hr/>
            {* TAB CMS PAGE *}
            {* TAB CMS PAGE *}
            {* TAB CMS PAGE *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <select id="extratabspro_cms" name="extratabspro_cms" style="max-width:100px; margin-bottom:10px;">
                            <option value="0" {if $extratabpro->cms!=1}selected="yes"{/if}>
                                {l s='No' mod='extratabspro'}
                            </option>
                            <option value="1" {if $extratabpro->cms==1}selected="yes"{/if}>
                                {l s='Yes' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Apply CMS page' mod='extratabspro'}<br/>{l s='This option applies CMS page contents to this tab. It will appear below contents you can define in rich text editor available above.' mod='extratabspro'}</p>
                </div>
                {l s='Select CMS page:' mod='extratabspro'}
                <select class="form-control" id="extratabspro_cms_body" name="extratabspro_cms_body" style="max-width:200px;">
                    <option value="0">{l s='- select -' mod='extratabspro'}</option>
                    {foreach CMS::getCMSPages($employee_idlang,null,false,$id_shop) AS $cms}
                        <option value="{$cms.id_cms}" {if $extratabpro->cms_body==$cms.id_cms}selected="yes"{/if}>
                            {$cms.meta_title}
                        </option>
                    {/foreach}
                </select>
            </div>
            <h2 class="tab" style="margin-top:40px;"><i class="icon-folder"></i> {l s='Tab visibility rules' mod='extratabspro'}</h2>
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_allshops" name="extratabspro_allshops" style="max-width:500px; margin-bottom:10px;">
                            <option value="0" {if $extratabpro->allshops == 0} selected="yes"{/if}>
                                {l s='Show in all shops' mod='extratabspro'}
                            </option>
                            <option value="1" {if $extratabpro->allshops==1} selected="yes"{/if}>
                                {l s='Show in shop that I currently manage' mod='extratabspro'}
                            </option>
                        </select>
                        <br/>
                        {l s='In multistore context you can create tab that will be visible in shop that you currently edit or in all shops' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_allconditions" name="extratabspro_allconditions" style="max-width:500px; margin-bottom:10px;">
                            <option value="0" {if $extratabpro->allConditions!=1}selected="yes"{/if}>
                                {l s='To display tab viewed product pages must meet at least one active condition' mod='extratabspro'}
                            </option>
                            <option value="1" {if $extratabpro->allConditions==1}selected="yes"{/if}>
                                {l s='To display tab viewed product pages must meet all active conditions' mod='extratabspro'}
                            </option>
                        </select>
                        <br/>
                        {l s='Set the way of how module will check conditions for this tab' mod='extratabspro'}
                    </p>
                </div>
            </div>
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_everywhere" name="extratabspro_everywhere" style="max-width:100px; margin-bottom:10px;">
                            <option value="0" {if $extratabpro->everywhere!=1}selected="yes"{/if}>
                                {l s='No' mod='extratabspro'}
                            </option>
                            <option value="1" {if $extratabpro->everywhere==1}selected="yes"{/if}>
                                {l s='Yes' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Display it everwhere' mod='extratabspro'}<br/>{l s='With this option (if enabled) you can display this tab on each product page' mod='extratabspro'}
                    </p>
                </div>
            </div>
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            {* TAB EVERYWHERE *}
            <hr/>

            {* TAB SUPPLIERS SELECTION *}
            {* TAB SUPPLIERS SELECTION *}
            {* TAB SUPPLIERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type4" name="block_type4" value="1" {if $extratabpro->block_type4 == 1}checked="yes"{/if}/>
                        {l s='Global tab based on suppliers' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with selected suppliers' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products that are associated with selected suppliers (you can select suppliers below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for supplier' mod='extratabspro'}
                        <input type="text" name="search_supplier" class="ex_supplier form-control"/>
                        <div class="ex_search_supplier"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of suppliers' mod='extratabspro'}<br/>
                        <textarea name="suppliers_block" class="ex_suppliers_ids form-control">{$extratabpro->suppliers}</textarea>
                    </div>
                </div>
            </div>
            <hr/>


            {* TAB CATEGORY SELECTION *}
            {* TAB CATEGORY SELECTION *}
            {* TAB CATEGORY SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type" name="block_type" value="2" {if $extratabpro->block_type==2}checked="yes"{/if}/>
                        {l s='Global tab based on categories' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with selected categories' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products that are associated with selected categories (you can select categories below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for category' mod='extratabspro'}
                        <input type="text" name="search_categories" class="ex_search form-control"/>
                        <div class="ex_search_result"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of categories' mod='extratabspro'}<br/>
                        <textarea name="categories_block" class="ex_pr_ids form-control">{$extratabpro->categories}</textarea>
                    </div>
                </div>
            </div>


            <hr/>
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type2" name="block_type2" value="1" {if $extratabpro->block_type2==1}checked="yes"{/if}/>
                        {l s='Global tab based on manufacturers' mod='extratabspro'}<br/>
                        {l s='Tab with this option will appear on many product pages associated with many manufacturers' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this block on other products pages that are associated with selected manufacturers (you can select manufacturers below)' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for manufacturer' mod='extratabspro'}
                        <input type="text" name="search_manufacturers" class="ex_search_manuf form-control"/>
                        <div class="ex_search_manufacturers"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of manufacturers' mod='extratabspro'}
                        <textarea name="manufacturers_block" class="ex_manufacturers_ids form-control">{$extratabpro->manufacturers}</textarea>
                    </div>
                </div>
            </div>

            <hr/>
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            {* TAB MANUFACTURERS SELECTION *}
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="block_type3" name="block_type3" value="1" {if $extratabpro->block_type3==1}checked="yes"{/if}/>
                        {l s='Display this tab also on selected product pages' mod='extratabspro'}<br/>
                        {l s='If you will select this option - this tab will appear also on selected product pages' mod='extratabspro'}<br/>
                        {l s='This option will allow to display this tab on many product pages. Define the products where it will appear below.' mod='extratabspro'}
                    </p>
                </div>
            </div>
            <div class="form_block">
                <div class="row">
                    <div class="col-lg-4">
                        {l s='Search for product' mod='extratabspro'}
                        <input type="text" name="search_product" class="ex_search_product form-control"/>
                        <div class="ex_search_products"></div>
                    </div>
                    <div class="col-lg-8">
                        {l s='ID numbers of products' mod='extratabspro'}
                        <textarea name="products_block" class="ex_products_ids form-control">{$extratabpro->products}</textarea>
                    </div>
                </div>
            </div>

            {* TAB STOCK *}
            {* TAB STOCK *}
            {* TAB STOCK *}
            <div class="form_block">
                <div class="alert alert-info">
                    <p class="alert-text">
                        <select id="extratabspro_stock" name="extratabspro_stock" style="max-width:100px; margin-bottom:10px;">
                            <option value="0" {if $extratabpro->stock == 0}selected="yes"{/if}>
                                {l s='Show for all products (stock + out of stock)' mod='extratabspro'}
                            </option>
                            <option value="1" {if $extratabpro->stock == 1}selected="yes"{/if}>
                                {l s='Show for in-stock products only' mod='extratabspro'}
                            </option>
                            <option value="2" {if $extratabpro->stock == 2}selected="yes"{/if}>
                                {l s='Show for out-of-stock products only' mod='extratabspro'}
                            </option>
                        </select>
                        {l s='Visibility by stock' mod='extratabspro'}<br/>{l s='This option gives you possibility to decide about appearance of tab depending on product stock' mod='extratabspro'}
                    </p>
                </div>
            </div>
            {* TAB STOCK *}
            {* TAB STOCK *}
            {* TAB STOCK *}


            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <select name="etab_feature">
                            <option value="0"
                            ' {if $extratabpro->feature!=1}selected="yes"{/if}>{l s='No' mod='extratabspro'}</option>
                            <option value="1"
                            ' {if $extratabpro->feature==1}selected="yes"{/if}>{l s='Yes' mod='extratabspro'}</option>
                        </select>
                        {l s='Global tab based on association with features' mod='extratabspro'}<br/>
                        {l s='Option when enabled will display tab on product page only if viewed product is associated with at least one selected feature.' mod='extratabspro'}
                    </p>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-lg-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{l s='Feature search' mod='extratabspro'}</span>
                            </div>
                            <input type="text" name="etab_feature_s" class="etab_feature_s form-control">
                        </div>
                    </div>
                    <div class="col-lg-12 etab_feature_s_result"></div>
                    <div class="col-lg-12 etab_feature_selected">
                        {$thismodule->getSelectedFeaturesDiv($extratabpro->feature_v)}
                    </div>
                </div>
            </div>
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="extratabspro_geoip" name="extratabspro_geoip" value="1" {if $extratabpro->geoip==1}checked="yes"{/if}/>
                        {l s='Geolocation' mod='extratabspro'}<br/>
                        {l s='Enable this option if you want to display tab only for guests from selected countries' mod='extratabspro'}
                    </p>
                </div>
                <div class="alert alert-info">
                    <p class="alert-text">
                        {l s='Select countries for which you want to enable the tab' mod='extratabspro'}<br/>
                        {l s='In order to use Geolocation, please download ' mod='extratabspro'} <a href="http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz">{l s='this file' mod='extratabspro'}</a> {l s='and extract it (using Winrar or Gzip) into the /app/Resources/geoip/ directory.' mod='extratabspro'}<br/>
                        {l s='(You dont have to enable it, only extract the file)' mod='extratabspro'}
                    </p>
                </div>
                <div style="margin-top:20px;">
                    {$thismodule->countriesSelection($extratabpro->selected_geoip)}
                </div>
            </div>
            <hr/>
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <input type="checkbox" id="extratabspro_for_groups" name="extratabspro_for_groups" value="1" {if $extratabpro->for_groups==1}checked="yes"{/if}/>
                        {l s='Groups of customers' mod='extratabspro'}<br/>
                        {l s='This option when enabled will display tab and its contents for customers associated with at least one group selected below' mod='extratabspro'}
                    </p>
                </div>
                <div class="alert alert-info">

                    <p class="alert-text">
                        {l s='Check groups of customers. These groups will have privileges to see the tab' mod='extratabspro'}<br/>
                    </p>
                </div>
                <div style="margin-top:20px;">
                    {$thismodule->groupsSelection($extratabpro->groups)}
                </div>
            </div>
            <hr/>
            <div class="form_block">
                <div class="alert alert-info">

                    <p class="alert-text">
                        <strong>{l s='Display date & time' mod='extratabspro'}</strong><br/>
                        {l s='If you want you can display this tab only during specific date / time. Just set the conditions below' mod='extratabspro'}<br/>
                        {l s='Select what kind of date & time conditions you want to use and then just set the values with date/time picker tool' mod='extratabspro'}<br/>
                    </p>
                </div>

                <div class="form_block">
                    <div class="col-lg-6">
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">date_range</i></span>
                                </div>
                                <input value="{$extratabpro->date_from}" id="extratabspro_datefrom" name="extratabspro_datefrom" type="text" class="form-control datepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                    <input {if $extratabpro->df==1}checked="yes"{/if} type="checkbox" name="extratabspro_df" value="1"> {l s='Date from' mod='extratabspro'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">access_time</i></span>
                                </div>
                                <input value="{$extratabpro->time_from}" id="extratabspro_timefrom" name="extratabspro_timefrom" type="text" class="form-control timepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text"><input {if $extratabpro->tf==1}checked="yes"{/if} type="checkbox" name="extratabspro_tf" value="1"> {l s='Time from' mod='extratabspro'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">date_range</i></span>
                                </div>
                                <input value="{$extratabpro->date_to}" id="extratabspro_dateto" name="extratabspro_dateto" type="text" class="form-control datepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text"><input {if $extratabpro->dt==1}checked="yes"{/if} type="checkbox" name="extratabspro_dt" value="1"> {l s='Date to' mod='extratabspro'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-10" style="margin-bottom:10px;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="material-icons">access_time</i></span>
                                </div>
                                <input value="{$extratabpro->time_to}" id="extratabspro_timeto" name="extratabspro_timeto" type="text" class="form-control timepicker">
                                <div class="input-group-append">
                                    <span class="input-group-text"><input {if $extratabpro->tt==1}checked="yes"{/if} type="checkbox" name="extratabspro_tt" value="1"> {l s='Time to' mod='extratabspro'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="clear:both; display:block; text-align:center; margin-top:10px;">
                <a href="javascript:extratabsprosubmit();" class="btn-primary btn-lg button bt-icon btn btn-default extra button">
                    <i class="material-icons">add_circle</i> <span>{l s='Update' mod='extratabspro'}</span>
                </a>
            </div>
        </div>
        </div>
        </div>
    {else}
        <div class="card">
            <div class="card-header"><i class="icon-folder"></i> {l s='Product Tabs' mod='extratabspro'} - {l s='List of tabs' mod='extratabspro'}</div>
            <div class="card-body">

            <div style="overflow:hidden;">
                    <div style="display:block;">
                        <div class="separation"></div>
                        <div class="card clearfix">
                            <div class="card-header">{l s='colors legend' mod='extratabspro'}</div>
                            <div class="slides row">
                                <ul class="slidesmall col-lg-6 col-md-6 col-sm-6 col-xs-6" style="display: inline-block; float: left;">
                                    <li class='global_block'>{l s='Tab appears on product pages associated with selected categories' mod='extratabspro'}</li>
                                    <li class='global_manufacturers_block'>{l s='Tab appears on product pages associated with selected manufacturers' mod='extratabspro'}</li>
                                </ul>
                                <ul class="slidesmall col-lg-6 col-md-6 col-sm-6 col-xs-6" style="display: inline-block; float: left;">
                                    <li class='global_products_block'>{l s='Tab appears on selected product pages' mod='extratabspro'}</li>
                                    <li class='global_suppliers_block'>{l s='Tab appears on product pages associated with selected suppliers' mod='extratabspro'}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="separation"></div>
                        <div class="card panel clearfix">
                            <div class="card-header">{l s='Tabs for this product' mod='extratabspro'}</div>
                            <div class="card-body">
                                {if isset($product_extratabs)}
                                    <ul class="slides" id="productextratab" style="padding:0px 10px;">
                                        <div class="dropmehere" style="z-index:3">
                                        </div>
                                        {foreach $product_extratabs AS $product_extratab}
                                            <li id="productextratab_{$product_extratab->id_tab}" class="{if $product_extratab->block_type==2}global_block{/if} {if $product_extratab->block_type2==1}global_manufacturers_block{/if} {if $product_extratab->block_type3==1}global_products_block{/if} {if $product_extratab->block_type4==1}global_suppliers_block{/if}">
                                                <span class="name">{$product_extratab->internal_name[{$employee_idlang}]} ({$product_extratab->name[{$employee_idlang}]}) | ID: #extratabpro{$product_extratab->id_tab}</span>
                                                {if $product_extratab->block_type3==1}
                                                    <span class="unhook" onclick="extratab_unhook({$product_extratab->id_tab},{Tools::getValue('id_product')})"></span>
                                                {else}
                                                    <span class="unhook"><a style="background:none; width:24px; height:24px; display:block;" class="unhook" href="?editblock={$product_extratab->id_tab}&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}"></a></span>
                                                {/if}
                                                <span class="remove" onclick="extratab_remove({$product_extratab->id_tab})"></span>
                                                <span class="edit"><a class="edit" href="?extratabspro=1&editblock={$product_extratab->id_tab}&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}"></a></span>
                                                <span class="{if $product_extratab->active==1}on{else}off{/if}" onclick="extratab_toggle({$product_extratab->id_tab})"></span>
                                            </li>
                                        {/foreach}
                                    </ul>
                                {else}
                                    <ul class="slides" id="productextratab">
                                        <div class="dropmehere" style="z-index:3">
                                        </div>

                                    </ul>
                                    <div class="alert alert-info">
                                        {l s='No tabs available for this product' mod='extratabspro'}
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <a class="btn-lg btn-primary button bt-icon btn btn-default" style="cursor:pointer;margin-bottom:15px;" style="cursor:pointer;" href="?extratabspro=1&addtab=1&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}">
                            <span>{l s='Create new tab' mod='extratabspro'}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <script type="text/javascript">
        var token = "{Tools::getValue('_token')}";
        var extratabspro_url = '{Context::getContext()->link->getBaseLink(Context::getContext()->shop->id,true)}modules/extratabspro/';
        var extratabspro_id_product = '{Tools::getValue('id_product')}';
        var extratabspro_delete_message = "{l s='This option removes tab from this product only. Are you sure that you want to remove it from this product?' mod='extratabspro'}";
        var extratabspro_delete_permanent_message = "{l s='This option removes tab permanently from extra tabs module database. Are you sure you want to remove it at all?' mod='extratabspro'}";
    </script>
    {if Configuration::get('mypresta_support')!=1}
    {literal}
        <script>/*<![CDATA[*/
            window.zEmbed || function (e, t) {
                var n, o, d, i, s, a = [], r = document.createElement("iframe");
                window.zEmbed = function () {
                    a.push(arguments)
                }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;
                try {
                    o = s
                } catch (c) {
                    n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s
                }
                o.open()._l = function () {
                    var o = this.createElement("script");
                    n && (this.domain = n), o.id = "js-iframe-async", o.src = e, this.t = +new Date, this.zendeskHost = t, this.zEQueue = a, this.body.appendChild(o)
                }, o.write('<body onload="document._l();">'), o.close()
            }("//assets.zendesk.com/embeddable_framework/main.js", "prestasupport.zendesk.com");
            /*]]>*/</script>
    {/literal}
        <br/>
    {/if}
</div>

{if Tools::getValue('editblock','false')=='false' && Tools::getValue('addtab','false')=='false'}
    <div class="panel " id="extraTabsProDivTemplates">
        <h2 class="tab"><i class="icon-folder"></i> {l s='All available tabs' mod='extratabspro'}</h2>
        <div class="alert alert-info">
            <p class="alert-text">{l s='Drag and drop selected tabs to the list above - you will add dropped tab to this product' mod='extratabspro'}</p>
        </div>

        <div class="tokenfield form-control">
            <div style="display:inline-block;" id="tabs_loader">
                <a id="load_all_tabs" class="btn-sm btn-primary bt-icon btn " style="cursor:pointer;">
                    {l s='Load list of tabs' mod='extratabspro'}
                </a>
            </div>
            <div style="display:inline-block;">
                <a class="btn-sm btn-primary bt-icon btn " style="cursor:pointer;" href="?extratabspro=1&addtab=1&without_product=1&_token={Tools::getValue('_token')}{if $thismodule->psversion(0)!=8}#hooks{/if}">
                    {l s='Add new' mod='extratabspro'}
                </a>
            </div>
        </div>
    </div>
{/if}