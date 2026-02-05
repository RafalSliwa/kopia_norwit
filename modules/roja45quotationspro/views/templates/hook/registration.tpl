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
    var id_default_language = {$id_default_language|escape:'html':'UTF-8'};
    var roja45_quotationspro_unknown_error = "{l s='An unexpected error has occurred, please raise this with your support provider.' mod='roja45quotationspro' js=1}";
</script>

<div id="fields_warning_dialog" title="{l s='Warning' mod='roja45quotationspro'}" style="display:none">
    <p>{l s='There are fields in a column that will be removed.  Please delete or move these first.' mod='roja45quotationspro'}
    </p>
</div>

<style>
    .bootstrap input[type=number] {
        -webkit-transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        background-color: #f5f8f9;
        background-image: none;
        border: 1px solid #c7d6db;
        border-radius: 3px;
        color: #555;
        display: block;
        font-size: 12px;
        height: 31px;
        line-height: 1.42857;
        padding: 6px 8px;
        transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        width: 100%;
    }

    #roja45quotationspro_form li.list-group-item,
    [data-is="ps-tabs"] li.list-group-item {
        padding: 0 !important;
    }

    #roja45quotationspro_form .nav.list-group li a,
    [data-is="ps-tabs"] .nav.list-group li a {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    #roja45quotationspro_form li.list-group-item.active a,
    [data-is="ps-tabs"] li.list-group-item.active a {
        color: white;
    }

    #roja45quotationspro_form li.list-group-item a,
    [data-is="ps-tabs"] li.list-group-item a {
        color: #555;
    }
</style>

<div class="bootstrap">
    <div class="panel">
        <div class="panel-heading"></div>
        <div class="panel-body">
            <form id="roja45quotationspro_form" class="defaultForm form-horizontal unregistered"
                action="{$url|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                <input type="hidden" name="submitRegistration" value="1">
                <input type="hidden" name="GDPRCompliance" value>
                <div class="row">
                    <div class="col-md-2">
                        <ul class="nav list-group">
                            <li class="list-group-item active">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_general_tab">{l s='General Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationcart_tab">{l s='Quotation Cart Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_shoppingcart_tab">{l s='Shopping Cart Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationorder_tab">{l s='Quotation Order Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationform_tab">{l s='Quotation Form Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_pdf_tab">{l s='Email/PDF Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_security_tab">{l s='Security Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_advanced_tab">{l s='Advanced Settings' mod='roja45quotationspro'}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content col-md-10">
                        <div id="roja45quotationspro_registration_tab" class="tab-pane active">
                            <div class="panel" id="fieldset_0">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Registration' mod='roja45quotationspro'}
                                </div>

                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            {if $module_source=='prestashop'}
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Enter the Prestashop addons email address used to purchase the module.' mod='roja45quotationspro'}">{l s='Prestashop Addons Email Address' mod='roja45quotationspro'}</span>
                                            {else}
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Enter the roja45.com email address used to purchase the module.' mod='roja45quotationspro'}">{l s='Roja45 Account Email Address' mod='roja45quotationspro'}</span>
                                            {/if}
                                        </label>
                                        <div class="col-lg-9">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS"
                                                id="ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS" class=""
                                                data-rule-required="true" data-rule-email="true"
                                                data-msg-required="{l s='Please enter the email address used to purchase the module' mod='roja45quotationspro'}"
                                                data-msg-email="{l s='Enter a valid email address' mod='roja45quotationspro'}" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            {if $module_source=='prestashop'}
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Enter your Prestashop order number' mod='roja45quotationspro'}">{l s='Prestashop Order Number' mod='roja45quotationspro'}</span>
                                            {else}
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Enter your Roja45 order reference' mod='roja45quotationspro'}">{l s='Roja45 Order Reference' mod='roja45quotationspro'}</span>
                                            {/if}
                                        </label>
                                        <div class="col-lg-9">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_ACCOUNTORDER"
                                                id="ROJA45_QUOTATIONSPRO_ACCOUNTORDER" class=""
                                                data-rule-required="true"
                                                {if $module_source=='prestashop'}data-rule-digits="true"
                                                {else}data-rule-lettersonly="true" 
                                                {/if}
                                                data-rule-minlength="{if $module_source=='prestashop'}7{else}9{/if}"
                                                data-rule-maxlength="{if $module_source=='prestashop'}7{else}9{/if}"
                                                {if $module_source=='prestashop'}data-msg-required="{l s='Please enter your Prestashop order number.' mod='roja45quotationspro'}"
                                                {else}data-msg-required="{l s='Please enter your Roja45 order reference.' mod='roja45quotationspro'}"
                                                {/if}
                                                data-msg-minlength="{if $module_source=='prestashop'}{l s='Enter a minimum of 7 characters' mod='roja45quotationspro'}{else}{l s='Enter a minimum of 9 characters' mod='roja45quotationspro'}{/if}"
                                                data-msg-maxlength="{if $module_source=='prestashop'}{l s='Enter a maximum of 7 characters' mod='roja45quotationspro'}{else}{l s='Enter a maximum of 9 characters' mod='roja45quotationspro'}{/if}"
                                                data-msg-lettersonly="{l s='The order reference has the following format: ABCDEFGHI' mod='roja45quotationspro'}"
                                                data-msg-digits="{l s='The order reference has the following format: 1234567' mod='roja45quotationspro'}" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Enter your main site domain.  If installing into a test domain you can place you final domain name here.' mod='roja45quotationspro'}">{l s='Registered Domain' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-9">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN"
                                                id="ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN" class=""
                                                data-rule-required="true" data-rule-validurl="true"
                                                data-msg-required="{l s='Please enter the domain where you will be using the module' mod='roja45quotationspro'}"
                                                data-msg-validurl="{l s='Please enter a valid domain in the format: domain.com or www.domain.com' mod='roja45quotationspro'}" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select this if you are installing into a test domain.' mod='roja45quotationspro'}">{l s='Test Domain' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_TESTDOMAIN"
                                                    id="ROJA45_QUOTATIONSPRO_TESTDOMAIN_on" value="0">
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_TESTDOMAIN_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_TESTDOMAIN"
                                                    id="ROJA45_QUOTATIONSPRO_TESTDOMAIN_off" value="1"
                                                    checked="checked">
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_TESTDOMAIN_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Have Authorization Key?' mod='roja45quotationspro'}">{l s='Have Authorization Key?' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-9">
                                            <a class="btn btn-default" role="button" data-toggle="collapse"
                                                href="#roja45_quotationspro_authkey" aria-expanded="false"
                                                aria-controls="collapseExample">
                                                <span
                                                    class="collapsed-text">{l s='Click Here' mod='roja45quotationspro'}</span>
                                            </a>
                                            <div class="row-margin-top alert alert-warning">
                                                {l s='The Authorization key will be sent when you first register.  Keep this key safe so you can use it to register the module again if you need to reinstall, or move domains.' mod='roja45quotationspro'}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="roja45_quotationspro_authkey" class="col-lg-12 collapse">
                                        <div class="form-group">
                                            <label class="control-label col-lg-3">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title=""
                                                    data-original-title="{l s='Authorization Key' mod='roja45quotationspro'}">{l s='Authorization Key' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-9">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_ACCOUNTKEY"
                                                    id="ROJA45_QUOTATIONSPRO_ACCOUNTKEY" class=""
                                                    data-rule-required="false" />

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-primary btn-register-module"><i
                                            class="process-icon-save"></i>{l s='Register' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
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

<script type="text/javascript">
    $.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^\w+$/i.test(value);
    }, "Letters, numbers, and underscores only please");

    $.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z]+$/i.test(value);
    }, "Letters only please");
    $.validator.addMethod('validurl', function(value, element) {
        var url = $.validator.methods.url.bind(this);
        return url(value, element) || url('http://' + value, element) || url('https://' + value, element);
    }, 'Please enter a valid URL');

    $(document).ready(function() {
        $('input[name=ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA]').on('change', function() {
            if ($("input[name='ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA']:checked").val() == '1') {
                $('.recaptcha_hidden').fadeIn();
            } else {
                $('.recaptcha_hidden').fadeOut();
            }
        }).trigger('change');

        $('input[name=ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE]').on('change', function() {
            if ($("input[name='ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE']:checked").val() == '1') {
                $('.new_addresses_hidden').fadeIn();
            } else {
                $('.new_addresses_hidden').fadeOut();
            }
        }).trigger('change');

        $('input[name=ROJA45_QUOTATIONSPRO_DISPLAY_LABEL]').on('change', function() {
            if ($("input[name='ROJA45_QUOTATIONSPRO_DISPLAY_LABEL']:checked").val() == '1') {
                $('.label_position_hidden').fadeIn();
            } else {
                $('.label_position_hidden').fadeOut();
            }
        }).trigger('change');

        $('input[name=ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE]').on('change', function() {
            if ($("input[name='ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE']:checked").val() == '1') {
                $('.customreference_hidden').fadeIn();
            } else {
                $('.customreference_hidden').fadeOut();
            }
        }).trigger('change');

        $('#roja45quotationspro_form.unregistered').validate({
            submitHandler: function(form) {
                /*
                roja45quotationspro.registerModule(
                    $('#ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS').val(),
                    $('#ROJA45_QUOTATIONSPRO_ACCOUNTORDER').val(),
                    $('#ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN').val(),
                    $('#ROJA45_QUOTATIONSPRO_ACCOUNTKEY').val()
                );

                 */
                $('#roja45quotationspro_form').submit();
            },
            // override jquery validate plugin defaults for bootstrap 3
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });
    });
</script>