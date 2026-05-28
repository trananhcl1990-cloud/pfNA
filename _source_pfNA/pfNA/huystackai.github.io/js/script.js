/*
    Interactive JavaScript for CV Website
    Author: Nguyen Gia Huy
    Description: Handles animations, language switching, and contact form behavior
*/

const DEFAULT_LANGUAGE = 'vi';
const FORMSUBMIT_ENDPOINT = 'https://formsubmit.co/ajax/giahuy.workhard@gmail.com';

const TYPING_TEXTS = {
    vi: [
        'Nhà phát triển AI',
        'Kỹ sư Machine Learning',
        'Chuyên gia Computer Vision',
        'Huấn luyện viên Yoga',
        'Nhà sáng tạo nội dung',
        'Người làm nghiên cứu'
    ],
    en: [
        'AI Developer',
        'Machine Learning Engineer',
        'Computer Vision Engineer',
        'Yoga Instructor',
        'Content Creator',
        'Researcher'
    ],
    zh: [
        'AI 开发者',
        '机器学习工程师',
        '计算机视觉工程师',
        '瑜伽教练',
        '内容创作者',
        '研究者'
    ]
};

class TypingAnimation {
    constructor(element, texts, typeSpeed = 100, deleteSpeed = 50, pauseTime = 2000) {
        this.element = element;
        this.typeSpeed = typeSpeed;
        this.deleteSpeed = deleteSpeed;
        this.pauseTime = pauseTime;
        this.timeoutId = null;
        this.setTexts(texts);
    }

    setTexts(texts) {
        this.texts = texts;
        this.currentIndex = 0;
        this.currentText = '';
        this.isDeleting = false;

        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        this.element.textContent = '';
        this.type();
    }

    type() {
        const fullText = this.texts[this.currentIndex];

        if (this.isDeleting) {
            this.currentText = fullText.substring(0, this.currentText.length - 1);
        } else {
            this.currentText = fullText.substring(0, this.currentText.length + 1);
        }

        this.element.textContent = this.currentText;

        let delay = this.isDeleting ? this.deleteSpeed : this.typeSpeed;

        if (!this.isDeleting && this.currentText === fullText) {
            delay = this.pauseTime;
            this.isDeleting = true;
        } else if (this.isDeleting && this.currentText === '') {
            this.isDeleting = false;
            this.currentIndex = (this.currentIndex + 1) % this.texts.length;
            delay = 500;
        }

        this.timeoutId = setTimeout(() => this.type(), delay);
    }
}

class ScrollAnimations {
    constructor() {
        this.initObserver();
        this.animateOnScroll();
    }

    initObserver() {
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('animate-in');

                if (entry.target.classList.contains('skill-progress')) {
                    const progress = entry.target.getAttribute('data-progress');
                    setTimeout(() => {
                        entry.target.style.width = `${progress}%`;
                    }, 200);
                }

                if (entry.target.classList.contains('stat-number')) {
                    this.animateCounter(entry.target);
                }

                if (entry.target.classList.contains('circle-progress')) {
                    this.animateCircleProgress(entry.target);
                }
            });
        }, options);
    }

    animateOnScroll() {
        const elements = document.querySelectorAll('.animate-on-scroll, .skill-progress, .stat-number, .circle-progress');
        elements.forEach((element) => this.observer.observe(element));
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target'), 10);
        if (Number.isNaN(target)) {
            return;
        }

        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };

        updateCounter();
    }

    animateCircleProgress(element) {
        const percent = parseInt(element.getAttribute('data-percent'), 10);
        if (Number.isNaN(percent) || percent < 0 || percent > 100) {
            return;
        }

        let currentPercent = 0;
        const duration = 2000;
        const increment = percent / (duration / 16);

        const updateProgress = () => {
            currentPercent += increment;
            if (currentPercent < percent) {
                element.style.setProperty('--progress', currentPercent);
                requestAnimationFrame(updateProgress);
            } else {
                element.style.setProperty('--progress', percent);
            }
        };

        setTimeout(() => {
            updateProgress();
        }, 300);
    }
}

