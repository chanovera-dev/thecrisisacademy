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
 * Track mouse position inside cards for dynamic glow effects.
 * Updates --mouse-x and --mouse-y CSS variables.
 *
 * @param {string} selector - CSS selector for target elements.
 */
function initCardGlowEffect(selector = '.accordion-item') {
    const cards = document.querySelectorAll(selector);
    if (!cards.length) return;

    cards.forEach(card => {
        const updateCoords = e => {
            if (window.innerWidth <= 768) return;

            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        };

        card.addEventListener('mousemove', updateCoords);
        card.addEventListener('mouseenter', updateCoords);
    });
}

/**
 * Lightbox controller for corporate templates.
 * Manages modal opening/closing, accessibility attributes, active panes,
 * and component wake-up events (e.g., Crisis Simulator animations).
 */
function initCorporateLightbox() {
    const lightbox = document.getElementById('corporate-lightbox');
    if (!lightbox) return;

    const titleEl = document.getElementById('corporate-lightbox-title');
    const panes = lightbox.querySelectorAll('.corporate-lightbox-pane');
    let previousActiveElement = null;
    let savedScrollY = 0;

    const openLightbox = (paneId) => {
        previousActiveElement = document.activeElement;
        savedScrollY = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;

        // Prevent horizontal layout shift when scrollbar is hidden
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        if (scrollbarWidth > 0) {
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        }

        // Freeze body at current scroll position
        document.body.style.top = `-${savedScrollY}px`;
        document.documentElement.classList.add('lightbox-open');
        document.body.classList.add('lightbox-open');

        // Activate requested pane or default to first
        let targetPane = null;
        panes.forEach(pane => {
            const matches = !paneId || pane.dataset.pane === paneId;
            pane.classList.toggle('active', matches);
            if (matches) targetPane = pane;
        });

        if (targetPane && titleEl) {
            titleEl.textContent = targetPane.dataset.title || 'Simulador de Crisis';
        }

        lightbox.classList.add('active');
        lightbox.setAttribute('aria-hidden', 'false');

        // Reset scroll position of lightbox scrollable body
        const scrollBody = lightbox.querySelector('.corporate-lightbox-body');
        if (scrollBody) {
            scrollBody.scrollTop = 0;
        }

        // Wake up Crisis Simulator animations if active
        if (targetPane && (paneId === 'crisis-simulator' || targetPane.id === 'pane-crisis-simulator')) {
            initCardGlowEffect('.sdc-card');
            const activeSection = targetPane.querySelector('.sdc-section.active') || targetPane.querySelector('.sdc-section[data-step="1"]');
            if (activeSection) {
                const animatables = activeSection.querySelectorAll('.sdc-animate');
                if (typeof animateIn === 'function') {
                    animateIn(animatables, ['animate-in'], { threshold: 0.01, stagger: 80 });
                } else {
                    animatables.forEach(el => el.classList.add('animate-in'));
                }
            }
        }

        // Focus close button for accessibility
        const closeBtn = lightbox.querySelector('.corporate-lightbox-close-btn');
        if (closeBtn) closeBtn.focus();
    };

    const closeLightbox = () => {
        lightbox.classList.remove('active');
        lightbox.setAttribute('aria-hidden', 'true');

        // Restore body position and scroll
        document.documentElement.classList.remove('lightbox-open');
        document.body.classList.remove('lightbox-open');
        document.body.style.top = '';
        document.body.style.paddingRight = '';
        window.scrollTo(0, savedScrollY);

        // Asegurar que cualquier selector de herramientas teletransportado se cierre
        const toolPicker = document.getElementById('sdc-tool-picker');
        if (toolPicker && toolPicker.classList.contains('show')) {
            toolPicker.classList.remove('show');
            toolPicker.style.display = 'none';
        }

        if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
            previousActiveElement.focus();
        }
    };

    // Prevent scroll leak on backdrop and non-scrollable parts
    const backdrop = lightbox.querySelector('.corporate-lightbox-backdrop');
    if (backdrop) {
        backdrop.addEventListener('wheel', (e) => e.preventDefault(), { passive: false });
        backdrop.addEventListener('touchmove', (e) => e.preventDefault(), { passive: false });
    }

    // Bind triggers with [data-open-lightbox]
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-open-lightbox]');
        if (trigger) {
            e.preventDefault();
            const paneId = trigger.getAttribute('data-open-lightbox');
            openLightbox(paneId);
            return;
        }

        const closeTrigger = e.target.closest('[data-close-lightbox]');
        if (closeTrigger && lightbox.classList.contains('active')) {
            e.preventDefault();
            closeLightbox();
        }
    });

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });

    // Expose helpers globally
    window.openCorporateLightbox = openLightbox;
    window.closeCorporateLightbox = closeLightbox;
}

/**
 * Simulator Showcase (#crisis-simulator).
 * Accessible tabs driving a console preview. Auto-advance is chained to the
 * CSS progress-bar `animationend`, so pausing (hover, focus, offscreen) is
 * handled purely via `animation-play-state`. Image changes reuse the Stories
 * WebGL displacement morph (`setupWebGLSlider` from stories-loop-gallery),
 * with the CSS crossfade as fallback.
 */
