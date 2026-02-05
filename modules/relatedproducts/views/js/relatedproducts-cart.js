// Cart management module with automatic cart counter updates

function buildRelatedProductsUrl(path) {
    var baseUrl = (window.prestashop && window.prestashop.urls && window.prestashop.urls.base_url) || '/';
    var sanitizedBase = baseUrl.replace(/\/+$/, '/');
    var sanitizedPath = path.replace(/^\/+/, '');
    return sanitizedBase + sanitizedPath;
}

var relatedProductsEndpoints = {
    cart: buildRelatedProductsUrl('index.php?controller=cart&ajax=1'),
    freeShipping: buildRelatedProductsUrl('modules/relatedproducts/ajax.php')
};

// Free shipping threshold - MUST be fetched from backend (database)
// Default 0 - forces AJAX fetch
var defaultFreeShippingThreshold = 0;
var currentFreeShippingThreshold = defaultFreeShippingThreshold;

// Product data cache
const productDataCache = new Map();
let modalListenersInitialized = false;

function parseShippingPrice(value) {
    if (typeof value !== 'string' || !value.trim()) {
        return NaN;
    }
    const normalized = value.replace(/[^\d,.-]/g, '').replace(',', '.');
    const parsed = parseFloat(normalized);
    return Number.isFinite(parsed) ? parsed : NaN;
}

function selectDisplayShippingCost(priceText, numericCost) {
    const globalMax = typeof window.currentMaxShippingCost === 'number' ? window.currentMaxShippingCost : 0;
    const numericFromInput = Number.isFinite(numericCost) ? numericCost : parseShippingPrice(priceText);
    const preferredNumeric = Number.isFinite(numericFromInput) ? numericFromInput : 0;

    if (globalMax > preferredNumeric && globalMax > 0) {
        return formatShippingPrice(globalMax);
    }

    if (preferredNumeric > 0) {
        return formatShippingPrice(preferredNumeric);
    }

    if (typeof priceText === 'string' && priceText.trim() && priceText.trim() !== '...') {
        return priceText.trim();
    }

    return '';
}

function prepareModalDeliveryForProduct(productId) {
    if (!productId) {
        return;
    }

    const deliveryElements = document.querySelectorAll('#blockcart-modal .delivery-text.dynamic-shipping-price, .nr-modal .delivery-text.dynamic-shipping-price');
    deliveryElements.forEach(element => {
        const wrapper = element.closest('.delivery-status-wrapper');
        
        // Don't hide element at all - just update the text
        // This prevents any visual flickering or fading
        if (wrapper) {
            // Don't add loading-delivery - element stays visible
            // Just mark that update is needed
            wrapper.dataset.updatePending = 'true';
        }
        
        const paidLabel = element.dataset.paidLabel || 'Delivery from:';
        element.dataset.productId = productId;
        element.setAttribute('data-product-id', productId);
        // DON'T clear lastPrice - prevents flickering
        // element.dataset.lastPrice = '';
        // element.dataset.initialPrice = '';
        element.classList.remove('is-free');

        if (wrapper) {
            wrapper.classList.remove('is-free');
            delete wrapper.dataset.freeDeliveryStatus;
        }

        // DON'T reset text to placeholder - prevents visible flickering
        // Element will be updated by AJAX with new price
    });
    
    // DON'T hide progress bar here - let AJAX decide based on cart_has_additional_costs
    // Hiding it here causes flickering (hide → show by AJAX)
}

document.addEventListener('click', function(e) {
    var button = e.target.closest('.add-to-cart-ajax');
    if (button) {
        e.preventDefault();
        
        var idProduct = button.dataset.idProduct;
        
        button.classList.add('disabled');
        
        var formData = new FormData();
        formData.append('action', 'update');
        formData.append('add', '1');
        formData.append('id_product', idProduct);
        formData.append('qty', '1');
        formData.append('ajax', '1');
        
        fetch(relatedProductsEndpoints.cart, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            return response.text().then(function(text) {
                return { response: response, text: text };
            });
        })
        .then(function(result) {
            var response = result.response;
            var text = result.text;
            button.classList.remove('disabled');
            
            let responseData = null;
            try {
                responseData = text ? JSON.parse(text) : null;
            } catch (e) {
                responseData = null;
            }
            if (!response.ok) {
                throw new Error('Żądanie koszyka zakończyło się błędem');
            }

            if (responseData && (responseData.hasError || (Array.isArray(responseData.errors) && responseData.errors.length > 0))) {
                var errorMessage = Array.isArray(responseData.errors) && responseData.errors.length > 0
                    ? responseData.errors.join(', ')
                    : 'Wystąpił błąd podczas dodawania produktu do koszyka';
                showErrorToast(errorMessage);
                return;
            }

            showSuccessToast('Produkt został pomyślnie dodany do koszyka');

            // Refresh PrestaShop cart - update product counter
            refreshCartDisplay(responseData);

            const syncPrestashopCart = () => {
                if (responseData && window.prestashop && prestashop.cart && responseData.cart) {
                    Object.assign(prestashop.cart, responseData.cart);
                }
            };

            // Only prepare modal if needed - don't hide/show unnecessarily
            // prepareModalDeliveryForProduct will be called inside updateShippingPrice when actually needed
            
            const refreshShippingState = () => {
                updateShippingMessagesSimple({ force: true })
                    .finally(() => {
                        ensureFreeDeliveryPlaceholdersVisible();
                    });
            };

            try {
                syncPrestashopCart();
                refreshShippingState();
            } catch (error) {
            }
        })
        .catch(function(error) {
            button.classList.remove('disabled');
            showErrorToast('Wystąpił błąd podczas dodawania produktu do koszyka');
        });
    }
});

