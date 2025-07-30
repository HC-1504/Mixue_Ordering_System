// assets/js/main.js

// Sticky header show/hide on scroll up/down
(function() {
    let lastScroll = 0;
    const header = document.querySelector('header.sticky-header');
    if (!header) return; // Safeguard if header doesn't exist

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        if (currentScroll <= 0) {
            header.classList.remove('hide');
            header.classList.add('show');
            return;
        }
        if (currentScroll > lastScroll && !header.classList.contains('hide')) {
            // Scrolling down
            header.classList.remove('show');
            header.classList.add('hide');
        } else if (currentScroll < lastScroll && header.classList.contains('hide')) {
            // Scrolling up
            header.classList.remove('hide');
            header.classList.add('show');
        }
        lastScroll = currentScroll;
    });
})();

// Hamburger menu toggle for custom header
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger-menu');
    const navLinks = document.querySelector('.nav-links');
    if (!hamburger || !navLinks) return; // Safeguard

    const toggleMenu = () => navLinks.classList.toggle('open');
    
    hamburger.addEventListener('click', toggleMenu);
    hamburger.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleMenu();
        }
    });
});