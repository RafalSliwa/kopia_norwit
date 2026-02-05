/**
 * Shipping Countdown Module - JavaScript
 */
(function() {
    if (typeof shippingCountdown === 'undefined') {
        return;
    }

    var config = shippingCountdown;

    /**
     * Calculate time remaining until deadline
     * Handles weekend logic: Fri 13:00 - Mon 13:00 counts to Monday 13:00
     */
    function getTimeRemaining() {
        var now = new Date();
        var deadline = new Date();
        var dayOfWeek = now.getDay(); // 0 = Sunday, 1 = Monday, ..., 5 = Friday, 6 = Saturday

        deadline.setHours(config.hour, config.minute, 0, 0);

        // Check if we're in the "weekend period" (Fri after deadline, Sat, Sun, Mon before deadline)
        var isFridayAfterDeadline = (dayOfWeek === 5 && now >= deadline);
        var isSaturday = (dayOfWeek === 6);
        var isSunday = (dayOfWeek === 0);
        var isMondayBeforeDeadline = (dayOfWeek === 1 && now < deadline);

        var isWeekendPeriod = isFridayAfterDeadline || isSaturday || isSunday || isMondayBeforeDeadline;

        if (isWeekendPeriod) {
            // Calculate days until Monday
            var daysUntilMonday = 0;
            if (dayOfWeek === 5) daysUntilMonday = 3; // Friday -> Monday
            else if (dayOfWeek === 6) daysUntilMonday = 2; // Saturday -> Monday
            else if (dayOfWeek === 0) daysUntilMonday = 1; // Sunday -> Monday
            else if (dayOfWeek === 1) daysUntilMonday = 0; // Monday (before deadline)

            deadline.setDate(now.getDate() + daysUntilMonday);
            deadline.setHours(config.hour, config.minute, 0, 0);
        } else if (now >= deadline) {
            // Past today's deadline on a weekday (Mon-Thu after 13:00)
            deadline.setDate(deadline.getDate() + 1);
        }

        var diff = deadline - now;
        var hours = Math.floor(diff / (1000 * 60 * 60));
        var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((diff % (1000 * 60)) / 1000);

        // Check if we're counting to next business day
        var todayDeadline = new Date(now.getFullYear(), now.getMonth(), now.getDate(), config.hour, config.minute, 0, 0);
        var isNextDay = now >= todayDeadline || isWeekendPeriod;

        return {
            hours: hours,
            minutes: minutes,
            seconds: seconds,
            total: diff,
            isNextDay: isNextDay,
            isWeekendPeriod: isWeekendPeriod
        };
    }

    function padZero(num) {
        return num < 10 ? '0' + num : num;
    }

    function isWeekend() {
        var day = new Date().getDay();
        return day === 0 || day === 6;
    }


    /**
     * Position countdown between in-stock and out-of-stock products
     */
    function positionCountdown() {
        var wrapper = document.querySelector('.shipping-countdown-wrapper');
        var countdown = wrapper ? wrapper.querySelector('.js-shipping-countdown') : null;

        if (!countdown) {
            return;
        }

        var cartItems = document.querySelectorAll('.cart-items .cart-item[data-stock-available]');

        if (cartItems.length === 0) {
            wrapper.style.display = 'none';
            return;
        }

        // Find last in-stock item
        var lastInStockItem = null;
        var hasOutOfStock = false;

        cartItems.forEach(function(item) {
            if (item.getAttribute('data-stock-available') === '1') {
                lastInStockItem = item;
            } else {
                hasOutOfStock = true;
            }
        });

        // Remove existing inserted countdown if any
        var existingInserted = document.querySelector('.cart-item--shipping-countdown');
        if (existingInserted) {
            existingInserted.remove();
        }

        // If we have in-stock items, insert countdown after last one
        if (lastInStockItem) {
            var li = document.createElement('li');
            li.className = 'cart-item cart-item--shipping-countdown';
            li.appendChild(countdown.cloneNode(true));
            lastInStockItem.parentNode.insertBefore(li, lastInStockItem.nextSibling);
            wrapper.style.display = 'none';
        } else {
            wrapper.style.display = 'none';
        }
    }

    /**
     * Update countdown display
     */
    function updateCountdowns() {
        var countdownElements = document.querySelectorAll('.js-shipping-countdown');

        if (countdownElements.length === 0) {
            return;
        }

        var remaining = getTimeRemaining();

        countdownElements.forEach(function(element) {
            var prefixEl = element.querySelector('.js-countdown-prefix');
            var orderEl = element.querySelector('.js-countdown-order');
            var shipEl = element.querySelector('.js-countdown-ship');
            var timerEl = element.querySelector('.js-countdown-timer');

            if (!config.showOnWeekend && isWeekend()) {
                element.style.display = 'none';
                return;
            }

            element.style.display = '';

            var timeString = remaining.hours + 'h ' + padZero(remaining.minutes) + 'm ' + padZero(remaining.seconds) + 's';

            // Weekend period: same as next day message
            if (remaining.isWeekendPeriod) {
                element.classList.add('shipping-countdown--weekend');
                element.classList.remove('shipping-countdown--tomorrow');
                prefixEl.textContent = config.textPrefix + ' - ';
                orderEl.textContent = 'zamów teraz, a ';
                shipEl.textContent = 'wyślemy za ';
                timerEl.textContent = timeString;
            } else if (remaining.isNextDay) {
                // After deadline on weekday, counting to next day
                element.classList.add('shipping-countdown--tomorrow');
                element.classList.remove('shipping-countdown--weekend');
                prefixEl.textContent = config.textPrefix + ' - ';
                orderEl.textContent = 'zamów teraz, a ';
                shipEl.textContent = 'wyślemy za ';
                timerEl.textContent = timeString;
            } else {
                // Before deadline on weekday
                element.classList.remove('shipping-countdown--tomorrow');
                element.classList.remove('shipping-countdown--weekend');
                prefixEl.textContent = config.textPrefix + ' - ';
                orderEl.textContent = config.textOrder + ' ';
                shipEl.textContent = config.textShip + ' ';
                timerEl.textContent = timeString;
            }
        });
    }

    function initCountdown() {
        positionCountdown();
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountdown);
    } else {
        initCountdown();
    }

    // Listen for PrestaShop cart AJAX updates
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updatedCart', function() {
            setTimeout(function() {
                positionCountdown();
                updateCountdowns();
            }, 100);
        });
    }
})();
