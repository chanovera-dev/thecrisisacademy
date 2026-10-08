/**
 * Hero section components for The Crisis Academy
 * Contains:
 * - initHeroCrisisGrid: Dynamic crisis particles and keyword clusters on #hero-canvas
 * - initDownChart: Interactive Bloomberg-style financial drop chart on #down-chart-canvas
 *
 * @package TheCrisisAcademy
 * @subpackage JS/Corporate-Hero
 */

/**
 * Hero Crisis Buildup — Tags cluster, escalate, and explode
 *
 * Crisis tags spawn rapidly, cluster via gravity, progressively
 * shift blue → orange → red as density rises. At critical mass
 * they detonate with a shockwave ring and spawn a burst of new
 * tags — the crisis multiplies. Cursor pushes tags away.
 */
function initHeroCrisisGrid(...crisisTags) {
    const hero = document.getElementById('hero');
    if (!hero || hero.dataset.gridInit) return;
    hero.dataset.gridInit = '1';

    let canvas = document.getElementById('hero-canvas');
    if (!canvas) {
        canvas = hero.querySelector('.hero-canvas');
    }
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'hero-canvas';
        canvas.className = 'hero-canvas';
        hero.insertBefore(canvas, hero.firstChild);
    } else {
        if (!canvas.id) canvas.id = 'hero-canvas';
        if (!canvas.classList.contains('hero-canvas')) canvas.classList.add('hero-canvas');
    }
    const ctx = canvas.getContext('2d');

    /* ── Crisis keywords ─────────────────────────────────────── */
    const DEFAULT_CRISIS_TAGS = [
        '#Negligencia', '#MalasPrácticas', '#Escándalo', '#Fraude',
        '#Corrupción', '#Acoso', '#Discriminación', '#Filtración',
        '#Demanda', '#Boicot', '#DespidoMasivo', '#FugaDeDatos',
        '#ProductoDefectuoso', '#Polémica', '#Difamación',
        '#AbusoLaboral', '#LavadoDeDinero', '#CrisisAmbiental',
        '#PublicidadEngañosa', '#Soborno', '#CrisisDeConfianza',
        '#DeclaracionesOfensivas', '#ConflictoDeInterés',
        '#FaltaDeTransparencia', '#ExplotaciónLaboral',
        '#MalaPraxis', '#PlagioCorporativo', '#DañoAmbiental',
        '#ManipulaciónMediatica', '#RetiradaDeProducto',
        '#CrisisSanitaria', '#Impunidad', '#RumoresVirales',
    ];
    let customTags = crisisTags.length > 0 ? crisisTags : [];
    if (customTags.length === 0 && canvas && canvas.dataset.tags) {
        customTags = canvas.dataset.tags.split(',').map(t => t.trim()).filter(Boolean);
    } else if (customTags.length === 0 && hero && hero.dataset.tags) {
        customTags = hero.dataset.tags.split(',').map(t => t.trim()).filter(Boolean);
    }
    const TAGS = customTags.length > 0 ? customTags : DEFAULT_CRISIS_TAGS;

    /* ── Configuration ───────────────────────────────────────── */
    const CLUSTER_COUNT = 3;
    const MAX_TAGS = 38;
    const MAX_DEBRIS = 24;
    const MAX_SHOCKWAVES = 6;
    const COLOR_NORMAL = { r: 131, g: 166, b: 208 };
    const COLOR_ALERT  = { r: 255, g: 45,  b: 45 };
    const COLOR_EXPLODE= { r: 201, g: 10,  b: 33 };
    const MOUSE_RADIUS_SQ = 200 * 200;
    const MOUSE_PUSH = 1.4;
    const GRAVITY = 0.032;
    const CRITICAL_DENSITY = 110;
    const EXPLOSION_DENSITY = 50;
    const SPAWN_MS = 60;

    // Pre-computed static color palette (Zero string allocations per frame)
    const COLOR_PALETTE = [
        'rgb(131,166,208)',
        'rgb(142,155,193)',
        'rgb(154,144,178)',
        'rgb(166,133,163)',
        'rgb(177,122,148)',
        'rgb(189,111,133)',
        'rgb(201,100,118)',
        'rgb(212,89,103)',
        'rgb(224,78,88)',
        'rgb(235,67,73)',
        'rgb(247,56,58)',
        'rgb(255,45,45)'
    ];
    const FONT_BASE = '600 12px "Manrope", sans-serif';
    const FONT_ALERT = '600 14px "Manrope", sans-serif';

    /* ── State ───────────────────────────────────────────────── */
    let W = 0, H = 0, dpr = 1.0;
    let isMobile = false;
    let mouseX = -9999, mouseY = -9999;
    let isVisible = true;
    let isLoopRunning = false;
    let heroRect = null;
    let lastSpawnTime = 0;

    /* ── Object Pools (Zero-RAM Allocations) ─────────────────── */
    const tagPool = [];
    for (let i = 0; i < MAX_TAGS; i++) {
        tagPool.push({
            active: false,
            x: 0, y: 0,
            vx: 0, vy: 0,
            label: '',
            alpha: 0,
            targetAlpha: 0.35,
            alertLevel: 0,
            cluster: 0,
            exploding: false
        });
    }

    const shockwavePool = [];
    for (let i = 0; i < MAX_SHOCKWAVES; i++) {
        shockwavePool.push({
            active: false,
            x: 0, y: 0,
            r: 0,
            maxR: 130,
            dr: 3.0,
            alpha: 0,
            dAlpha: 0.02,
            lineWidth: 2.5,
            isWarning: false
        });
    }

    const debrisPool = [];
    for (let i = 0; i < MAX_DEBRIS; i++) {
        debrisPool.push({
            active: false,
            x: 0, y: 0,
            vx: 0, vy: 0,
            r: 2,
            alpha: 0
        });
    }

    const clusters = [
        { x: 0, y: 0, vx: 0.15, vy: 0.1, count: 0, avgDist: 9999, cx: 0, cy: 0, lastExplode: 0, burstRemaining: 0, burstTimer: 0 },
        { x: 0, y: 0, vx: -0.12, vy: 0.12, count: 0, avgDist: 9999, cx: 0, cy: 0, lastExplode: 0, burstRemaining: 0, burstTimer: 0 },
        { x: 0, y: 0, vx: 0.1, vy: -0.14, count: 0, avgDist: 9999, cx: 0, cy: 0, lastExplode: 0, burstRemaining: 0, burstTimer: 0 }
    ];

    /* ── Sizing ──────────────────────────────────────────────── */
    function resize() {
        const rect = hero.getBoundingClientRect();
        dpr = 1.0; // Optimized memory footprint
        W = rect.width;
        H = rect.height;
        isMobile = W < 768;

        canvas.width = Math.round(W * dpr);
        canvas.height = Math.round(H * dpr);
        canvas.style.width = W + 'px';
        canvas.style.height = H + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // Position cluster centers if first time
        if (clusters[0].x === 0 && W > 0 && H > 0) {
            clusters[0].x = W * 0.28; clusters[0].y = H * 0.38;
            clusters[1].x = W * 0.72; clusters[1].y = H * 0.45;
            clusters[2].x = W * 0.50; clusters[2].y = H * 0.65;
        }

        if (isMobile) {
            stopLoop();
            render(performance.now());
            return;
        }
    }

    /* ── Tag Spawning (Zero Allocation) ─────────────────────── */
    function spawnTag(x, y, clusterIdx, labelText) {
        let tag = null;
        for (let i = 0; i < MAX_TAGS; i++) {
            if (!tagPool[i].active) {
                tag = tagPool[i];
                break;
            }
        }
        if (!tag) return null;

        const ci = clusterIdx !== undefined ? clusterIdx : Math.floor(Math.random() * CLUSTER_COUNT);
        const label = labelText || TAGS[Math.floor(Math.random() * TAGS.length)];
        const angle = Math.random() * Math.PI * 2;

        if (x === undefined || y === undefined) {
            const side = Math.floor(Math.random() * 4);
            if (side === 0) { x = -80; y = Math.random() * H; }
            else if (side === 1) { x = W + 80; y = Math.random() * H; }
            else if (side === 2) { x = Math.random() * W; y = -40; }
            else { x = Math.random() * W; y = H + 40; }
        }

        tag.active = true;
        tag.x = x;
        tag.y = y;
        tag.vx = Math.cos(angle) * 0.35;
        tag.vy = Math.sin(angle) * 0.35;
        tag.label = label;
        tag.alpha = 0;
        tag.targetAlpha = 0.28 + Math.random() * 0.18;
        tag.alertLevel = 0;
        tag.cluster = ci;
        tag.exploding = false;

        return tag;
    }

    /* ── Warning Pulse Wave (Zero Allocation) ────────────────── */
    function spawnWarningWave(x, y) {
        let sw = null;
        for (let i = 0; i < MAX_SHOCKWAVES; i++) {
            if (!shockwavePool[i].active) { sw = shockwavePool[i]; break; }
        }
        if (!sw) return;

        sw.active = true;
        sw.x = x;
        sw.y = y;
        sw.r = 8;
        sw.maxR = isMobile ? 65 : 95;
        sw.alpha = 0.50;
        sw.dr = 2.8;
        sw.dAlpha = 0.018;
        sw.lineWidth = 1.0;
        sw.isWarning = true;
    }

    /* ── Explosion Detonation (Zero Allocation) ──────────────── */
    function explodeCluster(ci) {
        const cluster = clusters[ci];
        const explodeX = cluster.cx || cluster.x;
        const explodeY = cluster.cy || cluster.y;

        // 1. Concentric Shockwaves
        const waveCount = isMobile ? 2 : 3;
        for (let w = 0; w < waveCount; w++) {
            let sw = null;
            for (let i = 0; i < MAX_SHOCKWAVES; i++) {
                if (!shockwavePool[i].active) { sw = shockwavePool[i]; break; }
            }
            if (sw) {
                sw.active = true;
                sw.x = explodeX;
                sw.y = explodeY;
                sw.r = 4 + w * 6;
                sw.maxR = (130 - w * 25) * (isMobile ? 0.75 : 1.0);
                sw.alpha = 0.75 - w * 0.15;
                sw.dr = 4.2 - w * 0.5;
                sw.dAlpha = 0.024;
                sw.lineWidth = 2.4 - w * 0.4;
                sw.isWarning = false;
            }
        }

        // 2. Debris Particles
        const debrisCount = isMobile ? 12 : 20;
        for (let d = 0; d < debrisCount; d++) {
            let p = null;
            for (let i = 0; i < MAX_DEBRIS; i++) {
                if (!debrisPool[i].active) { p = debrisPool[i]; break; }
            }
            if (p) {
                const angle = Math.random() * Math.PI * 2;
                const spd = 3.5 + Math.random() * 6.5;
                p.active = true;
                p.x = explodeX;
                p.y = explodeY;
                p.vx = Math.cos(angle) * spd;
                p.vy = Math.sin(angle) * spd;
                p.r = 1.2 + Math.random() * 1.8;
                p.alpha = 0.95;
            }
        }

        // 3. Cluster tags burst outward
        for (let i = 0; i < MAX_TAGS; i++) {
            const t = tagPool[i];
            if (!t.active || t.cluster !== ci || t.exploding) continue;

            const angle = Math.atan2(t.y - explodeY, t.x - explodeX) + (Math.random() - 0.5) * 0.4;
            const spd = 4 + Math.random() * 5.5;
            t.exploding = true;
            t.vx = Math.cos(angle) * spd;
            t.vy = Math.sin(angle) * spd;
        }

        // 4. Crisis Multiplication burst counter
        cluster.burstRemaining = isMobile ? 3 : 6;
        cluster.burstTimer = 0;

        // 5. Reset cluster position
        cluster.x = W * 0.15 + Math.random() * W * 0.7;
        cluster.y = H * 0.15 + Math.random() * H * 0.7;
        cluster.vx = (Math.random() - 0.5) * 0.3;
        cluster.vy = (Math.random() - 0.5) * 0.25;
    }

    /* ── Physics Updates (Pure Scalar Math, 0 Allocations) ──── */
    function updatePhysics() {
        const now = performance.now();

        // 1. Spawning check at urgent rate (SPAWN_MS = 60ms)
        const currentSpawnMs = isMobile ? 140 : SPAWN_MS;
        const currentMaxActive = isMobile ? 18 : 34;

        if (now - lastSpawnTime >= currentSpawnMs) {
            let activeCount = 0;
            for (let i = 0; i < MAX_TAGS; i++) {
                if (tagPool[i].active && !tagPool[i].exploding) activeCount++;
            }
            if (activeCount < currentMaxActive) {
                spawnTag();
                lastSpawnTime = now;
            }
        }

        // 2. Clusters drift, density calculation & burst spawns
        for (let ci = 0; ci < CLUSTER_COUNT; ci++) {
            const c = clusters[ci];
            c.x += c.vx;
            c.y += c.vy;
            if (c.x < W * 0.08 || c.x > W * 0.92) c.vx *= -1;
            if (c.y < H * 0.08 || c.y > H * 0.92) c.vy *= -1;

            let sx = 0, sy = 0, count = 0, totalDist = 0;
            for (let i = 0; i < MAX_TAGS; i++) {
                const t = tagPool[i];
                if (!t.active || t.cluster !== ci || t.exploding) continue;
                const dx = t.x - c.x, dy = t.y - c.y;
                totalDist += Math.sqrt(dx * dx + dy * dy);
                sx += t.x;
                sy += t.y;
                count++;
            }
            c.count = count;
            c.cx = count > 0 ? sx / count : c.x;
            c.cy = count > 0 ? sy / count : c.y;
            c.avgDist = count > 1 ? totalDist / count : 9999;

            // Crisis multiplication burst
            if (c.burstRemaining > 0) {
                c.burstTimer++;
                if (c.burstTimer >= 3) {
                    c.burstTimer = 0;
                    c.burstRemaining--;
                    const angle = Math.random() * Math.PI * 2;
                    const dist = 20 + Math.random() * 40;
                    spawnTag(c.x + Math.cos(angle) * dist, c.y + Math.sin(angle) * dist, ci);
                }
            }
        }

        // 3. Tags movement, gravity & cursor push
        for (let i = 0; i < MAX_TAGS; i++) {
            const t = tagPool[i];
            if (!t.active) continue;

            if (t.exploding) {
                t.x += t.vx;
                t.y += t.vy;
                t.vx *= 0.94;
                t.vy *= 0.94;
                t.alpha -= 0.032;
                if (t.alpha <= 0.01) {
                    t.active = false;
                    t.exploding = false;
                }
                continue;
            }

            // Smooth tag fade-in
            if (t.alpha < t.targetAlpha) {
                t.alpha = Math.min(t.targetAlpha, t.alpha + 0.04);
            }

            const c = clusters[t.cluster];
            if (c) {
                const dx = c.x - t.x, dy = c.y - t.y;
                const dSq = dx * dx + dy * dy;
                if (dSq > 25) {
                    const dist = Math.sqrt(dSq);
                    t.vx += (dx / dist) * GRAVITY;
                    t.vy += (dy / dist) * GRAVITY;
                }
            }

            // Mouse repulsion
            const mdx = t.x - mouseX, mdy = t.y - mouseY;
            const mdSq = mdx * mdx + mdy * mdy;
            if (mdSq < MOUSE_RADIUS_SQ && mdSq > 1) {
                const mDist = Math.sqrt(mdSq);
                const force = (1 - mDist / 200) * MOUSE_PUSH;
                t.vx += (mdx / mDist) * force;
                t.vy += (mdy / mDist) * force;
            }

            t.vx *= 0.975;
            t.vy *= 0.975;
            t.x += t.vx;
            t.y += t.vy;

            // Alert level based on cluster density
            if (c) {
                let targetAlert = 0;
                if (c.avgDist < CRITICAL_DENSITY) {
                    targetAlert = Math.min((CRITICAL_DENSITY - c.avgDist) / (CRITICAL_DENSITY - EXPLOSION_DENSITY), 1);
                }
                t.alertLevel += (targetAlert - t.alertLevel) * 0.10;
            }
        }

        // 4. Shockwave updates
        for (let i = 0; i < MAX_SHOCKWAVES; i++) {
            const sw = shockwavePool[i];
            if (!sw.active) continue;
            sw.r += sw.dr;
            sw.alpha -= sw.dAlpha;
            if (sw.alpha <= 0.01 || sw.r >= sw.maxR) {
                sw.active = false;
            }
        }

        // 5. Debris updates
        for (let i = 0; i < MAX_DEBRIS; i++) {
            const p = debrisPool[i];
            if (!p.active) continue;
            p.x += p.vx;
            p.y += p.vy;
            p.vx *= 0.93;
            p.vy *= 0.93;
            p.alpha -= 0.030;
            if (p.alpha <= 0.01) {
                p.active = false;
            }
        }

        // 6. Density-driven autonomous explosions (evaluated every frame)
        for (let ci = 0; ci < CLUSTER_COUNT; ci++) {
            const c = clusters[ci];
            if (c.count >= 4 && c.avgDist < EXPLOSION_DENSITY) {
                if (!c.lastExplode || now - c.lastExplode > 1200) {
                    c.lastExplode = now;
                    explodeCluster(ci);
                }
            }
        }
    }

    /* ── Render Loop (Zero Allocations, 0 Strings/Frame) ─────── */
    function render() {
        if (!isVisible || hero.classList.contains('is-bottom')) return;

        ctx.clearRect(0, 0, W, H);
        updatePhysics();

        // 1. Connection lines (Single batched stroke, zero string alloc)
        if (!isMobile) {
            ctx.globalAlpha = 0.12;
            ctx.strokeStyle = '#83a6d8';
            ctx.lineWidth = 0.5;
            ctx.beginPath();
            for (let i = 0; i < MAX_TAGS; i++) {
                const a = tagPool[i];
                if (!a.active || a.exploding) continue;
                for (let j = i + 1; j < MAX_TAGS; j++) {
                    const b = tagPool[j];
                    if (!b.active || b.exploding || a.cluster !== b.cluster) continue;
                    const dx = a.x - b.x, dy = a.y - b.y;
                    if (dx * dx + dy * dy < 16900) {
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                    }
                }
            }
            ctx.stroke();
        }

        // 2. Shockwaves
        for (let i = 0; i < MAX_SHOCKWAVES; i++) {
            const sw = shockwavePool[i];
            if (!sw.active || sw.alpha <= 0.01) continue;

            ctx.globalAlpha = sw.alpha;
            ctx.strokeStyle = sw.isWarning ? '#ff2d2d' : '#c90a21';
            ctx.lineWidth = sw.lineWidth;
            ctx.beginPath();
            ctx.arc(sw.x, sw.y, Math.max(1, sw.r), 0, Math.PI * 2);
            ctx.stroke();
        }

        // 3. Debris particles
        ctx.fillStyle = '#ff501e';
        for (let i = 0; i < MAX_DEBRIS; i++) {
            const p = debrisPool[i];
            if (!p.active || p.alpha <= 0.01) continue;

            ctx.globalAlpha = p.alpha;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fill();
        }

        // 4. Tags text (Zero string allocation, static palette & fonts)
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        let currentFont = '';

        for (let i = 0; i < MAX_TAGS; i++) {
            const t = tagPool[i];
            if (!t.active || t.alpha <= 0.01) continue;

            const targetFont = t.alertLevel > 0.65 ? FONT_ALERT : FONT_BASE;
            if (currentFont !== targetFont) {
                ctx.font = targetFont;
                currentFont = targetFont;
            }

            const pIdx = Math.min(11, Math.max(0, Math.floor(t.alertLevel * 11)));
            ctx.fillStyle = COLOR_PALETTE[pIdx];
            ctx.globalAlpha = t.alpha;
            ctx.fillText(t.label, t.x, t.y);
        }

        ctx.globalAlpha = 1.0;
    }

    /* ── Loop Lifecycle via gsap.ticker ──────────────────────── */
    function startLoop() {
        if (window.innerWidth < 768) return;
        if (!isLoopRunning && isVisible && !hero.classList.contains('is-bottom')) {
            lastSpawnTime = performance.now();
            if (typeof gsap !== 'undefined') {
                gsap.ticker.add(render);
            }
            isLoopRunning = true;
        }
    }

    function stopLoop() {
        if (isLoopRunning) {
            if (typeof gsap !== 'undefined') {
                gsap.ticker.remove(render);
            }
            isLoopRunning = false;
        }
    }

    /* ── Initialization ──────────────────────────────────────── */
    function init() {
        resize();

        // Spawn initial group around cluster centers
        for (let ci = 0; ci < CLUSTER_COUNT; ci++) {
            const c = clusters[ci];
            const count = 5 + Math.floor(Math.random() * 2);
            for (let j = 0; j < count; j++) {
                const angle = Math.random() * Math.PI * 2;
                const r = 55 + Math.random() * 25;
                const tag = spawnTag(c.x + Math.cos(angle) * r, c.y + Math.sin(angle) * r, ci);
                if (tag) {
                    tag.alertLevel = 0.3 + Math.random() * 0.2;
                }
            }
        }

        // On mobile: render single static frame and skip continuous ticker
        if (window.innerWidth < 768) {
            render(performance.now());
            window.__heroCanvasReady = true;
            window.dispatchEvent(new CustomEvent('hero:canvas-ready'));
            return;
        }

        // Scripted buildup and initial explosions via GSAP delayed calls
        if (typeof gsap !== 'undefined') {
            // Cúmulo 0 warning wave and explosion at 3.0s
            gsap.delayedCall(2.1, () => { if (isVisible) spawnWarningWave(clusters[0].x, clusters[0].y); });
            gsap.delayedCall(3.0, () => { if (isVisible) explodeCluster(0); });

            // Cúmulo 1 warning wave and explosion at 6.0s
            gsap.delayedCall(5.1, () => { if (isVisible) spawnWarningWave(clusters[1].x, clusters[1].y); });
            gsap.delayedCall(6.0, () => { if (isVisible) explodeCluster(1); });

            // Smooth canvas entrance
            gsap.fromTo(canvas, { opacity: 0 }, { opacity: 1, duration: 0.8, ease: 'power2.out' });
        }

        startLoop();
        window.__heroCanvasReady = true;
        window.dispatchEvent(new CustomEvent('hero:canvas-ready'));
    }

    /* ── Events & Observers ──────────────────────────────────── */
    hero.addEventListener('mouseenter', () => {
        heroRect = hero.getBoundingClientRect();
        startLoop();
    });
    hero.addEventListener('mousemove', (e) => {
        if (!heroRect) heroRect = hero.getBoundingClientRect();
        mouseX = e.clientX - heroRect.left;
        mouseY = e.clientY - heroRect.top;
        startLoop();
    });
    hero.addEventListener('mouseleave', () => { mouseX = -9999; mouseY = -9999; });

    hero.addEventListener('touchstart', (e) => {
        if (window.innerWidth < 768) return;
        heroRect = hero.getBoundingClientRect();
        if (e.touches && e.touches[0]) {
            mouseX = e.touches[0].clientX - heroRect.left;
            mouseY = e.touches[0].clientY - heroRect.top;
        }
        startLoop();
    }, { passive: true });
    hero.addEventListener('touchmove', (e) => {
        if (window.innerWidth < 768) return;
        if (e.touches && e.touches[0]) {
            mouseX = e.touches[0].clientX - heroRect.left;
            mouseY = e.touches[0].clientY - heroRect.top;
        }
    }, { passive: true });
    hero.addEventListener('touchend', () => { mouseX = -9999; mouseY = -9999; }, { passive: true });

    /* ── Lifecycle & Observers ────────────────────────────────────────── */
    const io = new IntersectionObserver(([entry]) => {
        isVisible = entry.isIntersecting;
        if (isVisible && !hero.classList.contains('is-bottom')) {
            startLoop();
        } else if (!isVisible) {
            stopLoop();
        }
    }, { threshold: 0.05 });
    io.observe(hero);

    // Cancel loop immediately when user switches tabs or minimizes browser
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopLoop();
        } else if (isVisible && !hero.classList.contains('is-bottom')) {
            startLoop();
        }
    });

    // MutationObserver to watch class changes and resume loop if 'is-bottom' is removed
    const classObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isBottom = hero.classList.contains('is-bottom');
                if (isVisible && !isBottom) {
                    startLoop();
                } else if (isBottom) {
                    stopLoop();
                }
            }
        });
    });
    classObserver.observe(hero, { attributes: true, attributeFilter: ['class'] });

    // Instantly cancel or resume loop on sticky overlap change
    hero.addEventListener('block:bottomChange', (e) => {
        if (e.detail.isBottom) {
            stopLoop();
        } else if (isVisible) {
            startLoop();
        }
    });

    /* ── Resize ──────────────────────────────────────────────── */
    let rt;
    window.addEventListener('resize', () => {
        clearTimeout(rt);
        rt = setTimeout(() => { resize(); heroRect = hero.getBoundingClientRect(); }, 200);
    }, { passive: true });

    init();
}