function initSimulatorShowcase() {
    document.querySelectorAll('[data-simulator-showcase]').forEach(showcase => {
        const tabs = Array.from(showcase.querySelectorAll('[role="tab"]'));
        const panels = tabs.map(tab => document.getElementById(tab.getAttribute('aria-controls')));
        const hud = showcase.querySelector('.simulator-hud');
        const kpiEl = showcase.querySelector('[data-sim-kpi]');
        const kpiLabelEl = showcase.querySelector('[data-sim-kpi-label]');
        const clockEl = showcase.querySelector('[data-sim-clock]');
        const screen = showcase.querySelector('.simulator-console-screen');
        if (!tabs.length) return;

        let current = Math.max(0, tabs.findIndex(t => t.classList.contains('is-active')));
        let morph = null;
        let morphRequested = false;
        let morphShown = current;
        let morphBusy = false;
        let morphPending = null;
        const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Lazily build the WebGL morph the first time the showcase is visible
        const initMorph = () => {
            if (morphRequested || reduceMotion || !screen || typeof setupWebGLSlider !== 'function') return;
            morphRequested = true;

            const images = panels.map(p => p && p.querySelector('img'));
            if (images.some(img => !img)) return;
            images.forEach(img => { img.loading = 'eager'; });

            // Same pipeline as Stories' .container.app (initAppSlideshow)
            morph = setupWebGLSlider(screen, images, current);
            if (!morph) return;

            morphShown = current;
            screen.classList.add('is-morph-ready');
            if ('ResizeObserver' in window) {
                new ResizeObserver(() => morph.resize()).observe(screen);
            }
        };

        // setupWebGLSlider can't be interrupted mid-tween, so queue the latest target
        const morphTo = (index) => {
            if (!morph) return;
            if (morphBusy) {
                morphPending = index;
                return;
            }
            if (index === morphShown) return;
            morphBusy = true;
            morph.transitionTo(index, () => {
                morphShown = index;
                morphBusy = false;
                if (morphPending !== null) {
                    const pending = morphPending;
                    morphPending = null;
                    morphTo(pending);
                }
            });
        };

        const activate = (index, { focus = false } = {}) => {
            const next = (index + tabs.length) % tabs.length;

            tabs.forEach((tab, i) => {
                const isActive = i === next;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                tab.tabIndex = isActive ? 0 : -1;

                const panel = panels[i];
                if (panel) {
                    panel.classList.toggle('is-active', isActive);
                    panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                }
            });

            // Restart progress animation even when re-selecting the same tab
            const bar = tabs[next].querySelector('.simulator-stage-progress span');
            if (bar) {
                bar.style.animation = 'none';
                void bar.offsetWidth;
                bar.style.animation = '';
            }

            if (next !== current) morphTo(next);

            if (next !== current && hud) {
                if (kpiEl) kpiEl.textContent = tabs[next].dataset.kpi || '';
                if (kpiLabelEl) kpiLabelEl.textContent = tabs[next].dataset.kpiLabel || '';
                hud.classList.remove('is-updating');
                void hud.offsetWidth;
                hud.classList.add('is-updating');
            }

            current = next;
            if (focus) tabs[next].focus();
        };

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => activate(i));

            tab.addEventListener('keydown', (e) => {
                const keyMap = { ArrowDown: current + 1, ArrowRight: current + 1, ArrowUp: current - 1, ArrowLeft: current - 1, Home: 0, End: tabs.length - 1 };
                if (e.key in keyMap) {
                    e.preventDefault();
                    activate(keyMap[e.key], { focus: true });
                }
            });

            const bar = tab.querySelector('.simulator-stage-progress span');
            if (bar) {
                bar.addEventListener('animationend', () => {
                    if (tab.classList.contains('is-active')) activate(current + 1);
                });
            }
        });

        // Mission clock (T+hh:mm:ss) — only ticks while visible
        let seconds = 0;
        let clockTimer = null;
        const pad = (n) => String(n).padStart(2, '0');
        const tick = () => {
            seconds++;
            if (clockEl) {
                clockEl.textContent = `T+${pad(Math.floor(seconds / 3600))}:${pad(Math.floor(seconds / 60) % 60)}:${pad(seconds % 60)}`;
            }
        };

        const setVisible = (visible) => {
            showcase.classList.toggle('is-paused', !visible);
            if (visible) initMorph();
            if (visible && !clockTimer) {
                clockTimer = setInterval(tick, 1000);
            } else if (!visible && clockTimer) {
                clockTimer = clearInterval(clockTimer);
            }
        };

        if ('IntersectionObserver' in window) {
            setVisible(false);
            new IntersectionObserver(([entry]) => setVisible(entry.isIntersecting), { threshold: 0.25 }).observe(showcase);
        } else {
            setVisible(true);
        }
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
 * Interactive 3D Timeline & Stepper for Trouble section (#trouble).
 * Synchronizes the stepper buttons on the left with the 3D card deck on the right.
 * Features automatic progression, smooth progress bar, previous/next controls,
 * click-to-activate steps, and hover pause.
 */
function initTroubleTimeline() {
    const showcase = document.querySelector('.timeline-3d-showcase');
    if (!showcase) return;

    const troubleSection = showcase.closest('section') || showcase;

    const stepper = showcase.querySelector('.timeline-nav-stepper');
    const stepButtons = Array.from(showcase.querySelectorAll('.timeline-step-btn'));
    const cards = Array.from(showcase.querySelectorAll('.timeline-3d-card'));
    const prevBtn = showcase.querySelector('.timeline-prev-btn');
    const nextBtn = showcase.querySelector('.timeline-next-btn');
    const playToggle = showcase.querySelector('.timeline-play-toggle');
    const progressFill = showcase.querySelector('.timeline-autoplay-fill');
    const dropTriggers = Array.from(showcase.querySelectorAll('.timeline-card-drop-trigger'));

    const total = cards.length;
    if (total === 0) return;

    let currentIndex = 0;
    let isPlaying = true;
    let isHovered = false;
    let isTroubleVisible = false;
    let animFrameId = null;
    const cycleDuration = 10000; // 10 seconds per step (3 points x ~3s each + buffer)
    let progressStartTime = null;
    let pausedProgress = 0; // fraction [0, 1]
    let isTransitioning = false;

    const stage = showcase.querySelector('.timeline-3d-stage');
    const leftBar = showcase.querySelector('.left-bar');

    // Set stepper height to 440px on desktop (or 3 buttons on mobile) + gaps + padding
    function adjustStepperHeight() {
        if (!stepper) return;
        if (window.innerWidth >= 768) {
            stepper.style.height = '440px';
            stepper.style.maxHeight = '440px';
        } else {
            const targetCount = 3;
            const visibleCount = Math.min(stepButtons.length, targetCount);
            if (visibleCount === 0) return;

            let height = 0;
            for (let i = 0; i < visibleCount; i++) {
                height += stepButtons[i].offsetHeight;
            }
            const computed = window.getComputedStyle(stepper);
            const gap = parseFloat(computed.gap) || 8;
            height += gap * (visibleCount - 1);
            const paddingTop = parseFloat(computed.paddingTop) || 0;
            const paddingBottom = parseFloat(computed.paddingBottom) || 0;
            height += paddingTop + paddingBottom;

            if (height > 0) {
                stepper.style.height = `${Math.ceil(height)}px`;
                stepper.style.maxHeight = `${Math.ceil(height)}px`;
            }
        }

        if (stage) {
            stage.style.height = '';
            stage.style.minHeight = '';
        }
    }

    // Dynamic mask based on scroll position of stepper
    function updateStepperMask() {
        if (!stepper || window.innerWidth < 768) return;
        const isAtTop = stepper.scrollTop <= 6;
        const isAtBottom = stepper.scrollTop + stepper.clientHeight >= stepper.scrollHeight - 6;

        let mask;
        if (isAtTop && isAtBottom) {
            mask = 'none';
        } else if (isAtTop) {
            mask = 'linear-gradient(to bottom, black 0%, black 80%, transparent 100%)';
        } else if (isAtBottom) {
            mask = 'linear-gradient(to bottom, transparent 0%, black 20%, black 100%)';
        } else {
            mask = 'linear-gradient(to bottom, transparent 0%, black 20%, black 80%, transparent 100%)';
        }

        stepper.style.maskImage = mask;
        stepper.style.webkitMaskImage = mask;
    }

    // Scroll active step into view so it is always prominently visible as it advances
    function scrollActiveStepIntoView(activeBtn, smooth = true) {
        if (!stepper || !activeBtn) return;
        const stepperRect = stepper.getBoundingClientRect();
        const btnRect = activeBtn.getBoundingClientRect();
        const currentScroll = stepper.scrollTop;

        // Position of active button relative to top of stepper scroll content
        const btnRelativeTop = (btnRect.top - stepperRect.top) + currentScroll;
        const btnHeight = activeBtn.offsetHeight;
        const stepperHeight = stepper.clientHeight;

        // Center active button inside stepper viewport
        const targetScroll = btnRelativeTop - ((stepperHeight - btnHeight) / 2);
        const maxScroll = Math.max(0, stepper.scrollHeight - stepperHeight);
        const clampedScroll = Math.max(0, Math.min(maxScroll, Math.round(targetScroll)));

        stepper.scrollTo({
            top: clampedScroll,
            behavior: smooth ? 'smooth' : 'auto'
        });
    }

    // Update all cards' data-stack-offset and classes
    function updateCardOffsets(activeIndex, outgoingIndex = null, direction = 'next') {
        cards.forEach((card, idx) => {
            const offset = (idx - activeIndex + total) % total;
            card.dataset.stackOffset = offset;

            // Remove cycle animation classes from previous actions
            card.classList.remove('cycle-to-back', 'cycle-to-front');

            if (offset === 0) {
                card.classList.add('is-active');
                card.classList.remove('is-stacked');
            } else {
                card.classList.remove('is-active');
                card.classList.add('is-stacked');
            }
        });

        // Trigger 3D flyaway animation on the outgoing or incoming card (desktop only)
        if (window.innerWidth >= 768 && outgoingIndex !== null && cards[outgoingIndex]) {
            const outgoingCard = cards[outgoingIndex];
            if (direction === 'next') {
                outgoingCard.classList.add('cycle-to-back');
                setTimeout(() => {
                    outgoingCard.classList.remove('cycle-to-back');
                }, 800);
            } else if (direction === 'prev') {
                const incomingCard = cards[activeIndex];
                if (incomingCard) {
                    incomingCard.classList.add('cycle-to-front');
                    setTimeout(() => {
                        incomingCard.classList.remove('cycle-to-front');
                    }, 800);
                }
            }
        }
    }

    // Update stepper buttons on the left and scroll active into view
    function updateStepperButtons(activeIndex) {
        let activeBtn = null;
        stepButtons.forEach((btn, idx) => {
            const isActive = idx === activeIndex;
            const isPassed = idx < activeIndex;

            btn.classList.toggle('is-active', isActive);
            btn.classList.toggle('is-passed', isPassed);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');

            if (isActive) activeBtn = btn;
        });

        if (activeBtn) {
            scrollActiveStepIntoView(activeBtn);
        }
    }

    // ─── Points Slideshow per Card ───
    const pointsSlideshows = cards.map((card, cardIdx) => {
        const container = card.querySelector('.points-slideshow, .trouble-points-slideshow');
        if (!container) return null;

        return initPointsSlideshow(container, {
            duration: 3000,
            hoverTarget: card.querySelector('.timeline-card-front') || container,
            shouldResume: () => currentIndex === cardIdx,
            onDotClick: () => resetProgress()
        });
    });

    // Transition to a specific step
    function goToStep(targetIndex, direction = 'next') {
        if (isTransitioning) return;
        isTransitioning = true;
        setTimeout(() => { isTransitioning = false; }, 450);

        const newIndex = ((targetIndex % total) + total) % total;
        const oldIndex = currentIndex;

        if (newIndex === oldIndex) {
            isTransitioning = false;
            return;
        }

        currentIndex = newIndex;
        updateCardOffsets(currentIndex, oldIndex, direction);
        updateStepperButtons(currentIndex);
        resetProgress();

        if (pointsSlideshows[oldIndex]) {
            pointsSlideshows[oldIndex].reset();
        }
        if (pointsSlideshows[currentIndex]) {
            if (isTroubleVisible && !troubleSection.classList.contains('is-bottom')) {
                pointsSlideshows[currentIndex].start();
            } else {
                pointsSlideshows[currentIndex].setPoint(0);
            }
        }
    }

    // Progress bar and Autoplay timer
    function resetProgress() {
        pausedProgress = 0;
        progressStartTime = performance.now();
        if (progressFill) {
            progressFill.style.transform = 'scaleX(0)';
        }
    }

    function stepAutoplay(timestamp) {
        if (!isTroubleVisible || troubleSection.classList.contains('is-bottom')) {
            animFrameId = null;
            return;
        }

        if (!isPlaying || isHovered) {
            animFrameId = null;
            return;
        }

        if (!progressStartTime) {
            progressStartTime = timestamp - (pausedProgress * cycleDuration);
        }

        const elapsed = timestamp - progressStartTime;
        const progress = Math.min(elapsed / cycleDuration, 1);

        if (progressFill) {
            progressFill.style.transform = `scaleX(${progress})`;
        }

        if (progress >= 1) {
            resetProgress();
            goToStep(currentIndex + 1, 'next');
        }

        animFrameId = requestAnimationFrame(stepAutoplay);
    }

    function startAutoplay() {
        if (!isTroubleVisible || troubleSection.classList.contains('is-bottom')) return;
        if (!animFrameId && isPlaying && !isHovered) {
            progressStartTime = performance.now() - (pausedProgress * cycleDuration);
            animFrameId = requestAnimationFrame(stepAutoplay);
        }
    }

    function pauseAutoplay() {
        if (progressStartTime) {
            const elapsed = performance.now() - progressStartTime;
            pausedProgress = Math.min(elapsed / cycleDuration, 1);
        }
        progressStartTime = null;
        if (animFrameId) {
            cancelAnimationFrame(animFrameId);
            animFrameId = null;
        }
    }

    // Stepper Button Clicks
    stepButtons.forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            const dir = idx >= currentIndex ? 'next' : 'prev';
            goToStep(idx, dir);
        });

        // Keyboard navigation
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                goToStep(currentIndex + 1, 'next');
                const nextBtnEl = stepButtons[(currentIndex + 1) % total];
                if (nextBtnEl) nextBtnEl.focus();
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                goToStep(currentIndex - 1, 'prev');
                const prevBtnEl = stepButtons[(currentIndex - 1 + total) % total];
                if (prevBtnEl) prevBtnEl.focus();
            }
        });
    });

    // Next & Previous Controls
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            goToStep(currentIndex + 1, 'next');
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            goToStep(currentIndex - 1, 'prev');
        });
    }

    // Card Drop Triggers ("Siguiente" / "Reiniciar" inside card footer)
    dropTriggers.forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            const card = trigger.closest('.timeline-3d-card');
            // Solo debe funcionar en la tarjeta frontal activa
            if (card && card.classList.contains('is-stacked')) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            e.stopPropagation();
            goToStep(currentIndex + 1, 'next');
        });
    });

    // Clicking any stacked card in the pile brings it forward
    cards.forEach((card, idx) => {
        card.addEventListener('click', (e) => {
            if (card.classList.contains('is-stacked') && idx !== currentIndex) {
                goToStep(idx, idx > currentIndex ? 'next' : 'prev');
                return;
            }
            if (e.target.closest('button, a, input, select, textarea')) return;
        });
    });

    // Play / Pause toggle
    if (playToggle) {
        playToggle.addEventListener('click', () => {
            isPlaying = !isPlaying;
            playToggle.classList.toggle('is-paused', !isPlaying);
            playToggle.setAttribute('aria-label', isPlaying ? 'Pausar reproducción automática' : 'Reanudar reproducción automática');
            if (isPlaying && isTroubleVisible && !troubleSection.classList.contains('is-bottom')) {
                startAutoplay();
            } else {
                pauseAutoplay();
            }
        });
    }

    // Hover pause behavior on entire showcase
    if (showcase) {
        showcase.addEventListener('mouseenter', () => {
            isHovered = true;
            pauseAutoplay();
        });

        showcase.addEventListener('mouseleave', () => {
            isHovered = false;
            if (isPlaying && isTroubleVisible && !troubleSection.classList.contains('is-bottom')) {
                startAutoplay();
            }
        });
    }

    // Pause trouble timeline animation loops when covered by sticky overlap (.is-bottom)
    troubleSection.addEventListener('block:bottomChange', (e) => {
        if (e.detail.isBottom) {
            pauseAutoplay();
            if (pointsSlideshows[currentIndex]) {
                pointsSlideshows[currentIndex].stop();
            }
        } else {
            if (isTroubleVisible && isPlaying && !isHovered) {
                startAutoplay();
            }
            if (isTroubleVisible && pointsSlideshows[currentIndex]) {
                pointsSlideshows[currentIndex].start();
            }
        }
    });

    // Set 4-button height and keep responsive on resize
    adjustStepperHeight();
    window.addEventListener('resize', adjustStepperHeight, { passive: true });

    // Stepper scroll listener for dynamic mask updates
    if (stepper) {
        stepper.addEventListener('scroll', updateStepperMask, { passive: true });
        updateStepperMask();
    }

    // Initialize initial view (without auto-starting timers until section is in view)
    updateCardOffsets(0);
    updateStepperButtons(0);
    resetProgress();

    // Viewport-based activation: only play when #trouble enters viewport and not .is-bottom
    if ('IntersectionObserver' in window) {
        const troubleObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isTroubleVisible = entry.isIntersecting;
                if (isTroubleVisible) {
                    if (isPlaying && !isHovered && !troubleSection.classList.contains('is-bottom')) {
                        startAutoplay();
                    }
                    if (pointsSlideshows[currentIndex] && !troubleSection.classList.contains('is-bottom')) {
                        pointsSlideshows[currentIndex].start();
                    }
                } else {
                    pauseAutoplay();
                    if (pointsSlideshows[currentIndex]) {
                        pointsSlideshows[currentIndex].stop();
                    }
                }
            });
        }, { threshold: 0.15 });

        troubleObserver.observe(troubleSection);
    } else {
        isTroubleVisible = true;
        startAutoplay();
        if (pointsSlideshows[0]) {
            pointsSlideshows[0].start();
        }
    }

    // Re-verify height and mask after next repaint
    requestAnimationFrame(() => {
        adjustStepperHeight();
        updateStepperMask();
    });
}

