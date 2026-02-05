<div class="form-group">
  <label>{l s='Retired product. ' mod='retiredproducts'}</label>
  <div class="col-lg-9">
    <span class="switch prestashop-switch fixed-width-lg">
      <input type="radio" name="retired_product" id="retired_on" value="1" {if $retired}checked="checked"{/if}>
      <label for="retired_on">{l s='Yes' mod='retiredproducts'}</label>
      <input type="radio" name="retired_product" id="retired_off" value="0" {if !$retired}checked="checked"{/if}>
      <label for="retired_off">{l s='No' mod='retiredproducts'}</label>
      <a class="slide-button btn"></a>
    </span>
  </div>
</div>

<div class="form-group">
  <label>{l s='Redirect to product. Search and select a replacement product.' mod='retiredproducts'}</label>
  <div class="col-lg-9">
    <input type="hidden" name="id_product_redirect" value="{$id_product_redirect|default:''}">
    <input type="text" name="product_redirect_search" class="form-control" placeholder="{l s='Search by name or index' mod='retiredproducts'}" autocomplete="off">
    <ul id="autocomplete-results"></ul>
    {if $id_product_redirect}
      <p class="help-block">
        {l s='Current redirect product ID:' mod='retiredproducts'} <strong>{$id_product_redirect}</strong>
        {if isset($id_product_redirect_name) && $id_product_redirect_name}
          <br>{l s='Current redirect product Name:' mod='retiredproducts'} <strong>{$id_product_redirect_name|escape:'html':'UTF-8'}</strong>
        {/if}
        {if isset($id_product_redirect_reference) && $id_product_redirect_reference}
          <br>{l s='Current redirect product Index:' mod='retiredproducts'} <strong>{$id_product_redirect_reference|escape:'html':'UTF-8'}</strong>
        {/if}
      </p>
    {/if}
  </div>
</div>
