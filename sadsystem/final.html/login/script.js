window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('.service-container, .team-container, .about, .contact, .location');
    
    sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= window.innerHeight && rect.bottom >= 0) {
            section.classList.add('show');
        }
    });
});


window.dispatchEvent(new Event('scroll'));


const navbar = document.querySelector('.navbar');
const toggleBtn = document.querySelector('.toggle-btn');

toggleBtn.addEventListener('click', () => {
    navbar.classList.toggle('toggle');
});