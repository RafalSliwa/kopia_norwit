{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<script type="text/javascript">
    var id_lang = {$id_lang|escape:"html":"UTF-8"};
    var id_lang_default = {$id_lang_default|escape:"html":"UTF-8"};
    var iso = iso_user;
    var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'quotes':'UTF-8'}";
    var ad = "{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}{basename($smarty.const._PS_ADMIN_DIR_)|escape:'quotes':'UTF-8'}";
    var languages = [];
    {foreach $languages as $index => $language}
    languages[{$index|escape:'html':'UTF-8'}] = {
        id_lang: {$language.id_lang|escape:'html':'UTF-8'},
        iso_code: '{$language.iso_code|escape:'html':'UTF-8'}',
        name: '{$language.name|escape:'html':'UTF-8'}'
    };
    {/foreach}

    var current_id_condition_group = 0;
    var last_condition_group = 0;
    var conditions = new Array();

    var roja45_delete_text = "{l s='Delete' mod='roja45quotationspro'}";
    var roja45_category_text = "{l s='Category' mod='roja45quotationspro'}";
</script>

<div id="container-quotation-form">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                    <form id="QUOTATION_FORM_ELEMENTS" class="defaultForm form-horizontal" action="{$controller|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-cogs"></i>{l s='Quotation Form Design' mod='roja45quotationspro'}
                            </div>
                            <div class="form-wrapper">
                                <div class="form-group">
                                    <label class="control-label col-lg-5">
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select the number of columns you would like on the quotation form.' mod='roja45quotationspro'}">{l s='Number of Columns' mod='roja45quotationspro'}</span>
                                    </label>
                                    <div class="col-lg-7">
                                        <select name="ROJA45_QUOTATIONSPRO_NUM_COL" class="fixed-width-xl" id="ROJA45_QUOTATIONSPRO_NUM_COL">
                                            <option value="1" {if ($columns == '1')}selected="selected"{/if}>1</option>
                                            <option value="2" {if ($columns == '2')}selected="selected"{/if}>2</option>
                                            <option value="3" {if ($columns == '3')}selected="selected"{/if}>3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-5">
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select a form component to add.' mod='roja45quotationspro'}">{l s='Select form element' mod='roja45quotationspro'}</span>
                                    </label>

                                    <div class="col-lg-7">
                                        <select name="ROJA45_QUOTATIONSPRO_FORM_ELEMENT" class="fixed-width-xl" id="ROJA45_QUOTATIONSPRO_FORM_ELEMENT">
                                            <option value="DEFAULT" selected="selected">{l s='Select form element' mod='roja45quotationspro'}</option>
                                            <option value="TEXT">{l s='Text Field' mod='roja45quotationspro'}</option>
                                            <option value="TEXTAREA">{l s='Text Area Field' mod='roja45quotationspro'}</option>
                                            <option value="CHECKBOX">{l s='Checkbox' mod='roja45quotationspro'}</option>
                                            <option value="SWITCH">{l s='Switch' mod='roja45quotationspro'}</option>
                                            <option value="SELECT">{l s='Select Field' mod='roja45quotationspro'}</option>
                                            <option value="DATE">{l s='Date Field' mod='roja45quotationspro'}</option>
                                            <option value="DATES">{l s='Start/End Date Fields' mod='roja45quotationspro'}</option>
                                            <option value="DATEPERIOD">{l s='Date Period Field' mod='roja45quotationspro'}</option>
                                            <option value="ADDRESS_SELECTOR">{l s='Address Selector Field' mod='roja45quotationspro'}</option>
                                            <option value="ADDRESS">{l s='Address Fields' mod='roja45quotationspro'}</option>
                                            <option value="HEADER">{l s='Section Header' mod='roja45quotationspro'}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {include
                    file='./form_components/text.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/textarea.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/checkbox.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/switch.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/select.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/date.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/dates.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/date_period.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/address_selector.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/address.tpl'
                    languages=$languages
                    url=$controller
                    }
                    {include
                    file='./form_components/header.tpl'
                    languages=$languages
                    url=$controller
                    }
                </div>
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Assignments' mod='roja45quotationspro'}
                        </div>
                        <div class="form-group">
                        <a class="btn btn-secondary" href="#" id="add_condition_group">
                            <i class="icon-plus-sign"></i> {l s='Add a new condition group' mod='roja45quotationspro'}
                        </a>
                        </div>
                        <div id="conditions">
                            <div id="condition_group_list"></div>
                        </div>

                        <div class="panel form-horizontal" id="conditions-panel" style="display:none;">
                            <h3><i class="icon-tasks"></i> {l s='Conditions' mod='roja45quotationspro'}</h3>
                            <div class="form-group">
                                <label for="id_category" class="control-label col-lg-3">{l s='Category' mod='roja45quotationspro'}</label>
                                <div class="col-lg-9">
                                    <div class="col-lg-8">
                                        <select id="id_category" name="id_category">
                                            {foreach $categories as $category}
                                                <option value="{$category.id_category|intval|escape:'htmlall':'UTF-8'}">({$category.id_category|intval|escape:'htmlall':'UTF-8'}) {$category.name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="col-lg-1">
                                        <a class="btn btn-secondary" href="#" id="add_condition_category">
                                            <i class="icon-plus-sign"></i> {l s='Add condition' mod='roja45quotationspro'}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script type="text/javascript">
                            $(document).ready(function() {
                                {foreach $conditions as $condition_group}
                                roja45quotationspro.new_condition_group();
                                {foreach $condition_group as $condition}
                                $('#id_{$condition.type|escape:'htmlall':'UTF-8'} option[value="{$condition.value|escape:'htmlall':'UTF-8'}"]').attr('selected', true);
                                $('#add_condition_{$condition.type|escape:'htmlall':'UTF-8'}').click();
                                {/foreach}
                                {/foreach}
                            });
                        </script>
                    </div>
                </div>
            </div>

            <form id="QUOTATION_FORM_ELEMENTS" class="defaultForm form-horizontal" action="{$controller|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                <input type="hidden" name="submitAddroja45_quotationspro_form" value="1">
                <input type="hidden" name="id_quotation_form" id="id_quotation_form" value="{$id_quotation_form}">
                <input type="hidden" name="ROJA45_QUOTATIONSPRO_FORM_ID" value="{$id_quotation_form}">
                <input type="hidden" name="ROJA45_QUOTATIONSPRO_FORM">
                <input type="hidden" name="ROJA45_QUOTATIONSPRO_NUM_COL" value="{$columns|escape:'html':'UTF-8'}"/>
                <input type="hidden" name="ROJA45_QUOTATIONSPRO_FORM_ID" value="{$form.id|escape:'html':'UTF-8'}"/>
                <div id="condition_data">
                </div>
                <div class="panel clearfix">
                    <div class="panel-heading">
                        <i class="icon-cogs"></i>{l s='Quotation Form' mod='roja45quotationspro'}
                    </div>
                    <div class="panel-body">
                        <div class="form-wrapper">
                            <div class="form-group">
                                <label class="control-label col-lg-2">
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                      data-original-title="{l s='Set a name for this form' mod='roja45quotationspro'}">{l
                                    s='Name'
                                    mod='roja45quotationspro'}</span>
                                </label>
                                <div class="col-lg-5">
                                    <input name="ROJA45_QUOTATIONSPRO_FORM_NAME" type="text" value="{$form.form_name}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select a shop id for the form.  If none available for current shop, then the form for the default shop will be used.' mod='roja45quotationspro'}">{l s='Shop' mod='roja45quotationspro'}</span>
                                </label>
                                <div class="col-lg-5">
                                    <select name="ROJA45_QUOTATIONSPRO_FORM_SHOP_ID" class="fixed-width-xl" id="ROJA45_QUOTATIONSPRO_FORM_SHOP_ID" {if $id_quotation_form==1}disabled="disabled"{/if}>
                                        {foreach $shops as $shop}
                                            <option value="{$shop.id_shop}" {if ($id_quotation_shop_id == $shop.id_shop)}selected="selected"{/if}>({$shop.id_shop}) {$shop.name}</option>
                                        {/foreach}
                                    </select>
                                    {if $id_quotation_form==1}<div class="row-margin-top alert alert-warning">{l s='Default form shop cannot be changed' mod='roja45quotationspro'}</div>{/if}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='The default form will be used if no product specific form is found.  NB. You should have 1 default form per store.' mod='roja45quotationspro'}" data-original-title="{l s='The default form will be used if no product specific form is found.  NB. You should have 1 default form per store.' mod='roja45quotationspro'}">{l s='Default Form' mod='roja45quotationspro'}</span>
                                </label>
                                <div class="col-lg-7">
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        <input type="radio" name="ROJA45_QUOTATIONSPRO_FORM_DEFAULT"
                                               id="ROJA45_QUOTATIONSPRO_FORM_DEFAULT_on" value="1"
                                               {if ($form.default_form== "1")}checked="checked"{/if}>
                                        <label for="ROJA45_QUOTATIONSPRO_FORM_DEFAULT_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                        <input type="radio" name="ROJA45_QUOTATIONSPRO_FORM_DEFAULT" id="ROJA45_QUOTATIONSPRO_FORM_DEFAULT_off" value="0" {if ($form.default_form == "0")}checked="checked"{/if}>
                                        <label for="ROJA45_QUOTATIONSPRO_FORM_DEFAULT_off">{l s='No' mod='roja45quotationspro'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2">

                                </label>
                                <div id="form_design_columns" class="col-lg-9">
                                    {for $col_counter=1 to $columns}
                                        {assign var=field_title value="form_element_column_title_"|cat:{$col_counter|escape:'html':'UTF-8'}}
                                        <section id="sortable{$col_counter|escape:'html':'UTF-8'}" data-column="{$col_counter|escape:'html':'UTF-8'}" class="form-column col-lg-{$col_width|escape:'html':'UTF-8'}">
                                            <section id="form_design_column_{$col_counter|escape:'html':'UTF-8'}" class="filter_panel">
                                                <header class="clearfix">
                                                    <div class="panel-heading-icon">
                                                        <i class="icon-list-ul"></i>
                                                    </div>
                                                    <div class="panel-heading-name">
                                                        <input type="text" class="form-column-title" name="{$field_title|escape:'html':'UTF-8'}" {if isset($form.titles.$field_title)} value="{$form.titles.$field_title|escape:'html':'UTF-8'}"{else}value="Column {$col_counter|escape:'html':'UTF-8'}" {/if}data-validate="isText" onfocus="if(this.value == 'Column {$col_counter|escape:'html':'UTF-8'}') { this.value = ''; }" onblur="if(this.value == '') { this.value = 'Column {$col_counter|escape:'html':'UTF-8'}'; }">
                                                    </div>
                                                </header>
                                                <section class="filter_list">
                                                    <ul class="list-unstyled droppable sortable connectedSortable">
                                                        {if isset($form.fields.$col_counter)}
                                                            {foreach from=$form.fields.$col_counter item=field}
                                                                <li id="{$field.id|escape:'html':'UTF-8'}"
                                                                    class="filter_list_item"
                                                                    draggable="true"
                                                                    data-id="{$field.id|escape:'html':'UTF-8'}"
                                                                    data-name="{$field.name|escape:'html':'UTF-8'}"
                                                                    data-type="{$field.type|escape:'html':'UTF-8'}"
                                                                    data-column="{$col_counter|escape:'html':'UTF-8'}">
                                                                    <input type="hidden" name="id" value="{$field.id|escape:'html':'UTF-8'}"/>
                                                                    <input type="hidden" name="name" value="{$field.name|escape:'html':'UTF-8'}"/>
                                                                    <input type="hidden" name="configuration" value="{$field.configuration|escape:'html':'UTF-8'}"/>
                                                                    <input type="hidden" name="type" value="{$field.type|escape:'html':'UTF-8'}"/>
                                                                    <div class="col-lg-1 drag-icon"><h4><i class="icon-bars"></i></h4></div>
                                                                    <div class="col-lg-5">
                                                                        <h4><span class="panel-heading-name">{$field.name|escape:'html':'UTF-8'}</span></h4>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <h4><span class="panel-heading-type">[{$field.type|escape:'html':'UTF-8'}]</span></h4>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <span class="panel-heading-action pull-right">
                                                                            <a class="list-toolbar-btn edit-configuration" href="#" title="Configure"><i class="process-icon-configure"></i></a>
                                                                            {if $field.deletable==1}
                                                                                <a class="list-toolbar-btn delete-configuration" href="#" title="Delete"><i class="process-icon-delete"></i></a>
                                                                            {/if}
                                                                        </span>
                                                                    </div>
                                                                </li>
                                                            {/foreach}
                                                        {/if}
                                                    </ul>
                                                </section>
                                            </section>
                                        </section>
                                    {/for}
                                </div>
                            </div>
                        </div><!-- /.form-wrapper -->
                        <div class="panel-footer">
                            <button type="submit"
                                    value="1"
                                    id="roja45_quotationspro_answer_form_submit_btn"
                                    name="submitAddroja45_quotationspro_form"
                                    class="roja45quotations_submitFormDesign btn btn-secondary btn btn-secondary pull-right">
                                <i class="process-icon-save"></i>{l s='Save' mod='roja45quotationspro'}
                            </button>
                            <button type="submit"
                                    value="1"
                                    id="roja45_quotationspro_answer_form_submit_btn"
                                    name="submitAddroja45_quotationspro_formAndStay"
                                    class="roja45quotations_submitFormDesign btn btn-secondary pull-right">
                                <i class="process-icon-save"></i> {l s='Save & Stay' mod='roja45quotationspro'}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="roja45_quotation_modal">
    <div id="roja45_quotation_modal_dialog" class="roja45-quotation-modal-dialog">
        <div id="modal_wait_icon">
            <i class="icon-refresh icon-spin animated"></i>
            <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
        </div>
    </div>
</div>