    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');
    mobileToggle.addEventListener('click', () => { navLinks.classList.toggle('active'); });
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => { navLinks.classList.remove('active'); });
    });
    const observerOptions = { threshold: .15, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => { entry.target.classList.add('visible'); }, index * 100);
            }
        });
    }, observerOptions);
    document.querySelectorAll('.feature-card, .project-card, .value-card, .testimonial').forEach(el => { observer.observe(el); });
    
    // Contact form: open email client with prefilled data
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !message) {
            formMessage.textContent = 'All fields are required.';
            formMessage.className = 'form-message show error';
            return;
        }
        
        if (!email.includes('@') || !email.includes('.')) {
            formMessage.textContent = 'Please enter a valid email address.';
            formMessage.className = 'form-message show error';
            return;
        }
        
        const recipients = 'ollyphel@gmail.com,olamiposiayeriyina@gmail.com';
        const subject = encodeURIComponent('New contact from Invincible Studio');
        const body = encodeURIComponent(
            `Name: ${name}\n` +
            `Email: ${email}\n` +
            `Message: ${message}`
        );
        
        const mailtoLink = `mailto:${recipients}?subject=${subject}&body=${body}`;
        
        formMessage.textContent = 'Opening your email client...';
        formMessage.className = 'form-message show success';
        
        setTimeout(() => {
            window.location.href = mailtoLink;
            formMessage.classList.remove('show');
            contactForm.reset();
        }, 300);
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
            }
        });
    });
