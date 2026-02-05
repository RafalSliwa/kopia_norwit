/**
 * Clear Cart Module - JavaScript
 */
(function() {
    'use strict';

    // Polish plural forms for product count
    function getProductWord(count) {
        var absCount = Math.abs(count);
        var lastDigit = absCount % 10;
        var lastTwoDigits = absCount % 100;

        if (absCount === 1) {
            return 'produkt';
        } else if (lastDigit >= 2 && lastDigit <= 4 && (lastTwoDigits < 10 || lastTwoDigits >= 20)) {
            return 'produkty';
        } else {
            return 'produktów';
        }
    }

    // Update product counter display
    function updateProductCounter() {
        var container = document.querySelector('.clearcart-module-output');
        if (!container) return;

        // Get cart data from PrestaShop
        if (typeof prestashop !== 'undefined' && prestashop.cart) {
            var cart = prestashop.cart;
            var productCount = cart.products_count || 0;

            var label = container.querySelector('.clearcart-label');
            if (label && productCount > 0) {
                label.textContent = productCount + ' ' + getProductWord(productCount);
                container.setAttribute('data-product-count', productCount);
            }

            // Hide entire module if cart is empty
            if (productCount === 0) {
                container.style.display = 'none';
            } else {
                container.style.display = '';
            }
        }
    }

    // Listen for PrestaShop cart updates
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateCart', function(event) {
            setTimeout(updateProductCounter, 100);
        });

        prestashop.on('updatedCart', function(event) {
            setTimeout(updateProductCounter, 100);
        });
    }

    // Modal functions
    function showModal() {
        var modal = document.getElementById('clearcart-confirm-modal');
        if (modal) {
            modal.classList.add('active');
        }
    }

    function hideModal() {
        var modal = document.getElementById('clearcart-confirm-modal');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // Clear cart AJAX request
    function clearCart() {
        var clearBtn = document.querySelector('.js-clear-cart');
        if (clearBtn) {
            clearBtn.style.pointerEvents = 'none';
            clearBtn.style.opacity = '0.5';
        }

        fetch(clearcart_ajax_url + '?action=clear&token=' + clearcart_token, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Error clearing cart');
                if (clearBtn) {
                    clearBtn.style.pointerEvents = '';
                    clearBtn.style.opacity = '';
                }
            }
        })
        .catch(function(error) {
            console.error('Clear cart error:', error);
            window.location.reload();
        });
    }

    // Clear cart button handler
    document.addEventListener('DOMContentLoaded', function() {
        // Show modal on clear cart click
        document.addEventListener('click', function(e) {
            var clearBtn = e.target.closest('.js-clear-cart');
            if (clearBtn) {
                e.preventDefault();
                showModal();
            }
        });

        // Modal cancel button
        var cancelBtn = document.querySelector('.clearcart-modal-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', hideModal);
        }

        // Modal confirm button
        var confirmBtn = document.querySelector('.clearcart-modal-confirm');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                hideModal();
                clearCart();
            });
        }

        // Close modal on backdrop click
        var backdrop = document.querySelector('.clearcart-modal-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', hideModal);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal();
            }
        });
    });

})();
