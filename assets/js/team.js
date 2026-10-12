/**
 * Corporate page scripts for The Crisis Academy
 */

/**
 * Handle hover interaction for accordion lists.
 * Scoped to each container (.accordion-interactive-list) so multiple sections
 * manage their active item independently.
 */
function initAccordionInteractiveList() {
    const items = document.querySelectorAll('.accordion-interactive-list .accordion-item');
    if (!items.length) return;

    items.forEach(item => {
        const handleActivate = () => {
            const container = item.closest('.accordion-interactive-list');
            if (!container) return;

            const currentActive = container.querySelector('.accordion-item.active');
            if (currentActive && currentActive !== item) {
                currentActive.classList.remove('active');
            }

            item.classList.add('active');

            // If the item belongs to a section with linked targets (e.g. radar in #hearings)
            const dept = item.dataset.department;
            if (dept) {
                const section = item.closest('section');
                if (section) {
                    const targets = section.querySelectorAll('[data-target]');
                    targets.forEach(target => {
                        target.classList.toggle('active', target.dataset.target === dept);
                    });
                }
            }
        };

        item.addEventListener('mouseenter', handleActivate);
        item.addEventListener('click', handleActivate);
    });

    // Bidirectional sync: hovering or clicking radar quadrants activates the corresponding accordion item
    const radarQuadrants = document.querySelectorAll('.radar-quadrant[data-target]');
    radarQuadrants.forEach(quadrant => {
        const targetDept = quadrant.dataset.target;
        const section = quadrant.closest('section');
        if (!section) return;

        const targetAccordionItem = section.querySelector(`.accordion-item[data-department="${targetDept}"]`);
        if (!targetAccordionItem) return;

        const activateFromRadar = () => {
            const container = targetAccordionItem.closest('.accordion-interactive-list');
            if (!container) return;

            const currentActive = container.querySelector('.accordion-item.active');
            if (currentActive && currentActive !== targetAccordionItem) {
                currentActive.classList.remove('active');
            }
            targetAccordionItem.classList.add('active');

            const allQuadrants = section.querySelectorAll('.radar-quadrant[data-target]');
            allQuadrants.forEach(q => {
                q.classList.toggle('active', q === quadrant);
            });
        };

        quadrant.addEventListener('mouseenter', activateFromRadar);
        quadrant.addEventListener('click', activateFromRadar);
    });
}

/**
 * Reusable Points Slideshow Component (.points-slideshow).
 * Supports automatic loop crossfade, hover pause, and interactive pagination dots.
 * Can be reused across any section or template.
 */