/**
 * Turn the certification content into a scroll-led story.
 * Without GSAP, all scenes remain in normal document flow and fully readable.
 */
function initCertificationCinematic() {
    const section = document.querySelector('#certification.certification-story');
    const scenes = section ? Array.from(section.querySelectorAll('[data-story-scene]')) : [];

    if (
        !section ||
        !scenes.length ||
        !window.gsap ||
        !window.ScrollTrigger ||
        (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)
    ) {
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    scenes.forEach((scene) => {
        gsap.fromTo(scene, {
            autoAlpha: 0.2,
            y: 44
        }, {
            autoAlpha: 1,
            y: 0,
            ease: 'none',
            scrollTrigger: {
                trigger: scene,
                start: 'top 84%',
                end: 'top 46%',
                scrub: 0.7,
                invalidateOnRefresh: true
            }
        });
    });

    const progress = section.querySelector('.certification-story__progress span');
    if (progress) {
        ScrollTrigger.create({
            trigger: section,
            start: 'top top',
            end: 'bottom bottom',
            scrub: true,
            onUpdate: self => gsap.set(progress, { scaleX: self.progress })
        });
    }

    requestAnimationFrame(() => ScrollTrigger.refresh());
}

/**
 * Main initialization runner.
 */
function initCorporateScripts() {
    initAccordionInteractiveList();
    initCardGlowEffect('.accordion-item, .animated-card, #cta .content, .cta-spotlight-layer, .sdc-card, .sdc-metric-card, #hero, .blue-background-00, .blue-background-01, .white-background-00, .white-background-01, .data-block, .timeline-card-front, .signal-item, .certification-story__card');
    initCorporateLightbox();
    initSimulatorShowcase();
    initTroubleTimeline();
    initCorporateSlideshows();
    initProgramStateTabs();
    initCtaWhatsAppForm();
    initUpcomingEventsScroll();
    initUpcomingEventsOpacityCascade();
    initNewsCategoryFilter();
    initNewsRadioWavesCanvas();
    initSignalsTelemetryCanvas();
    initStickyOverlapEffect();
    initBlockViewportObserver();
    initStickyAnchorLinks();
    initUnifiedAnimations();
    initCertificationCinematic();

    // Auto-initialize any standalone points slideshows outside .timeline-3d-showcase with viewport visibility
    document.querySelectorAll('.points-slideshow:not(#trouble .points-slideshow, .timeline-3d-showcase .points-slideshow)').forEach(slideshow => {
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

/**
 * WhatsApp Form submission handler for the #cta section.
 * Validates inputs, formats message, opens WhatsApp URL in new tab,
 * and handles UI state transition to success message.
 */
function initCtaWhatsAppForm() {
    const form = document.getElementById('cta-whatsapp-form');
    if (!form || form.dataset.wsInitialized) return;
    form.dataset.wsInitialized = 'true';

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const phone = form.getAttribute('data-phone') || '525543910088';
        const nameEl = document.getElementById('wa_name');
        const emailEl = document.getElementById('wa_email');
        const phoneEl = document.getElementById('wa_phone');
        const interestEl = document.getElementById('wa_interest');

        const name = nameEl ? nameEl.value.trim() : '';
        const email = emailEl ? emailEl.value.trim() : '';
        const userPhone = phoneEl ? phoneEl.value.trim() : '';
        const interest = interestEl ? interestEl.value.trim() : '';

        let message = `*Nueva Inscripción / Solicitud*\n\n`;
        if (name) message += `*Nombre:* ${name}\n`;
        if (email) message += `*Correo:* ${email}\n`;
        if (userPhone) message += `*Teléfono:* ${userPhone}\n`;
        if (interest) message += `*Interés:* ${interest}\n`;

        const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank', 'noopener,noreferrer');

        // Hide form fields and display success confirmation
        const formFields = form.querySelector('.cta-form-fields');
        const successMsg = form.querySelector('.cta-success-message');
        if (formFields && successMsg) {
            formFields.style.display = 'none';
            successMsg.style.display = 'block';
        }
    });

    // Reset button handler to allow submitting a new form
    const resetBtn = document.getElementById('cta-reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            form.reset();
            const formFields = form.querySelector('.cta-form-fields');
            const successMsg = form.querySelector('.cta-success-message');
            if (formFields && successMsg) {
                formFields.style.display = 'block';
                successMsg.style.display = 'none';
            }
        });
    }
}

