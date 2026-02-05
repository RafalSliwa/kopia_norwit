/**
 * Cash on Delivery - Disable payment option when cart exceeds limit
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find COD payment option
    const codOptions = document.querySelectorAll('input[name="payment-option"][data-module-name="ps_cashondelivery"]');
    
    console.log('COD: Found options:', codOptions.length);
    
    codOptions.forEach(function(codOption) {
        // Check if label contains "NIEDOSTĘPNE"
        const label = document.querySelector('label[for="' + codOption.id + '"]');
        
        console.log('COD: Label text:', label ? label.textContent.trim() : 'NO LABEL');
        
        if (label && label.textContent.includes('NIEDOSTĘPNE')) {
            console.log('COD: Disabling option - cart exceeds limit');
            
            // Disable the option
            codOption.disabled = true;
            
            // Add visual feedback with CSS class
            const paymentOption = codOption.closest('.payment-option');
            if (paymentOption) {
                paymentOption.classList.add('cod-disabled');
            }
            
            // Style the label
            if (label) {
                label.style.cursor = 'not-allowed';
                label.style.color = '#999';
                
                // Make "NIEDOSTĘPNE" text red
                const text = label.innerHTML;
                label.innerHTML = text.replace(/(NIEDOSTĘPNE[^<]*)/i, '<span style="color: #d9534f; font-weight: 600;">$1</span>');
            }
            
            // Prevent any clicks
            codOption.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
            
            if (label) {
                label.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            }
        } else {
            console.log('COD: Option is available - cart within limit');
        }
    });
});