function updateFreeShippingInfo() {
    fetch(relatedProductsEndpoints.freeShipping, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=getFreeShippingInfo'
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        var deliveryText = document.querySelector('.free-delivery-info .delivery-text');
        if (deliveryText && !deliveryText.dataset.originalContent) {
            deliveryText.dataset.originalContent = deliveryText.innerHTML;
        }


        var amountEl = document.querySelector('.free-delivery-info .amount-needed');
        if (amountEl) {
            if (data.mode === 'price') {
                amountEl.textContent = data.formattedRemainingAmount || data.formattedRemaining || '--';
            } else if (data.mode === 'weight') {
                if (data.isOverWeightLimit) {
                    amountEl.textContent = 'Limit przekroczony';
                } else {
                    amountEl.textContent = data.formattedRemainingAmount || data.formattedRemaining || '--';
                }
            }
        }
        
    
    })
    .catch(function(error) {
    });
}

function showSuccessToast(message) {
    var targetContainer = document.body;
    var toast = document.createElement('div');
    toast.textContent = message;
    
    toast.style.cssText = `
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        background: #009703 !important;
        color: white !important;
        padding: 15px 20px !important;
        border-radius: 5px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3) !important;
        z-index: 999999 !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
        width: auto !important;
        height: auto !important;
        min-width: 200px !important;
        max-width: 400px !important;
        pointer-events: auto !important;
        transition: none !important;
        text-align: center !important;
    `;
    
    targetContainer.appendChild(toast);
    
    setTimeout(function() {
        toast.style.setProperty('opacity', '1', 'important');
        toast.style.setProperty('visibility', 'visible', 'important');
        toast.style.setProperty('display', 'block', 'important');
        toast.style.setProperty('background', '#009703', 'important');
    }, 0);
    
    setTimeout(function() {
        toast.style.opacity = '0';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function showErrorToast(message) {
    var modalContent = document.querySelector('.modal-content') || 
                      document.querySelector('.related-products-modal') || 
                      document.querySelector('.modal-body') ||
                      document.querySelector('#blockcart-modal') ||
                      document.body;
    
    var toast = document.createElement('div');
    toast.className = 'error-toast';
    toast.textContent = message;
    
    if (modalContent !== document.body) {
        toast.style.position = 'absolute';
        toast.style.top = '50%';
        toast.style.left = '50%';
        toast.style.transform = 'translate(-50%, -50%) scale(0.9)';
        
        if (getComputedStyle(modalContent).position === 'static') {
            modalContent.style.position = 'relative';
        }
    } else {
        toast.style.position = 'fixed';
        toast.style.top = '50%';
        toast.style.left = '50%';
        toast.style.transform = 'translate(-50%, -50%) scale(0.9)';
    }
    
    toast.style.background = '#dc3545';
    toast.style.color = 'white';
    toast.style.padding = '10px 15px';
    toast.style.borderRadius = '5px';
    toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    toast.style.zIndex = '10000';
    toast.style.fontWeight = '500';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.3s ease';
    toast.style.fontSize = '14px';
    
    modalContent.appendChild(toast);
    
    setTimeout(function() {
        toast.style.opacity = '1';
        if (modalContent !== document.body) {
            toast.style.transform = 'translate(-50%, -50%) scale(1)';
        } else {
            toast.style.transform = 'translate(-50%, -50%) scale(1)';
        }
    }, 10);
    
    setTimeout(function() {
        toast.style.opacity = '0';
        if (modalContent !== document.body) {
            toast.style.transform = 'translate(-50%, -50%) scale(0.9)';
        } else {
            toast.style.transform = 'translate(-50%, -50%) scale(0.9)';
        }
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

// Cart display refresh function
function refreshCartDisplay(responseData) {
    if (typeof prestashop !== 'undefined' && prestashop.emit) {
        // Use direct blockcart refresh
        tryDirectCartRefresh();
        
        // Send custom event for compatibility
        setTimeout(function() {
            var event = new CustomEvent('updatedCart');
            document.dispatchEvent(event);
        }, 200);
        
    } else {
        tryDirectCartRefresh();
    }
}

// Direct blockcart refresh function
function tryDirectCartRefresh() {
    var blockcart = document.querySelector('.blockcart, #_desktop_cart, .cart-preview, [data-refresh-url]');
    
    if (blockcart) {
        var cartRefreshUrl = blockcart.getAttribute('data-refresh-url');
        
        if (cartRefreshUrl) {
            fetch(cartRefreshUrl)
                .then(function(response) {
                    return response.text();
                })
                .then(function(data) {
                    // Use regex to extract count from HTML (without creating DOM)
                    var countMatch = data.match(/<span[^>]*class[^>]*(?:cart-products-count|mobile_count)[^>]*>(\d+)<\/span>/i);
                    if (countMatch) {
                        var newCount = parseInt(countMatch[1]);
                        
                        // Update only mobile_count counters
                        document.querySelectorAll('.mobile_count').forEach(function(counter) {
                            counter.textContent = newCount;
                        });
                    } else {
                        reloadCartSection();
                    }
                })
                .catch(function(error) {
                });
        } else {
            reloadCartSection();
        }
    } else {
        reloadCartSection();
    }
}

// Helper function to reload cart section
function reloadCartSection() {
    var counters = document.querySelectorAll('.cart-products-count, .mobile_count, .cart_count');
    var blockcart = document.querySelector('.blockcart, #_desktop_cart, .cart-preview');
    var cartRefreshUrl = null;
    
    if (blockcart) {
        cartRefreshUrl = blockcart.getAttribute('data-refresh-url');
    }
    
    if (!cartRefreshUrl) {
        cartRefreshUrl = '/index.php?controller=cart&ajax=1&action=refresh';
    }
    fetch(cartRefreshUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        return response.text();
    })
    .then(function(data) {
        try {
            var jsonData = JSON.parse(data);
            var productCount = null;
            
            // Search for product count in preview HTML using regex
            if (jsonData.preview) {
                var patterns = [
                    /<span[^>]*class[^>]*cart-products-count[^>]*>(\d+)<\/span>/i,
                    /<span[^>]*mobile_count[^>]*>(\d+)<\/span>/i,
                    /data-nb-products="(\d+)"/i,
                    /(\d+)\s+(?:produkt|item|sztuk)/i,
                    /<span[^>]*>(\d+)<\/span>/g
                ];
                
                for (var i = 0; i < patterns.length; i++) {
                    var match = jsonData.preview.match(patterns[i]);
                    if (match) {
                        var potentialCount = parseInt(match[1]);
                        if (potentialCount > 0 && potentialCount < 1000) {
                            productCount = potentialCount;
                            break;
                        }
                    }
                }
            }
            
            // Fallback - try modal HTML
            if (productCount === null && jsonData.modal) {
                var modalMatch = jsonData.modal.match(/(\d+)\s+(?:produkt|item)/i);
                if (modalMatch) {
                    productCount = parseInt(modalMatch[1]);
                }
            }
            
            if (productCount !== null) {
                counters.forEach(function(counter) {
                    // Update only mobile_count or counters with digits
                    if (counter.classList.contains('mobile_count') || 
                        (counter.classList.contains('cart-products-count') && /^\s*\d+\s*$/.test(counter.textContent))) {
                        counter.textContent = productCount;
                    }
                });
            } else {
                // Fallback - increment current count by 1
                var currentMobileCount = document.querySelector('.mobile_count');
                if (currentMobileCount && /^\d+$/.test(currentMobileCount.textContent)) {
                    var currentCount = parseInt(currentMobileCount.textContent);
                    var newCount = currentCount + 1;
                    
                    document.querySelectorAll('.mobile_count').forEach(function(counter) {
                        counter.textContent = newCount;
                    });
                }
            }
        } catch (e) {
            // Fallback in case of parsing error
            var currentMobileCount = document.querySelector('.mobile_count');
            if (currentMobileCount && /^\d+$/.test(currentMobileCount.textContent)) {
                var currentCount = parseInt(currentMobileCount.textContent);
                document.querySelectorAll('.mobile_count').forEach(function(counter) {
                    counter.textContent = currentCount + 1;
                });
            }
        }
    })
    .catch(function(error) {
    });
}

/**
 * Simple system for updating delivery messages based on cart
 */
// Throttling dla updateShippingMessagesSimple
let updateMessagesTimeout = null;
let lastUpdateMessagesTime = 0;
let pendingMessagesPromise = null;
let initializeTimeout = null;
let lastInitializeTime = 0;

function formatShippingPrice(value) {
    if (typeof value !== 'number' || !isFinite(value) || value <= 0) {
        return '';
    }
    return value.toFixed(2).replace('.', ',') + ' zł';
}

function applyDeliveryText(element, isFree, priceText) {
    if (!element) {
        return;
    }
    const paidLabel = element.dataset.paidLabel || 'Dostawa od:';
    const freeLabel = element.dataset.freeLabel || paidLabel;
    const freeValue = element.dataset.freeValue || 'Free!';
    const wrapper = element.closest('.delivery-status-wrapper');
    if (!element.dataset.initialText) {
        element.dataset.initialText = element.textContent.trim();
    }
    if (typeof element.dataset.initialPrice === 'undefined') {
        let derivedInitial = '';
        const initialText = element.dataset.initialText;
        if (initialText) {
            const lowerInitial = initialText.toLowerCase();
            if (lowerInitial.includes(freeValue.toLowerCase())) {
                derivedInitial = freeValue;
            } else if (initialText.startsWith(paidLabel)) {
                derivedInitial = initialText.substring(paidLabel.length).trim();
            }
        }
        element.dataset.initialPrice = derivedInitial;
    }
    if (isFree) {
        element.innerHTML = `${freeLabel} ${freeValue}`.trim();
        element.classList.add('is-free');
        if (wrapper) {
            wrapper.classList.add('is-free');
            // Change loading-delivery to delivery-loaded ONLY if it has loading-delivery
            // Don't touch it if it already has delivery-loaded (prevents conflicts)
            if (wrapper.classList.contains('loading-delivery')) {
                wrapper.classList.remove('loading-delivery');
                wrapper.classList.add('delivery-loaded');
            } else if (!wrapper.classList.contains('delivery-loaded')) {
                // First time - add delivery-loaded
                wrapper.classList.add('delivery-loaded');
            }
            wrapper.dataset.freeDeliveryStatus = 'free';
        }
    } else {
        let effectivePrice = typeof priceText === 'string' ? priceText : '';
        if (!effectivePrice) {
            if (element.dataset.lastPrice) {
                effectivePrice = element.dataset.lastPrice;
            } else if (element.dataset.initialPrice) {
                effectivePrice = element.dataset.initialPrice;
            } else if (element.dataset.initialText) {
                const initial = element.dataset.initialText;
                if (initial.toLowerCase().includes('za darmo')) {
                    effectivePrice = '';
                } else {
                    const parts = initial.split(':');
                    effectivePrice = parts.length > 1 ? parts.slice(1).join(':').trim() : initial.trim();
                }
            }
        }
        if (effectivePrice && effectivePrice.toLowerCase() === freeValue.toLowerCase()) {
            effectivePrice = '';
        }
        if (!effectivePrice) {
            effectivePrice = '...';
        }
        element.dataset.lastPrice = effectivePrice;
        // If effectivePrice contains <br>, don't add paidLabel (special message)
        const hasLineBreak = effectivePrice.includes('<br');
        const content = effectivePrice ? (hasLineBreak ? effectivePrice : `${paidLabel} ${effectivePrice}`) : paidLabel;
        element.innerHTML = content.trim();
        element.classList.remove('is-free');
        if (wrapper) {
            wrapper.classList.remove('is-free');
            // Change loading-delivery to delivery-loaded ONLY if it has loading-delivery
            // Don't touch it if it already has delivery-loaded (prevents conflicts)
            if (wrapper.classList.contains('loading-delivery')) {
                wrapper.classList.remove('loading-delivery');
                wrapper.classList.add('delivery-loaded');
            } else if (!wrapper.classList.contains('delivery-loaded')) {
                // First time - add delivery-loaded
                wrapper.classList.add('delivery-loaded');
            }
            wrapper.dataset.freeDeliveryStatus = 'paid';
        }
    }
    element.classList.add('js-updated');
}

function setDeliveryStatus(wrapper, isEligibleForFree) {
    if (!wrapper) {
        return;
    }
    const deliveryElement = wrapper.querySelector('.dynamic-shipping-price');
    if (!deliveryElement) {
        return;
    }
    applyDeliveryText(deliveryElement, isEligibleForFree);
}

function syncDeliveryStatusWrappers(isEligibleForFree) {
    const statusWrappers = document.querySelectorAll('.delivery-status-wrapper');
    statusWrappers.forEach(wrapper => {
        setDeliveryStatus(wrapper, isEligibleForFree);
    });
}

function ensureFreeDeliveryPlaceholdersVisible() {
    const wrappers = document.querySelectorAll('.free-delivery-info-wrapper');
    wrappers.forEach(wrapper => {
        if (wrapper.dataset.freeDeliveryPrepared === 'hidden') {
            wrapper.classList.remove('is-visible');
            wrapper.classList.add('is-hidden');
            return;
        }
        if (!wrapper.dataset.freeDeliveryPrepared) {
            wrapper.dataset.freeDeliveryPrepared = '1';
            wrapper.classList.remove('is-visible');
            wrapper.classList.add('is-hidden');
        }
    });
    
    const statusWrappers = document.querySelectorAll('.delivery-status-wrapper');
    statusWrappers.forEach(wrapper => {
        const deliveryElement = wrapper.querySelector('.dynamic-shipping-price');
        if (!deliveryElement) {
            return;
        }
        if (!deliveryElement.dataset.initialText) {
            deliveryElement.dataset.initialText = deliveryElement.textContent.trim();
        }
        
        // DON'T update element if it's waiting for AJAX (loading-delivery class)
        // This prevents flickering - AJAX will update it with correct value
        // Also keep it hidden (CSS handles visibility)
        if (wrapper.classList.contains('loading-delivery')) {
            return;
        }
        
        // Only show element if it has delivery-loaded class (AJAX completed)
        if (!wrapper.classList.contains('delivery-loaded')) {
            return;
        }
        
        if (!wrapper.dataset.freeDeliveryStatus || wrapper.dataset.freeDeliveryStatus === 'hidden') {
            const cartTotalAttr = parseFloat(wrapper.getAttribute('data-cart-total'));
            const thresholdAttr = parseFloat(wrapper.getAttribute('data-free-threshold'));
            const hasThreshold = !Number.isNaN(thresholdAttr) && thresholdAttr > 0;
            const hasTotal = !Number.isNaN(cartTotalAttr);
            const initialFree = deliveryElement.dataset.initialFree === '1';
            const calculatedEligible = initialFree || (hasThreshold && hasTotal ? cartTotalAttr >= thresholdAttr : false);
            setDeliveryStatus(wrapper, calculatedEligible);
        } else {
            setDeliveryStatus(wrapper, wrapper.dataset.freeDeliveryStatus === 'free');
        }
    });
    
    const shippingPriceNodes = document.querySelectorAll('.dynamic-shipping-price');
    shippingPriceNodes.forEach(node => {
        if (!node.dataset.initialText) {
            node.dataset.initialText = node.textContent.trim();
        }
        if (!node.dataset.initialText) {
            node.textContent = '';
        }
    });
    
    const amountNodes = document.querySelectorAll('.amount-needed');
    amountNodes.forEach(node => {
        if (!node.dataset.initialAmount) {
            node.dataset.initialAmount = node.textContent.trim();
        }
    });
}

function hideFreeDeliveryWrappers() {
    const wrappers = document.querySelectorAll('.free-delivery-info-wrapper');
    wrappers.forEach(wrapper => {
        wrapper.classList.remove('is-visible');
        wrapper.classList.add('is-hidden');
        wrapper.dataset.freeDeliveryPrepared = 'hidden';
    });
    
    const statusWrappers = document.querySelectorAll('.delivery-status-wrapper');
    statusWrappers.forEach(wrapper => {
        wrapper.dataset.freeDeliveryStatus = 'hidden';
        setDeliveryStatus(wrapper, false);
        wrapper.dataset.freeDeliveryStatus = 'hidden';
    });
}

function updateShippingMessagesSimple(options = {}) {
    const { force = false } = options;
    const now = Date.now();
    
    if (!force && now - lastUpdateMessagesTime < 200) {
        return pendingMessagesPromise || Promise.resolve();
    }
    
    if (updateMessagesTimeout) {
        clearTimeout(updateMessagesTimeout);
        updateMessagesTimeout = null;
    }
    
    pendingMessagesPromise = new Promise(resolve => {
        const executeUpdate = () => {
            try {
                lastUpdateMessagesTime = Date.now();
                const cartData = getCurrentCartData();
                
                if (cartData.total > 0) {
                    getShippingThresholdForCart(cartData)
                        .then(threshold => {
                            const remainingAmount = Math.max(0, threshold - cartData.total);
                            const isEligibleForFree = cartData.total >= threshold;
                            
                            try {
                                updateRemainingAmountText(remainingAmount, isEligibleForFree);
                                updateProgressEndThreshold(threshold);
                                setTimeout(() => {
                                    updateProgressEndThreshold(threshold);
                                }, 500);
                                handleFreeShippingElements(isEligibleForFree);
                                updateProgressBarSimple(cartData.total, threshold);
                            } catch (error) {
                            }
                        })
                        .catch(error => {
                        })
                        .finally(() => {
                            updateMessagesTimeout = null;
                            pendingMessagesPromise = null;
                            resolve();
                        });
                } else {
                    hideFreeDeliveryWrappers();
                    window.currentMaxShippingCost = 0;
                    updateMessagesTimeout = null;
                    pendingMessagesPromise = null;
                    resolve();
                }
            } catch (error) {
                updateMessagesTimeout = null;
                pendingMessagesPromise = null;
                resolve();
            }
        };
        
        if (force) {
            executeUpdate();
        } else {
            updateMessagesTimeout = setTimeout(executeUpdate, 120);
        }
    });
    
    return pendingMessagesPromise;
}

function getCurrentCartData() {
    let cartTotal = 0;
    let productCount = 0;
    
    // From prestashop.cart - ALWAYS use gross amount (total_including_tax)
    if (window.prestashop && prestashop.cart) {
        // Priorytet: total_including_tax > total.amount
        cartTotal = prestashop.cart.totals?.total_including_tax?.amount || 
                   prestashop.cart.totals?.total?.amount || 0;
        productCount = prestashop.cart.products_count || 0;
        
        // threshold handled downstream
    }
    
    // From DOM as backup - check more selectors
    if (cartTotal === 0) {
        const cartSelectors = [
            '#blockcart-modal .cart-total .value',
            '.cart-summary .value',
            '.cart-totals .value',
            '.total .value',
            '#header .cart-preview .cart-total',
            '.shopping-cart .total'
        ];
        
        cartSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                const text = el.textContent.replace(/[^\d,.-]/g, '').replace(',', '.');
                const value = parseFloat(text);
                if (!isNaN(value) && value > cartTotal) {
                    cartTotal = value;
                }
            });
        });
    }
    
    // If still 0, check if there are any products in cart
    if (cartTotal === 0 && productCount === 0) {
        // Check product counter in header
        const cartCounters = document.querySelectorAll('.cart-products-count, .cart_block .count, [data-nb-products]');
        cartCounters.forEach(counter => {
            const count = parseInt(counter.textContent || counter.getAttribute('data-nb-products'));
            if (!isNaN(count) && count > productCount) {
                productCount = count;
            }
        });
    }
    
    return { total: cartTotal, productCount: productCount };
}

async function getShippingThresholdForCart(cartData) {
    // Default threshold from global variable
    let maxThreshold = defaultFreeShippingThreshold;
    
    // Try to fetch thresholds for ALL products in cart
    if (window.prestashop && prestashop.cart && prestashop.cart.products && prestashop.cart.products.length > 0) {
        
        const thresholdPromises = prestashop.cart.products.map(async (product, index) => {
            const productId = product.id_product;
            const cacheKey = String(productId);
            
            try {
                if (productDataCache.has(cacheKey)) {
                    const cached = productDataCache.get(cacheKey);
                    if (cached && typeof cached.threshold === 'number') {
                        return { productId, threshold: cached.threshold, shippingCost: cached.shippingCost || 0 };
                    }
                }

                const [thresholdResponse, shippingResponse] = await Promise.all([
                    fetch(relatedProductsEndpoints.freeShipping, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=getFreeShippingThreshold&id_product=${productId}`
                    }),
                    fetch(relatedProductsEndpoints.freeShipping, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=getShippingCost&id_product=${productId}`
                    })
                ]);

                const [thresholdData, shippingData] = await Promise.all([
                    thresholdResponse.json(),
                    shippingResponse.json()
                ]);
                
                const threshold = (thresholdData.success && (thresholdData.threshold || thresholdData.free_shipping_threshold)) 
                    ? parseFloat(thresholdData.threshold || thresholdData.free_shipping_threshold) 
                    : defaultFreeShippingThreshold;
                
                const shippingCost = (shippingData.success && shippingData.shipping_cost) 
                    ? parseFloat(shippingData.shipping_cost) 
                    : 0;

                if (threshold > 0 || shippingCost > 0) {
                    productDataCache.set(cacheKey, Object.assign({}, productDataCache.get(cacheKey), {
                        threshold,
                        shippingCost
                    }));
                }
                
                return { productId, threshold, shippingCost };
                
            } catch (error) {
                return { productId, threshold: defaultFreeShippingThreshold, shippingCost: 0 };
            }
        });
        
        try {
            const results = await Promise.all(thresholdPromises);
            const allThresholds = results.map(r => r.threshold);
            const allShippingCosts = results.map(r => r.shippingCost).filter(cost => cost > 0);
            
            maxThreshold = Math.max(...allThresholds);
            const maxShippingCost = allShippingCosts.length > 0 ? Math.max(...allShippingCosts) : 0;
            
            // Update global variable
            currentFreeShippingThreshold = maxThreshold;
            
            // IMPORTANT: Check if customer already has free delivery
            const isEligibleForFree = cartData.total >= maxThreshold;
            // ONLY if NOT free delivery, save the cost
            if (!isEligibleForFree) {
                window.currentMaxShippingCost = maxShippingCost;
            } else {
                // Clear cache - customer has free delivery
                window.currentMaxShippingCost = 0;
            }
            
            results.forEach(({ productId, threshold, shippingCost }) => {
                const isMaxThreshold = threshold === maxThreshold;
                const isMaxShipping = shippingCost === maxShippingCost && shippingCost > 0;
            });
            
            // TEST - check if we have different thresholds
            const uniqueThresholds = [...new Set(allThresholds)];
            if (uniqueThresholds.length > 1) {
            } else {
            }
            
        } catch (error) {
        }
    }
    return maxThreshold;
}