/**
 * Upcoming Events — Scroll fade edges & floating scrollbar sync
 *
 * Toggles .at-start / .at-end classes on the track wrapper
 * and synchronizes the floating overlay scrollbar thumb position and interaction.
 */
function initUpcomingEventsScroll() {
    const wrapper = document.getElementById('upcoming-events-track-wrapper');
    const track = document.getElementById('upcoming-events-track');
    if (!wrapper || !track) return;

    const scrollbar = document.getElementById('upcoming-events-scrollbar') || wrapper.querySelector('.upcoming-events__scrollbar');
    const thumb = document.getElementById('upcoming-events-scrollbar-thumb') || (scrollbar ? scrollbar.querySelector('.upcoming-events__scrollbar-thumb') : null);

    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;

    const check = () => {
        const { scrollLeft, scrollWidth, clientWidth } = track;
        wrapper.classList.toggle('at-start', scrollLeft <= 4);
        wrapper.classList.toggle('at-end', scrollLeft + clientWidth >= scrollWidth - 4);

        if (scrollbar && thumb) {
            const scrollableWidth = scrollWidth - clientWidth;
            if (scrollableWidth <= 0) {
                scrollbar.style.display = 'none';
                return;
            }
            scrollbar.style.display = '';

            const trackBarWidth = scrollbar.clientWidth;
            const thumbWidth = Math.max(48, Math.round((clientWidth / scrollWidth) * trackBarWidth));
            const maxThumbTravel = trackBarWidth - thumbWidth;
            const thumbLeft = (scrollLeft / scrollableWidth) * maxThumbTravel;

            thumb.style.width = `${thumbWidth}px`;
            thumb.style.transform = `translateX(${thumbLeft}px)`;
        }
    };

    track.addEventListener('scroll', check, { passive: true });
    window.addEventListener('resize', check, { passive: true });
    check();

    // Enable drag and click interaction on floating scrollbar
    if (scrollbar && thumb) {
        thumb.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            startScrollLeft = track.scrollLeft;
            thumb.classList.add('is-dragging');
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const deltaX = e.clientX - startX;
            const trackBarWidth = scrollbar.clientWidth;
            const thumbWidth = thumb.offsetWidth;
            const maxThumbTravel = trackBarWidth - thumbWidth;
            if (maxThumbTravel <= 0) return;

            const scrollableWidth = track.scrollWidth - track.clientWidth;
            const scrollDelta = (deltaX / maxThumbTravel) * scrollableWidth;
            track.scrollLeft = startScrollLeft + scrollDelta;
        });

        window.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;
            thumb.classList.remove('is-dragging');
            document.body.style.userSelect = '';
        });

        // Click on scrollbar track to jump smoothly
        scrollbar.addEventListener('click', (e) => {
            if (e.target === thumb) return;
            const rect = scrollbar.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const thumbWidth = thumb.offsetWidth;
            const targetThumbLeft = clickX - thumbWidth / 2;
            const maxThumbTravel = rect.width - thumbWidth;
            if (maxThumbTravel <= 0) return;

            const clampedThumbLeft = Math.max(0, Math.min(maxThumbTravel, targetThumbLeft));
            const scrollableWidth = track.scrollWidth - track.clientWidth;
            track.scrollTo({
                left: (clampedThumbLeft / maxThumbTravel) * scrollableWidth,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Upcoming Events — Opacity cascade based on visible position
 *
 * Observes which cards are inside the scroll track viewport on each
 * scroll/resize and applies opacity based on their ORDER within the
 * visible window (not their global index in the array).
 *
 * First visible card  → opacity 1.00
 * Second visible card → opacity 0.82
 * Third visible card  → opacity 0.64
 * … capped at 0.30 minimum
 * Cards outside view  → opacity 0.25
 */
function initUpcomingEventsOpacityCascade() {
    const track = document.getElementById('upcoming-events-track');
    if (!track) return;

    const cards = Array.from(track.querySelectorAll('.event-card'));
    if (cards.length === 0) return;

    const STEP = 0.18;   // opacity drop per visible position
    const MIN_VIS = 0.30;   // minimum opacity while still visible
    const OFF = 0.25;   // opacity for off-screen cards

    function update() {
        const trackLeft = track.scrollLeft;
        const trackRight = trackLeft + track.clientWidth;

        // Classify each card
        const visible = [];

        cards.forEach(card => {
            // offsetLeft is relative to scrollable parent
            const cardLeft = card.offsetLeft;
            const cardRight = cardLeft + card.offsetWidth;

            // Consider visible if at least 30px of the card is inside the track
            const overlap = Math.min(cardRight, trackRight) - Math.max(cardLeft, trackLeft);

            if (overlap >= 30) {
                visible.push({ card, cardLeft });
            } else {
                // Off-screen — skip featured
                if (!card.classList.contains('event-card--featured')) {
                    card.style.opacity = OFF;
                }
            }
        });

        // Sort left-to-right so position 0 is always the leftmost visible
        visible.sort((a, b) => a.cardLeft - b.cardLeft);

        visible.forEach(({ card }, i) => {
            // Featured card is always fully opaque
            if (card.classList.contains('event-card--featured')) {
                card.style.opacity = '1';
                return;
            }
            const opacity = Math.max(MIN_VIS, 1 - i * STEP);
            card.style.opacity = opacity;
        });
    }

    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });

    // Run once after layout is ready
    requestAnimationFrame(update);
}

/**
 * AJAX News Category Filter Controller
 * Handles category button clicks in the #news section, triggers an asynchronous
 * request to the WordPress backend, and updates #news-results with the returned HTML.
 */
function initNewsCategoryFilter() {
    const filterContainer = document.querySelector('#news .news-filters');
    const resultsContainer = document.getElementById('news-results');
    if (!filterContainer || !resultsContainer) return;

    const filterBtns = filterContainer.querySelectorAll('.news-filter-btn');
    if (!filterBtns.length) return;

    let isFetching = false;

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (this.classList.contains('active') || isFetching) return;

            const category = this.dataset.category || 'all';

            // Update active states on filter buttons
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');

            // Indicate loading state with subtle opacity transition
            isFetching = true;
            resultsContainer.style.opacity = '0.4';
            resultsContainer.style.pointerEvents = 'none';

            const formData = new FormData();
            formData.append('action', 'thecrisisacademy_filter_news');
            formData.append('category', category);

            if (typeof thecrisisacademy_ajax !== 'undefined' && thecrisisacademy_ajax.nonce) {
                formData.append('nonce', thecrisisacademy_ajax.nonce);
            }

            const ajaxUrl = (typeof thecrisisacademy_ajax !== 'undefined' && thecrisisacademy_ajax.ajax_url)
                ? thecrisisacademy_ajax.ajax_url
                : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.html) {
                        resultsContainer.innerHTML = data.data.html;

                        // Re-initialize card glow effect on newly loaded post cards
                        if (typeof initCardGlowEffect === 'function') {
                            initCardGlowEffect('#news .story-card');
                        }
                    }
                })
                .catch(err => {
                    console.error('Error filtering news:', err);
                })
                .finally(() => {
                    resultsContainer.style.opacity = '1';
                    resultsContainer.style.pointerEvents = 'auto';
                    isFetching = false;
                });
        });
    });
}

/**
 * Continuously propagating radio waves animation on HTML5 Canvas for the #news section.
 * Recreates the concentric radio wave pattern previously in ::before (repeating 60px intervals),
 * but as a dynamic, continuous, expanding wave emission that smoothly fades in and out.
 * Reads --color-primary dynamically from the theme CSS variables.
 */
