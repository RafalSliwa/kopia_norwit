/**
 * Free Shipping Progress Module
 * JavaScript for updating progress bar on cart changes
 */

(function() {
    'use strict';

    // AJAX URL for fetching fresh shipping data
    var ajaxUrl = prestashop.urls.base_url + 'module/freeshippingprogress/ajax';

    // Get delivery section - try multiple selectors
    function getDeliverySection() {
        var selectors = [
            '.cart-grid-right .cart-delivery-section',
            '.cart-summary .cart-delivery-section',
            '.cart-delivery-section'
        ];
        for (var i = 0; i < selectors.length; i++) {
            var section = document.querySelector(selectors[i]);
            if (section) return section;
        }
        return null;
    }

    // Fetch fresh shipping data from server
    function fetchShippingData(callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', ajaxUrl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            callback(data);
                        }
                    } catch (e) {
                        console.log('freeshippingprogress: JSON parse error', e);
                    }
                }
            }
        };
        xhr.send();
    }

    // Update UI with shipping data
    function updateUI(data) {
        var section = getDeliverySection();
        if (!section) return;

        // Update data attributes
        section.setAttribute('data-cart-total', data.cart_total);
        section.setAttribute('data-base-shipping-cost', data.base_shipping_cost);
        section.setAttribute('data-shipping-cost', data.shipping_cost);
        section.setAttribute('data-has-additional-shipping', data.has_additional_shipping_cost ? '1' : '0');

        // Update progress bar width
        var progressFill = section.querySelector('.delivery-progress-fill');
        if (progressFill) {
            progressFill.style.width = data.progress_percentage + '%';
        }

        // Update amount needed text
        var amountNeeded = section.querySelector('.amount-needed');
        if (amountNeeded) {
            amountNeeded.textContent = formatPrice(data.amount_needed);
        }

        // Show/hide elements based on free shipping status
        var progressWrapper = section.querySelector('.free-delivery-progress-wrapper');
        var achievedMessage = section.querySelector('.free-shipping-achieved');
        var deliveryValue = section.querySelector('.delivery-value');

        if (progressWrapper) {
            progressWrapper.style.display = data.is_free_shipping ? 'none' : 'block';
        }

        if (achievedMessage) {
            achievedMessage.style.display = data.is_free_shipping ? 'flex' : 'none';
        }

        if (deliveryValue) {
            if (data.is_free_shipping) {
                var freeText = section.getAttribute('data-free-text') || 'Za darmo!';
                deliveryValue.innerHTML = '<span class="free-shipping-badge">' + freeText + '</span>';
            } else if (data.base_shipping_cost > 0) {
                deliveryValue.innerHTML = formatPrice(data.base_shipping_cost) + ' zł';
            }
        }
    }

    // Update progress bar when cart changes (fetch fresh data from server)
    function updateDeliveryProgress() {
        var section = getDeliverySection();
        if (!section) {
            return;
        }

        // Fetch fresh data from server
        fetchShippingData(function(data) {
            updateUI(data);
        });
    }

    // Format price in Polish format (without currency - it's in HTML)
    function formatPrice(value) {
        var formatted = value.toFixed(2).replace('.', ',');
        var parts = formatted.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.join(',');
    }

    // Watch for DOM changes in cart totals area
    function setupMutationObserver() {
        var target = document.querySelector('.js-cart-detailed-totals');
        if (!target) {
            target = document.querySelector('.cart-detailed-totals');
        }
        if (!target) return;

        var observer = new MutationObserver(function() {
            setTimeout(updateDeliveryProgress, 100);
        });

        observer.observe(target, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }

    // Listen for PrestaShop cart updates
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateCart', function() {
            // Fetch fresh data after cart update
            setTimeout(updateDeliveryProgress, 300);
        });

        prestashop.on('updatedCart', function() {
            // Fetch fresh data after cart update
            setTimeout(updateDeliveryProgress, 100);
            setTimeout(updateDeliveryProgress, 500);
        });
    }

    // Also listen for AJAX complete as fallback (if jQuery available)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url && settings.url.indexOf('cart') !== -1) {
                setTimeout(updateDeliveryProgress, 300);
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupMutationObserver();
    });

    if (document.readyState !== 'loading') {
        setupMutationObserver();
    }

})();