function initPointsSlideshow(container, options = {}) {
    if (!container) return null;

    const pointsList = container.querySelector('.card-points, .trouble-card-points, ul');
    if (!pointsList) return null;

    const items = Array.from(pointsList.querySelectorAll('li'));
    if (items.length <= 1) return null;

    let nav = container.querySelector('.points-nav, .trouble-points-nav');
    if (!nav) {
        nav = document.createElement('div');
        nav.className = 'points-nav';
        nav.setAttribute('role', 'tablist');
        nav.setAttribute('aria-label', 'Puntos clave');
        pointsList.after(nav);
    }

    let dots = Array.from(nav.querySelectorAll('.point-dot, .trouble-point-dot'));
    if (dots.length !== items.length) {
        nav.innerHTML = '';
        dots = items.map((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'point-dot' + (i === 0 ? ' is-active' : '');
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
            dot.setAttribute('aria-label', `Punto ${i + 1} de ${items.length}`);
            nav.appendChild(dot);
            return dot;
        });
    }

    let activePointIdx = 0;
    let pointTimer = null;
    let isHovered = false;
    const pointDuration = options.duration || 3000;

    function setPoint(idx) {
        activePointIdx = (idx + items.length) % items.length;
        items.forEach((item, i) => {
            item.classList.toggle('is-active', i === activePointIdx);
        });
        dots.forEach((dot, i) => {
            const isActive = i === activePointIdx;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        if (typeof options.onChange === 'function') {
            options.onChange(activePointIdx);
        }
    }

    function nextPoint() {
        setPoint(activePointIdx + 1);
    }

    const parentBlock = container.closest('.block');

    function start() {
        stop();
        if (isHovered) return;
        if (parentBlock && parentBlock.classList.contains('is-bottom')) return;
        pointTimer = setInterval(nextPoint, pointDuration);
    }

    function stop() {
        if (pointTimer) {
            clearInterval(pointTimer);
            pointTimer = null;
        }
    }

    function reset() {
        stop();
        setPoint(0);
    }

    dots.forEach((dot, dotIdx) => {
        dot.addEventListener('click', (e) => {
            e.stopPropagation();
            setPoint(dotIdx);
            start();
            if (typeof options.onDotClick === 'function') {
                options.onDotClick(dotIdx);
            }
        });
    });

    const hoverTarget = options.hoverTarget || container;
    hoverTarget.addEventListener('mouseenter', () => {
        isHovered = true;
        stop();
    });
    hoverTarget.addEventListener('mouseleave', () => {
        isHovered = false;
        if (options.shouldResume ? options.shouldResume() : true) {
            start();
        }
    });

    // Pause points slideshow if parent section is covered by .is-bottom
    if (parentBlock) {
        parentBlock.addEventListener('block:bottomChange', (e) => {
            if (e.detail.isBottom) {
                stop();
            } else if (!isHovered && (options.shouldResume ? options.shouldResume() : true)) {
                start();
            }
        });
    }

    setPoint(0);

    return {
        start,
        stop,
        reset,
        setPoint,
        getCurrentIndex: () => activePointIdx
    };
}
window.initPointsSlideshow = initPointsSlideshow;

/**
 * Track mouse position inside cards for dynamic glow effects.
 * Updates --mouse-x and --mouse-y CSS variables.
 *
 * @param {string} selector - CSS selector for target elements.
 */
function initCardGlowEffect(selector = '.accordion-item') {
    const cards = document.querySelectorAll(selector);
    if (!cards.length) return;

    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            if (window.innerWidth <= 768) return;

            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
}

/**
 * Sticky Overlap Effect
 * Sections stack on top of each other as the user scrolls down.
 * For sections taller than the viewport, a negative `top` is used so the
 * section only sticks once the user has scrolled to its bottom.
 */
function initStickyOverlapEffect() {
    const blocks = Array.from(document.querySelectorAll('.page-template-corporate .site-main > .block, .site-main > .block'));
    if (blocks.length < 2) return;

    // Recalculate `top` for each block based on its full content height.
    function applyStickyTops() {
        if (window.innerWidth < 1024) {
            blocks.forEach(block => {
                block.style.position = '';
                block.style.zIndex = '';
                block.style.top = '';
                block.classList.remove('is-bottom');
            });
            return;
        }

        const vh = window.innerHeight;
        blocks.forEach((block, index) => {
            block.style.position = 'sticky';
            block.style.zIndex = index + 1;
            const bh = block.scrollHeight;
            // Negative top: section scrolls until its bottom hits the viewport bottom,
            // then sticks — ensuring the user sees ALL content before overlap.
            block.style.top = bh > vh ? `${vh - bh}px` : '0px';
        });
    }

    applyStickyTops(); // Initial estimate

    // Re-run after all resources (images, fonts) are loaded
    window.addEventListener('load', applyStickyTops);
    window.addEventListener('resize', applyStickyTops, { passive: true });

    // Re-run if any section changes its rendered size
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(applyStickyTops);
        blocks.forEach(block => ro.observe(block));
    }

    function updateOverlap() {
        if (window.innerWidth < 1024) {
            blocks.forEach(block => {
                if (block.classList.contains('is-bottom')) {
                    block.classList.remove('is-bottom');
                    block.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: false } }));
                }
            });
            return;
        }

        const states = [];
        const threshold = window.innerHeight * 0.5;
        for (let i = 0; i < blocks.length - 1; i++) {
            const nextTop = blocks[i + 1].getBoundingClientRect().top;
            states.push(nextTop <= threshold);
        }

        for (let i = 0; i < states.length; i++) {
            const block = blocks[i];
            const shouldBeBottom = states[i];
            const wasBottom = block.classList.contains('is-bottom');

            if (shouldBeBottom !== wasBottom) {
                block.classList.toggle('is-bottom', shouldBeBottom);
                block.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: shouldBeBottom } }));
            }
        }

        const lastBlock = blocks[blocks.length - 1];
        if (lastBlock && lastBlock.classList.contains('is-bottom')) {
            lastBlock.classList.remove('is-bottom');
            lastBlock.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: false } }));
        }
    }

    window.addEventListener('scroll', updateOverlap, { passive: true });
    updateOverlap();
}