/**
 * High-Precision Financial Market Drop Chart (#down-chart-canvas)
 * Modern fintech / Bloomberg-inspired interactive canvas chart.
 * Renders an ultra-smooth cubic spline market timeline curve with progressive draw animation,
 * glowing gradient fill, crisis impact beacon, -11% callout badge, and interactive hover inspection.
 */
function initDownChart() {
    const canvas = document.getElementById('down-chart-canvas');
    if (!canvas || canvas.dataset.chartInit) return;
    canvas.dataset.chartInit = '1';

    const ctx = canvas.getContext('2d');
    const dataBlock = canvas.closest('.data-block') || canvas.parentElement;
    const parentBlock = canvas.closest('.block');

    let width = 0;
    let height = 0;
    let dpr = 1;
    let rafId = null;
    let startTime = null;
    let isVisible = false;
    let isHovering = false;
    let mouseX = -1;
    let mouseY = -1;

    // Timeline data points reflecting the PwC + Oxford Metrica research
    const data = [
        { label: 'Día -1', desc: 'Estabilidad', xRatio: 0.00, val: 0.0 },
        { label: 'Día 0', desc: 'Impacto Crisis', xRatio: 0.18, val: -0.6, isEvent: true },
        { label: 'Día 1', desc: '24 hrs', xRatio: 0.38, val: -4.8 },
        { label: 'Día 2', desc: '48 hrs', xRatio: 0.60, val: -8.5 },
        { label: 'Día 3', desc: '72 hrs', xRatio: 0.82, val: -10.5 },
        { label: 'Día 4', desc: 'Punto Crítico', xRatio: 1.00, val: -11.4, isEnd: true }
    ];

    const minVal = -12.5;
    const maxVal = 1.0;
    const valRange = maxVal - minVal;

    // Layout padding
    const padL = 38;
    const padR = 24;
    const padT = 38;
    const padB = 28;

    function getChartW() { return Math.max(10, width - padL - padR); }
    function getChartH() { return Math.max(10, height - padT - padB); }

    function getX(xRatio) {
        return padL + xRatio * getChartW();
    }

    function getY(val) {
        return padT + ((maxVal - val) / valRange) * getChartH();
    }

    // Get dynamic primary color
    function getPrimaryColor() {
        const rootStyle = getComputedStyle(document.documentElement);
        return rootStyle.getPropertyValue('--color-primary').trim() || '#0079ff';
    }

    // Compute cubic Bézier control points
    function getControlPoints(points) {
        const cp = [];
        for (let i = 0; i < points.length - 1; i++) {
            const p0 = i > 0 ? points[i - 1] : points[i];
            const p1 = points[i];
            const p2 = points[i + 1];
            const p3 = i < points.length - 2 ? points[i + 2] : p2;

            const cp1x = p1.x + (p2.x - p0.x) / 5;
            const cp1y = p1.y + (p2.y - p0.y) / 5;
            const cp2x = p2.x - (p3.x - p1.x) / 5;
            const cp2y = p2.y - (p3.y - p1.y) / 5;
            cp.push({ cp1x, cp1y, cp2x, cp2y });
        }
        return cp;
    }

    // Calculate curve points
    function getScreenPoints() {
        return data.map(d => ({
            x: getX(d.xRatio),
            y: getY(d.val),
            data: d
        }));
    }

    // Evaluate Y on the composite Bézier spline at any target X
    function interpolateYAtX(points, controlPoints, targetX) {
        if (targetX <= points[0].x) return points[0].y;
        if (targetX >= points[points.length - 1].x) return points[points.length - 1].y;

        for (let i = 0; i < points.length - 1; i++) {
            const p1 = points[i];
            const p2 = points[i + 1];
            if (targetX >= p1.x && targetX <= p2.x) {
                const segT = (targetX - p1.x) / (p2.x - p1.x);
                const cp = controlPoints[i];
                const t = Math.max(0, Math.min(1, segT));
                const mt = 1 - t;
                return (
                    mt * mt * mt * p1.y +
                    3 * mt * mt * t * cp.cp1y +
                    3 * mt * t * t * cp.cp2y +
                    t * t * t * p2.y
                );
            }
        }
        return points[points.length - 1].y;
    }

    function easeOutQuart(x) {
        return 1 - Math.pow(1 - x, 4);
    }

    let areaGrad = null;
    let lineGrad = null;
    const chartState = { progress: 0 };
    let chartTween = null;

    function updateGradients() {
        const chartW = getChartW();
        const chartH = getChartH();
        const primaryColor = getPrimaryColor();

        areaGrad = ctx.createLinearGradient(0, padT, 0, padT + chartH);
        areaGrad.addColorStop(0, 'rgba(239, 68, 68, 0.20)');
        areaGrad.addColorStop(0.35, 'rgba(239, 68, 68, 0.10)');
        areaGrad.addColorStop(1, 'rgba(239, 68, 68, 0.00)');

        lineGrad = ctx.createLinearGradient(padL, 0, padL + chartW, 0);
        lineGrad.addColorStop(0.00, primaryColor);
        lineGrad.addColorStop(0.20, '#f59e0b');
        lineGrad.addColorStop(0.55, '#ef4444');
        lineGrad.addColorStop(1.00, '#dc2626');
    }

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        dpr = 1.0;
        width = rect.width;
        height = rect.height;
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);
        updateGradients();

        if (startTime) {
            render(performance.now(), chartState.progress || 1.0);
        }
    }

    function render(currentTime, overrideProgress) {
        if (!startTime) startTime = currentTime || performance.now();
        const elapsed = (currentTime || performance.now()) - startTime;

        ctx.clearRect(0, 0, width, height);

        const primaryColor = getPrimaryColor();
        const chartW = getChartW();
        const chartH = getChartH();
        const pts = getScreenPoints();
        const cp = getControlPoints(pts);

        // Entrance animation progress (GSAP tween or fallback)
        let drawProgress;
        if (typeof overrideProgress === 'number') {
            drawProgress = overrideProgress;
        } else if (typeof gsap !== 'undefined' && chartState.progress !== undefined) {
            drawProgress = chartState.progress;
        } else {
            const rawProgress = Math.min(elapsed / 1800, 1);
            drawProgress = easeOutQuart(rawProgress);
        }

        // 1. Horizontal Reference Grid & Y Axis
        const gridLevels = [
            { lvl: 0, label: '0%' },
            { lvl: -4, label: '-4%' },
            { lvl: -8, label: '-8%' },
            { lvl: -11, label: '-11%' }
        ];

        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.font = '600 10.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';

        gridLevels.forEach(({ lvl, label }) => {
            const y = getY(lvl);
            ctx.beginPath();
            if (lvl === 0) {
                ctx.setLineDash([]);
                ctx.strokeStyle = 'rgba(100, 116, 139, 0.28)';
                ctx.lineWidth = 1.25;
            } else if (lvl === -11) {
                ctx.setLineDash([4, 4]);
                ctx.strokeStyle = 'rgba(220, 38, 38, 0.35)';
                ctx.lineWidth = 1;
            } else {
                ctx.setLineDash([2, 4]);
                ctx.strokeStyle = 'rgba(148, 163, 184, 0.18)';
                ctx.lineWidth = 1;
            }
            ctx.moveTo(padL - 4, y);
            ctx.lineTo(width - padR + 6, y);
            ctx.stroke();

            // Labels
            ctx.fillStyle = lvl === 0 ? '#64748b' : lvl === -11 ? '#dc2626' : '#94a3b8';
            ctx.fillText(label, padL - 8, y);
        });
        ctx.setLineDash([]);

        // 2. X Axis Timeline Labels
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.font = '500 10.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        data.forEach(d => {
            const x = getX(d.xRatio);
            ctx.fillStyle = d.isEvent ? '#d97706' : d.isEnd ? '#dc2626' : '#64748b';
            ctx.fillText(d.label, x, height - padB + 8);
        });

        // 3. Draw Spline Curve and Area Fill with Clipping according to drawProgress
        const currentEndX = padL + chartW * drawProgress;
        const currentEndY = interpolateYAtX(pts, cp, currentEndX);

        ctx.save();
        ctx.beginPath();
        ctx.rect(0, 0, currentEndX, height);
        ctx.clip();

        // Area Fill using cached gradient
        if (areaGrad) {
            ctx.beginPath();
            ctx.moveTo(pts[0].x, pts[0].y);
            for (let i = 0; i < pts.length - 1; i++) {
                const p = cp[i];
                const nextP = pts[i + 1];
                ctx.bezierCurveTo(p.cp1x, p.cp1y, p.cp2x, p.cp2y, nextP.x, nextP.y);
            }
            ctx.lineTo(pts[pts.length - 1].x, getY(minVal));
            ctx.lineTo(pts[0].x, getY(minVal));
            ctx.closePath();
            ctx.fillStyle = areaGrad;
            ctx.fill();
        }

        // Spline Stroke Line using cached gradient (zero blur allocation)
        if (lineGrad) {
            ctx.beginPath();
            ctx.moveTo(pts[0].x, pts[0].y);
            for (let i = 0; i < pts.length - 1; i++) {
                const p = cp[i];
                const nextP = pts[i + 1];
                ctx.bezierCurveTo(p.cp1x, p.cp1y, p.cp2x, p.cp2y, nextP.x, nextP.y);
            }
            ctx.save();
            ctx.strokeStyle = lineGrad;
            ctx.lineWidth = 3.0;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.restore();
        }

        ctx.restore();

        // 4. Draw Leading Glow Dot during entrance animation
        if (drawProgress < 1.0) {
            ctx.save();
            ctx.beginPath();
            ctx.arc(currentEndX, currentEndY, 7, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(239, 68, 68, 0.28)';
            ctx.fill();
            ctx.beginPath();
            ctx.arc(currentEndX, currentEndY, 4.5, 0, Math.PI * 2);
            ctx.fillStyle = '#ef4444';
            ctx.fill();
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();
            ctx.restore();
        }

        // 5. Draw Key Event Marker at Day 0 (Crisis Start)
        const pEvent = pts[1];
        if (currentEndX >= pEvent.x) {
            const pulse = (Math.sin(elapsed / 300) + 1) / 2;
            ctx.save();

            // Pulsing ring
            ctx.beginPath();
            ctx.arc(pEvent.x, pEvent.y, 4 + pulse * 4, 0, Math.PI * 2);
            ctx.strokeStyle = `rgba(245, 158, 11, ${(0.4 * (1 - pulse)).toFixed(3)})`;
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Event Center Point
            ctx.beginPath();
            ctx.arc(pEvent.x, pEvent.y, 4.5, 0, Math.PI * 2);
            ctx.fillStyle = '#f59e0b';
            ctx.fill();
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();

            // Event Tag with subtle pointer
            ctx.font = '600 9px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            const evText = 'Impacto Crisis';
            const evTw = ctx.measureText(evText).width;
            const evW = evTw + 12;
            const evH = 17;
            const evX = pEvent.x - evW / 2;
            const evY = pEvent.y - evH - 7;

            // Pill box
            ctx.beginPath();
            if (typeof ctx.roundRect === 'function') ctx.roundRect(evX, evY, evW, evH, 4);
            else ctx.rect(evX, evY, evW, evH);
            ctx.fillStyle = '#fffbeb';
            ctx.fill();
            ctx.strokeStyle = 'rgba(245, 158, 11, 0.6)';
            ctx.lineWidth = 1;
            ctx.stroke();

            // Small downward pointer
            ctx.beginPath();
            ctx.moveTo(pEvent.x - 3, evY + evH);
            ctx.lineTo(pEvent.x, evY + evH + 3);
            ctx.lineTo(pEvent.x + 3, evY + evH);
            ctx.fillStyle = '#fffbeb';
            ctx.fill();

            ctx.fillStyle = '#b45309';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(evText, pEvent.x, evY + evH / 2 + 0.5);

            ctx.restore();
        }

        // 6. Draw Final -11% Badge and Endpoint
        if (drawProgress >= 0.95) {
            const pEnd = pts[pts.length - 1];
            const endOpacity = Math.min((drawProgress - 0.95) / 0.05, 1);

            ctx.save();
            ctx.globalAlpha = endOpacity;

            // Final Dot
            ctx.beginPath();
            ctx.arc(pEnd.x, pEnd.y, 5.5, 0, Math.PI * 2);
            ctx.fillStyle = '#dc2626';
            ctx.fill();
            ctx.lineWidth = 2.5;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();

            // Floating -11% Value Badge
            if (!isHovering) {
                const badgeTitle = '▼ -11% Pérdida';
                ctx.font = 'bold 10.5px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                const bTw = ctx.measureText(badgeTitle).width;
                const bW = bTw + 14;
                const bH = 22;
                const bX = Math.min(width - padR - bW + 4, pEnd.x - bW + 6);
                const bY = pEnd.y - bH - 7;

                ctx.beginPath();
                if (typeof ctx.roundRect === 'function') ctx.roundRect(bX, bY, bW, bH, 5);
                else ctx.rect(bX, bY, bW, bH);
                ctx.fillStyle = '#dc2626';
                ctx.fill();

                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(badgeTitle, bX + bW / 2, bY + bH / 2 + 0.5);
            }

            ctx.restore();
        }

        // 7. Interactive Hover Cursor & Tooltip
        if (isHovering && mouseX >= padL && mouseX <= padL + chartW && drawProgress >= 1.0) {
            const hoverY = interpolateYAtX(pts, cp, mouseX);
            const hoverVal = maxVal - ((hoverY - padT) / chartH) * valRange;

            ctx.save();

            // Vertical Crosshair Line
            ctx.beginPath();
            ctx.setLineDash([3, 3]);
            ctx.strokeStyle = 'rgba(100, 116, 139, 0.45)';
            ctx.lineWidth = 1;
            ctx.moveTo(mouseX, padT);
            ctx.lineTo(mouseX, height - padB);
            ctx.stroke();
            ctx.setLineDash([]);

            // Inspection Halo Dot
            ctx.beginPath();
            ctx.arc(mouseX, hoverY, 6, 0, Math.PI * 2);
            ctx.fillStyle = hoverVal < -6 ? '#dc2626' : hoverVal < -1 ? '#f59e0b' : primaryColor;
            ctx.fill();
            ctx.lineWidth = 2.5;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();

            // Inspection Tooltip
            const valStr = `▼ ${hoverVal.toFixed(1)}% Valor`;
            ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            const tipW = Math.max(ctx.measureText(valStr).width + 18, 90);
            const tipH = 26;
            let tipX = mouseX - tipW / 2;
            let tipY = hoverY - tipH - 10;
            if (tipX < padL) tipX = padL;
            if (tipX + tipW > width - padR) tipX = width - padR - tipW;
            if (tipY < padT) tipY = hoverY + 12;

            ctx.beginPath();
            if (typeof ctx.roundRect === 'function') ctx.roundRect(tipX, tipY, tipW, tipH, 6);
            else ctx.rect(tipX, tipY, tipW, tipH);
            ctx.fillStyle = '#0f172a';
            ctx.fill();
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.lineWidth = 1;
            ctx.stroke();

            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(valStr, tipX + tipW / 2, tipY + tipH / 2 + 0.5);

            ctx.restore();
        }

        // Keep animating fallback only if GSAP is not present
        if (typeof gsap === 'undefined') {
            if (parentBlock && parentBlock.classList.contains('is-bottom')) {
                rafId = null;
                return;
            }

            if (drawProgress < 1.0 || isHovering || elapsed < 3500) {
                rafId = requestAnimationFrame(render);
            } else {
                rafId = null;
            }
        }
    }

    function startAnimation() {
        if (!isVisible || (parentBlock && parentBlock.classList.contains('is-bottom'))) return;

        if (typeof gsap !== 'undefined') {
            if (chartTween) chartTween.kill();
            chartState.progress = 0;
            startTime = performance.now();
            chartTween = gsap.to(chartState, {
                progress: 1,
                duration: 1.8,
                ease: 'power3.out',
                onUpdate: () => render(performance.now(), chartState.progress),
                onComplete: () => {
                    chartTween = null;
                    render(performance.now(), 1.0);
                }
            });
        } else if (!rafId && isVisible) {
            startTime = performance.now();
            rafId = requestAnimationFrame(render);
        }
    }

    canvas.style.cursor = 'crosshair';

    // Mouse interactions (Instant repaint, zero idle rAF loops)
    canvas.addEventListener('mousemove', (e) => {
        const rect = canvas.getBoundingClientRect();
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;
        isHovering = true;
        if (typeof gsap !== 'undefined') {
            render(performance.now(), chartState.progress >= 1 ? 1.0 : chartState.progress);
        } else if (!rafId) {
            rafId = requestAnimationFrame(render);
        }
    });

    canvas.addEventListener('mouseleave', () => {
        isHovering = false;
        mouseX = -1;
        mouseY = -1;
        if (typeof gsap !== 'undefined') {
            render(performance.now(), chartState.progress >= 1 ? 1.0 : chartState.progress);
        } else if (!rafId) {
            rafId = requestAnimationFrame(render);
        }
    });

    // Touch interactions
    function handleTouch(e) {
        if (e.touches && e.touches.length > 0) {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.touches[0].clientX - rect.left;
            mouseY = e.touches[0].clientY - rect.top;
            isHovering = true;
            if (typeof gsap !== 'undefined') {
                render(performance.now(), chartState.progress >= 1 ? 1.0 : chartState.progress);
            } else if (!rafId) {
                rafId = requestAnimationFrame(render);
            }
        }
    }
    canvas.addEventListener('touchstart', handleTouch, { passive: true });
    canvas.addEventListener('touchmove', handleTouch, { passive: true });
    canvas.addEventListener('touchend', () => {
        isHovering = false;
        mouseX = -1;
        mouseY = -1;
        if (typeof gsap !== 'undefined') {
            render(performance.now(), chartState.progress >= 1 ? 1.0 : chartState.progress);
        } else if (!rafId) {
            rafId = requestAnimationFrame(render);
        }
    }, { passive: true });

    resize();

    // Use ResizeObserver for responsive adaptation
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(() => {
            resize();
        });
        ro.observe(canvas);
        if (dataBlock) ro.observe(dataBlock);
    }
    window.addEventListener('resize', resize, { passive: true });

    // IntersectionObserver to trigger animation when visible
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    isVisible = true;
                    if (!(parentBlock && parentBlock.classList.contains('is-bottom'))) {
                        if (chartTween && chartState.progress < 1) {
                            chartTween.resume();
                        } else if (!chartTween && chartState.progress === 0) {
                            startAnimation();
                        }
                    }
                } else {
                    isVisible = false;
                    if (chartTween) chartTween.pause();
                    if (rafId) {
                        cancelAnimationFrame(rafId);
                        rafId = null;
                    }
                }
            });
        }, { threshold: 0.15 });

        observer.observe(canvas);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (chartTween) chartTween.pause();
                if (rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            } else if (isVisible && !(parentBlock && parentBlock.classList.contains('is-bottom'))) {
                if (chartTween && chartState.progress < 1) {
                    chartTween.resume();
                } else if (!chartTween && chartState.progress === 0) {
                    startAnimation();
                }
            }
        });
    } else {
        isVisible = true;
        if (!(parentBlock && parentBlock.classList.contains('is-bottom'))) {
            startAnimation();
        }
    }

    // Pause down chart animation when covered by sticky overlap
    if (parentBlock) {
        parentBlock.addEventListener('block:bottomChange', (e) => {
            if (e.detail.isBottom) {
                if (chartTween) chartTween.pause();
                if (rafId) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            } else if (isVisible) {
                if (chartTween && chartState.progress < 1) {
                    chartTween.resume();
                } else if (!chartTween && chartState.progress === 0) {
                    startAnimation();
                }
            }
        });
    }
}

// Expose functions globally for corporate templates
window.initHeroCrisisGrid = initHeroCrisisGrid;
window.initDownChart = initDownChart;

// Auto-initialize hero components if DOM is ready
function initHeroComponents() {
    if (typeof initHeroCrisisGrid === 'function') initHeroCrisisGrid();
    if (typeof initDownChart === 'function') initDownChart();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroComponents);
} else {
    initHeroComponents();
}
