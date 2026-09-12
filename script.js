const hamburger = document.querySelector('.hamburger');
const mobileMenu = document.querySelector('.mobile-menu');

if (hamburger) {
hamburger.addEventListener('click', function() {
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
});
}


const carousel = document.querySelector('.carousel-track');
let isDragging = false;
let startX;
let scrollLeftStart;

if (carousel) {

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

    if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 5) 
        { closestIndex = cards.length - 1; }

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === closestIndex);
    });
}

carousel.addEventListener('scroll', updateActiveDot);

}


document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        const category = button.getAttribute('data-category');
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        document.querySelectorAll('.category-section').forEach(section => {
            section.style.display = 'none';
        });

        document.getElementById(category + '-section').style.display = 'block';
    });
});