class SmoothNavigation {
    constructor() {
        this.initSmoothScroll();
        this.initNavToggle();
        this.initScrollSpy();
        this.initBackToTop();
    }

    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (event) => {
                const targetSelector = anchor.getAttribute('href');
                if (!targetSelector || targetSelector === '#') {
                    return;
                }

                const target = document.querySelector(targetSelector);
                if (!target) {
                    return;
                }

                event.preventDefault();

                const navHeight = document.querySelector('.main-nav')?.offsetHeight || 0;
                const targetPosition = target.offsetTop - navHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                document.querySelector('.nav-menu')?.classList.remove('active');
                document.querySelector('.nav-toggle')?.classList.remove('active');
            });
        });
    }

    initNavToggle() {
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.querySelector('.nav-menu');

        if (!navToggle || !navMenu) {
            return;
        }

        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }

    initScrollSpy() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';

            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                if (window.scrollY >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach((link) => {
                link.classList.toggle('active', link.getAttribute('href') === `#${current}`);
            });
        });
    }

    initBackToTop() {
        const backToTop = document.getElementById('backToTop');
        if (!backToTop) {
            return;
        }

        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('visible', window.scrollY > 500);
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

class NavbarEffects {
    constructor() {
        this.initScrollEffect();
    }

    initScrollEffect() {
        const navbar = document.querySelector('.main-nav');
        if (!navbar) {
            return;
        }

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            navbar.classList.toggle('scrolled', currentScroll > 100);

            if (currentScroll > lastScroll && currentScroll > 500) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });
    }
}

class TiltEffect {
    constructor() {
        this.initTilt();
    }

