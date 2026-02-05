{**
 * @author    Ceneo.
 * @copyright 2024 Ceneo.
 *}
<div class="panel paymentList">
	<div class="panel-heading">
       {l s='Delivery' mod='ceneo_basketservice'}
	</div>
	<div class="table-responsive">
       {if isset($list) }
			 <table class="table" style="max-width:100%">
				 <thead class="thead-default">
				 <tr class="nodrag nodrop">
					 <th class="center">
						 <span class="title_box">{l s='ID' mod='ceneo_basketservice'}</span>
					 </th>
					 <th class="center">
						 <span class="title_box">{l s='Name' mod='ceneo_basketservice'}</span>
					 </th>
					 <th class="center">
						 <span class="title_box">{l s='Country' mod='ceneo_basketservice'}</span>
					 </th>
					 <th class="center">
						 <span class="title_box">{l s='Ceneo carrier id' mod='ceneo_basketservice'}</span>
					 </th>
				 </tr>
				 </thead>

				 <tbody>
             {foreach $list as $l}
					 <tr>
						 <td class="text-center">
			               <span class="center">
				               {$l['carrier_id']|escape:'htmlall':'UTF-8'}
			               </span>
						 </td>
						 <td class="text-center">
			               <span class="center">
				               {$l['name']|escape:'htmlall':'UTF-8'}
			               </span>
						 </td>
						 <td class="text-center">
			               <span class="center">
				               {$l['countries']|escape:'htmlall':'UTF-8'}
			               </span>
						 </td>
						 <td class="text-center">
							 <select name="codes" data-id="{$l['id']|escape:'htmlall':'UTF-8'}" data-carrier-id="{$l['carrier_id']|escape:'htmlall':'UTF-8'}"
							         class="fixed-width-xl center ceneoChangeDeliveryMapping">
								 <option value="0">---</option>
                          {foreach $select as $s}
									  <option value="{$s['Id']|escape:'htmlall':'UTF-8'}" {if $l['ceneo_carrier_id'] == $s['Id']}selected{/if}>
                                 {$s['Name']|escape:'htmlall':'UTF-8'}
									  </option>
                          {/foreach}
							 </select>
						 </td>
					 </tr>
             {/foreach}
				 </tbody>
			 </table>
       {/if}
	</div>
</div>



<script type="text/javascript">
	let ceneo_ajax = "{$ajax_controller|escape:'htmlall':'UTF-8'}"
	let ceneo_token = "{$ajax_token|escape:'htmlall':'UTF-8'}";

	let success_msg = "{l s='Configuration saved successfully' mod='ceneo_basketservice'}"
	let error_msg = "{l s='Error, configuration not saved' mod='ceneo_basketservice'}"

	$('.ceneoChangeDeliveryMapping').on('change', function (e) {
		let value = $(this).val();
		let carrierId = $(this).attr('data-carrier-id');
	   let id = $(this).attr('data-id');

		var speditionCode = '&ajax=true&action=ChangeDeliveryMapping&token=' + ceneo_token + '&id=' + id + '&carrier_id=' + carrierId + '&value=' + value;
		ajaxUpdateDeliveryOption(speditionCode)

	});

	function ajaxUpdateDeliveryOption($data) {
		$.ajax({
			type: 'POST',
			cache: false,
			dataType: 'json',
			url: ceneo_ajax,
			data: $data,
			success: function (data) {
				if(data.success) {
					showSuccessMessage(success_msg);
				} else {
					showErrorMessage(error_msg);
				}
			},
			error: function (data) {
				showErrorMessage(error_msg);
			}
		});
	}

</script>