// Global flag to track if cart has additional shipping costs
let cartHasAdditionalShippingCosts = false;

// Global flag to track if any product in cart has free shipping (via product settings)
let anyProductHasFreeShipping = false;

function updateRemainingAmountText(remainingAmount, isEligibleForFree) {
    // ALWAYS skip progress bar update - let AJAX handle it after getting cart_has_additional_costs
    // This prevents showing progress bar before we know if cart has additional shipping costs

    // DON'T call syncDeliveryStatusWrappers - it conflicts with AJAX updates
    // Element will be updated by AJAX via updateShippingPrice

    // Hide/show the ENTIRE free delivery wrapper (contains message + progress bar)
    const freeDeliveryWrapperElements = document.querySelectorAll('.free-delivery-info-wrapper');
    freeDeliveryWrapperElements.forEach(element => {
        if (isEligibleForFree) {
            element.style.display = 'none';
        } else {
            element.style.display = '';
        }
    });

    // Also hide individual elements as fallback
    const freeDeliveryInfoElements = document.querySelectorAll('.free-delivery-info');
    freeDeliveryInfoElements.forEach(element => {
        if (isEligibleForFree) {
            element.style.display = 'none';
        } else {
            element.style.display = '';
        }
    });

    // Also hide/show progress bar wrapper when shipping is free
    const progressWrappers = document.querySelectorAll('.delivery-progress-wrapper');
    progressWrappers.forEach(element => {
        if (isEligibleForFree) {
            element.style.display = 'none';
        } else {
            element.style.display = '';
        }
    });

    // ONLY selectors for remaining amount to free delivery
    const messageSelectors = [
        '.amount-needed'
    ];

    let foundElements = 0;

    messageSelectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            foundElements++;

            const oldValue = element.textContent;

            // Update only amount in .amount-needed
            if (isEligibleForFree) {
                element.textContent = '0,00';
                element.style.color = 'green';
            } else {
                const newValue = remainingAmount.toFixed(2).replace('.', ',');
                element.textContent = newValue;
                element.style.color = '';
            }

            // Oznacz jako zaktualizowany przez JS
            element.classList.add('js-updated');
        });
    });
    
    const shippingPriceElements = document.querySelectorAll('.dynamic-shipping-price');
    
    // NEVER use cache for shipping prices - they depend on entire cart state
    // Always fetch fresh data via AJAX to prevent flickering with stale values
    
    shippingPriceElements.forEach(element => {
        const productId = element.getAttribute('data-product-id') || element.dataset.productId;
        const wrapper = element.closest('.delivery-status-wrapper');

        // Don't hide element - just update text seamlessly
        // Removing loading-delivery class manipulation to prevent fading effect
        
        if (productId) {
            // DON'T call applyDeliveryText before AJAX - element keeps current text during update
            // This prevents showing old/cached values that cause flickering
            setTimeout(() => updateShippingPrice(element, productId), 0);
        }
        // If no productId, keep element hidden - don't show fallback
    });
    
    // Oznacz delivery-text jako zaktualizowany
    const deliveryTextElements = document.querySelectorAll('.delivery-text');
    deliveryTextElements.forEach(element => {
        element.classList.add('js-updated');
    });
}

