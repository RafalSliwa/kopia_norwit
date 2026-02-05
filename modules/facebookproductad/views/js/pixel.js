/*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*/

$(document).ready(function () {

    // Only proceed if Pixel is properly configured
    if (typeof btPixel === 'undefined' || !btPixel.pixel_id || btPixel.activate_pixel != 1) {
        console.warn('Facebook Pixel: btPixel not configured or pixel disabled');
        return;
    }

    // Event deduplication tracking
    const sentEvents = new Set();

    // Store exact timestamp when pixel loads to ensure consistency
    const initialTimestamp = Math.floor(Date.now() / 1000);

    /**
     * Get a precise timestamp for API calls
     * This ensures consistency between pixel and API events
     */
    function getPreciseTimestamp() {
        return initialTimestamp + Math.floor((Date.now() - (initialTimestamp * 1000)) / 1000);
    }

    // Enhanced fbclid capture and fbc cookie management
    function captureFbclid() {
        // Get fbclid from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const fbclid = urlParams.get('fbclid');

        if (fbclid && fbclid.length > 10) {
            // Check if _fbc cookie already exists
            const existingFbc = getCookie('_fbc');

            if (!existingFbc || !isValidFbc(existingFbc) || shouldUpdateFbc(existingFbc, fbclid)) {
                // Calculate subdomain index according to Meta specifications
                const subdomainIndex = calculateSubdomainIndex();

                // Use milliseconds timestamp as required by Meta
                const timestamp = Date.now();

                // Format: fb.<subdomainIndex>.<timestamp>.<fbclid>
                const fbc = `fb.${subdomainIndex}.${timestamp}.${fbclid}`;

                // Set cookie with 90-day expiration as recommended by Meta
                const expirationDate = new Date();
                expirationDate.setDate(expirationDate.getDate() + 90);

                document.cookie = `_fbc=${fbc}; expires=${expirationDate.toUTCString()}; path=/; secure; samesite=lax`;

                console.log('Facebook Pixel: fbc cookie set from fbclid');
            }
        }
    }

    // Helper function to get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Helper function to validate fbc format
    function isValidFbc(fbc) {
        if (!fbc || typeof fbc !== 'string') return false;
        const parts = fbc.split('.');
        return parts.length >= 4 && parts[0] === 'fb' && !isNaN(parseInt(parts[1])) && !isNaN(parseInt(parts[2]));
    }

    // Helper function to check if fbc should be updated with new fbclid
    function shouldUpdateFbc(existingFbc, newFbclid) {
        const parts = existingFbc.split('.');
        if (parts.length < 4) return true;

        const existingFbclid = parts.slice(3).join('.');
        return existingFbclid !== newFbclid;
    }

    // Helper function to calculate subdomain index
    function calculateSubdomainIndex() {
        const hostname = window.location.hostname;
        const dotCount = (hostname.match(/\./g) || []).length;

        // Meta specification: 'com' = 0, 'example.com' = 1, 'www.example.com' = 2
        if (dotCount === 0) return 0;
        if (dotCount === 1) return 1;
        return 2;
    }

    // Capture fbclid on page load
    captureFbclid();

    /**
     * Generate a unique event_id for each event occurrence
     * According to Meta documentation: "The event_id parameter is an identifier that can uniquely distinguish between similar events"
     * Each event occurrence must have a unique event_id for proper deduplication between browser and server
     */
    function generateConsistentEventId(eventName, params) {
        // For Purchase events, use order reference if available (these are naturally unique per order)
        if (eventName === 'Purchase') {
            if (btPixel.id_order && btPixel.orderReference) {
                return 'purchase_' + btPixel.orderReference;
            } else if (btPixel.id_order) {
                return 'purchase_order_' + btPixel.id_order;
            }
        }

        // For other events, create a unique ID for each event occurrence
        // Meta documentation: "For other events without an intrinsic ID number, a random number can be used"
        const timestamp = Math.floor(Date.now() / 1000);
        const randomComponent = Math.floor(Math.random() * 10000);
        const userId = btPixel.external_id || 'anonymous';

        // Each occurrence gets a unique ID combining timestamp and random component
        return eventName.toLowerCase() + '_' + userId + '_' + timestamp + '_' + randomComponent;
    }

    /**
     * Helper function to extract category ID from URL
     */
    function getCategoryIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id_category');
    }

    /**
     * Helper function to extract page number from URL
     */
    function getPageNumberFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('p') || 1;
    }

    /**
     * Helper function to extract search query from URL
     */
    function getSearchQueryFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('search_query') || urlParams.get('s');
    }

    /**
     * Generate a unique key for event deduplication
     */
    function generateEventKey(eventName, params) {
        // Enhanced key generation to prevent conflicts with GTM
        let keyComponents = [eventName];

        // Add content_ids if available
        if (params.content_ids) {
            if (Array.isArray(params.content_ids)) {
                keyComponents.push(params.content_ids.join(','));
            } else {
                keyComponents.push(params.content_ids);
            }
        }

        // Add currency and value for better deduplication
        if (params.currency) {
            keyComponents.push(params.currency);
        }
        if (params.value) {
            keyComponents.push(params.value);
        }

        // Add content_type for better specificity
        if (params.content_type) {
            keyComponents.push(params.content_type);
        }

        // Add timestamp component to prevent over-aggressive deduplication
        const now = Math.floor(Date.now() / 1000);
        const timeWindow = Math.floor(now / 60); // 1-minute windows
        keyComponents.push(timeWindow.toString());

        // Add module identifier to avoid conflicts with other tracking systems
        keyComponents.push('fpa_module');

        const key = keyComponents.join('_');
        return btoa(key); // Base64 encode for consistent key format
    }

    /**
     * Check if event should be sent (not already sent)
     */
    function shouldSendEvent(eventName, params) {
        // Enhanced deduplication logic
        if (eventName !== 'ViewContent' && eventName !== 'PageView') {
            return true; // Only deduplicate ViewContent and PageView events
        }

        // Don't deduplicate if content_ids is missing (let it fail with proper error)
        if (!params.content_ids) {
            return true;
        }

        const eventKey = generateEventKey(eventName, params);
        if (sentEvents.has(eventKey)) {
            console.log(`Facebook Pixel: ${eventName} event deduplicated (key: ${eventKey.substring(0, 20)}...)`);
            return false; // Already sent
        }

        sentEvents.add(eventKey);

        // Clean up old entries to prevent memory leaks (keep only last 100 events)
        if (sentEvents.size > 100) {
            const eventsArray = Array.from(sentEvents);
            sentEvents.clear();
            // Keep the most recent 50 events
            eventsArray.slice(-50).forEach(event => sentEvents.add(event));
        }

        return true;
    }

    // Track category page changes via AJAX pagination
    function setupCategoryPaginationTracking() {
        if (btPixel.currentPage === 'category') {
            // Initial page load already tracked by server-side code
            let currentPage = getPageNumberFromUrl();
            let categoryId = getCategoryIdFromUrl();

            // Function to extract product IDs from the page
            function extractProductIdsFromPage() {
                const productIds = [];
                // Find all product elements in the category listing
                $('[data-id-product]').each(function() {
                    const productId = $(this).data('id-product');
                    if (productId) {
                        productIds.push(productId);
                    }
                });
                return productIds;
            }

            // Function to track ViewCategory event with current page products
            function trackCategoryPageView() {
                const productIds = extractProductIdsFromPage();

                if (productIds.length > 0) {
                    // Format product IDs according to module settings
                    let formattedIds = [];
                    productIds.forEach(id => {
                        let formattedId = id;
                        if (btPixel.prefix) {
                            formattedId = btPixel.prefix + formattedId;
                        }
                        if (btPixel.prefixLang) {
                            formattedId = btPixel.prefixLang + formattedId;
                        }
                        formattedIds.push(formattedId);
                    });

                    // Get category name
                    const categoryName = $('h1.h1').first().text().trim();

                    // Build event parameters
                    const viewCategoryParams = {
                        content_name: categoryName,
                        content_category: categoryName,
                        content_type: 'product',
                        content_ids: formattedIds
                    };

                    // Generate shared event_id for ViewCategory
                    const sharedViewCategoryEventId = generateConsistentEventId('ViewCategory', viewCategoryParams);
                    window.btPixelSharedEventId = sharedViewCategoryEventId;

                    // Track the event
                    fbq("trackCustom", "ViewCategory", viewCategoryParams, { 'eventID': sharedViewCategoryEventId });

                    console.log('Facebook Pixel: ViewCategory tracked for page ' + currentPage + ' with ' + productIds.length + ' products');

                    // Send to API if enabled
                    if (btPixel.useConversionApi) {
                        $.ajax({
                            type: "POST",
                            url: btPixel.ajaxUrl,
                            dataType: "json",
                            data: {
                                ajax: 1,
                                action: 'sendApiData',
                                token: btPixel.token,
                                tagContent: JSON.stringify({
                                    aDynTags: {
                                        content_ids: { value: formattedIds },
                                        content_name: { value: categoryName },
                                        content_category: { value: categoryName },
                                        content_type: { value: 'product' }
                                    },
                                    aTrackingType: { value: 'ViewCategory' }
                                }),
                                pagetype: 'category',
                                clientTimestamp: getPreciseTimestamp(), // Use consistent timestamp
                                client_url: window.location.href,
                                shared_event_id: sharedViewCategoryEventId
                            }
                        });
                    }
                }
            }

            // Listen for AJAX page changes in PrestaShop 1.7+
            $(document).ajaxComplete(function(event, xhr, settings) {
                // Check if this is a category pagination AJAX request
                if (settings.url && (
                    settings.url.indexOf('controller=category') !== -1 ||
                    settings.url.indexOf('?p=') !== -1 ||
                    settings.url.indexOf('&p=') !== -1
                )) {
                    // Get the new page number
                    const urlParams = new URLSearchParams(settings.url.split('?')[1]);
                    const newPage = urlParams.get('p') || 1;

                    // Only track if page has changed
                    if (newPage !== currentPage) {
                        currentPage = newPage;
                        console.log('Facebook Pixel: Category page changed to ' + currentPage);

                        // Allow DOM to update before extracting product IDs
                        setTimeout(trackCategoryPageView, 500);
                    }
                }
            });

            // For themes using history.pushState for pagination
            if (window.history && window.history.pushState) {
                $(window).on('popstate', function() {
                    const newPage = getPageNumberFromUrl();
                    if (newPage !== currentPage) {
                        currentPage = newPage;
                        console.log('Facebook Pixel: Category page changed to ' + currentPage + ' (popstate)');

                        // Allow DOM to update before extracting product IDs
                        setTimeout(trackCategoryPageView, 500);
                    }
                });
            }

            // For themes using custom pagination events
            $(document).on('updateProductList', function() {
                const newPage = getPageNumberFromUrl();
                if (newPage !== currentPage) {
                    currentPage = newPage;
                    console.log('Facebook Pixel: Category page changed to ' + currentPage + ' (updateProductList)');

                    // Allow DOM to update before extracting product IDs
                    setTimeout(trackCategoryPageView, 500);
                }
            });
        }
    }

    if (btPixel.pixel_id != "" && btPixel.activate_pixel == 1) {
        console.log('Facebook Pixel: Initializing pixel with ID:', btPixel.pixel_id);

        // Init the pixel code
        !(function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = "2.0";
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s);
        })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");

        fbq("init", btPixel.pixel_id);
        fbq('set', 'autoConfig', false, btPixel.pixel_id);

        console.log('Facebook Pixel: Pixel initialized successfully');

        if (btPixel.bUseConsent == true) {
            if (btPixel.bUseAxeption == 1) {
                void 0 === window._axcb && (window._axcb = []);
                window._axcb.push(function (axeptio) {
                    axeptio.on("cookies:complete", function (choices) {
                        if (choices.facebook_pixel) {
                            $.ajax({
                                type: "POST",
                                url: btPixel.ajaxUrl,
                                dataType: "json",
                                data: {
                                    ajax: 1,
                                    action: 'updateConsent',
                                    token: btPixel.token
                                },
                                success: function (jsonData) {
                                    // Generate consistent event_id for PageView
                                    const pageViewEventId = generateConsistentEventId('PageView', {});
                                    fbq("track", "PageView", {}, { 'eventID': pageViewEventId });

                                    if (btPixel.tagContent) {
                                        const trackingType = btPixel.tagContent.aTrackingType.value;
                                        const dynTags = btPixel.tagContent.aDynTags;
                                        trackEvent(trackingType, dynTags);
                                    }
                                }
                            });
                        }
                    });
                });
            } else {
                // Use case for the HTML element
                if (btPixel.bConsentHtmlElement != "") {
                    $(document).on('click', '#' + btPixel.bConsentHtmlElement, function (e) {
                        $.ajax({
                            type: "POST",
                            url: btPixel.ajaxUrl,
                            dataType: "json",
                            data: {
                                ajax: 1,
                                action: 'updateConsent',
                                token: btPixel.token
                            },
                            success: function (jsonData) {
                                // Generate consistent event_id for PageView and store it for API sharing
                                const pageViewEventId = generateConsistentEventId('PageView', {});
                                window.btPixelSharedEventId = pageViewEventId;
                                fbq("track", "PageView", {}, { 'eventID': pageViewEventId });

                                if (btPixel.tagContent) {
                                    const trackingType = btPixel.tagContent.aTrackingType.value;
                                    const dynTags = btPixel.tagContent.aDynTags;
                                    trackEvent(trackingType, dynTags);
                                }
                            }
                        });
                    });
                }
                // Use case for the HTML element
                if (btPixel.bConsentHtmlElementSecond != "") {
                    $(document).on('click', '#' + btPixel.bConsentHtmlElementSecond, function (e) {
                        $.ajax({
                            type: "POST",
                            url: btPixel.ajaxUrl,
                            dataType: "json",
                            data: {
                                ajax: 1,
                                action: 'updateConsent',
                                token: btPixel.token
                            },
                            success: function (jsonData) {
                                // Generate consistent event_id for PageView and store it for API sharing
                                const pageViewEventId = generateConsistentEventId('PageView', {});
                                window.btPixelSharedEventId = pageViewEventId;
                                fbq("track", "PageView", {}, { 'eventID': pageViewEventId });

                                if (btPixel.tagContent) {
                                    const trackingType = btPixel.tagContent.aTrackingType.value;
                                    const dynTags = btPixel.tagContent.aDynTags;
                                    trackEvent(trackingType, dynTags);
                                }
                            }
                        });
                    });
                }
            }
        } else {
            // Generate consistent event_id for PageView and store it for API sharing
            const pageViewEventId = generateConsistentEventId('PageView', {});
            window.btPixelSharedEventId = pageViewEventId;
            fbq("track", "PageView", {}, { 'eventID': pageViewEventId });

            // Setup category pagination tracking
            setupCategoryPaginationTracking();

            if (btPixel.useConversionApi && btPixel.useApiForPageView) {
                $.ajax({
                    type: "POST",
                    url: btPixel.ajaxUrl,
                    dataType: "json",
                    data: {
                        ajax: 1,
                        action: 'sendApiData',
                        token: btPixel.token,
                        tagContent: JSON.stringify({
                            aTrackingType: { value: 'PageView' }
                        }),
                        useApiForPageView: btPixel.useApiForPageView,
                        pagetype: btPixel.currentPage,
                        clientTimestamp: getPreciseTimestamp(), // Use consistent timestamp
                        client_url: window.location.href,
                        shared_event_id: pageViewEventId
                    }
                });
            }

            // Rest of the code...

            /**
             * Generate a unique event_id for each event occurrence
             * According to Meta documentation: "The event_id parameter is an identifier that can uniquely distinguish between similar events"
             * Each event occurrence must have a unique event_id for proper deduplication between browser and server
             */
            function generateConsistentEventId(eventName, params) {
                // For Purchase events, use order reference if available (these are naturally unique per order)
                if (eventName === 'Purchase') {
                    if (btPixel.id_order && btPixel.orderReference) {
                        return 'purchase_' + btPixel.orderReference;
                    } else if (btPixel.id_order) {
                        return 'purchase_order_' + btPixel.id_order;
                    }
                }

                // For other events, create a unique ID for each event occurrence
                // Meta documentation: "For other events without an intrinsic ID number, a random number can be used"
                const timestamp = getPreciseTimestamp(); // Use consistent timestamp
                const randomComponent = Math.floor(Math.random() * 10000);
                const userId = btPixel.external_id || 'anonymous';

                // Each occurrence gets a unique ID combining timestamp and random component
                return eventName.toLowerCase() + '_' + userId + '_' + timestamp + '_' + randomComponent;
            }

            /**
             * Helper function to extract category ID from URL
             */
            function getCategoryIdFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('id_category');
            }

            /**
             * Helper function to extract search query from URL
             */
            function getSearchQueryFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('search_query') || urlParams.get('s');
            }

            /**
             * Generate a unique key for event deduplication
             */
            function generateEventKey(eventName, params) {
                // Enhanced key generation to prevent conflicts with GTM
                let keyComponents = [eventName];

                // Add content_ids if available
                if (params.content_ids) {
                    if (Array.isArray(params.content_ids)) {
                        keyComponents.push(params.content_ids.join(','));
                    } else {
                        keyComponents.push(params.content_ids);
                    }
                }

                // Add currency and value for better deduplication
                if (params.currency) {
                    keyComponents.push(params.currency);
                }
                if (params.value) {
                    keyComponents.push(params.value);
                }

                // Add content_type for better specificity
                if (params.content_type) {
                    keyComponents.push(params.content_type);
                }

                // Add timestamp component to prevent over-aggressive deduplication
                const now = Math.floor(Date.now() / 1000);
                const timeWindow = Math.floor(now / 60); // 1-minute windows
                keyComponents.push(timeWindow.toString());

                // Add module identifier to avoid conflicts with other tracking systems
                keyComponents.push('fpa_module');

                const key = keyComponents.join('_');
                return btoa(key); // Base64 encode for consistent key format
            }

            /**
             * Check if event should be sent (not already sent)
             */
            function shouldSendEvent(eventName, params) {
                // Enhanced deduplication logic
                if (eventName !== 'ViewContent' && eventName !== 'PageView') {
                    return true; // Only deduplicate ViewContent and PageView events
                }

                // Don't deduplicate if content_ids is missing (let it fail with proper error)
                if (!params.content_ids) {
                    return true;
                }

                const eventKey = generateEventKey(eventName, params);
                if (sentEvents.has(eventKey)) {
                    console.log(`Facebook Pixel: ${eventName} event deduplicated (key: ${eventKey.substring(0, 20)}...)`);
                    return false; // Already sent
                }

                sentEvents.add(eventKey);

                // Clean up old entries to prevent memory leaks (keep only last 100 events)
                if (sentEvents.size > 100) {
                    const eventsArray = Array.from(sentEvents);
                    sentEvents.clear();
                    // Keep the most recent 50 events
                    eventsArray.slice(-50).forEach(event => sentEvents.add(event));
                }

                return true;
            }

            if (btPixel.tagContent && (btPixel.tagContent.aTrackingType || btPixel.tagContent.aDynTags.content_name || btPixel.tagContent.aDynTags.content_ids)) {

                const trackingType = btPixel.tagContent.aTrackingType?.value;
                const dynTags = btPixel.tagContent.aDynTags;

                // Function to track events
                const trackEvent = (eventName, params) => {
                    // Enhanced validation for ViewContent events
                    if (eventName === "ViewContent") {
                        // Validate required parameters for ViewContent
                        if (!params.content_ids || (Array.isArray(params.content_ids) && params.content_ids.length === 0)) {
                            console.warn('Facebook Pixel: ViewContent event missing or empty content_ids');
                            return;
                        }

                        // Ensure content_type is always provided for ViewContent
                        if (!params.content_type) {
                            params.content_type = 'product'; // Default to product
                        }

                        // Enhanced currency validation
                        if (params.currency) {
                            if (typeof params.currency !== 'string' || params.currency.length !== 3 || !/^[A-Z]{3}$/.test(params.currency)) {
                                console.warn('Facebook Pixel: Invalid currency format for ViewContent event, using fallback');
                                params.currency = btPixel.pixelCurrency ? btPixel.pixelCurrency.toUpperCase() : 'EUR';
                            }
                        }

                        // Enhanced value validation
                        if (params.value !== undefined) {
                            const numValue = parseFloat(params.value);
                            if (isNaN(numValue) || numValue < 0) {
                                console.warn('Facebook Pixel: Invalid value for ViewContent event, removing value parameter');
                                delete params.value;
                            } else {
                                // Ensure value is properly formatted
                                params.value = numValue.toFixed(2);
                            }
                        }

                        // Validate content_name if provided
                        if (params.content_name && typeof params.content_name !== 'string') {
                            console.warn('Facebook Pixel: Invalid content_name for ViewContent event');
                            delete params.content_name;
                        }

                        // Validate content_category if provided
                        if (params.content_category && typeof params.content_category !== 'string') {
                            console.warn('Facebook Pixel: Invalid content_category for ViewContent event');
                            delete params.content_category;
                        }
                    }

                    // Enhanced validation for Purchase events
                    if (eventName === "Purchase") {
                        // Ensure required parameters are present
                        if (!params.content_ids) {
                            console.warn('Facebook Pixel: Purchase event missing content_ids');
                            return;
                        }

                        if (!params.currency) {
                            console.warn('Facebook Pixel: Purchase event missing currency');
                            params.currency = btPixel.pixelCurrency ? btPixel.pixelCurrency.toUpperCase() : 'EUR';
                        }

                        if (params.value === undefined || params.value === null) {
                            console.warn('Facebook Pixel: Purchase event missing value');
                            return;
                        }
                    }

                    // Enhanced validation for AddToCart events
                    if (eventName === "AddToCart") {
                        if (!params.content_ids) {
                            console.warn('Facebook Pixel: AddToCart event missing content_ids');
                            return;
                        }

                        if (!params.content_type) {
                            params.content_type = 'product';
                        }
                    }

                    // Generate consistent event_id for proper deduplication
                    const consistentEventId = generateConsistentEventId(eventName, params);

                    fbq(eventName === "ViewCategory" || eventName === "ViewContentHomepage" ? "trackCustom" : "track",
                        eventName,
                        params,
                        { 'eventID': consistentEventId }
                    );
                };

                // Common parameters for events
                const commonParams = {
                    content_name: dynTags.content_name?.value,
                    content_category: dynTags.content_category?.value,
                    content_type: dynTags.content_type?.value,
                };

                // Handle different event types
                switch (trackingType) {
                    case "ViewContent":
                        // Enhanced ViewContent parameter preparation
                        let currency = dynTags.currency?.value;
                        let value = dynTags.value?.value;
                        let contentIds = dynTags.content_ids?.value;
                        let contentName = dynTags.content_name?.value;
                        let contentCategory = dynTags.content_category?.value;

                        // Validate and ensure content_ids is present
                        if (!contentIds || (Array.isArray(contentIds) && contentIds.length === 0)) {
                            console.warn('Facebook Pixel: ViewContent event aborted - missing content_ids');
                            break;
                        }

                        // Ensure currency is uppercase and exactly 3 characters
                        if (currency && typeof currency === 'string') {
                            currency = currency.trim().toUpperCase();
                            if (currency.length !== 3 || !/^[A-Z]{3}$/.test(currency)) {
                                currency = btPixel.pixelCurrency ? btPixel.pixelCurrency.toUpperCase() : 'EUR';
                            }
                        } else {
                            currency = btPixel.pixelCurrency ? btPixel.pixelCurrency.toUpperCase() : 'EUR';
                        }

                        // Ensure value is a valid number with 2 decimal places
                        if (value !== undefined && value !== null && value !== '') {
                            const numValue = parseFloat(value);
                            if (!isNaN(numValue) && numValue >= 0) {
                                value = numValue.toFixed(2);
                            } else {
                                value = undefined; // Remove invalid value
                            }
                        }

                        // Sanitize content_name
                        if (contentName && typeof contentName === 'string') {
                            contentName = contentName.trim();
                            if (contentName === '') {
                                contentName = undefined;
                            }
                        }

                        // Sanitize content_category
                        if (contentCategory && typeof contentCategory === 'string') {
                            contentCategory = contentCategory.trim();
                            if (contentCategory === '') {
                                contentCategory = undefined;
                            }
                        }

                        const viewContentParams = {
                            content_ids: contentIds,
                            content_type: dynTags.content_type?.value || 'product', // Always ensure content_type is set
                        };

                        // Add optional parameters only if they're valid
                        if (contentName) {
                            viewContentParams.content_name = contentName;
                        }
                        if (contentCategory) {
                            viewContentParams.content_category = contentCategory;
                        }
                        if (currency) {
                            viewContentParams.currency = currency;
                        }
                        if (value !== undefined && value !== '0.00') {
                            viewContentParams.value = value;
                        }

                        // Enhanced deduplication check
                        if (shouldSendEvent('ViewContent', viewContentParams)) {
                            // Generate consistent event_id that will be shared with API
                            const sharedEventId = generateConsistentEventId('ViewContent', viewContentParams);

                            // Store the event_id globally for API use
                            window.btPixelSharedEventId = sharedEventId;

                            // Track with the shared event_id
                            fbq("track", "ViewContent", viewContentParams, { 'eventID': sharedEventId });
                        } else {
                            console.log('Facebook Pixel: ViewContent event skipped due to deduplication');
                        }
                        break;

                    case "ViewCategory":
                        let viewCategoryParams = {};
                        if (dynTags.content_ids) {
                            viewCategoryParams = {
                                ...commonParams,
                                content_ids: dynTags.content_ids.value,
                            };
                        } else if (dynTags.content_name) {
                            viewCategoryParams = commonParams;
                        }

                        // Generate shared event_id for ViewCategory
                        const sharedViewCategoryEventId = generateConsistentEventId('ViewCategory', viewCategoryParams);
                        window.btPixelSharedEventId = sharedViewCategoryEventId;

                        fbq("trackCustom", "ViewCategory", viewCategoryParams, { 'eventID': sharedViewCategoryEventId });
                        break;

                    case "ViewContentHomepage":
                        trackEvent("ViewContentHomepage", dynTags.content_ids?.value
                            ? { ...commonParams, content_ids: dynTags.content_ids.value }
                            : commonParams
                        );
                        break;

                    case "AddToCart":
                        let addToCartParams = {
                            content_ids: dynTags.content_ids?.value,
                            content_type: dynTags.content_type?.value,
                            value: dynTags.value?.value,
                            currency: dynTags.currency?.value,
                        };

                        // Generate shared event_id for AddToCart
                        const sharedAddToCartEventId = generateConsistentEventId('AddToCart', addToCartParams);
                        window.btPixelSharedEventId = sharedAddToCartEventId;

                        fbq("track", "AddToCart", addToCartParams, { 'eventID': sharedAddToCartEventId });
                        break;

                    case "Purchase":
                        trackEvent(trackingType, {
                            content_ids: dynTags.content_ids?.value,
                            content_type: dynTags.content_type?.value,
                            value: dynTags.value?.value,
                            currency: dynTags.currency?.value,
                        });
                        break;

                    case "Contact":
                        trackEvent("Contact", {});
                        break;

                    case "Search":
                        trackEvent("Search", {
                            content_ids: dynTags.content_ids?.value,
                            content_type: dynTags.content_type?.value,
                            query: dynTags.search_string?.value,
                        });
                        break;

                    case "InitiateCheckout":
                        trackEvent("InitiateCheckout", {});
                        break;
                }

                // Add to Wishlist event
                $(btPixel.btnAddToWishlist).on("click", function () {
                    trackEvent("AddToWishlist", { content_type: 'product' });
                });

                // Add Payment Info event
                $('.ps-shown-by-js').on("click", function () {
                    // Get cart value from btPixel if available
                    let paymentInfoParams = {};

                    // Check if we have cart data available
                    if (btPixel.tagContent &&
                        btPixel.tagContent.aDynTags &&
                        btPixel.tagContent.aDynTags.value &&
                        btPixel.tagContent.aDynTags.currency) {

                        paymentInfoParams = {
                            value: btPixel.tagContent.aDynTags.value.value,
                            currency: btPixel.tagContent.aDynTags.currency.value
                        };

                        // Ensure proper formatting of the value
                        if (paymentInfoParams.value) {
                            // Convert to float and format with 2 decimal places
                            const numValue = parseFloat(paymentInfoParams.value);
                            if (!isNaN(numValue)) {
                                paymentInfoParams.value = numValue.toFixed(2);
                            }
                        }
                    }

                    trackEvent("AddPaymentInfo", paymentInfoParams);

                    // Send API event if Conversion API is enabled
                    if (btPixel.useConversionApi == 1) {
                        $.ajax({
                            type: "POST",
                            url: btPixel.ajaxUrl,
                            dataType: "json",
                            data: {
                                ajax: 1,
                                action: "sendPaymentInfoToApi",
                                token: btPixel.token,
                                eventId: generateConsistentEventId('AddPaymentInfo', paymentInfoParams),
                            }
                        });
                    }
                });
            }

            if (typeof prestashop !== 'undefined') {
                prestashop.on(
                    'updateCart',
                    function (event) {
                        if (event && event.reason && event.reason.linkAction == "add-to-cart") {
                            //Check the variable for ipa and ip
                            var idProduct = 0;
                            var idProductAttribute = 0;

                            if (typeof event.reason.idProductAttribute !== "undefined") {
                                idProductAttribute = event.reason.idProductAttribute;
                            } else if (typeof event.resp.id_product_attribute !== "undefined") {
                                idProductAttribute = event.resp.id_product_attribute;
                            }

                            if (typeof event.reason.idProduct !== "undefined") {
                                idProduct = event.reason.idProduct;
                            } else if (typeof event.resp.id_product !== "undefined") {
                                idProduct = event.resp.id_product;
                            }

                                                    // First generate the shared event_id before making the AJAX call
                            const preliminaryAddToCartParams = {
                                content_ids: [idProduct], // Use basic product ID for preliminary generation
                                content_type: 'product',
                                value: 0, // Will be updated from AJAX response
                                currency: btPixel.pixelCurrency || 'EUR',
                            };

                            const sharedAddToCartEventId = generateConsistentEventId('AddToCart', preliminaryAddToCartParams);
                            window.btPixelSharedEventId = sharedAddToCartEventId;

                            $.ajax({
                                type: "POST",
                                url: btPixel.ajaxUrl,
                                dataType: "json",
                                data: {
                                    ajax: 1,
                                    action: "addToCart",
                                    id_product_attribute: idProductAttribute,
                                    id_product: idProduct,
                                    token: btPixel.token,
                                    shared_event_id: sharedAddToCartEventId,
                                },
                                success: function (jsonData, textStatus, jqXHR) {
                                    const addToCartParams = {
                                        content_ids: jsonData.content_ids,
                                        content_type: 'product',
                                        value: jsonData.value,
                                        currency: jsonData.currency,
                                    };

                                    fbq('set', 'autoConfig', false, btPixel.pixel_id);
                                    fbq("track", "AddToCart", addToCartParams, { 'eventID': sharedAddToCartEventId });
                                },
                            });
                        }
                    }
                );

                // Handle case for combination update
                prestashop.on(
                    'updatedProduct',
                    function (event) {
                        if (event) {
                            $.ajax({
                                type: "POST",
                                url: btPixel.ajaxUrl,
                                dataType: "json",
                                data: {
                                    ajax: 1,
                                    action: "updateCombination",
                                    id_product_attribute: event.id_product_attribute,
                                    id_product: $('input[name="id_product"').val(),
                                    token: btPixel.token,
                                },
                                success: function (jsonData, textStatus, jqXHR) {
                                    // Validate and format the response data
                                    if (jsonData.currency && jsonData.value) {
                                        // Ensure currency is uppercase and valid
                                        let currency = jsonData.currency.toString().trim().toUpperCase();
                                        if (currency.length !== 3 || !/^[A-Z]{3}$/.test(currency)) {
                                            currency = btPixel.pixelCurrency || 'EUR';
                                        }

                                        // Ensure value is properly formatted
                                        let value = parseFloat(jsonData.value);
                                        if (isNaN(value) || value < 0) {
                                            value = 0;
                                        }
                                        value = value.toFixed(2);

                                        const combinationParams = {
                                            content_name: jsonData.content_name,
                                            content_category: jsonData.content_category,
                                            content_ids: jsonData.content_id,
                                            content_type: 'product',
                                            currency: currency,
                                            value: value,
                                        };

                                        // Check for deduplication before sending
                                        if (shouldSendEvent('ViewContent', combinationParams)) {
                                            fbq('set', 'autoConfig', false, btPixel.pixel_id);
                                            const viewContentEventId = generateConsistentEventId('ViewContent', combinationParams);
                                            fbq("track", "ViewContent", combinationParams, { 'eventID': viewContentEventId });
                                        }
                                    }
                                },
                            });
                        }
                    }
                );
            }
        }
    }
});
