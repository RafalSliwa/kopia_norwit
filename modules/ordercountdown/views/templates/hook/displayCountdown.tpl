<div id="product_delivery">

  {block name='product_quantities'}
    <div class="delivery_wraper{if isset($product_quantity) && $product_quantity <= 0} supplier-shipping{/if}">
      <span class="material-icon">
        <img class="icon" src="{$img_url}delivery.svg" width="40" height="40" alt="Delivery icon" />
      </span>
      <div class="product-quantities">
        <h4 class="product_quantity">{l s='Availability' mod='ordercountdown'}</h4>

        {* Zawsze renderujemy ilość + etykietę, JS uaktualni wartości *}
        <span class="quantity-desktop">
          {if isset($product_quantity)}{$product_quantity|intval}{elseif isset($product.quantity)}{$product.quantity|intval}{else}0{/if}
        </span>

        {assign var=__in_stock_label value={l s='pcs. in stock' mod='ordercountdown'}}
        {if isset($available_later) && $available_later}
          {assign var=__out_of_stock_label value=$available_later}
        {else}
          {assign var=__out_of_stock_label value=$message}
        {/if}

        <label class="label"
               data-instock-label="{$__in_stock_label|escape:'html'}"
               data-supplier-label="{$__out_of_stock_label|escape:'html'}"
               data-supplier-fallback="{l s='Shipping from supplier warehouse' mod='ordercountdown' escape='html'}">
          {if isset($product_quantity) && $product_quantity > 0}
            {$__in_stock_label}
          {else}
            {$__out_of_stock_label}
          {/if}
        </label>
      </div>
    </div>
  {/block}

  {block name='delivery_countdown'}
    <div class="delivery_wraper">
      <span class="material-icon">
        <img class="icon" src="{$img_url}delivery_truck_speed.svg" width="40" height="40" alt="Delivery icon" />
      </span>
      <div class="delivery_block">
        <h4 class="order_time">{l s='Time to ship' mod='ordercountdown'}</h4>

        {* Zostawiamy blok zawsze, a widoczność kontroluje JS (oraz ten style na start) *}
        <div class="delivery_countdown_block"
             {if (isset($product_quantity) && $product_quantity <= 0) || (isset($show_timer) && !$show_timer)}style="display:none"{/if}>
          <span id="countdown-timer-desktop">
            {if isset($hours)}{$hours|intval}h {/if}
            {if isset($minutes)}{$minutes|intval}m {/if}
            {if isset($seconds)}{$seconds|intval}s{/if}
          </span>
        </div>
      </div>
    </div>
  {/block}

  {block name='delivery_message_header'}
    <div class="delivery_wraper prduct_delivery">
      <span class="material-icon">
        <img class="icon" src="{$img_url}time.svg" width="40" height="40" alt="Delivery icon" />
      </span>
      <div class="delivery_block">
        {* <h4 class="order_message">{l s='Delivery date' mod='ordercountdown'}</h4> *}
        <span class="order_by delivery_message">
          {if isset($message)}{$message}{else}{l s='No information available' mod='ordercountdown'}{/if}
        </span>
      </div>
    </div>
  {/block}

</div>

{* Zostawiamy tylko serverTime (JS modułu i CSS ładowane są w hookDisplayHeader) *}
<script>
  var serverTime = {if isset($serverTime)}'{$serverTime|escape:'html'}'{else}'0'{/if};
</script>
