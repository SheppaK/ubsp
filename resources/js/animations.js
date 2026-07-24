import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { animate, stagger, inView, press } from 'motion';

gsap.registerPlugin(ScrollTrigger);

/** Reset inline opacity/transform GSAP may leave behind (e.g. after bfcache refresh). */
function resetAnimationStyles() {
    document.querySelectorAll(
        '.hero-animate, [data-animate="main"], [data-animate="sidebar"], .bento-card, .bento-card-dark, .bento-card-accent, .stat-card, .stagger-item'
    ).forEach((el) => {
        el.style.removeProperty('opacity');
        el.style.removeProperty('transform');
    });
}

function initPageEntrance() {
    const heroItems = document.querySelectorAll('.hero-animate');
    if (heroItems.length) {
        gsap.fromTo(
            heroItems,
            { opacity: 0, y: 32 },
            {
                opacity: 1,
                y: 0,
                duration: 0.7,
                stagger: 0.08,
                ease: 'power3.out',
                delay: 0.05,
                clearProps: 'opacity,transform',
            }
        );
    }

    const sidebar = document.querySelector('[data-animate="sidebar"]');
    if (sidebar) {
        gsap.fromTo(
            sidebar,
            { x: -24 },
            { x: 0, duration: 0.5, ease: 'power3.out', clearProps: 'transform' }
        );
    }

    // Do NOT animate main opacity — it caused the whole page to look faint when animation failed.
    const mainContent = document.querySelector('[data-animate="main"]');
    if (mainContent) {
        gsap.fromTo(
            mainContent,
            { y: 12 },
            { y: 0, duration: 0.45, ease: 'power2.out', delay: 0.1, clearProps: 'transform' }
        );
    }
}

function initScrollAnimations() {
    gsap.utils.toArray('.stagger-item').forEach((item, index) => {
        gsap.fromTo(
            item,
            { opacity: 0, y: 24 },
            {
                opacity: 1,
                y: 0,
                duration: 0.5,
                delay: (index % 4) * 0.06,
                ease: 'power2.out',
                clearProps: 'opacity,transform',
                scrollTrigger: {
                    trigger: item,
                    start: 'top 92%',
                    toggleActions: 'play none none none',
                },
            }
        );
    });

    // Skip cards already animated on page load via .hero-animate (avoid double opacity:0).
    inView(
        '.bento-card:not(.hero-animate), .bento-card-dark, .bento-card-accent',
        (element) => {
            animate(
                element,
                { opacity: [0, 1], transform: ['translateY(20px)', 'translateY(0)'] },
                { duration: 0.5, easing: [0.22, 1, 0.36, 1] }
            );
        },
        { margin: '-40px 0px -40px 0px' }
    );

    inView('.stat-card', (element) => {
        animate(
            element,
            { opacity: [0, 1], scale: [0.97, 1] },
            { duration: 0.45, easing: 'ease-out' }
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

function bootAnimations() {
    resetAnimationStyles();
    initPageEntrance();
    initScrollAnimations();
    initFloatingElements();
    initInteractions();
    initCounterAnimations();

    document.documentElement.classList.add('js-animations-ready');

    // Safety net: if anything is still invisible after animations, restore visibility.
    window.setTimeout(resetAnimationStyles, 1200);
}

document.addEventListener('DOMContentLoaded', bootAnimations);

// Fix faint page when restored from browser back/forward cache.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        resetAnimationStyles();
        bootAnimations();
    }
});

export { gsap, animate };