function initNewsRadioWavesCanvas() {
    const canvas = document.getElementById('news-radio-waves-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = 0;
    let height = 0;
    let dpr = 1;
    let rafId = null;
    let isVisible = false;
    let lastTime = performance.now();
    let maxR = 1000;
    let cx = 0;
    let cy = 0;

    // Mathematical parameters for smooth, uniform radio waves
    const WAVE_GAP = 115; // Increased spacing between adjacent wave rings (px)
    const WAVE_SPEED = 38; // Expansion speed (px/sec) - smooth, organic propagation
    const LINE_WIDTH = 1.35; // Fine, elegant line thickness
    const MAX_ALPHA = 0.22; // Softer, less intense color
    const SPAWN_INTERVAL = WAVE_GAP / WAVE_SPEED; // Spawn interval in seconds
    let spawnTimer = 0;

    const waves = [];

    // Get current theme primary color (supports any CSS color format: hex, rgb, hsl, color-mix, etc.)
    function getThemePrimaryColor() {
        const rootStyle = getComputedStyle(document.documentElement);
        const col = rootStyle.getPropertyValue('--color-primary').trim();
        return col || '#0079ff';
    }

    let primaryColor = getThemePrimaryColor();

    function updateFocalPoint() {
        const parent = canvas.parentElement;
        cx = width * 0.5;
        cy = height * 0.15;
        if (parent) {
            const badge = parent.querySelector('.sub-heading');
            if (badge) {
                const parentRect = parent.getBoundingClientRect();
                const badgeRect = badge.getBoundingClientRect();
                if (parentRect.width > 0 && badgeRect.width > 0) {
                    cx = (badgeRect.left - parentRect.left) + badgeRect.width * 0.5;
                    cy = (badgeRect.top - parentRect.top) + badgeRect.height * 0.5;
                }
            }
        }
        maxR = Math.hypot(Math.max(cx, width - cx), Math.max(cy, height - cy)) + 100;
    }

    function seedWaves() {
        waves.length = 0;
        updateFocalPoint();
        const fadeDist = Math.min(Math.max(width * 0.5, 650), maxR);
        for (let r = 0; r <= fadeDist; r += WAVE_GAP) {
            waves.push({ r });
        }
        spawnTimer = 0;
    }

    function resize() {
        if (window.innerWidth < 768) {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
            return;
        }

        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;

        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = rect.width;
        height = rect.height;
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);

        primaryColor = getThemePrimaryColor();
        updateFocalPoint();

        if (waves.length === 0) {
            seedWaves();
        }
    }

    resize();

    // Use ResizeObserver for responsive adaptation when layout settles
    if (window.ResizeObserver && canvas.parentElement) {
        const ro = new ResizeObserver(() => {
            resize();
        });
        ro.observe(canvas.parentElement);
    }
    window.addEventListener('resize', resize, { passive: true });

    const newsBlock = canvas.closest('.block') || document.getElementById('news');

    function loop(currentTime) {
        if (window.innerWidth < 768 || !isVisible || (newsBlock && newsBlock.classList.contains('is-bottom'))) {
            rafId = null;
            return;
        }

        if (!lastTime) lastTime = currentTime;
        const dt = Math.min((currentTime - lastTime) / 1000, 0.1);
        lastTime = currentTime;

        // Periodic spawn aligned with WAVE_GAP
        spawnTimer += dt;
        while (spawnTimer >= SPAWN_INTERVAL) {
            spawnTimer -= SPAWN_INTERVAL;
            waves.push({ r: spawnTimer * WAVE_SPEED });
        }

        ctx.clearRect(0, 0, width, height);

        // Pronounced fade distance outward from the epicenter
        const fadeDist = Math.min(Math.max(width * 0.5, 650), maxR);

        // Update and draw each radio wave
        for (let i = waves.length - 1; i >= 0; i--) {
            const wave = waves[i];
            wave.r += WAVE_SPEED * dt;

            // Remove wave once it travels past the fade distance
            if (wave.r >= fadeDist) {
                waves.splice(i, 1);
                continue;
            }

            // Alpha calculations:
            // Smooth fade-in over the first 50px
            const fadeIn = Math.min(wave.r / 50, 1);
            // Pronounced, smooth falloff towards outer boundary
            const progress = Math.min(wave.r / fadeDist, 1);
            const fadeOut = Math.pow(1 - progress, 1.6);
            const alpha = fadeIn * fadeOut * MAX_ALPHA;

            if (alpha > 0.003) {
                ctx.beginPath();
                ctx.arc(cx, cy, wave.r, 0, Math.PI * 2);
                ctx.strokeStyle = primaryColor;
                ctx.globalAlpha = alpha;
                ctx.lineWidth = LINE_WIDTH;
                ctx.stroke();
            }
        }

        // Reset globalAlpha
        ctx.globalAlpha = 1;

        rafId = requestAnimationFrame(loop);
    }

    // Pause rAF loop when covered by .is-bottom, resume when active and in view
    if (newsBlock) {
        newsBlock.addEventListener('block:bottomChange', (e) => {
            if (e.detail.isBottom) {
                if (rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            } else if (isVisible && !rafId) {
                lastTime = performance.now();
                rafId = requestAnimationFrame(loop);
            }
        });
    }

    // Viewport-based activation: only run when canvas enters view
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isVisible = entry.isIntersecting;
                if (isVisible && !(newsBlock && newsBlock.classList.contains('is-bottom'))) {
                    lastTime = performance.now();
                    if (!rafId) {
                        rafId = requestAnimationFrame(loop);
                    }
                } else if (!isVisible && rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            });
        }, { rootMargin: '300px 0px', threshold: 0 });

        observer.observe(canvas.parentElement || canvas);
    } else {
        isVisible = true;
        if (!(newsBlock && newsBlock.classList.contains('is-bottom'))) {
            rafId = requestAnimationFrame(loop);
        }
    }
}

/**
 * Signals Telemetry & Volatility Canvas Background (#signals-canvas)
 *
 * High-performance, hardware-accelerated Canvas 2D telemetry dashboard:
 * - Fluid risk-waveform curves (cyan, electric blue, alert red)
 * - Scrolling market-volatility candlestick bars & wicks
 * - Dynamic data grid lines with glowing peak nodes
 * - Automatic visibility pause via IntersectionObserver, .in-view, and .is-bottom
 *
 * @package TheCrisisAcademy
 */
function initSignalsTelemetryCanvas() {
    const canvas = document.getElementById('signals-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = 0;
    let height = 0;
    let dpr = 1;
    let rafId = null;
    let isVisible = false;
    let lastTime = performance.now();
    let scrollOffset = 0;

    // Fixed telemetry candlestick bars definition (periodic loop pattern over 1200px)
    const BARS_CYCLE_WIDTH = 1200;
    const RAW_BARS = [
        { x: 34, y: 320, h: 40, isAlert: true },
        { x: 74, y: 300, h: 40, isAlert: true },
        { x: 114, y: 280, h: 30, isAlert: false },
        { x: 154, y: 250, h: 40, isAlert: false },
        { x: 194, y: 270, h: 30, isAlert: true },
        { x: 234, y: 230, h: 40, isAlert: false },
        { x: 274, y: 250, h: 30, isAlert: true },
        { x: 314, y: 210, h: 40, isAlert: false },
        { x: 354, y: 180, h: 40, isAlert: false },
        { x: 394, y: 150, h: 50, isAlert: false },
        { x: 434, y: 180, h: 30, isAlert: true },
        { x: 474, y: 140, h: 40, isAlert: false },
        { x: 514, y: 110, h: 40, isAlert: false },
        { x: 554, y: 140, h: 40, isAlert: false },
        { x: 594, y: 170, h: 40, isAlert: false },
        { x: 634, y: 210, h: 40, isAlert: false },
        { x: 674, y: 240, h: 40, isAlert: false },
        { x: 714, y: 280, h: 40, isAlert: true },
        { x: 754, y: 320, h: 40, isAlert: true },
        { x: 794, y: 360, h: 40, isAlert: true },
        { x: 834, y: 340, h: 30, isAlert: false },
        { x: 874, y: 300, h: 40, isAlert: false },
        { x: 914, y: 260, h: 40, isAlert: false },
        { x: 954, y: 230, h: 30, isAlert: false },
        { x: 994, y: 260, h: 40, isAlert: true },
        { x: 1034, y: 290, h: 40, isAlert: true },
        { x: 1074, y: 330, h: 40, isAlert: true },
        { x: 1114, y: 360, h: 30, isAlert: true },
        { x: 1154, y: 320, h: 30, isAlert: false }
    ];

    const SCROLL_SPEED = 30; // px/sec

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;

        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = rect.width;
        height = rect.height;
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);
    }

    resize();

    if (window.ResizeObserver && canvas.parentElement) {
        const ro = new ResizeObserver(() => resize());
        ro.observe(canvas.parentElement);
    }
    window.addEventListener('resize', resize, { passive: true });

    const signalsBlock = canvas.closest('.block') || document.getElementById('signals');

    function drawTelemetry() {
        ctx.clearRect(0, 0, width, height);

        const scaleY = height / 600;

        // 1. Grid Lines
        ctx.save();
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.035)';

        for (let y = 150 * scaleY; y < height; y += 150 * scaleY) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(width, y);
            ctx.stroke();
        }

        const vGridSpacing = 150;
        const vGridOffset = scrollOffset % vGridSpacing;
        for (let x = -vGridOffset; x < width + vGridSpacing; x += vGridSpacing) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, height);
            ctx.stroke();
        }
        ctx.restore();

        // 2. Volatility Candlestick Bars & Wicks
        ctx.save();
        const cycleX = scrollOffset % BARS_CYCLE_WIDTH;
        const numCycles = Math.ceil(width / BARS_CYCLE_WIDTH) + 2;

        for (let c = -1; c < numCycles; c++) {
            const baseCycleOffset = c * BARS_CYCLE_WIDTH - cycleX;

            for (let i = 0; i < RAW_BARS.length; i++) {
                const b = RAW_BARS[i];
                const bx = baseCycleOffset + b.x;

                if (bx < -30 || bx > width + 30) continue;

                const by = b.y * scaleY;
                const bh = b.h * scaleY;
                const wickTop = by - 25 * scaleY;
                const wickBottom = by + bh + 25 * scaleY;

                // Wick
                ctx.beginPath();
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
                ctx.lineWidth = 1;
                ctx.moveTo(bx + 6, wickTop);
                ctx.lineTo(bx + 6, wickBottom);
                ctx.stroke();

                // Bar body
                ctx.fillStyle = b.isAlert ? 'rgba(255, 0, 85, 0.4)' : 'rgba(0, 255, 195, 0.38)';
                ctx.fillRect(bx, by, 12, bh);
            }
        }
        ctx.restore();

        // 3. Flowing Risk Waveforms (Cyberpunk telemetry curves)
        ctx.save();
        const step = 8;

        // Wave A: Primary Cyan Wave (with area gradient fill)
        ctx.beginPath();
        let firstA = true;
        for (let x = 0; x <= width; x += step) {
            const worldX = x + scrollOffset;
            const y = (260 + Math.sin(worldX * 0.005) * 60 + Math.cos(worldX * 0.012) * 35) * scaleY;
            if (firstA) {
                ctx.moveTo(x, y);
                firstA = false;
            } else {
                ctx.lineTo(x, y);
            }
        }

        // Fill area under Wave A
        ctx.lineTo(width, height);
        ctx.lineTo(0, height);
        ctx.closePath();
        const gradA = ctx.createLinearGradient(0, 200 * scaleY, 0, height);
        gradA.addColorStop(0, 'rgba(0, 255, 195, 0.04)');
        gradA.addColorStop(1, 'rgba(0, 255, 195, 0.00)');
        ctx.fillStyle = gradA;
        ctx.fill();

        // Wave A Stroke
        ctx.beginPath();
        firstA = true;
        for (let x = 0; x <= width; x += step) {
            const worldX = x + scrollOffset;
            const y = (260 + Math.sin(worldX * 0.005) * 60 + Math.cos(worldX * 0.012) * 35) * scaleY;
            if (firstA) {
                ctx.moveTo(x, y);
                firstA = false;
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.strokeStyle = 'rgba(0, 255, 195, 0.35)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // Wave B: Secondary Electric Blue Wave
        ctx.beginPath();
        let firstB = true;
        for (let x = 0; x <= width; x += step) {
            const worldX = x + scrollOffset * 1.15;
            const y = (340 + Math.sin(worldX * 0.007 + 1.2) * 55 + Math.sin(worldX * 0.003) * 40) * scaleY;
            if (firstB) {
                ctx.moveTo(x, y);
                firstB = false;
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.strokeStyle = 'rgba(0, 162, 255, 0.45)';
        ctx.lineWidth = 2.2;
        ctx.stroke();

        // Wave C: Alert Red Pulse Wave
        ctx.beginPath();
        let firstC = true;
        for (let x = 0; x <= width; x += step) {
            const worldX = x + scrollOffset * 0.85;
            const y = (430 + Math.cos(worldX * 0.004) * 45 + Math.sin(worldX * 0.01) * 20) * scaleY;
            if (firstC) {
                ctx.moveTo(x, y);
                firstC = false;
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.strokeStyle = 'rgba(255, 0, 85, 0.35)';
        ctx.lineWidth = 1.75;
        ctx.stroke();

        ctx.restore();
    }

    function loop(currentTime) {
        // Stop execution if offscreen, covered by sticky bottom, or not in-view
        if (!isVisible || (signalsBlock && (signalsBlock.classList.contains('is-bottom') || !signalsBlock.classList.contains('in-view')))) {
            rafId = null;
            return;
        }

        if (!lastTime) lastTime = currentTime;
        const dt = Math.min((currentTime - lastTime) / 1000, 0.1);
        lastTime = currentTime;

        scrollOffset += SCROLL_SPEED * dt;

        drawTelemetry();

        rafId = requestAnimationFrame(loop);
    }

    function resume() {
        if (!rafId && isVisible && signalsBlock && !signalsBlock.classList.contains('is-bottom') && signalsBlock.classList.contains('in-view')) {
            lastTime = performance.now();
            rafId = requestAnimationFrame(loop);
        }
    }

    function pause() {
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    // Connect to sticky overlap events (block:bottomChange)
    if (signalsBlock) {
        signalsBlock.addEventListener('block:bottomChange', (e) => {
            if (e.detail && e.detail.isBottom) {
                pause();
            } else if (isVisible) {
                resume();
            }
        });

        // MutationObserver to watch .in-view class changes set by initBlockViewportObserver()
        if (window.MutationObserver) {
            const mo = new MutationObserver((mutations) => {
                for (const m of mutations) {
                    if (m.type === 'attributes' && m.attributeName === 'class') {
                        if (signalsBlock.classList.contains('in-view')) {
                            resume();
                        } else {
                            pause();
                        }
                    }
                }
            });
            mo.observe(signalsBlock, { attributes: true, attributeFilter: ['class'] });
        }
    }

    // Viewport-based activation: only run when canvas enters view
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isVisible = entry.isIntersecting;
                if (isVisible) {
                    resume();
                } else {
                    pause();
                }
            });
        }, { rootMargin: '200px 0px', threshold: 0 });

        observer.observe(canvas.parentElement || canvas);
    } else {
        isVisible = true;
        resume();
    }
}

