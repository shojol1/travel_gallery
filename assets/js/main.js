/**
 * Main Application JavaScript
 * Travel Memories Website
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sticky Glassmorphic Navbar Scroll Effect
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. AJAX Trip Filtering System
    const filterButtons = document.querySelectorAll('.ajax-filter-btn');
    const tripsGrid = document.getElementById('trips-grid-container');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            filterButtons.forEach(b => b.classList.remove('active', 'btn-primary-custom'));
            filterButtons.forEach(b => b.classList.add('btn-outline-custom'));

            btn.classList.add('active', 'btn-primary-custom');
            btn.classList.remove('btn-outline-custom');

            const year = btn.getAttribute('data-year') || '';
            const district = btn.getAttribute('data-district') || '';

            if (tripsGrid) {
                fetchTripsAJAX(year, district);
            }
        });
    });

    function fetchTripsAJAX(year, district) {
        tripsGrid.style.opacity = '0.4';
        const url = `/travel-memories/api/filter-trips.php?year=${encodeURIComponent(year)}&district=${encodeURIComponent(district)}`;

        fetch(url)
            .then(res => res.text())
            .then(html => {
                tripsGrid.innerHTML = html;
                tripsGrid.style.opacity = '1';
                // Re-bind image lazy loading or animations if needed
            })
            .catch(err => {
                console.error('Error filtering trips:', err);
                tripsGrid.style.opacity = '1';
            });
    }

    // 3. Smooth Scroll to Anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                targetEl.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
