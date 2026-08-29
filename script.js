const hamburger = document.querySelector('.hamburger');
const mobileMenu = document.querySelector('.mobile-menu');

hamburger.addEventListener('click', function() {
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
});


const carousel = document.querySelector('.carousel-track');
let isDragging = false;
let startX;
let scrollLeftStart;

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