function updateProgressEndThreshold(currentThreshold) {
    
    // Check if modal is open and visible
    const modal = document.querySelector('.modal.show');
    
    // Check all elements in DOM
    
    // Update threshold in .progress-end element
    const progressEndElements = document.querySelectorAll('.progress-end');
    
    progressEndElements.forEach((element, index) => {
        
        // Try to find element with threshold value - different possibilities
        let thresholdP = element.querySelector('.threshold-value') || 
                        element.querySelector('p:first-child') ||
                        element.querySelector('p');
                        
                        
        if (thresholdP) {
            const oldValue = thresholdP.textContent;
            const formattedThreshold = Math.round(currentThreshold).toLocaleString('pl-PL');
            thresholdP.textContent = formattedThreshold;
        } else {
        }
    });
    
    if (progressEndElements.length === 0) {
        // Check if element might have different structure
        const alternativeSelectors = [
            '.progress-end p',
            '.progress-threshold',
            '[class*="progress"] [class*="end"]',
            '[class*="threshold"]'
        ];
        
        alternativeSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
            }
        });
    }
}

// Track ongoing AJAX requests to prevent duplicates
const pendingShippingRequests = new Map();

function updateShippingPrice(element, productId) {
    // Never show free delivery without checking AJAX first
    // Cart shipping cost in prestashop object may not be calculated yet

    if (!productId) {
        const fallbackPrice = selectDisplayShippingCost('', undefined);
        applyDeliveryText(element, false, fallbackPrice || '...');
        return;
    }

    const cacheKey = String(productId);
    
    // Prevent duplicate AJAX calls for the same product
    if (pendingShippingRequests.has(cacheKey)) {
        return;
    }

    // DON'T use cache for shipping prices - they depend on entire cart state
    // Cache can show outdated prices when cart changes
    
    // Ensure modal element is hidden (may already be hidden by updateShippingMessagesSimple)
    prepareModalDeliveryForProduct(productId);
    
    // Mark request as pending
    pendingShippingRequests.set(cacheKey, true);
    
    fetch(relatedProductsEndpoints.freeShipping, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=getShippingInfo&id_product=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        // Update global flag if backend reports additional costs
        const previousFlag = cartHasAdditionalShippingCosts;
        if (typeof data.cart_has_additional_costs === 'boolean') {
            cartHasAdditionalShippingCosts = data.cart_has_additional_costs;
            
            // DON'T update progress bar visibility here - it causes flickering
            // Multiple AJAX calls would show/hide it multiple times
            // Let updateRemainingAmountText handle it after all AJAX complete
        }
        
        if (data.success && (data.formatted_price || data.lowest_price)) {
            let price = data.formatted_price || '';
            let numericCost;

            if (typeof data.lowest_price === 'number') {
                numericCost = data.lowest_price;
            } else if (typeof data.lowest_price === 'string') {
                const parsed = parseFloat(data.lowest_price);
                if (Number.isFinite(parsed)) {
                    numericCost = parsed;
                }
            }

            if (price && price.toLowerCase().startsWith('od ')) {
                price = price.substring(3);
            }

            // Check if shipping is TRULY free (cart reached threshold)
            // Don't trust numericCost === 0 alone, it may be carrier error
            let displayPrice = price || formatShippingPrice(numericCost);

            if (data.is_free_shipping === true) {
                applyDeliveryText(element, true, null); // true = free shipping
                // Set global flag - product has free shipping via settings
                anyProductHasFreeShipping = true;
                // Hide "remaining to free shipping" message when product has free shipping
                updateRemainingAmountText(0, true);
            } else {
                applyDeliveryText(element, false, displayPrice || '...');
            }

            productDataCache.set(cacheKey, Object.assign({}, productDataCache.get(cacheKey), {
                formattedPrice: price || displayPrice,
                shippingCost: Number.isFinite(numericCost) ? numericCost : undefined,
                isFreeShipping: data.is_free_shipping === true
            }));
            
            // Clear pending request flag
            pendingShippingRequests.delete(cacheKey);
            
            // After AJAX completes, check if ALL requests finished
            // If yes, update progress bar visibility
            if (pendingShippingRequests.size === 0) {
                // Get cart total to check if eligible for free shipping
                const cartData = getCurrentCartData();
                const threshold = currentFreeShippingThreshold || 1500; // Use global or default
                const isEligibleForFree = cartData.total >= threshold;

                const freeDeliveryWrappers = document.querySelectorAll('.free-delivery-info-wrapper');
                freeDeliveryWrappers.forEach(wrapper => {
                    // Hide if: has additional costs OR eligible for free shipping OR product has free shipping via settings
                    if (cartHasAdditionalShippingCosts || isEligibleForFree || anyProductHasFreeShipping) {
                        wrapper.classList.remove('is-visible');
                        wrapper.classList.add('is-hidden');
                        wrapper.dataset.freeDeliveryPrepared = 'hidden';
                        wrapper.style.display = 'none'; // Force hide with inline style
                    } else {
                        wrapper.classList.remove('is-hidden');
                        wrapper.classList.add('is-visible');
                        wrapper.dataset.freeDeliveryPrepared = '1';
                        wrapper.style.display = ''; // Remove inline style
                    }
                });
            }
        } else {
            applyDeliveryText(element, false, '...');  // Placeholder
            pendingShippingRequests.delete(cacheKey);
        }
    })
    .catch(error => {
        applyDeliveryText(element, false, '...');
        pendingShippingRequests.delete(cacheKey);
    });
}

