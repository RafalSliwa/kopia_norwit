{**
 * Clear Cart Module - Product counter and clear cart button
 *}
<div class="clearcart-module-output" data-product-count="{$clearcart_product_count|default:0}">
    <div class="clearcart-product-counter">
        <span class="clearcart-label">{$clearcart_summary_string}</span>
    </div>
    <a href="#" class="clear-cart-btn js-clear-cart" title="{l s='Remove all products' mod='clearcart'}">
        {l s='Clear cart' mod='clearcart'}
        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor">
            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
        </svg>
    </a>
</div>

{* Confirmation modal *}
<div class="clearcart-modal" id="clearcart-confirm-modal">
    <div class="clearcart-modal-backdrop"></div>
    <div class="clearcart-modal-content">
        <p class="clearcart-modal-message">{l s='Are you sure you want to remove all products from cart?' mod='clearcart'}</p>
        <div class="clearcart-modal-buttons">
            <button type="button" class="clearcart-modal-cancel">{l s='Cancel' mod='clearcart'}</button>
            <button type="button" class="clearcart-modal-confirm">{l s='Yes, clear cart' mod='clearcart'}</button>
        </div>
    </div>
</div>
