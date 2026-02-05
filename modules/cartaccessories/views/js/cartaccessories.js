/**
 * Cart Accessories Module - Carousel JavaScript
 * Matching the theme's accessories carousel behavior
 */
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('#cart-accessories-carousel');
    const prevButton = document.querySelector('.js-cart-acc-prev');
    const nextButton = document.querySelector('.js-cart-acc-next');

    if (!carousel) {
        return;
    }

    let totalVisibleItems = 6;
    let currentIndex = 0;

    // Function to update number of visible items based on screen width
    const updateTotalVisibleItems = () => {
        if (window.innerWidth <= 576) {
            totalVisibleItems = 2;
        } else if (window.innerWidth <= 966) {
            totalVisibleItems = 2;
        } else if (window.innerWidth <= 992) {
            totalVisibleItems = 4;
        } else {
            totalVisibleItems = 6;
        }
    };

    // Function to update carousel position
    const updateCarousel = () => {
        updateTotalVisibleItems();

        const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));
        const itemWidth = 100 / totalVisibleItems;

        // Limit the index to the maximum value of actual elements
        const maxIndex = Math.max(0, realItems.length - totalVisibleItems);
        currentIndex = Math.min(currentIndex, maxIndex);

        // Move carousel with smooth transition
        carousel.style.transform = `translateX(-${currentIndex * itemWidth}%)`;
    };

    // Handle clicking the "next" arrow
    if (nextButton) {
        nextButton.addEventListener('click', () => {
            const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));
            const maxIndex = Math.max(0, realItems.length - totalVisibleItems);
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateCarousel();
            }
        });
    }

    // Handle clicking the "prev" arrow
    if (prevButton) {
        prevButton.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });
    }

    // Gesture support for touch devices
    let startX = 0;
    let endX = 0;

    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });

    carousel.addEventListener('touchmove', (e) => {
        endX = e.touches[0].clientX;
    });

    carousel.addEventListener('touchend', () => {
        const deltaX = endX - startX;

        if (deltaX > 50) {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        } else if (deltaX < -50) {
            const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));
            const maxIndex = Math.max(0, realItems.length - totalVisibleItems);
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateCarousel();
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        updateCarousel();
    });

    // Initial update
    updateCarousel();
});
