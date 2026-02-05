<div class="customer-type-block" style="margin-bottom: 20px;">
  <div class="customer-title">{l s='You are buying as:' mod='removeaddressdni'}</div>
  <div class="btn-group" role="group" aria-label="{l s='Choose customer type' mod='removeaddressdni'}">
    <button type="button" class="btn customer-type-btn{if $customer_type != 'company'} active{/if}" data-type="private">{l s='Private person' mod='removeaddressdni'}</button>
    <button type="button" class="btn customer-type-btn{if $customer_type == 'company'} active{/if}" data-type="company">{l s='Company' mod='removeaddressdni'}</button>
  </div>
</div>
<input type="hidden" name="customer_type" id="customer_type" value="{$customer_type|default:'private'}">
<script>
  window.vatValidationMsg = '{$vatValidationMsg|escape:'js'}';
</script>

