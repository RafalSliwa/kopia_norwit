// Scroll page bottom to top
$(window).scroll(function () {
	if ($(this).scrollTop() > 500) {
		$('.top_button').fadeIn(500);
	} else {
		$('.top_button').fadeOut(500);
	}
});

$('.top_button').click(function (event) {
	event.preventDefault();
	$('html, body').animate({ scrollTop: 0 }, 800);
});


$(document).ready(function () {
    bindGrid();
});

function bindGrid() {
    var view = $.totalStorage("display");

    if (!view) {
        view = 'list';
        $.totalStorage("display", view);
    }

    display(view);

    if (view === 'list') {
        $('.display').find('li#list').addClass('selected');
    } else {
        $('.display').find('li#grid').addClass('selected');
    }

    $(document).on('click', '#grid', function (e) {
        e.preventDefault();
        display('grid');
        $.totalStorage("display", 'grid');
    });

    $(document).on('click', '#list', function (e) {
        e.preventDefault();
        display('list');
        $.totalStorage("display", 'list');
    });
}

function display(view) {
	var windowWidth = $(window).width();

	if (view == 'list') {
		if (windowWidth <= 966) {
			$('#products ul.product_list').removeClass('grid').addClass('list row');
			$('#products .product_list > li').removeClass('col-xs-12 col-sm-6 col-md-6 col-lg-5').addClass('col-xs-6');
		} else {
			$('#products ul.product_list').removeClass('grid').addClass('list row');
			$('#products .product_list > li').removeClass('col-xs-12 col-sm-6 col-md-6 col-lg-4').addClass('col-xs-12');
		}

		// Processing product HTML for list view
		$('#products .product_list > li').each(function (index, element) {
			var html = '';
			html = '<div class="product-miniature js-product-miniature" data-id-product="' + $(element).find('.product-miniature').data('id-product') + '" data-id-product-attribute="' + $(element).find('.product-miniature').data('id-product-attribute') + '" itemscope itemtype="http://schema.org/Product">';
			html += '<div class="thumbnail-container">' + $(element).find('.thumbnail-container').html() + '</div>';

			html += '<div class="product-description-information">';
			html += '<div class="product-description center-block">';

			html += '<h3 class="h3 product-title" itemprop="name">' + $(element).find('h3').html() + '</h3>';
			var comment = $(element).find('.comments_note').html();
			if (comment) {
				html += '<div class="comments_note">' + comment + '</div>';
			}
			html += '<div class="product-reference">' + $(element).find('.product-reference').html() + '</div>';
			html += '<div class="brand-title" itemprop="name">' + $(element).find('.brand-title').html() + '</div>';

			// Add product-features to center-block
			var productFeatures = $(element).find('.product-features').html();
			if (productFeatures) {
				html += '<div class="product-features">' + productFeatures + '</div>';
			}
			html += '</div>'; // Closing the center-block

			// Center-right: dla delivery, price, financing i actions
			html += '<div class="product-description center-right">';
			var deliveryMessage = $(element).find('.delivery-message-wrapper').html();
			if (deliveryMessage) {
				html += '<div class="delivery-message-wrapper">' + deliveryMessage + '</div>';
			}

			var price = $(element).find('.product-price-and-shipping').html();
			if (price) {
				html += '<div class="product-price-and-shipping">' + price + '</div>';
			}


			//var actions = $(element).find('.product-actions').html();
			//if (actions) {
			//	html += '<div class="product-actions">' + actions + '</div>';
			//}

			html += '</div>'; 

			html += '</div>'; 
			html += '</div>';

			$(element).html(html);
		});

		$('.display').find('li#list').addClass('selected');
			$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
	} else {
		// Layout for grid
		$('#products ul.product_list').removeClass('list').addClass('grid row');
		$('#products .product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-6 col-lg-4');

		$('#products .product_list > li').each(function (index, element) {
			var html = '';
			html += '<div class="product-miniature js-product-miniature" data-id-product="' + $(element).find('.product-miniature').data('id-product') + '" data-id-product-attribute="' + $(element).find('.product-miniature').data('id-product-attribute') + '" itemscope itemtype="http://schema.org/Product">';
			html += '<div class="thumbnail-container">' + $(element).find('.thumbnail-container').html() + '</div>';

			html += '<div class="product-description-information">';
			html += '<div class="product-description">';

			html += '<h3 class="h3 product-title" itemprop="name">' + $(element).find('h3').html() + '</h3>';
			html += '<div class="product-reference">' + $(element).find('.product-reference').html() + '</div>';
			html += '<div class="brand-title" itemprop="name">' + $(element).find('.brand-title').html() + '</div>';

			var comment = $(element).find('.comments_note').html();
			if (comment) {
				html += '<div class="comments_note">' + comment + '</div>';
			}
			//var actions = $(element).find('.product-actions').html();
			//if (actions) {
			//	html += '<div class="product-actions">' + actions + '</div>';
			//}

			var deliveryMessage = $(element).find('.delivery-message-wrapper').html();
			if (deliveryMessage) {
				html += '<div class="delivery-message-wrapper">' + deliveryMessage + '</div>';
			}

			var price = $(element).find('.product-price-and-shipping').html();
			if (price) {
				html += '<div class="product-price-and-shipping">' + price + '</div>';
			}

			var productFeatures = $(element).find('.product-features').html();
			if (productFeatures) {
				html += '<div class="product-features">' + productFeatures + '</div>';
			}

			html += '</div>'; 
			html += '</div>'; 
			html += '</div>'; 

			$(element).html(html);
		});

		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');
	}
}



