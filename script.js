const hamburger = document.querySelector('.hamburger');
const mobileMenu = document.querySelector('.mobile-menu');

if (hamburger) {
hamburger.addEventListener('click', function() {
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
});
}


const carousel = document.querySelector('.carousel-track');
const prevButton = document.querySelector('.carousel-prev');
const nextButton = document.querySelector('.carousel-next');

let isDragging = false;
let startX;
let scrollLeftStart;

if (carousel) {
if (prevButton) {
    prevButton.addEventListener('click', function() {
        carousel.scrollBy({
            left: -carousel.clientWidth,
            behavior: 'smooth'
        });
    });
}

if (nextButton) {
    nextButton.addEventListener('click', function() {
        carousel.scrollBy({
            left: carousel.clientWidth,
            behavior: 'smooth'
        });
    });
}

carousel.addEventListener('mousedown', function(e) {
    isDragging = true;
    startX = e.pageX;
    scrollLeftStart = carousel.scrollLeft;
});

carousel.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    const distance = e.pageX - startX;
    carousel.scrollLeft = scrollLeftStart - distance;
});

carousel.addEventListener('mouseup', function() {
    isDragging = false;
});

carousel.addEventListener('mouseleave', function() {
    isDragging = false;
});

const dotsContainer = document.querySelector('.carousel-dots');
const cards = carousel.querySelectorAll('.product-card');

cards.forEach((card, index) => {
    const dot = document.createElement('div');
    dot.classList.add('dot');
    if (index === 0) dot.classList.add('active');

    dot.addEventListener('click', () => {
        carousel.scrollTo({ left: card.offsetLeft, behavior: 'smooth' });
    });

    dotsContainer.appendChild(dot);
});

function updateActiveDot() {
    const dots = dotsContainer.querySelectorAll('.dot');
    let closestIndex = 0;
    let closestDistance = Infinity;

    cards.forEach((card, index) => {
        const distance = Math.abs(card.offsetLeft - carousel.scrollLeft);
        if (distance < closestDistance) {
            closestDistance = distance;
            closestIndex = index;
        }
    });

    if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 10) 
        { closestIndex = cards.length - 5; }

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === closestIndex);
    });
}

carousel.addEventListener('scroll', updateActiveDot);

}

const tabButtons = document.querySelectorAll('.tab-btn');
const tabArrowLeft = document.querySelector('.tab-arrow-left');
const tabArrowRight = document.querySelector('.tab-arrow-right');

function showCategory(index) {
    if (index < 0) {
        index = tabButtons.length - 1;
    }

    if (index >= tabButtons.length) {
        index = 0;
    }

    tabButtons.forEach((button, i) => {
        button.classList.toggle('active', i === index);
    });

    document.querySelectorAll('.category-section').forEach(section => {
        section.style.display = 'none';
    });

    const category = tabButtons[index].getAttribute('data-category');
    const targetSection = document.getElementById(category + '-section');

    if (targetSection) {
        targetSection.style.display = 'block';
    }
}


// Click category tab
tabButtons.forEach((button, index) => {
    button.addEventListener('click', () => {
        showCategory(index);
    });
});


// Click <
if (tabArrowLeft) {
    tabArrowLeft.addEventListener('click', () => {
        const currentIndex = [...tabButtons].findIndex(
            button => button.classList.contains('active')
        );

        showCategory(currentIndex - 1);
    });
}


// Click >
if (tabArrowRight) {
    tabArrowRight.addEventListener('click', () => {
        const currentIndex = [...tabButtons].findIndex(
            button => button.classList.contains('active')
        );

        showCategory(currentIndex + 1);
    });
};