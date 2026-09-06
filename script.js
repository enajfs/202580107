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