// Handling dynamic resolution changes
$(window).on('resize', function () {
	var preferredView = $.totalStorage('display') || 'grid';
	display(preferredView);
});

// Calling the function at the start of the page
$(document).ready(function () {
	var preferredView = $.totalStorage('display') || 'grid';
	display(preferredView);
});

// Listening to AJAX events in PrestaShop
$(document).ajaxComplete(function (event, xhr, settings) {
	if (settings.url.indexOf('category') !== -1 || settings.url.indexOf('pagination') !== -1) {
		
		var preferredView = $.totalStorage('display') || 'grid';
		display(preferredView);
	}
});




// Filtr for category

document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (event) {
		const button = event.target.closest('.nr-btn');
		if (button) {
			event.preventDefault();
			const leftColumn = document.getElementById('left-column');
			if (leftColumn) {
				leftColumn.classList.remove('nr-left-column');
			}
		}
	});
});

/*
document.addEventListener("DOMContentLoaded", function () {
	var bottonElement = document.querySelector(".botton-category-description");
	var topElement = document.querySelector(".top-category-description");
	var bottonParent = bottonElement?.parentNode; // ZapamiÄtaj rodzica bottonElement
	var topParent = topElement?.parentNode; // ZapamiÄtaj rodzica topElement

	// Komentarze jako znaczniki miejsca
	var bottonPlaceholder = document.createComment("botton-category-description placeholder");
	var topPlaceholder = document.createComment("top-category-description placeholder");

	function toggleCategoryDescriptions() {
		// ObsĹuga botton-category-description
		if (window.innerWidth <= 966) {
			if (!bottonParent.contains(bottonElement)) {
				bottonParent.insertBefore(bottonElement, bottonPlaceholder.nextSibling); // PrzywrĂłÄ element
			}
		} else {
			if (bottonParent.contains(bottonElement)) {
				bottonParent.replaceChild(bottonPlaceholder, bottonElement); // UsuĹ element
			}
		}

		// ObsĹuga top-category-description
		if (window.innerWidth <= 966) {
			if (topParent.contains(topElement)) {
				topParent.replaceChild(topPlaceholder, topElement); // UsuĹ element
			}
		} else {
			if (!topParent.contains(topElement)) {
				topParent.insertBefore(topElement, topPlaceholder.nextSibling); // PrzywrĂłÄ element
			}
		}
	}

	// Wykonaj funkcjÄ przy zaĹadowaniu strony
	toggleCategoryDescriptions();

	// Wykonaj funkcjÄ przy zmianie rozmiaru okna
	window.addEventListener("resize", toggleCategoryDescriptions);
});
*/
document.addEventListener("DOMContentLoaded", function() {
	let element = document.querySelector(".pse-p");
	if (element) {
	  element.style.minHeight = element.clientHeight + "px"; 
	}
  });



