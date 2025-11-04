document.addEventListener('DOMContentLoaded', function() {
    const carouselSlide = document.querySelector('.carousel-slide');
    const carouselWrapper = document.querySelector('.carousel-slide-wrapper');
    const images = document.querySelectorAll('.carousel-slide img');
    const dots = document.querySelectorAll('.carousel-dot');
    const prevButton = document.querySelector('.carousel-arrow.prev');
    const nextButton = document.querySelector('.carousel-arrow.next');
    let currentIndex = 0;
    const totalImages = images.length;
    let slideInterval;
    
    // Définir la largeur du conteneur de diapositives
    function updateCarouselSize() {
        const containerWidth = carouselWrapper.offsetWidth;
        const viewportWidth = window.innerWidth;
        
        // Ajuster la largeur de chaque image à 100% de la largeur de la fenêtre
        images.forEach(img => {
            img.style.width = `${viewportWidth}px`;
            img.style.height = '100%';
        });
        
        // Ajuster la largeur totale du conteneur de slides
        carouselSlide.style.width = `${viewportWidth * totalImages}px`;
        
        // Mettre à jour la position actuelle
        goToSlide(currentIndex);
    }

    // Set initial active dot
    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }

    // Go to specific slide
    function goToSlide(index) {
        currentIndex = (index + totalImages) % totalImages;
        const viewportWidth = window.innerWidth;
        const offset = -currentIndex * viewportWidth;
        carouselSlide.style.transform = `translateX(${offset}px)`;
        updateDots();
        
        // Réinitialiser le minuteur à chaque changement de slide
        resetInterval();
    }

    // Next slide
    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    // Previous slide
    function prevSlide() {
        goToSlide(currentIndex - 1);
    }
    
    // Réinitialiser l'intervalle de défilement automatique
    function resetInterval() {
        clearInterval(slideInterval);
        startAutoSlide();
    }

    // Auto slide
    function startAutoSlide() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    // Stop auto slide on hover
    function pauseAutoSlide() {
        clearInterval(slideInterval);
    }

    // Initialize carousel
    function initCarousel() {
        // Mettre à jour la taille initiale
        updateCarouselSize();
        
        // Mettre à jour la taille lors du redimensionnement de la fenêtre
        window.addEventListener('resize', updateCarouselSize);

        // Add event listeners to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
                clearInterval(slideInterval);
                startAutoSlide();
            });
        });

        // Add event listeners to navigation arrows
        prevButton.addEventListener('click', (e) => {
            e.preventDefault();
            prevSlide();
        });

        nextButton.addEventListener('click', (e) => {
            e.preventDefault();
            nextSlide();
        });

        // Start auto slide
        startAutoSlide();

        // Pause on hover
        const carouselContainer = document.querySelector('.carousel-container');
        carouselContainer.addEventListener('mouseenter', pauseAutoSlide);
        carouselContainer.addEventListener('mouseleave', startAutoSlide);

        // Touch events for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        carouselContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            pauseAutoSlide();
        }, {passive: true});

        carouselContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
            startAutoSlide();
        }, {passive: true});

        function handleSwipe() {
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) { // Minimum swipe distance
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
    }

    // Initialize the carousel
    initCarousel();
});
