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
<div id="pdgetdatabyvatnumberpro" class="form-horizontal card ">
	<div class="card-header">
    <i class="material-icons">mail_outline</i>
   {l s='If you want, you can download data by NIP number.' mod='pdgetdatabyvatnumberpro'}
  </div>
	<div class="card-body">
		<p class="info-block">
			{l s='To do this please enter the VAT number and click the button below.' mod='pdgetdatabyvatnumberpro'}
		</p>

		<div class="card-text form-horizontal">

			<div class="form-group row type-text">
				<label class="form-control-label">{l s='EU VAT number' mod='pdgetdatabyvatnumberpro'}</label>
				<div class="col-sm">
					<input id="pdgetdatabyvatnumberpro_nip" class="form-control validate" data-validate="isGenericName" name="nip" type="text">
				</div>
			</div>

			<p class="submit text-right">
				<button id="pdgetdatabyvatnumberpro_get" class="btn btn-primary" type="button" name="get">
					<span style="display:none;margin-right:10px;margin-bottom: 4px;" class="pd-spinner-border pd-spinner-border-sm" role="status" aria-hidden="true"></span>
					{l s='Collect data' mod='pdgetdatabyvatnumberpro'}
				</button>
			</p>
			<div id="pdgetdatabyvatnumberpro_err" class="alert alert-danger" style="display:none;"></div>
			<div id="pdgetdatabyvatnumberpro_info" class="alert alert-info" style="display:none;"></div>
		</div>
	</div>
</div>



<script type="text/javascript">

	var pdgetdatabyvatnumberpro_ajax_link='{$pdgetdatabyvatnumberpro_ajax_link|escape:'htmlall':'UTF-8'}';
	var pdgetdatabyvatnumberpro_secure_key='{$pdgetdatabyvatnumberpro_secure_key|escape:'htmlall':'UTF-8'}';
	var pdgetdatabyvatnumberpro_response_ok='{$pdgetdatabyvatnumberpro_response_ok}';

document.addEventListener("DOMContentLoaded", function(){

		$("#pdgetdatabyvatnumberpro_get").click( function(){

			var nip = $( "input#pdgetdatabyvatnumberpro_nip" ).val();
			var nip_country_iso = $( "select#nip_country_iso" ).val();

			$("#pdgetdatabyvatnumberpro_err").hide();
			$('body').css('cursor', 'wait');
			$(this).prop('disabled', true).find('.pd-spinner-border').css('display', 'inline-block');

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
								$('input[name="customer_address[first_name]"]').val(data.firstname).parent().removeClass('form-error').addClass('form-ok');
							} else {
								if ($('input[name="customer_address[first_name]"]').val() == ''){
									$('input[name="customer_address[first_name]"]').parent().removeClass('form-ok').addClass('form-error');
								}
							}

							if (data.lastname) {
								$('customer_address[last_name]').val(data.lastname).parent().removeClass('form-error').addClass('form-ok');
							} else {
								if ($('input[name="customer_address[last_name]"]').val() == ''){
									$('input[name="customer_address[last_name]"]').parent().removeClass('form-ok').addClass('form-error');
								}
							}

							if (data.company) {
								$('input[name="customer_address[company]"]').val(data.company).parent().removeClass('form-error').addClass('form-ok');
							} else {
								$('input[name="customer_address[company]"]').parent().removeClass('form-ok').addClass('form-error');
							}

							if (data.address1) {
								$('input[name="customer_address[address1]"]').val(data.address1).parent().removeClass('form-error').addClass('form-ok');
							} else {
								$('input[name="customer_address[address1]"]').parent().removeClass('form-ok').addClass('form-error');
							}

							if (data.postcode) {
								$('input[name="customer_address[postcode]"]').val(data.postcode).parent().removeClass('form-error').addClass('form-ok');
							} else {
								$('input[name="customer_address[postcode]"]').parent().removeClass('form-ok').addClass('form-error');
							}

							if (data.vat_number) {
								$('input[name="customer_address[dni]"]').val(data.vat_number).parent().removeClass('form-error').addClass('form-ok');
								$('input[name="customer_address[vat_number]"]').val(data.vat_number).parent().removeClass('form-error').addClass('form-ok');
								$('div#vat_number').show();
								$('div#vat_number_block').show();
							} else {
								$('input[name="customer_address[dni]"]').parent().removeClass('form-ok').addClass('form-error');
								$('input[name="customer_address[vat_number]"]').parent().removeClass('form-ok').addClass('form-error');
							}

							if (data.city) {
								$('input[name="customer_address[city]"]').val(data.city).parent().removeClass('form-error').addClass('form-ok');
							} else {
								$('input[name="customer_address[city]"]').parent().removeClass('form-ok').addClass('form-error');
							}

							if (data.id_country) {
								$('select[name="customer_address[id_country]"]').val(data.id_country).change();
							}
						}

						$("#pdgetdatabyvatnumberpro_get").prop('disabled', false).find('.pd-spinner-border').css('display', 'none');
					}

				},
				timeout: 20000 // sets timeout to 20 sec

	});
</script>