/*======  Carousel Slider For Accessories Product ==== */

document.addEventListener('DOMContentLoaded', function () {
	const carousel = document.querySelector('#accessories-carousel');
	const prevButton = document.querySelector('.prev-btn');
	const nextButton = document.querySelector('.next-btn');

	let totalVisibleItems = 8; // Default number of visible elements
	let currentIndex = 0;

	if (carousel) {

	// Function for adding placeholders
	const addPlaceholders = () => {
		const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));
		const placeholdersNeeded = totalVisibleItems - (realItems.length % totalVisibleItems || totalVisibleItems);

		// Remove existing placeholders
		Array.from(carousel.children).forEach(item => {
			if (item.classList.contains('placeholder')) {
				item.remove();
			}
		});

		// Add new placeholders
		for (let i = 0; i < placeholdersNeeded; i++) {
			const placeholder = document.createElement('li');
			placeholder.classList.add('item', 'placeholder');
			placeholder.style.width = `calc(100% / ${totalVisibleItems})`;
			carousel.appendChild(placeholder);
		}
	};

	// Funkcja do aktualizacji liczby widocznych elementĂłw
	const updateTotalVisibleItems = () => {
		totalVisibleItems = window.innerWidth < 966 ? 2 : 8;
	};

	// Funkcja do aktualizacji karuzeli
	const updateCarousel = () => {
		updateTotalVisibleItems(); // Update the number of visible elements
		addPlaceholders(); // Add placeholders according to the new number of visible elements

		const itemWidth = carousel.getBoundingClientRect().width / totalVisibleItems;
		const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));

		// Limit the index to the maximum value of actual elements
		currentIndex = Math.min(currentIndex, realItems.length - totalVisibleItems);

		// Move carousel with smooth transition
		carousel.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
	};
	}
	if (nextButton) {
	// Handle clicking the "next" arrow
	nextButton.addEventListener('click', () => {
		const realItems = Array.from(carousel.children).filter(item => !item.classList.contains('placeholder'));
		if (currentIndex < realItems.length - totalVisibleItems) {
			currentIndex++;
			updateCarousel();
		}
	});
    }
	if (prevButton) {
	// Handle clicking on the "previous" arrow
	prevButton.addEventListener('click', () => {
		if (currentIndex > 0) {
			currentIndex--;
			updateCarousel();
		}
	});
	}

	// Gesture support
	let startX = 0;
	let endX = 0;
    if (carousel) {
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
			if (currentIndex < realItems.length - totalVisibleItems) {
				currentIndex++;
				updateCarousel();
			}
		}
	});
	}
	if (carousel) {
			
	window.addEventListener('resize', () => {
		updateCarousel();
	});

	
	updateCarousel();
	
	carousel.style.transition = 'transform 0.5s ease'; 
	}
});




document.addEventListener("DOMContentLoaded", function () {
	// Zapisz oryginalnego rodzica elementu 'search_widget'
	const searchWidget = document.getElementById("search_widget");
	const originalParent = searchWidget?.parentNode; // Oryginalny rodzic
	const targetContainer = document.querySelector(".ets_mm_megamenu_content_content");

	// Function that handles moving an element
	function moveElementIfNeeded() {
		if (window.innerWidth < 966) {
			// Move the element to a new container
			if (searchWidget && targetContainer && targetContainer !== searchWidget.parentNode) {
				targetContainer.appendChild(searchWidget);

			}
		} else {
			// Restore the element to its original parent
			if (searchWidget && originalParent && originalParent !== searchWidget.parentNode) {
				originalParent.appendChild(searchWidget);

			}
		}
	}

	
	moveElementIfNeeded();

	
	window.addEventListener("resize", moveElementIfNeeded);
});





