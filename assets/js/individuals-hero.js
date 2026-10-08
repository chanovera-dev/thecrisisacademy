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
    const COLOR_ALERT = { r: 255, g: 45, b: 45 };
    const COLOR_EXPLODE = { r: 201, g: 10, b: 33 };
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

// Expose functions globally for corporate templates
window.initHeroCrisisGrid = initHeroCrisisGrid;

// Auto-initialize hero components if DOM is ready
function initHeroComponents() {
    if (typeof initHeroCrisisGrid === 'function') initHeroCrisisGrid();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroComponents);
} else {
    initHeroComponents();
}
