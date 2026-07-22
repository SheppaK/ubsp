import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { animate, stagger, inView, press } from 'motion';

gsap.registerPlugin(ScrollTrigger);

function initPageEntrance() {
    const heroItems = document.querySelectorAll('.hero-animate');
    if (heroItems.length) {
        gsap.from(heroItems, {
            opacity: 0,
            y: 48,
            duration: 0.9,
            stagger: 0.12,
            ease: 'power3.out',
            delay: 0.1,
        });
    }

    const sidebar = document.querySelector('[data-animate="sidebar"]');
    if (sidebar) {
        gsap.from(sidebar, {
            x: -32,
            opacity: 0,
            duration: 0.7,
            ease: 'power3.out',
        });
    }

    const mainContent = document.querySelector('[data-animate="main"]');
    if (mainContent) {
        gsap.from(mainContent, {
            opacity: 0,
            y: 20,
            duration: 0.6,
            ease: 'power2.out',
            delay: 0.2,
        });
    }
}

function initScrollAnimations() {
    gsap.utils.toArray('.stagger-item').forEach((item, index) => {
        gsap.from(item, {
            scrollTrigger: {
                trigger: item,
                start: 'top 92%',
                toggleActions: 'play none none none',
            },
            opacity: 0,
            y: 32,
            duration: 0.55,
            delay: (index % 4) * 0.08,
            ease: 'power2.out',
        });
    });

    inView('.bento-card, .bento-card-dark, .bento-card-accent', (element) => {
        animate(
            element,
            { opacity: [0, 1], transform: ['translateY(28px)', 'translateY(0)'] },
            { duration: 0.65, easing: [0.22, 1, 0.36, 1] }
        );
    }, { margin: '-40px 0px -40px 0px' });

    inView('.stat-card', (element) => {
        animate(
            element,
            { opacity: [0, 1], scale: [0.95, 1] },
            { duration: 0.5, easing: 'ease-out' }
        );
    }, { margin: '-20px' });
}

function initFloatingElements() {
    gsap.utils.toArray('.float-blob').forEach((blob, i) => {
        gsap.to(blob, {
            y: `-=${12 + i * 4}`,
            duration: 2.5 + i * 0.4,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
        });
    });

    gsap.utils.toArray('.float-shape').forEach((shape, i) => {
        gsap.to(shape, {
            rotation: i % 2 === 0 ? 8 : -8,
            duration: 4 + i,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
        });
    });
}

function initInteractions() {
    document.querySelectorAll('.btn-primary, .btn-secondary, .btn-accent, .bento-card, .surface-card-hover').forEach((el) => {
        press(el, () => {
            animate(el, { scale: 0.97 }, { duration: 0.1 });
        }, () => {
            animate(el, { scale: 1 }, { duration: 0.25, easing: 'ease-out' });
        });
    });

    document.querySelectorAll('[data-hover-lift]').forEach((el) => {
        el.addEventListener('mouseenter', () => {
            animate(el, { y: -4 }, { duration: 0.25, easing: 'ease-out' });
        });
        el.addEventListener('mouseleave', () => {
            animate(el, { y: 0 }, { duration: 0.3, easing: 'ease-out' });
        });
    });
}

function initCounterAnimations() {
    document.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseInt(el.dataset.count, 10);
        if (isNaN(target)) return;

        ScrollTrigger.create({
            trigger: el,
            start: 'top 90%',
            once: true,
            onEnter: () => {
                gsap.to({ val: 0 }, {
                    val: target,
                    duration: 1.2,
                    ease: 'power2.out',
                    onUpdate: function () {
                        el.textContent = Math.round(this.targets()[0].val);
                    },
                });
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPageEntrance();
    initScrollAnimations();
    initFloatingElements();
    initInteractions();
    initCounterAnimations();
});

export { gsap, animate };