/**
 * Viewport Observer for Page Blocks
 * Toggles .in-view class on blocks to pause/resume animations when scrolled into view.
 */
function initBlockViewportObserver() {
    const blocks = document.querySelectorAll('.page-template-team .site-main > .block, .site-main > .block');
    if (blocks.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('in-view', entry.isIntersecting);
        });
    }, {
        threshold: 0.01 // Trigger as soon as 1% of the section enters the screen
    });

    blocks.forEach(block => observer.observe(block));
}

/**
 * Unified Entrance Animations
 * Integrates .pretext-reveal, .title-reveal, .card-reveal, and .object-reveal.
 * - Instantly reveals elements already scrolled past (e.g. on page reload or hash navigation).
 * - Reveals in-viewport elements using a rapid, non-blocking spatial cascade (50ms per item, capped at 300ms max).
 * - Handles sticky overlap without blocking or causing visual delays.
 */
function initUnifiedAnimations() {
    const isMobile = window.innerWidth < 768;
    const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%&';

    // Helper: GSAP-driven scramble decipher effect for pretext elements
    function scramble(el, originalText, callback) {
        if (el._scrambleTween) el._scrambleTween.kill();

        const len = originalText.length;
        if (!len) {
            el.classList.add('is-visible');
            gsap.set(el, { opacity: 1 });
            if (callback) callback();
            return;
        }

        el.classList.add('is-visible');

        // Smooth fade-in concurrently with the scramble
        gsap.to(el, { opacity: 1, duration: 0.35, ease: 'power1.out' });

        // Deliberate duration (0.75s to 1.15s) so the decipher effect is clearly appreciated
        const duration = Math.min(Math.max(0.65 + len * 0.018, 0.75), 1.15);
        const state = { progress: 0 };

        el._scrambleTween = gsap.to(state, {
            progress: 1,
            duration: duration,
            ease: 'power2.out',
            onUpdate: () => {
                const lockedCount = Math.floor(state.progress * len);
                let output = '';
                for (let i = 0; i < len; i++) {
                    if (originalText[i] === ' ') {
                        output += ' ';
                    } else if (i < lockedCount) {
                        output += originalText[i];
                    } else {
                        output += CHARS[Math.floor(Math.random() * CHARS.length)];
                    }
                }
                el.textContent = output;
            },
            onComplete: () => {
                el.textContent = originalText;
                el._scrambleTween = null;
                if (callback) callback();
            }
        });
    }

    // Helper: wrap words inside title for word-by-word fade-in
    function wrapWords(el) {
        if (isMobile) return; // Skip word fragmentation into spans on mobile (<768px)
        const walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
        const textNodes = [];
        let node;
        while ((node = walker.nextNode())) textNodes.push(node);

        let wordIndex = 0;

        textNodes.forEach(textNode => {
            const parts = textNode.textContent.split(/(\s+)/);
            const frag = document.createDocumentFragment();

            parts.forEach(part => {
                if (!part || /^\s+$/.test(part)) {
                    frag.appendChild(document.createTextNode(part || ''));
                } else {
                    const span = document.createElement('span');
                    span.className = 'title-word';
                    span.style.setProperty('--word-index', wordIndex++);
                    span.textContent = part;
                    frag.appendChild(span);
                }
            });

            textNode.parentNode.replaceChild(frag, textNode);
        });
    }

    // Helper: Reveal a single element
    function revealElement(el, instant = false) {
        if (el.classList.contains('is-visible')) return;

        if (instant || isMobile) {
            if (el._scrambleTween) el._scrambleTween.kill();
            if (el._titleTween) el._titleTween.kill();
            if (el._originalText) el.textContent = el._originalText;
            el.classList.add('is-visible');
            el.style.opacity = '1';
            const words = el.querySelectorAll('.title-word');
            if (words.length) {
                words.forEach(w => {
                    w.style.opacity = '1';
                    w.style.transform = 'none';
                });
            }
            return;
        }

        if (el.classList.contains('pretext-reveal')) {
            scramble(el, el._originalText || el.textContent.trim());
        } else if (el.classList.contains('title-reveal')) {
            const words = el.querySelectorAll('.title-word');
            if (words.length === 0) {
                if (el._titleTween) el._titleTween.kill();
                el.classList.add('is-visible');
                gsap.set(el, { opacity: 1 });
            } else {
                if (el._titleTween) el._titleTween.kill();
                el.classList.add('is-visible');
                gsap.set(el, { opacity: 1 });
                // Fluid word-by-word entrance from CSS initial state
                el._titleTween = gsap.to(words, {
                    opacity: 1,
                    y: '0em',
                    duration: 0.9,
                    stagger: 0.075,
                    ease: 'power3.out',
                    onComplete: () => {
                        el._titleTween = null;
                    }
                });
            }
        } else {
            el.classList.add('is-visible');
        }
    }

    // Prepare Title elements
    document.querySelectorAll('.title-reveal').forEach(el => {
        if (el.dataset.revealedInit) return;
        el.dataset.revealedInit = 'true';
        el.setAttribute('aria-label', el.textContent.trim());
        wrapWords(el);
    });

    // Prepare Pretext elements
    document.querySelectorAll('.pretext-reveal').forEach(el => {
        if (el.dataset.revealedInit) return;
        el.dataset.revealedInit = 'true';
        const originalText = el.textContent.trim();
        el.setAttribute('aria-label', originalText);
        el._originalText = originalText;
    });

    const selectors = '.pretext-reveal, .title-reveal, .card-reveal, .object-reveal, #hero .page-title';
    const allTargets = Array.from(document.querySelectorAll(selectors));

    // Mobile (< 768px): Instantly reveal elements without observer overhead to eliminate scroll jank
    if (isMobile) {
        allTargets.forEach(el => revealElement(el, true));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        const visibleEntries = entries.filter(e => e.isIntersecting && !e.target.classList.contains('is-visible'));
        if (visibleEntries.length === 0) return;

        const instantList = [];
        const staggerList = [];

        visibleEntries.forEach(entry => {
            io.unobserve(entry.target);
            const el = entry.target;
            const rect = el.getBoundingClientRect();
            const block = el.closest('.block');

            // If the element is already above the viewport or inside a covered sticky block (.is-bottom),
            // reveal it immediately without any queue or animation delay
            if (rect.bottom <= 0 || (block && block.classList.contains('is-bottom'))) {
                instantList.push(el);
            } else {
                staggerList.push(el);
            }
        });

        // Instant reveal for elements already scrolled past
        instantList.forEach(el => revealElement(el, true));

        // Spatial sort (top-to-bottom, left-to-right) for elements visible in the active viewport
        staggerList.sort((a, b) => {
            const rectA = a.getBoundingClientRect();
            const rectB = b.getBoundingClientRect();
            if (Math.abs(rectA.top - rectB.top) < 40) {
                return rectA.left - rectB.left;
            }
            return rectA.top - rectB.top;
        });

        function scheduleReveal(el, delay) {
            const isHero = el.closest('#hero');
            if (isHero && !window.__heroCanvasReady) {
                let executed = false;
                const onReady = () => {
                    if (executed) return;
                    executed = true;
                    window.removeEventListener('hero:canvas-ready', onReady);
                    setTimeout(() => revealElement(el, false), delay);
                };
                window.addEventListener('hero:canvas-ready', onReady);
                setTimeout(onReady, 500); // Safety fallback
                return;
            }
            if (delay === 0) {
                revealElement(el, false);
            } else {
                setTimeout(() => revealElement(el, false), delay);
            }
        }

        // Rapid, non-blocking stagger (50ms per item, capped at 300ms max so no element waits long)
        staggerList.forEach((el, index) => {
            const delay = Math.min(index * 50, 300);
            scheduleReveal(el, delay);
        });

    }, {
        threshold: 0.05,
        rootMargin: '0px 0px -20px 0px'
    });

    // Check all targets on initialization:
    // If element is already above the current viewport, reveal immediately without observing
    allTargets.forEach(el => {
        const rect = el.getBoundingClientRect();
        const block = el.closest('.block');
        if (rect.bottom < 0 || (block && block.classList.contains('is-bottom'))) {
            revealElement(el, true);
        } else {
            io.observe(el);
        }
    });
}