function initMobileCarousel() {
  const carousel = document.querySelector('.nr-carousel'); // Carousel item list
  const dotsContainer = document.querySelector('.carousel-dots'); // Dots container
  const slides = document.querySelectorAll('.nr-carousel .thumb-container'); // Slides
  const totalSlides = slides.length;
  let currentIndex = 0;

   // Check if there are slides and carousel container
  if (!carousel || totalSlides === 0) {
    return;
  }

   // Reset transforms and classes (in case of re-init)
  carousel.style.transform = 'translateX(0)';
  if (dotsContainer) {
    dotsContainer.innerHTML = '';
    dotsContainer.style.display = '';
  }

  const carouselWrapper = document.querySelector('.nr-carousel-wrapper');
  if (carouselWrapper) {
    carouselWrapper.classList.remove('single-slide');
  }

   // Handle single slide
  if (totalSlides <= 1) {
    if (dotsContainer) {
      dotsContainer.style.display = 'none';
    }
    if (carouselWrapper) {
      carouselWrapper.classList.add('single-slide');
    }
    return;
  }

   // Create dots
  slides.forEach((_, index) => {
    const dot = document.createElement('div');
    dot.classList.add('dot');
    if (index === 0) dot.classList.add('active');
    dot.dataset.index = index;
    dotsContainer.appendChild(dot);
  });

  const dots = document.querySelectorAll('.carousel-dots .dot');

   // Update carousel
  const updateCarousel = (index) => {
    const offset = -index * 100;
    carousel.style.transform = `translateX(${offset}%)`;
    dots.forEach((dot) => dot.classList.remove('active'));
    dots[index].classList.add('active');
  };

   // Click on dots
  dots.forEach((dot) => {
    dot.addEventListener('click', (e) => {
      currentIndex = parseInt(e.target.dataset.index, 10);
      updateCarousel(currentIndex);
    });
  });

   // Handle gestures (touch)
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

    if (deltaX > 50 && currentIndex > 0) {
      currentIndex--;
      updateCarousel(currentIndex);
    } else if (deltaX < -50 && currentIndex < totalSlides - 1) {
      currentIndex++;
      updateCarousel(currentIndex);
    }
  });
}

 // Initialize on first page load
document.addEventListener('DOMContentLoaded', initMobileCarousel);

 // Initialize after variant change in PrestaShop
if (typeof prestashop !== 'undefined') {
  prestashop.on('updatedProduct', function () {
     // Wait a moment for DOM to update
    setTimeout(initMobileCarousel, 100);
  });
}



// Przechowuj referencje do usuniÄtych elementĂłw
let removedMobileDivs = [];
let removedDesktopDivs = [];

// Function that manages div visibility based on resolution
function manageDivsBasedOnResolution() {
	const mobileElements = document.querySelectorAll('.row.product-container.js-product-container.mobile');
	const desktopElements = document.querySelectorAll('.row.product-container.js-product-container.desktop');

	// Find the section <section id="main">
	const mainSection = document.querySelector('section#main');

	if (!mainSection) {
		// console.error("Could not find <section id='main'>. Make sure it exists.");
		return;
	}

	if (window.innerWidth > 966) {
		// PrzywrĂłÄ desktop divy jako drugi element w <section id="main">
		removedDesktopDivs.forEach(div => {
			if (mainSection.children.length > 1) {
				mainSection.insertBefore(div, mainSection.children[1]);
			} else {
				mainSection.appendChild(div);
			}
		});
		removedDesktopDivs = []; // Wyczyszczenie tablicy

		// UsuĹ mobile divy i przechowaj w tablicy
		mobileElements.forEach(element => {
			removedMobileDivs.push(element);
			element.remove();
		});
	} else {
		// PrzywrĂłÄ mobile divy jako pierwszy element w <section id="main">
		removedMobileDivs.forEach(div => {
			mainSection.prepend(div);
		});
		removedMobileDivs = []; // Wyczyszczenie tablicy

		// UsuĹ desktop divy i przechowaj w tablicy
		desktopElements.forEach(element => {
			removedDesktopDivs.push(element);
			element.remove();
		});
	}
}