/**
 * Universal Corporate Slideshow Engine (CorporateSlideshow)
 *
 * Consolidates all carousels, slideshows, and sliders across the corporate theme:
 * - 3D Flip Card Slideshows (#diff: "Por qué nosotros")
 * - Process & Metrics Track Carousels (#program: "Fases del programa")
 * - Future card, testimony, and metric sliders (.corporate-slideshow or [data-slideshow])
 *
 * Key Capabilities:
 * - Pluggable transitions: 'flip' (smooth 3D card rotation), 'fade' (crossfade active class), 'slide'
 * - Declarative HTML config: data-effect, data-autoplay, data-duration, data-pause-on-hover
 * - Flexible auto-discovery of slides, tracks, bullets/dots, and prev/next buttons
 * - High-performance IntersectionObserver viewport visibility (stops offscreen CPU/battery drain)
 * - Touch & swipe gesture detection on mobile/tablets
 * - Hover pause / resume
 * - Comprehensive programmatic API (goTo, next, prev, play, pause, reset, destroy)
 *
 * @package TheCrisisAcademy
 */
class CorporateSlideshow {
    constructor(container, options = {}) {
        if (!container || container.dataset.slideshowInit === 'true') return;
        this.container = container;
        this.container.dataset.slideshowInit = 'true';

        // Options resolution: data-* attributes take priority over JS options
        const ds = container.dataset;
        const isFlipDefault = container.classList.contains('diff-slideshow-container') || container.classList.contains('cert-container');
        this.effect = ds.effect || options.effect || (isFlipDefault ? 'flip' : 'fade');

        const defaultAutoplay = (this.effect === 'flip') ? 14000 : 6000;
        this.autoplayDelay = ds.autoplay !== undefined ? parseInt(ds.autoplay, 10) : (options.autoplay !== undefined ? options.autoplay : defaultAutoplay);
        this.pauseOnHover = ds.pauseOnHover !== 'false' && options.pauseOnHover !== false;
        this.flipDuration = parseInt(ds.duration || options.duration || 600, 10);
        this.touchEnabled = ds.touch !== 'false' && options.touch !== false;
        this.onChange = options.onChange || null;

        this.currentIndex = 0;
        this.isAnimating = false;
        this.autoTimer = null;
        this.isHovered = false;
        this.isVisible = false;

        this._initElements();
        if (this.slides.length <= 1) return;

        this._bindControls();
        this._bindTouch();
        this._bindVisibility();

        // Mark initial active slide
        this._setActive(0, false);
    }

    _initElements() {
        // Track element
        this.track = this.container.querySelector('.process-steps-track, .slideshow, .points-list, ul') || this.container;

        // 3D Flip element: in #diff the entire card container has 3D perspective and card styles
        this.flipTarget = (this.container.classList.contains('diff-slideshow-container') || this.container.classList.contains('cert-container'))
            ? this.container
            : (this.container.querySelector('.slideshow--wrapper') || this.container);

        // Slides discovery
        let slides = Array.from(this.container.querySelectorAll('.diff-slide-item, .process-step-item, .slideshow-item, .slide-item'))
            .filter(el => el.closest('.corporate-slideshow, .diff-slideshow-container, .cert-container, .process-carousel-wrapper') === this.container);

        if (!slides.length && this.track) {
            slides = Array.from(this.track.children).filter(el =>
                !el.classList.contains('slideshow-bullets-wrapper') &&
                !el.classList.contains('process-carousel-controls') &&
                !el.classList.contains('slideshow-bullets')
            );
        }
        this.slides = slides;

        // Bullets / Dots discovery
        this.bulletsContainer = this.container.querySelector('.slideshow-bullets, .carousel-dots, .points-nav, [data-slideshow-bullets]');
        this.bullets = [];

        if (this.bulletsContainer) {
            let existingBullets = Array.from(this.bulletsContainer.querySelectorAll('.bullet, .carousel-dot, .point-dot, button'));
            if (!existingBullets.length || existingBullets.length !== this.slides.length) {
                this.bulletsContainer.innerHTML = '';
                const isDots = this.bulletsContainer.classList.contains('carousel-dots');
                const isPoints = this.bulletsContainer.classList.contains('points-nav');
                const bulletClass = isDots ? 'carousel-dot' : (isPoints ? 'point-dot' : 'bullet');
                const tagName = (isDots || isPoints) ? 'button' : 'div';

                this.bullets = this.slides.map((_, i) => {
                    const b = document.createElement(tagName);
                    if (tagName === 'button') {
                        b.type = 'button';
                        b.setAttribute('aria-label', `Ir a diapositiva ${i + 1}`);
                    }
                    b.className = bulletClass + (i === 0 ? ' active is-active' : '');
                    b.dataset.index = i;
                    b.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
                    this.bulletsContainer.appendChild(b);
                    return b;
                });
            } else {
                this.bullets = existingBullets;
            }
        }

        // Navigation buttons
        this.prevBtn = this.container.querySelector('.slideshow-prev, .slide-prev, #aboutMetricsPrevBtn, [data-slide-prev]');
        this.nextBtn = this.container.querySelector('.slideshow-next, .slide-next, #aboutMetricsNextBtn, [data-slide-next]');
    }