/**
 * Hero Data-Block Split-Flap 3D Display (#hero-data-board).
 * Transforms the entire .data-block into a mechanical split-flap card divided horizontally,
 * flipping downward in 3D while preserving the block's isometric perspective.
 */
function initHeroDataBlockSplitFlap() {
    const container = document.getElementById('hero-data-board');
    if (!container) return;

    const dataScript = container.querySelector('.db-data-source');
    if (!dataScript) return;

    let items = [];
    try {
        items = JSON.parse(dataScript.textContent.trim());
    } catch (e) {
        console.error('Data-block split-flap JSON parse error:', e);
        return;
    }
    if (!items.length) return;

    const dataBlock = container.querySelector('.data-block');
    const plateTopBg = container.querySelector('.db-plate-top-bg');
    const plateBottomBg = container.querySelector('.db-plate-bottom-bg');
    const leafTop = container.querySelector('.db-leaf-top');
    const leafTopShadow = leafTop ? leafTop.querySelector('.db-shadow') : null;
    const leafBottom = container.querySelector('.db-leaf-bottom');
    const leafBottomShadow = leafBottom ? leafBottom.querySelector('.db-shadow') : null;
    const dots = Array.from(container.querySelectorAll('.point-dot'));

    if (!dataBlock || !plateTopBg || !plateBottomBg || !leafTop || !leafBottom) return;

    let currentIndex = 0;
    let isFlipping = false;
    let timer = null;
    let isHovered = false;
    const intervalDuration = 3500;

    function updatePlateText(plate, item) {
        if (!plate || !item) return;
        const num = plate.querySelector('.stat-number');
        const lbl = plate.querySelector('.stat-label');
        if (num) num.textContent = item.number;
        if (lbl) lbl.textContent = item.label;
    }

    // Initialize plate values to resting state (item 0)
    updatePlateText(plateTopBg, items[currentIndex]);
    updatePlateText(plateBottomBg, items[currentIndex]);
    updatePlateText(leafTop, items[currentIndex]);
    updatePlateText(leafBottom, items[currentIndex]);

    function flipTo(targetIndex) {
        if (isFlipping || targetIndex === currentIndex) return;
        isFlipping = true;

        const incomingItem = items[targetIndex];
        const currentItem = items[currentIndex];

        // 1. Update navigation dots immediately for instant tactile feedback
        dots.forEach((dot, idx) => {
            const active = idx === targetIndex;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        // 2. Prepare plate contents for the split-flap drop
        updatePlateText(plateTopBg, incomingItem);
        updatePlateText(plateBottomBg, currentItem);
        updatePlateText(leafTop, currentItem);
        updatePlateText(leafBottom, incomingItem);

        // 3. Perform GSAP 3D Split-Flap Animation (single continuous mechanical drop)
        if (window.gsap) {
            gsap.set(leafTop, { rotateX: 0, visibility: 'visible' });
            gsap.set(leafTopShadow, { opacity: 0 });
            gsap.set(leafBottom, { rotateX: 90, visibility: 'hidden' });
            gsap.set(leafBottomShadow, { opacity: 0.7 });

            const flapHalfTime = 0.14; // Snappy 140ms per half -> 280ms total fluid continuous flip

            const tl = gsap.timeline({
                onComplete: () => {
                    currentIndex = targetIndex;
                    isFlipping = false;

                    // Restore top leaf with incoming item at resting state (0deg)
                    updatePlateText(leafTop, incomingItem);
                    gsap.set(leafTop, { rotateX: 0, visibility: 'visible' });
                    gsap.set(leafTopShadow, { opacity: 0 });

                    // Hide and reset bottom leaf for next flip
                    gsap.set(leafBottom, { rotateX: 90, visibility: 'hidden' });
                    gsap.set(leafBottomShadow, { opacity: 0.7 });

                    // Sync all background plates to current resting state
                    updatePlateText(plateBottomBg, incomingItem);
                    updatePlateText(plateTopBg, incomingItem);
                    updatePlateText(leafBottom, incomingItem);
                }
            });

            // Phase 1: Top leaf falls forward down to the center seam (-90deg), accelerating
            tl.to(leafTop, {
                rotateX: -90,
                duration: flapHalfTime,
                ease: 'power1.in'
            });
            tl.to(leafTopShadow, {
                opacity: 0.7,
                duration: flapHalfTime,
                ease: 'power1.in'
            }, 0);

            // Midpoint handoff at the seam: seamless instantaneous swap at 90deg edge
            tl.set(leafTop, { visibility: 'hidden' });
            tl.set(leafBottom, { visibility: 'visible' });

            // Phase 2: Bottom leaf continues the fall from 90deg down to 0deg, matching velocity
            tl.to(leafBottom, {
                rotateX: 0,
                duration: flapHalfTime,
                ease: 'power1.out'
            });
            tl.to(leafBottomShadow, {
                opacity: 0,
                duration: flapHalfTime,
                ease: 'power1.out'
            }, `-=${flapHalfTime}`);
        } else {
            // Fallback without GSAP
            currentIndex = targetIndex;
            updatePlateText(plateTopBg, incomingItem);
            updatePlateText(plateBottomBg, incomingItem);
            updatePlateText(leafTop, incomingItem);
            updatePlateText(leafBottom, incomingItem);
            dots.forEach((dot, idx) => {
                dot.classList.toggle('is-active', idx === currentIndex);
            });
            isFlipping = false;
        }
    }

    function next() {
        const nextIdx = (currentIndex + 1) % items.length;
        flipTo(nextIdx);
    }

    function startTimer() {
        stopTimer();
        if (isHovered) return;
        timer = setInterval(next, intervalDuration);
    }

    function stopTimer() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    // Pagination dots click
    dots.forEach((dot, idx) => {
        dot.addEventListener('click', (e) => {
            e.stopPropagation();
            flipTo(idx);
            startTimer();
        });
    });

    // Data-block click advances to next
    dataBlock.addEventListener('click', () => {
        next();
        startTimer();
    });

    // Pause on hover
    container.addEventListener('mouseenter', () => {
        isHovered = true;
        stopTimer();
    });

    container.addEventListener('mouseleave', () => {
        isHovered = false;
        startTimer();
    });

    // Viewport IntersectionObserver
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                startTimer();
            } else {
                stopTimer();
            }
        }, { threshold: 0.1 });
        observer.observe(container);
    } else {
        startTimer();
    }
}