    initTilt() {
        const tiltElements = document.querySelectorAll('[data-tilt]');

        tiltElements.forEach((element) => {
            element.addEventListener('mousemove', (event) => {
                const rect = element.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;

                element.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
            });

            element.addEventListener('mouseleave', () => {
                element.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    }
}

class ContactForm {
    constructor(getLanguage) {
        this.getLanguage = getLanguage;
        this.initForm();
    }

    initForm() {
        const form = document.querySelector('.form-container');
        if (!form) {
            return;
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.handleSubmit(form);
        });
    }

    async handleSubmit(form) {
        const lang = this.getLanguage();
        const subjectMetaInput = form.querySelector('#formSubjectMeta');

        const submitBtn = form.querySelector('.submit-btn');
        const label = submitBtn?.querySelector('span');
        const statusNote = form.querySelector('.form-note');
        const originalLabel = label?.textContent || '';
        const originalNote = statusNote?.textContent || '';

        const buttonStates = {
            vi: 'Đang gửi...',
            en: 'Sending...',
            zh: '发送中...'
        };

        const successMessages = {
            vi: 'Tin nhắn đã được gửi thành công. Tôi sẽ phản hồi bạn sớm nhất có thể.',
            en: 'Your message has been sent successfully. I will get back to you as soon as possible.',
            zh: '消息已发送成功，我会尽快回复你。'
        };

        const errorMessages = {
            vi: 'Chưa gửi được lúc này. Bạn thử lại sau hoặc gửi trực tiếp đến email của tôi nhé.',
            en: 'The message could not be sent right now. Please try again later or email me directly.',
            zh: '暂时无法发送消息。请稍后重试，或直接给我发邮件。'
        };

        const defaultSubjects = {
            vi: 'Liên hệ từ portfolio của Nguyễn Gia Huy',
            en: 'Portfolio contact from Nguyen Gia Huy',
            zh: '来自阮嘉辉作品集的联系'
        };

        if (subjectMetaInput) {
            const subjectInput = form.querySelector('#subject');
            subjectMetaInput.value = subjectInput?.value.trim() || defaultSubjects[lang];
        }

        const formData = new FormData(form);

        if (submitBtn && label) {
            submitBtn.disabled = true;
            label.textContent = buttonStates[lang];
        }
        if (statusNote) {
            statusNote.textContent = buttonStates[lang];
        }

        try {
            const response = await fetch(FORMSUBMIT_ENDPOINT, {
                method: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok || (result.success !== true && result.success !== 'true')) {
                throw new Error(result.message || 'FormSubmit request failed');
            }

            form.reset();
            if (subjectMetaInput) {
                subjectMetaInput.value = defaultSubjects[lang];
            }
            if (statusNote) {
                statusNote.textContent = successMessages[lang];
            }
        } catch (error) {
            console.error('FormSubmit send failed:', error);
            if (statusNote) {
                statusNote.textContent = errorMessages[lang];
            }
        } finally {
            if (submitBtn && label) {
                submitBtn.disabled = false;
                label.textContent = originalLabel;
            }

            setTimeout(() => {
                if (statusNote) {
                    statusNote.textContent = originalNote;
                }
            }, 5000);
        }
    }
}

class ParticleSystem {
    constructor() {
        this.initParticles();
    }

    initParticles() {
        const particles = document.querySelectorAll('.particle');
        particles.forEach((particle) => {
            const randomX = Math.random() * 100;
            const randomY = Math.random() * 100;
            const randomDelay = Math.random() * 5;
            const randomDuration = 6 + Math.random() * 4;

            particle.style.left = `${randomX}%`;
            particle.style.top = `${randomY}%`;
            particle.style.animationDelay = `${randomDelay}s`;
            particle.style.animationDuration = `${randomDuration}s`;
        });
    }
}

function createLanguageController(typingAnimation) {
    let currentLanguage = localStorage.getItem('portfolio-language') || DEFAULT_LANGUAGE;

    const applyTranslations = (lang) => {
        document.querySelectorAll('[data-vi][data-en]').forEach((element) => {
            const translatedValue = element.dataset[lang] || element.dataset.en;
            if (!translatedValue) {
                return;
            }

            if (element.dataset.i18nMode === 'html') {
                element.innerHTML = translatedValue;
                return;
            }

            if (element.dataset.i18nAttr) {
                element.setAttribute(element.dataset.i18nAttr, translatedValue);
                return;
            }

            if (element.children.length === 0) {
                element.textContent = translatedValue;
            }
        });

        document.querySelectorAll('[data-vi-placeholder][data-en-placeholder]').forEach((element) => {
            const placeholder = element.getAttribute(`data-${lang}-placeholder`) || element.getAttribute('data-en-placeholder');
            if (placeholder) {
                element.setAttribute('placeholder', placeholder);
            }
        });

        document.querySelectorAll('.lang-btn').forEach((button) => {
            button.classList.toggle('active', button.dataset.lang === lang);
        });

        document.documentElement.lang = lang === 'zh' ? 'zh-CN' : lang;
        localStorage.setItem('portfolio-language', lang);
        currentLanguage = lang;

        if (typingAnimation) {
            typingAnimation.setTexts(TYPING_TEXTS[lang]);
        }
    };

    return {
        getCurrentLanguage: () => currentLanguage,
        setLanguage: applyTranslations,
        init: () => applyTranslations(currentLanguage)
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const typingElement = document.querySelector('.typing-text');
    const typingAnimation = typingElement ? new TypingAnimation(typingElement, TYPING_TEXTS[DEFAULT_LANGUAGE]) : null;

    const languageController = createLanguageController(typingAnimation);

    new ScrollAnimations();
    new SmoothNavigation();
    new NavbarEffects();
    new TiltEffect();
    new ContactForm(languageController.getCurrentLanguage);
    new ParticleSystem();

    document.querySelectorAll('.lang-btn').forEach((button) => {
        button.addEventListener('click', () => {
            languageController.setLanguage(button.dataset.lang);
        });
    });

    languageController.init();

    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });

    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item) => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateX(10px)';
        });

        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateX(0)';
        });
    });

    const preloader = document.createElement('div');
    preloader.className = 'preloader';
    preloader.innerHTML = `
        <div class="preloader-content">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    `;
    document.body.appendChild(preloader);

    setTimeout(() => {
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.remove();
        }, 500);
    }, 1000);
});

window.addEventListener('load', () => {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
        });
    });

    images.forEach((img) => imageObserver.observe(img));
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    const navMenu = document.querySelector('.nav-menu');
    const navToggle = document.querySelector('.nav-toggle');

    if (navMenu?.classList.contains('active')) {
        navMenu.classList.remove('active');
        navToggle?.classList.remove('active');
    }
});
