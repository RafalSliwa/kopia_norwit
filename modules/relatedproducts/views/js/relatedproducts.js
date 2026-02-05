
function initCarousel() {
  const carousel = document.querySelector('.custom-carousel');
  const prevBtn = document.querySelector('.carousel-btn.prev');
  const nextBtn = document.querySelector('.carousel-btn.next');

  if (!carousel) {
    return;
  }

  const scrollAmount = 300;

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // 1. Try immediately
  initCarousel();

  // 2. Use MutationObserver if carousel loads later (e.g. in modal)
  const observer = new MutationObserver((mutations, obs) => {
    const carousel = document.querySelector('.custom-carousel');
    if (carousel) {
      initCarousel();
      obs.disconnect(); // Stop observing after finding carousel
    }
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
