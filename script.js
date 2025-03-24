document.addEventListener('DOMContentLoaded', function() {
    // Header scroll effect
    const header = document.querySelector('header');
    const scrollThreshold = 100;

    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileMenuBtn.classList.toggle('active');
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                // Close mobile menu if open
                if (navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                }
                
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Testimonials slider
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    let currentSlide = 0;

    function showSlide(index) {
        testimonialSlides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        testimonialSlides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
            currentSlide = (currentSlide - 1 + testimonialSlides.length) % testimonialSlides.length;
            showSlide(currentSlide);
        });

        nextBtn.addEventListener('click', function() {
            currentSlide = (currentSlide + 1) % testimonialSlides.length;
            showSlide(currentSlide);
        });
    }

    if (dots.length > 0) {
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
    }

    // Auto-rotate testimonials
    const autoRotateInterval = 5000; // 5 seconds
    let testimonialInterval;

    function startAutoRotate() {
        testimonialInterval = setInterval(function() {
            currentSlide = (currentSlide + 1) % testimonialSlides.length;
            showSlide(currentSlide);
        }, autoRotateInterval);
    }

    function stopAutoRotate() {
        clearInterval(testimonialInterval);
    }

    if (testimonialSlides.length > 0) {
        startAutoRotate();

        // Pause auto-rotate on hover
        const testimonialSlider = document.querySelector('.testimonials-slider');
        if (testimonialSlider) {
            testimonialSlider.addEventListener('mouseenter', stopAutoRotate);
            testimonialSlider.addEventListener('mouseleave', startAutoRotate);
        }
    }

    // Form validation
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const requiredFields = contactForm.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            const emailField = contactForm.querySelector('input[type="email"]');
            if (emailField && emailField.value.trim()) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('error');
                }
            }
            
            if (isValid) {
                // Here you would normally send the form data to a server
                // For now, we'll just show a success message
                const formMessage = document.createElement('div');
                formMessage.className = 'form-message success';
                formMessage.textContent = 'Thank you for your message! We will get back to you soon.';
                
                contactForm.reset();
                contactForm.appendChild(formMessage);
                
                setTimeout(() => {
                    formMessage.remove();
                }, 5000);
            } else {
                const formMessage = document.createElement('div');
                formMessage.className = 'form-message error';
                formMessage.textContent = 'Please fill in all required fields correctly.';
                
                contactForm.appendChild(formMessage);
                
                setTimeout(() => {
                    formMessage.remove();
                }, 5000);
            }
        });
    }

    // Improved language toggle functionality
    const languageToggle = document.querySelector('.language-toggle');
    const langButtons = document.querySelectorAll('.lang-btn');
    
    // Function to set language with improved handling
    function setLanguage(lang) {
        // Get all elements with data-lang attribute
        const allLangElements = document.querySelectorAll('[data-lang]');
        
        // First hide all language elements
        allLangElements.forEach(el => {
            el.style.display = 'none';
            // Add fade-out class for smooth transition
            el.classList.remove('fade-in');
        });
        
        // Then show only the elements for the selected language
        const selectedElements = document.querySelectorAll(`[data-lang="${lang}"]`);
        selectedElements.forEach(el => {
            el.style.display = 'block';
            // Use setTimeout to allow CSS transitions to work
            setTimeout(() => {
                el.classList.add('fade-in');
            }, 10);
        });
        
        // Save preference to localStorage
        localStorage.setItem('preferredLanguage', lang);
        
        // Update active button state
        langButtons.forEach(btn => {
            if (btn.getAttribute('data-lang') === lang) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Update form placeholders and messages based on language
        updateFormLanguage(lang);
    }
    
    // Function to update form language
    function updateFormLanguage(lang) {
        const formElements = {
            nameField: document.querySelector('input[name="name"]'),
            emailField: document.querySelector('input[name="email"]'),
            phoneField: document.querySelector('input[name="phone"]'),
            messageField: document.querySelector('textarea[name="message"]'),
            submitButton: document.querySelector('button[type="submit"]')
        };
        
        if (lang === 'en') {
            if (formElements.nameField) formElements.nameField.placeholder = 'Your Name';
            if (formElements.emailField) formElements.emailField.placeholder = 'Your Email';
            if (formElements.phoneField) formElements.phoneField.placeholder = 'Your Phone';
            if (formElements.messageField) formElements.messageField.placeholder = 'Your Message';
            if (formElements.submitButton) formElements.submitButton.textContent = 'Submit';
        } else if (lang === 'pt') {
            if (formElements.nameField) formElements.nameField.placeholder = 'Seu Nome';
            if (formElements.emailField) formElements.emailField.placeholder = 'Seu Email';
            if (formElements.phoneField) formElements.phoneField.placeholder = 'Seu Telefone';
            if (formElements.messageField) formElements.messageField.placeholder = 'Sua Mensagem';
            if (formElements.submitButton) formElements.submitButton.textContent = 'Enviar';
        }
    }

    if (langButtons.length > 0) {
        // Check for saved language preference
        const savedLanguage = localStorage.getItem('preferredLanguage') || 'en';
        
        // Set initial language
        setLanguage(savedLanguage);
        
        // Add event listeners to language buttons
        langButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const lang = this.getAttribute('data-lang');
                setLanguage(lang);
            });
        });
    }

    // Animate elements on scroll with improved performance
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    // Use Intersection Observer for better performance
    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    // Unobserve after animation is triggered
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    } else {
        // Fallback for browsers that don't support Intersection Observer
        function checkIfInView() {
            const windowHeight = window.innerHeight;
            const windowTopPosition = window.scrollY;
            const windowBottomPosition = windowTopPosition + windowHeight;
            
            animatedElements.forEach(element => {
                const elementHeight = element.offsetHeight;
                const elementTopPosition = element.offsetTop;
                const elementBottomPosition = elementTopPosition + elementHeight;
                
                // Check if element is in view
                if (
                    (elementBottomPosition >= windowTopPosition) &&
                    (elementTopPosition <= windowBottomPosition)
                ) {
                    element.classList.add('animated');
                }
            });
        }
        
        window.addEventListener('scroll', checkIfInView);
        window.addEventListener('resize', checkIfInView);
        window.addEventListener('load', checkIfInView);
        checkIfInView();
    }

    // Initialize counters for stat numbers with improved animation
    const statNumbers = document.querySelectorAll('.stat-number');
    
    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const startTime = performance.now();
        const startValue = 0;
        
        function updateCounter(currentTime) {
            const elapsedTime = currentTime - startTime;
            
            if (elapsedTime < duration) {
                const progress = elapsedTime / duration;
                // Use easeOutQuad for smoother animation
                const easedProgress = 1 - (1 - progress) * (1 - progress);
                const currentValue = Math.floor(startValue + (target - startValue) * easedProgress);
                
                el.textContent = currentValue;
                requestAnimationFrame(updateCounter);
            } else {
                el.textContent = target;
            }
        }
        
        requestAnimationFrame(updateCounter);
    }
    
    // Use Intersection Observer for counter animation too
    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    animateCounter(entry.target);
                    entry.target.classList.add('counted');
                    counterObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        statNumbers.forEach(statNumber => {
            counterObserver.observe(statNumber);
        });
    } else {
        // Fallback
        function checkCounters() {
            statNumbers.forEach(statNumber => {
                if (
                    !statNumber.classList.contains('counted') &&
                    statNumber.getBoundingClientRect().top < window.innerHeight
                ) {
                    animateCounter(statNumber);
                    statNumber.classList.add('counted');
                }
            });
        }
        
        window.addEventListener('scroll', checkCounters);
        checkCounters();
    }
});
