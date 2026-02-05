<span class="product-variant-current" data-product-refresh="current-variant">
  {assign var=first value=true}
  {foreach from=$product_attributes item=attr}
    {if !$first}, {/if}
    <span class="control-label">
      {$attr.group}{l s=': ' d='Shop.Theme.Catalog'}
    </span>
    <span class="control-value">{$attr.name}</span>
    {assign var=first value=false}
  {/foreach}
</span>