function handleFreeShippingElements(isEligibleForFree) {
    // DON'T hide .dynamic-shipping-price elements - they are controlled by updateRemainingAmountText
    
    // Hide only cart elements that show "Free!" when they shouldn't
    const cartFreeElements = document.querySelectorAll(
        '#blockcart-modal .value, ' +
        '.cart-summary-line .value, ' + 
        '#cart-subtotal-shipping .value'
    );
    
    cartFreeElements.forEach((element, index) => {
        const trimmedText = element.textContent ? element.textContent.trim() : '';
        if (trimmedText === 'Za darmo!' || trimmedText === 'Free!') {
            if (isEligibleForFree) {
                element.style.display = '';
                element.style.visibility = '';
            } else {
                element.style.display = 'none';
                element.style.visibility = 'hidden';
            }
        }
    });
}

// Throttling dla progress bara
let updateProgressTimeout = null;
let lastProgressUpdate = 0;

function updateProgressBarSimple(cartTotal, threshold) {
    const now = Date.now();
    
    // INCREASE throttling to 1000ms (from 500ms)
    if (now - lastProgressUpdate < 1000) {
        return;
    }
    
    // Cancel previous scheduled update
    if (updateProgressTimeout) {
        clearTimeout(updateProgressTimeout);
    }
    
    // Schedule update in 100ms (debouncing)
    updateProgressTimeout = setTimeout(() => {
        try {
            lastProgressUpdate = Date.now();
            
            // IMPORTANT: Limit to 100% maximum
            const percentage = Math.min(100, (cartTotal / threshold) * 100);
            
            const progressBars = document.querySelectorAll('.progress-fill, .delivery-progress-fill');
            progressBars.forEach((bar, index) => {
                // DON'T reset to 0% - allow smooth transition from current value
                // bar.style.width = '0%';  ← REMOVE
                // bar.style.transition = 'none';  ← REMOVE
                
                // Set smooth animation
                bar.style.transition = 'width 0.5s ease-in-out';
                bar.style.opacity = '1';
                bar.style.width = `${percentage}%`;
            });
            
            updateProgressTimeout = null;
        } catch (error) {
        }
    }, 100);
}

