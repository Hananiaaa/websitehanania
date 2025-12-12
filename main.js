document.addEventListener('DOMContentLoaded', () => {
    const animatedSections = document.querySelectorAll('.animated-fade-in');

    if (!animatedSections.length) return;

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null, // viewport
        threshold: 0.1, // 10% of the item must be visible
        rootMargin: '0px 0px -50px 0px' // trigger a little before it's fully in view
    });

    animatedSections.forEach(section => {
        observer.observe(section);
    });

    const themeSwitcher = document.getElementById('theme-switcher');
    const body = document.body;
    const themeIcon = themeSwitcher.querySelector('i');

    // Check for saved theme in local storage
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        body.classList.add(currentTheme);
        if (currentTheme === 'dark-theme') {
            themeIcon.classList.remove('bi-moon-stars-fill');
            themeIcon.classList.add('bi-sun-fill');
        }
    }

    themeSwitcher.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        
        // Update icon
        if (body.classList.contains('dark-theme')) {
            themeIcon.classList.remove('bi-moon-stars-fill');
            themeIcon.classList.add('bi-sun-fill');
            localStorage.setItem('theme', 'dark-theme');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-stars-fill');
            localStorage.removeItem('theme');
        }
    });
});