    _bindControls() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.prev();
                this.resetAutoplay();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.next();
                this.resetAutoplay();
            });
        }

        this.bullets.forEach((bullet, idx) => {
            bullet.addEventListener('click', (e) => {
                e.preventDefault();
                const targetIdx = parseInt(bullet.dataset.slide ?? bullet.dataset.index ?? idx, 10);
                this.goTo(isNaN(targetIdx) ? idx : targetIdx);
                this.resetAutoplay();
            });
        });

        if (this.pauseOnHover) {
            this.container.addEventListener('mouseenter', () => {
                this.isHovered = true;
                this.pause();
            });
            this.container.addEventListener('mouseleave', () => {
                this.isHovered = false;
                this.play();
            });
        }
    }

    _bindTouch() {
        if (!this.touchEnabled) return;
        let startX = 0;
        let startY = 0;

        const touchTarget = this.track || this.container;
        touchTarget.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches.length > 0) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }
        }, { passive: true });

        touchTarget.addEventListener('touchend', (e) => {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            const deltaX = e.changedTouches[0].clientX - startX;
            const deltaY = e.changedTouches[0].clientY - startY;

            if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
                if (deltaX < 0) {
                    this.goTo(this.currentIndex + 1, 'touch-next');
                } else {
                    this.goTo(this.currentIndex - 1, 'touch-prev');
                }
                this.resetAutoplay();
            }
        }, { passive: true });
    }

    _bindVisibility() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    this.isVisible = entry.isIntersecting;
                    if (!this.isVisible) {
                        this.pause();
                    } else if (!this.isHovered) {
                        this.play();
                    }
                });
            }, { threshold: 0.1 });
            this.observer.observe(this.container);
        } else {
            this.isVisible = true;
            this.play();
        }

        // Listen for sticky overlap state changes on parent block
        const parentBlock = this.container.closest('.block');
        if (parentBlock) {
            this._bottomChangeHandler = (e) => {
                if (e.detail.isBottom) {
                    this.pause();
                } else if (this.isVisible && !this.isHovered) {
                    this.play();
                }
            };
            parentBlock.addEventListener('block:bottomChange', this._bottomChangeHandler);
        }
    }

    _setActive(idx, triggerCallback = true) {
        const prevIdx = this.currentIndex;
        this.currentIndex = idx;

        this.slides.forEach((slide, i) => {
            const isActive = (i === idx);
            slide.classList.toggle('active', isActive);
            slide.classList.toggle('is-active', isActive);
            if (isActive) {
                slide.removeAttribute('aria-hidden');
            } else {
                slide.setAttribute('aria-hidden', 'true');
            }
        });

        this.bullets.forEach((bullet, i) => {
            const isActive = (i === idx);
            bullet.classList.toggle('active', isActive);
            bullet.classList.toggle('is-active', isActive);
            bullet.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        if (triggerCallback && typeof this.onChange === 'function') {
            this.onChange(this.currentIndex, prevIdx);
        }
    }

    goTo(targetIdx, customDirection = null) {
        const total = this.slides.length;
        if (total <= 1 || this.isAnimating) return;

        const normalizedIdx = ((targetIdx % total) + total) % total;
        if (normalizedIdx === this.currentIndex) return;

        const direction = customDirection || (targetIdx > this.currentIndex ? 'next' : 'prev');

        if (this.effect === 'flip') {
            this._runFlipTransition(normalizedIdx, direction);
        } else {
            this._runFadeTransition(normalizedIdx);
        }
    }

    _runFlipTransition(targetIdx, direction) {
        this.isAnimating = true;

        // Natural touch inversion:
        // - Swiping left ('touch-next'): flip towards left (-90°) following finger motion
        // - Swiping right ('touch-prev'): flip towards right (+90°) following finger motion
        // - Button 'next' / autoplay: +90° (right side recedes into background)
        // - Button 'prev': -90° (left side recedes into background)
        const isLeftFlip = (direction === 'touch-next' || direction === 'prev');
        const phase1Angle = isLeftFlip ? -90 : 90;
        const phase2Angle = isLeftFlip ? 90 : -90;
        const halfDuration = this.flipDuration / 2;

        // Phase 1: rotate 0 → ±90° (card "falls away")
        this.flipTarget.style.transition = `transform ${halfDuration}ms cubic-bezier(0.4, 0, 1, 1)`;
        this.flipTarget.style.transform = `rotateY(${phase1Angle}deg)`;

        setTimeout(() => {
            // At ±90° the card is edge-on and invisible — swap slides
            this._setActive(targetIdx);

            // Instantly jump to ∓90° on the other side
            this.flipTarget.style.transition = 'none';
            this.flipTarget.style.transform = `rotateY(${phase2Angle}deg)`;
            void this.flipTarget.offsetWidth; // Force reflow

            // Phase 2: rotate ∓90° → 0° (card "comes back")
            this.flipTarget.style.transition = `transform ${halfDuration}ms cubic-bezier(0, 0, 0.6, 1)`;
            this.flipTarget.style.transform = 'rotateY(0deg)';

            setTimeout(() => {
                this.flipTarget.style.transition = '';
                this.flipTarget.style.transform = '';
                this.isAnimating = false;
            }, halfDuration);
        }, halfDuration);
    }

    _runFadeTransition(targetIdx) {
        this.isAnimating = true;
        this._setActive(targetIdx);
        setTimeout(() => {
            this.isAnimating = false;
        }, 400);
    }

    next() {
        this.goTo(this.currentIndex + 1);
    }

    prev() {
        this.goTo(this.currentIndex - 1);
    }

    play() {
        this.pause();
        const parentBlock = this.container.closest('.block');
        if (parentBlock && parentBlock.classList.contains('is-bottom')) return;
        if (this.autoplayDelay <= 0 || !this.isVisible || this.isHovered) return;
        this.autoTimer = setInterval(() => {
            this.next();
        }, this.autoplayDelay);
    }

    pause() {
        if (this.autoTimer) {
            clearInterval(this.autoTimer);
            this.autoTimer = null;
        }
    }

    resetAutoplay() {
        this.pause();
        this.play();
    }

    destroy() {
        this.pause();
        if (this.observer) this.observer.disconnect();
        const parentBlock = this.container.closest('.block');
        if (parentBlock && this._bottomChangeHandler) {
            parentBlock.removeEventListener('block:bottomChange', this._bottomChangeHandler);
        }
        delete this.container.dataset.slideshowInit;
    }
}

/**
 * Master initializer for all corporate slideshows
 */
function initCorporateSlideshows(customSelector = null, defaultOptions = {}) {
    const selector = customSelector || '.corporate-slideshow, [data-slideshow], .diff-slideshow-container, .cert-container, .process-carousel-wrapper';
    const containers = document.querySelectorAll(selector);
    const instances = [];

    containers.forEach(container => {
        if (container.dataset.slideshowInit === 'true') return;
        const instance = new CorporateSlideshow(container, defaultOptions);
        if (instance && instance.slides) {
            instances.push(instance);
        }
    });

    return instances;
}

/**
 * Keyboard navigation for Program state radio tabs (#program .state-buttons)
 */
function initProgramStateTabs() {
    const stateLabels = document.querySelectorAll('#program .state-buttons label');
    stateLabels.forEach(label => {
        if (label.dataset.tabInit) return;
        label.dataset.tabInit = '1';
        label.setAttribute('tabindex', '0');
        label.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const forId = label.getAttribute('for');
                const radio = document.getElementById(forId);
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
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

        blocks.forEach((block, index) => {
            if (index === blocks.length - 1) {
                if (block.classList.contains('is-bottom')) {
                    block.classList.remove('is-bottom');
                    block.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: false } }));
                }
                return;
            }

            const nextBlock = blocks[index + 1];
            const nextTop = nextBlock.getBoundingClientRect().top;

            // Start dimming when the next block is within 50% of the viewport
            const shouldBeBottom = nextTop <= window.innerHeight * 0.5;
            const wasBottom = block.classList.contains('is-bottom');

            if (shouldBeBottom !== wasBottom) {
                block.classList.toggle('is-bottom', shouldBeBottom);
                block.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: shouldBeBottom } }));
            }
        });
    }

    window.addEventListener('scroll', updateOverlap, { passive: true });
    updateOverlap();
}

/**
 * Viewport Observer for Page Blocks
 * Toggles .in-view class on blocks to pause/resume animations when scrolled into view.
 */
