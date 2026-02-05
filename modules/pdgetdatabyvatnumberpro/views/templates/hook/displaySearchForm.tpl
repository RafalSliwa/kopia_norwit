{**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - Pd Get data by vat number Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   Pd Get data by vat number Pro for - PrestaShop 1.5.x and 1.6.x and 1.7.x
* @version   1.0.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      7-06-2018
*}

<!-- Pd Regon Api Pro customHook -->
<div id="pdgetdatabyvatnumberpro" class="std">
	<p class="info-title">{l s='If you want, you can download data by NIP number.' mod='pdgetdatabyvatnumberpro'}
    &nbsp;{l s='To do this please enter the VAT number and click the button below.' mod='pdgetdatabyvatnumberpro'}
    </p>
	<div class="form-group">
		<label>{l s='Country' mod='pdgetdatabyvatnumberpro'}</label>
		<select id="nip_country_iso" name="vat_country_iso" class="form-control">
			{foreach from=$vat_iso_codes key=k item=country_iso}
				<option {if isset($sl_country_iso) && $sl_country_iso == $k} selected {/if} value="{$k|escape:'htmlall':'UTF-8'}">{$country_iso|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
	</div>

	<div class="form-group">
		<label>{l s='EU VAT number' mod='pdgetdatabyvatnumberpro'}</label>
		<input id="pdgetdatabyvatnumberpro_nip" class="form-control validate" data-validate="isGenericName" name="nip" type="text">
	</div>

	<p class="submit">
		<button id="pdgetdatabyvatnumberpro_get" class="btn btn-default button button-medium" type="button" name="get">{l s='Collect data' mod='pdgetdatabyvatnumberpro'}</button><span id="pdgetdatabyvatnumberpro_ajax-loader" class="unvisible">
			<img src="{$img_dir|escape:'htmlall':'UTF-8'}ajax-loader.gif" alt="loader" />
		</span>
	</p>
	<div id="pdgetdatabyvatnumberpro_err" class="error" style="display:none;"></div>
	<div id="pdgetdatabyvatnumberpro_info" class="info" style="display:none;"></div>
	<div class="clearfix"></div>
</div>



<script type="text/javascript">

	var pdgetdatabyvatnumberpro_ajax_link='{$pdgetdatabyvatnumberpro_ajax_link|escape:'htmlall':'UTF-8'}';
	var pdgetdatabyvatnumberpro_secure_key='{$pdgetdatabyvatnumberpro_secure_key|escape:'htmlall':'UTF-8'}';
	var pdgetdatabyvatnumberpro_response_ok='{$pdgetdatabyvatnumberpro_response_ok}';

document.addEventListener("DOMContentLoaded", function(){
        $('body').on('click', "button#pdgetdatabyvatnumberpro_get", function(){

            var nip = $( "input#pdgetdatabyvatnumberpro_nip" ).val();
            var nip_country_iso = $( "select#nip_country_iso" ).val();

            $("#pdgetdatabyvatnumberpro_err").hide();
            $('body').css('cursor', 'wait');
            $('#pdgetdatabyvatnumberpro_ajax-loader').fadeIn();

            console.log('clicked');

            $.ajax({
                type: "POST",
                headers: {ldelim}"cache-control": "no-cache"{rdelim},
                url: pdgetdatabyvatnumberpro_ajax_link,
                data: {ldelim}'nip': nip, 'nip_country_iso' : nip_country_iso, 'secure_key':  pdgetdatabyvatnumberpro_secure_key{rdelim},
                dataType: "json",
                cache: false,
                success: function(response) {
                    if (response) {
                        var data = response.data;
                        $('body').css('cursor', 'default');
                        if (data.hasOwnProperty('error')) {
                            $("#pdgetdatabyvatnumberpro_err").fadeIn(800);
                            $("#pdgetdatabyvatnumberpro_err").html(data.error);

                        } else {

                            $("#pdgetdatabyvatnumberpro_info").fadeIn(600);
                            $("#pdgetdatabyvatnumberpro_info").html(pdgetdatabyvatnumberpro_response_ok);
                            $("#pdgetdatabyvatnumberpro_info").fadeOut(600);

                            if (data.firstname) {
                                $('input#firstname').val(data.firstname).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                if ($('input#firstname').val() == ''){
                                    $('input#firstname').parent().removeClass('form-ok').addClass('form-error');
                                }
                            }

                            if (data.lastname) {
                                $('input#lastname').val(data.lastname).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                if ($('input#lastname').val() == ''){
                                    $('input#lastname').parent().removeClass('form-ok').addClass('form-error');
                                }
                            }

                            if (data.company) {
                                $('input#company').val(data.company).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                $('input#company').parent().removeClass('form-ok').addClass('form-error');
                            }

                            if (data.address1) {
                                $('input#address1').val(data.address1).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                $('input#address1').parent().removeClass('form-ok').addClass('form-error');
                            }

                            if (data.postcode) {
                                $('input#postcode').val(data.postcode).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                $('input#postcode').parent().removeClass('form-ok').addClass('form-error');
                            }

                            if (data.vat_number) {
                                $('input#vat_number').val(data.vat_number).parent().removeClass('form-error').addClass('form-ok');
                                $('input#vat-number').val(data.vat_number).parent().removeClass('form-error').addClass('form-ok');
                                $('div#vat_number').show();
                                $('div#vat_number_block').show();
                            } else {
                                $('input#vat_number').parent().removeClass('form-ok').addClass('form-error');
                                $('input#vat-number').parent().removeClass('form-ok').addClass('form-error');
                            }

                            if (data.city) {
                                $('input#city').val(data.city).parent().removeClass('form-error').addClass('form-ok');
                            } else {
                                $('input#city').parent().removeClass('form-ok').addClass('form-error');
                            }

                            if (data.id_country) {
                                $("select#id_country").val(data.id_country).change();
                            }
                        }

                        $('#pdgetdatabyvatnumberpro_ajax-loader').fadeOut();
                    }

                },
                timeout: 20000 // sets timeout to 20 seconds
            });
        });

    });
</script>