// Initialize on page load
function bootstrapShippingModules(immediateInit) {
    initializeAllShippingElements({ immediate: immediateInit });
    setupModalListeners();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        bootstrapShippingModules(true);
    });
} else {
    bootstrapShippingModules(true);
}

// Function for manual invocation in console (for testing)
window.forceUpdateShipping = function() {
    
    // Reset throttling
    lastInitializeTime = 0;
    lastUpdateMessagesTime = 0;
    
    // Force update
    initializeAllShippingElements({ immediate: true });
    
};

function initializeAllShippingElements(options = {}) {
    const { immediate = false } = options;
    const now = Date.now();
    
    // If function was called less than 300ms ago, skip
    if (!immediate && now - lastInitializeTime < 300) {
        return;
    }
    
    // If already scheduled, cancel previous
    if (initializeTimeout) {
        clearTimeout(initializeTimeout);
        initializeTimeout = null;
    }
    
    const executeInitialization = async function() {
        try {
            lastInitializeTime = Date.now();
            
            // IMPORTANT: FIRST update messages and check free delivery threshold
            // This sets window.currentMaxShippingCost (or 0 if free)
            // WAIT until it finishes (updateShippingMessagesSimple is async)
            await updateShippingMessagesSimple({ force: true });
            ensureFreeDeliveryPlaceholdersVisible();
            
            // NOW update delivery prices - window.currentMaxShippingCost is ready
            const shippingElements = document.querySelectorAll('.dynamic-shipping-price');
            shippingElements.forEach(element => {
                const productId = element.getAttribute('data-product-id');
                if (productId) {
                    // Now window.currentMaxShippingCost is set
                    // (0 if free, or correct value)
                    updateShippingPrice(element, productId);
                }
            });
            
        } catch (error) {
        }
        
        initializeTimeout = null;
    };

    if (immediate) {
        executeInitialization();
    } else {
        // Zaplanuj wykonanie za 300ms
        initializeTimeout = setTimeout(executeInitialization, 300);
    }
}