function initBlockViewportObserver() {
    const blocks = document.querySelectorAll('.page-template-corporate .site-main > .block, .site-main > .block');
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
 * Smooth Scroll for Sticky Anchor Links
 * Bypasses the native anchor jump bug where browsers won't scroll 
 * to a sticky element if it's currently stuck at the top.
 */
function initStickyAnchorLinks() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#' || targetId === '#0') return;

            const targetEl = document.querySelector(targetId);
            if (!targetEl) return;

            // Check if target is a block or inside a block
            const targetBlock = targetEl.classList.contains('block') ? targetEl : targetEl.closest('.block');

            if (targetBlock) {
                e.preventDefault();

                const blocks = Array.from(document.querySelectorAll('.page-template-corporate .site-main > .block, .site-main > .block'));
                const targetIndex = blocks.indexOf(targetBlock);

                if (targetIndex !== -1) {
                    let scrollPos = 0;

                    // Add initial container position
                    const siteMain = document.querySelector('.page-template-corporate .site-main, .site-main');
                    if (siteMain) {
                        scrollPos += siteMain.getBoundingClientRect().top + window.scrollY;
                    }

                    // Sum heights of all blocks preceding target
                    for (let i = 0; i < targetIndex; i++) {
                        scrollPos += blocks[i].offsetHeight;
                    }

                    // Remove .is-bottom preventively to ensure visibility and resume scripts on arrival
                    if (targetBlock.classList.contains('is-bottom')) {
                        targetBlock.classList.remove('is-bottom');
                        targetBlock.dispatchEvent(new CustomEvent('block:bottomChange', { detail: { isBottom: false } }));
                    }

                    window.scrollTo({
                        top: scrollPos,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
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

// Backward compatibility wrappers
window.CorporateSlideshow = CorporateSlideshow;
window.initCorporateSlideshows = initCorporateSlideshows;
window.initStickyOverlapEffect = initStickyOverlapEffect;
window.initBlockViewportObserver = initBlockViewportObserver;
window.initStickyAnchorLinks = initStickyAnchorLinks;
window.initUnifiedAnimations = initUnifiedAnimations;
window.initProcessCarousel = () => {
    initCorporateSlideshows('.process-carousel-wrapper', { effect: 'fade' });
    initProgramStateTabs();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCorporateScripts);
} else {
    initCorporateScripts();
}

/* ═══════════════════════════════════════════════════════════
   testimonies.js — Infinite loop overlapping coverflow carousel
   ═══════════════════════════════════════════════════════════ */
document.addEventListener("DOMContentLoaded", function () {
    const section = document.getElementById("testimonies");
    if (!section) return;

    const avatars = Array.from(section.querySelectorAll(".avatar-item"));
    const cards = Array.from(section.querySelectorAll(".testimony-card"));
    const bulletsWrapper = section.querySelector(".testi-bullets");
    const prevBtn = section.querySelector(".testi-prev");
    const nextBtn = section.querySelector(".testi-next");

    if (avatars.length === 0 || cards.length === 0) return;

    const total = cards.length;
    const half = Math.floor(total / 2);
    let current = Math.floor(total / 2); // Start in the middle index for balanced entrance
    let isAnimating = false;

    // Build bullets
    if (bulletsWrapper) {
        bulletsWrapper.innerHTML = "";
        for (let i = 0; i < total; i++) {
            const b = document.createElement("div");
            b.classList.add("bullet");
            if (i === current) b.classList.add("active");
            b.dataset.index = i;
            bulletsWrapper.appendChild(b);
        }
    }
    const bullets = bulletsWrapper ? bulletsWrapper.querySelectorAll(".bullet") : [];

    function updateBullets(idx) {
        bullets.forEach((b, i) => b.classList.toggle("active", i === idx));
    }

    function renderCarousel(direction) {
        const width = window.innerWidth;
        const isMobile = width < 768;
        const isTablet = width >= 768 && width < 1024;

        if (direction && !isMobile) {
            cards.forEach(card => card.classList.remove("from-next", "from-prev"));
            void section.offsetWidth; // Force reflow to replay CSS keyframes
        }

        // Render cards
        cards.forEach((card, i) => {
            let diff = i - current;
            if (diff < -half) diff += total;
            if (diff > half) diff -= total;

            const absDiff = Math.abs(diff);

            // Overlapping Card z-index
            card.style.zIndex = Math.max(1, 20 - absDiff * 2);

            let translateX = 0;
            let scale = Math.max(0.5, 1 - absDiff * 0.1);
            let opacity = 1;

            if (isMobile) {
                translateX = diff * 50; // compact stacking
                scale = 1 - absDiff * 0.15;
                opacity = absDiff > 1 ? 0 : 1; // only show immediate neighbors
            } else if (isTablet) {
                translateX = diff * 160;
                scale = 1 - absDiff * 0.12;
                opacity = absDiff > 2 ? 0 : 1;
            } else {
                translateX = diff * 240; // desktop full layout
                opacity = absDiff > 3 ? 0 : 1;
            }

            card.style.transform = `translateX(${translateX}px) scale(${scale})`;
            card.style.opacity = opacity;
            card.style.pointerEvents = absDiff > 1 ? "none" : "auto";
            card.classList.toggle("active", diff === 0);
            card.classList.toggle("prev", diff < 0);
            card.classList.toggle("next", diff > 0);

            if (direction) {
                card.classList.add(direction === "next" ? "from-next" : "from-prev");
            }
        });

        // Render avatars
        avatars.forEach((avatar, i) => {
            let diff = i - current;
            if (diff < -half) diff += total;
            if (diff > half) diff -= total;

            const absDiff = Math.abs(diff);
            avatar.style.zIndex = Math.max(1, 20 - absDiff * 2);

            let translateX = 0;
            let scale = Math.max(0.5, 1.3 - absDiff * 0.22);
            let opacity = 1 - absDiff * 0.22;

            if (isMobile) {
                translateX = diff * 45;
                scale = 1.2 - absDiff * 0.28;
                opacity = absDiff > 2 ? 0 : (1 - absDiff * 0.35);
            } else if (isTablet) {
                translateX = diff * 70;
                opacity = absDiff > 3 ? 0 : Math.max(0, 1 - absDiff * 0.22);
            } else {
                translateX = diff * 90; // desktop spacing
                opacity = absDiff > 3 ? 0 : Math.max(0, 1 - absDiff * 0.22);
            }

            avatar.style.transform = `translateX(${translateX}px) scale(${scale})`;
            avatar.style.opacity = opacity >= 0 ? opacity : 0;
            avatar.style.pointerEvents = opacity <= 0 ? "none" : "auto";
            avatar.classList.toggle("active", diff === 0);
        });

        updateBullets(current);
    }

    function goToSlide(targetIdx, explicitDirection) {
        if (isAnimating) return;
        isAnimating = true;

        let dir = explicitDirection;
        if (!dir) {
            let diff = targetIdx - current;
            if (diff < -half) diff += total;
            if (diff > half) diff -= total;
            dir = diff > 0 ? "next" : "prev";
        }

        current = ((targetIdx % total) + total) % total;
        renderCarousel(dir);

        setTimeout(() => {
            isAnimating = false;
        }, 500);
    }

    // Controls listeners
    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            goToSlide(current - 1, "prev");
            resetAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            goToSlide(current + 1, "next");
            resetAutoplay();
        });
    }

    if (bulletsWrapper) {
        bulletsWrapper.addEventListener("click", (e) => {
            const b = e.target.closest(".bullet");
            if (!b) return;
            goToSlide(parseInt(b.dataset.index));
            resetAutoplay();
        });
    }

    // Click on avatar to navigate to its slide
    avatars.forEach((avatar, i) => {
        avatar.addEventListener("click", () => {
            goToSlide(i);
            resetAutoplay();
        });
    });

    // Swipe Support
    let startX = 0;
    const container = section.querySelector(".testimonies-interactive-container");
    if (container) {
        container.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        container.addEventListener("touchend", (e) => {
            const deltaX = e.changedTouches[0].clientX - startX;
            if (Math.abs(deltaX) > 50) {
                goToSlide(deltaX < 0 ? current + 1 : current - 1);
                resetAutoplay();
            }
        });
    }

    // Viewport-based Autoplay: only run timer while testimonies section is in view
    let autoplayInterval = null;
    let isSectionVisible = false;

    function startAutoplay() {
        stopAutoplay();
        if (!isSectionVisible || section.classList.contains('is-bottom')) return;
        autoplayInterval = setInterval(() => {
            goToSlide(current + 1);
        }, 10000);
    }

    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
        }
    }

    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    // Viewport-based activation for testimonies
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                isSectionVisible = entry.isIntersecting;
                if (isSectionVisible) {
                    startAutoplay();
                } else {
                    stopAutoplay();
                }
            });
        }, { threshold: 0.1 });
        observer.observe(section);
    } else {
        isSectionVisible = true;
        startAutoplay();
    }

    // Pause testimonies autoplay interval when section has .is-bottom
    section.addEventListener('block:bottomChange', (e) => {
        if (e.detail.isBottom) {
            stopAutoplay();
        } else if (isSectionVisible) {
            startAutoplay();
        }
    });

    // First Render
    renderCarousel();

    // Responsive adaptation
    window.addEventListener("resize", renderCarousel);
});

/**
 * Backward compatibility wrapper for 3D Flip Card Slideshow (.cert-container)
 */
function initCertSlideshows() {
    return initCorporateSlideshows('.cert-container, .diff-slideshow-container', { effect: 'flip' });
}
window.initCertSlideshows = initCertSlideshows;

/**
 * Initialize .container.app slideshow using Stories WebGL slideshow
 */
document.addEventListener("DOMContentLoaded", function () {
    if (typeof window.storiesInitLoopGalleries === "function") {
        window.storiesInitLoopGalleries();
    } else if (typeof window.storiesInitAppSlideshow === "function") {
        document.querySelectorAll(".container.app, .slideshow--wrapper").forEach(window.storiesInitAppSlideshow);
    }
});