/**
 * Main initialization runner.
 */
function initCorporateScripts() {
    initHeroDataBlockSplitFlap();
    initAccordionInteractiveList();
    initCardGlowEffect('.accordion-item, .animated-card, #cta .content, .cta-spotlight-layer, .sdc-card, .sdc-metric-card, #hero, .blue-background-00, .white-background-00, .white-background-01, .data-block, .timeline-card-front');
    // initCorporateLightbox();
    // initTroubleTimeline();
    // initCorporateSlideshows();
    // initProgramStateTabs();
    // initCtaWhatsAppForm();
    // initUpcomingEventsScroll();
    // initUpcomingEventsOpacityCascade();
    // initNewsCategoryFilter();
    // initNewsRadioWavesCanvas();
    initStickyOverlapEffect();
    initBlockViewportObserver();
    // initStickyAnchorLinks();
    initUnifiedAnimations();

    // Auto-initialize any standalone points slideshows outside #trouble with viewport visibility
    document.querySelectorAll('.points-slideshow:not(#trouble .points-slideshow)').forEach(slideshow => {
        const instance = initPointsSlideshow(slideshow);
        if (!instance) return;
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(([entry]) => {
                if (entry.isIntersecting) {
                    instance.start();
                } else {
                    instance.stop();
                }
            }, { threshold: 0.1 });
            observer.observe(slideshow);
        } else {
            instance.start();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCorporateScripts);
} else {
    initCorporateScripts();
}