// Funkcja obsĹugujÄca zmianÄ rozdzielczoĹci i wymuszajÄca natychmiastowe zmiany
function onResize() {
	manageDivsBasedOnResolution();
}

// Uruchom funkcjÄ przy zaĹadowaniu strony i nasĹuchuj na zmiany rozdzielczoĹci
document.addEventListener('DOMContentLoaded', () => {
	manageDivsBasedOnResolution(); // Wykonanie przy zaĹadowaniu
	window.addEventListener('resize', onResize); // Dynamiczna zmiana przy zmianie rozdzielczoĹci
});


/*======  Carousel Slider For Subcategory ==== */
document.addEventListener('DOMContentLoaded', () => {
	if (document.body.classList.contains('category')) {
	const carousel = document.querySelector('.nr-subcategory-carousel');
	const track = carousel.querySelector('.carousel-track');
	const dotsContainer = document.querySelector('.carousel-dots');
	const items = Array.from(track.children);
	let visibleItems = 10; // DomyĹlna liczba widocznych elementĂłw
	let itemWidth;
	let currentIndex = 0;
	let currentMode = ''; // Obecny tryb ('carousel' lub 'scroll')

	// Funkcja ustawiajÄca tryb dziaĹania karuzeli w zaleĹźnoĹci od rozdzielczoĹci
	const setCarouselMode = () => {
		if (window.innerWidth <= 966) {
			// PrzeĹÄcz na tryb natywnego przewijania (scroll)
			if (currentMode !== 'scroll') {
				currentMode = 'scroll'; // ZmieĹ tryb
				track.style.overflowX = 'auto'; // WĹÄcz przewijanie poziome
				track.style.display = 'flex'; // Zapewnij ukĹad poziomy
				track.style.scrollBehavior = 'smooth'; // Dodaj pĹynne przewijanie
				track.style.transform = ''; // UsuĹ transformacje karuzeli
				track.style.width = ''; // UsuĹ szerokoĹÄ kontenera
				dotsContainer.innerHTML = ''; // UsuĹ kropki
				dotsContainer.style.display = 'none'; // Ukryj kropki

				// Reset stylĂłw elementĂłw w trybie scroll
				items.forEach((item) => {
					item.style.flex = ''; // PrzywrĂłÄ natywne zachowanie flexbox
					item.style.width = ''; // UsuĹ szerokoĹÄ dla karuzeli
				});

				// Wymuszenie aktualizacji przeglÄdarki (usuwamy poprzednie style)
				requestAnimationFrame(() => {
					track.scrollLeft = 0; // Ustaw poczÄtkowÄ pozycjÄ przewijania
				});
			}
		} else {
			
			if (currentMode !== 'carousel') {
				currentMode = 'carousel'; 
				track.style.overflowX = 'hidden'; 
				track.style.scrollBehavior = ''; 
				track.style.display = 'flex'; 
				dotsContainer.style.display = 'flex'; 
				setVisibleItems(); 
				generateDots(); 
				updateCarousel();
			}
		}
	};

	// Function that sets the visibility of elements
	const setVisibleItems = () => {
		if (window.innerWidth <= 966) {
			visibleItems = 3.5; // Przy mniejszych rozdzielczoĹciach widaÄ 3,5 elementu
		} else {
			visibleItems = 10; // DomyĹlna liczba widocznych elementĂłw
		}

		
		const marginRight = parseFloat(getComputedStyle(items[0]).marginRight) || 0;
		itemWidth = (carousel.offsetWidth - (marginRight * (visibleItems - 1))) / visibleItems;

		
		if (currentMode === 'carousel') {
			track.style.width = `${items.length * (itemWidth + marginRight)}px`;
			items.forEach((item) => {
				item.style.flex = `0 0 auto`;
				item.style.width = `${itemWidth}px`;
			});
		}
	};

	// Function that updates the carousel position
	const updateCarousel = () => {
		if (currentMode === 'scroll') return; 

		const marginRight = parseFloat(getComputedStyle(items[0]).marginRight) || 0;
		const maxIndex = Math.max(0, items.length - visibleItems);

		
		currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));

		
		const offset = currentIndex * (itemWidth + marginRight);
		track.style.transform = `translateX(-${offset}px)`;

		
		const dots = Array.from(dotsContainer.children);
		dots.forEach((dot, index) => {
			dot.classList.toggle('active', index === currentIndex);
		});
	};

	// Function that generates navigation dots
	const generateDots = () => {
		dotsContainer.innerHTML = ''; 

		const diff = items.length - visibleItems;

		if (diff <= 0) {
			dotsContainer.style.display = 'none'; 
			return;
		}

		for (let i = 0; i <= diff; i++) {
			const dot = document.createElement('button');
			dot.classList.add('carousel-dot');
			dot.setAttribute('data-index', i); 
			dot.addEventListener('click', (e) => {
				currentIndex = parseInt(e.target.dataset.index, 10);
				updateCarousel();
			});
			dotsContainer.appendChild(dot);
		}
	};

	
	let startX = 0;
	let endX = 0;

	track.addEventListener('touchstart', (e) => {
		if (currentMode === 'carousel') return; 
		startX = e.touches[0].clientX;
	});

	track.addEventListener('touchmove', (e) => {
		if (currentMode === 'carousel') return;
		endX = e.touches[0].clientX;
	});

	track.addEventListener('touchend', () => {
		if (currentMode === 'carousel') return;

		const diff = startX - endX;
		const maxIndex = items.length - visibleItems;

		if (diff > 50 && currentIndex < maxIndex) {
			currentIndex++;
			updateCarousel();
		} else if (diff < -50 && currentIndex > 0) {
			currentIndex--;
			updateCarousel();
		}
	});

	
	window.addEventListener('resize', () => {
		setCarouselMode(); 
	});

	
	setCarouselMode();
}
});
document.addEventListener("DOMContentLoaded", function () {
    // Check if the body has the id 'category'
    if (document.body.id === "category") {
        const zamknijBtn = document.getElementById("zamknijoknodwa"); // Close button
        const leftColumn = document.getElementById("left-column"); // Left column
        const contentWrapper = document.getElementById("content-wrapper"); // Main content

         // Zamykamy lewą kolumnę
        zamknijBtn.addEventListener("click", function () {
            leftColumn.classList.add("nr-left-column");
            if (contentWrapper.classList.contains("hidden-md-down")) {
                contentWrapper.classList.remove("hidden-md-down");
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-tabs .nav-link");

    navLinks.forEach(link => {
      link.addEventListener("click", function () {
        navLinks.forEach(l => l.classList.remove("active"));
        this.classList.add("active");
      });
    });
  });

document.getElementById('phoneToggle').addEventListener('click', function () {
	this.classList.toggle('active');
});

const btn = document.getElementById('shakeBtn');

  function startShakeCycle() {
    btn.classList.add('shake');
    setTimeout(() => {
      btn.classList.remove('shake');
      setTimeout(startShakeCycle, 5000); // 5s przerwy
    }, 5000); // 5s animacji
  }

  startShakeCycle();