function setupModalListeners() {
    if (modalListenersInitialized) {
        return;
    }
    modalListenersInitialized = true;
    // PRE-FETCH: Pobierz dane przy hover nad przyciskiem "Add to cart"
    document.addEventListener('mouseover', function(e) {
        const addToCartBtn = e.target.closest('.add-to-cart, [data-button-action="add-to-cart"], .add-to-cart-ajax');
        if (addToCartBtn) {
            const productId = addToCartBtn.getAttribute('data-id-product') || 
                            addToCartBtn.getAttribute('data-product-id') ||
                            addToCartBtn.dataset.idProduct;
            
            if (productId) {
                prefetchProductData(productId);
            }
        }
    });
    
    // Listen ONLY to modal opening - one call is enough
    $(document).on('shown.bs.modal', '.modal', function() {
        const runInitialization = () => initializeAllShippingElements({ immediate: true });
        if (typeof window.requestAnimationFrame === 'function') {
            window.requestAnimationFrame(runInitialization);
        } else {
            setTimeout(runInitialization, 0);
        }
    });
    
    // REMOVE prestashop.on('updateCart') listener - causes double invocation
    // Product addition already handled at line 88: updateShippingMessagesSimple()
}

// Pre-fetch danych produktu przy hover lub click
function prefetchProductData(productId) {
    const cacheKey = String(productId);
    // If already in cache, skip
    if (productDataCache.has(cacheKey)) {
        return Promise.resolve(productDataCache.get(cacheKey));
    }
    
    // Fetch threshold and delivery cost in parallel
    const thresholdPromise = fetch(relatedProductsEndpoints.freeShipping, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=getFreeShippingThreshold&id_product=${productId}`
    }).then(r => r.json());
    
    const shippingPromise = fetch(relatedProductsEndpoints.freeShipping, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=getShippingInfo&id_product=${productId}`
    }).then(r => r.json());
    
    return Promise.all([thresholdPromise, shippingPromise])
        .then(([thresholdData, shippingData]) => {
            // Check if we received valid data
            const hasValidThreshold = thresholdData && thresholdData.success && 
                                     (thresholdData.threshold || thresholdData.free_shipping_threshold);
            const hasValidShipping = shippingData && shippingData.success && 
                                    (shippingData.lowest_price || shippingData.formatted_price);
            
            // ONLY save to cache when we have VALID data from backend
            if (!hasValidThreshold || !hasValidShipping) {
                return null; // DON'T save to cache
            }
            
            const data = {
                threshold: parseFloat(thresholdData.threshold || thresholdData.free_shipping_threshold),
                shippingCost: parseFloat(shippingData.lowest_price),
                formattedPrice: shippingData.formatted_price
            };
            
            // Save to cache ONLY when we have complete data
            productDataCache.set(cacheKey, data);
            return data;
        })
        .catch(error => {
            return null; // DON'T save to cache on error
        });
}

// Fix aria-hidden accessibility warning - remove focus before modal closes
if (typeof jQuery !== 'undefined') {
    jQuery(document).on('hide.bs.modal', '#blockcart-modal', function() {
        // Remove focus from any focused element inside modal
        var focusedElement = this.querySelector(':focus');
        if (focusedElement) {
            focusedElement.blur();
        }
    });
}
