document.addEventListener('DOMContentLoaded', function () {
    const inputCards = document.querySelectorAll('.phase-00 .card[data-step="input"]');
    const phase01 = document.querySelector('.phase-01');
    const cardEscucha = document.getElementById('card-escucha');
    const phase02 = document.querySelector('.phase-02');
    const clasifCards = document.querySelectorAll('.phase-02 .card[data-step="clasificacion"]');
    const phase03 = document.querySelector('.phase-03');
    const evalCards = document.querySelectorAll('.phase-03 .card[data-step="evaluacion"]');
    const phase04 = document.querySelector('.phase-04');
    const cardCompromiso = document.getElementById('card-compromiso');
    const cardMatriz = document.getElementById('card-matriz');
    const cardRegistro = document.getElementById('card-registro');
    const path12 = document.getElementById('flux-path-1-2');
    const path23 = document.getElementById('flux-path-2-3');
    const path34 = document.getElementById('flux-path-3-4');
    const path45 = document.getElementById('flux-path-4-5');
    const path5Loop = document.getElementById('flux-path-5-loop');
    const pathCompMatriz = document.getElementById('flux-path-5-comp-matriz');
    const pathMatrizRegistro = document.getElementById('flux-path-5-matriz-registro');
    const connectorSvg = document.querySelector('.flux-connector-svg');

    let currentTheme = null;
    let isPhase03Completed = false;

    function getThemeColor(theme) {
        if (theme === 'blue') return '#2563eb';
        if (theme === 'orange') return '#ea580c';
        if (theme === 'red') return '#dc2626';
        return '#3b82f6';
    }

    function buildOrthogonalPath(startRect, endRect, svgRect, radius = 10) {
        const x1 = startRect.right - svgRect.left;
        const y1 = (startRect.top + startRect.bottom) / 2 - svgRect.top;
        const x2 = endRect.left - svgRect.left;
        const y2 = (endRect.top + endRect.bottom) / 2 - svgRect.top;

        if (Math.abs(y2 - y1) < 4) {
            return `M ${x1} ${y1} L ${x2} ${y2}`;
        }

        const midX = (x1 + x2) / 2;
        const r = Math.min(radius, Math.abs(midX - x1), Math.abs(y2 - y1) / 2);

        if (y2 > y1) {
            return `M ${x1} ${y1} L ${midX - r} ${y1} Q ${midX} ${y1}, ${midX} ${y1 + r} L ${midX} ${y2 - r} Q ${midX} ${y2}, ${midX + r} ${y2} L ${x2} ${y2}`;
        } else {
            return `M ${x1} ${y1} L ${midX - r} ${y1} Q ${midX} ${y1}, ${midX} ${y1 - r} L ${midX} ${y2 + r} Q ${midX} ${y2}, ${midX + r} ${y2} L ${x2} ${y2}`;
        }
    }

    function updateAllConnectors() {
        if (!connectorSvg) return;
        const svgRect = connectorSvg.getBoundingClientRect();

        // 1. Sección 1 a la 2 (Inputs -> Escucha)
        const selectedInput = document.querySelector('.phase-00 .card.is-selected');
        if (selectedInput && cardEscucha && path12) {
            const startRect = selectedInput.getBoundingClientRect();
            const endRect = cardEscucha.getBoundingClientRect();
            path12.setAttribute('d', buildOrthogonalPath(startRect, endRect, svgRect));
            path12.style.opacity = '1';
        } else if (path12) {
            path12.style.opacity = '0';
        }

        // 2. Sección 2 a la 3 (Escucha -> Clasificación seleccionada)
        const selectedClasif = document.querySelector('.phase-02 .card.is-selected');
        if (selectedClasif && cardEscucha && path23) {
            const startRect = cardEscucha.getBoundingClientRect();
            const endRect = selectedClasif.getBoundingClientRect();
            path23.setAttribute('d', buildOrthogonalPath(startRect, endRect, svgRect));
            path23.setAttribute('stroke', getThemeColor(currentTheme));
            path23.style.opacity = '1';
        } else if (path23) {
            path23.style.opacity = '0';
        }

        // 3. Sección 3 a la 4 (Clasificación seleccionada -> Evaluación activa)
        const activeEval = document.querySelector('.phase-03 .card:not(.is-disabled)');
        if (selectedClasif && activeEval && path34) {
            const startRect = selectedClasif.getBoundingClientRect();
            const endRect = activeEval.getBoundingClientRect();
            path34.setAttribute('d', buildOrthogonalPath(startRect, endRect, svgRect));
            path34.setAttribute('stroke', getThemeColor(currentTheme));
            path34.style.opacity = '1';
        } else if (path34) {
            path34.style.opacity = '0';
        }

        // 4. Sección 4 a la 5 (Evaluación activa -> Salida: Compromiso de plazo)
        if (isPhase03Completed && activeEval && cardCompromiso && path45) {
            const startRect = activeEval.getBoundingClientRect();
            const endRect = cardCompromiso.getBoundingClientRect();
            path45.setAttribute('d', buildOrthogonalPath(startRect, endRect, svgRect));
            path45.setAttribute('stroke', getThemeColor(currentTheme));
            path45.style.opacity = '1';
        } else if (path45) {
            path45.style.opacity = '0';
        }

        // 5. Salida: Compromiso de plazo -> Registro y trazabilidad (Flujo azul / Evaluación y variables)
        if (isPhase03Completed && currentTheme === 'blue' && cardCompromiso && cardRegistro && path5Loop && cardCompromiso.classList.contains('is-selected')) {
            const startRect = cardCompromiso.getBoundingClientRect();
            const endRect = cardRegistro.getBoundingClientRect();
            const startX = startRect.right - svgRect.left;
            const y1 = (startRect.top + startRect.bottom) / 2 - svgRect.top;
            const endX = endRect.right - svgRect.left;
            const y2 = (endRect.top + endRect.bottom) / 2 - svgRect.top;

            const rightX = Math.max(startX, endX);
            const offset = Math.min(rightX + 10, svgRect.width - 4);
            const r = Math.min(8, Math.max(2, offset - rightX), Math.abs(y2 - y1) / 2);

            const d = `M ${startX} ${y1} L ${offset - r} ${y1} Q ${offset} ${y1}, ${offset} ${y1 + r} L ${offset} ${y2 - r} Q ${offset} ${y2}, ${offset - r} ${y2} L ${endX} ${y2}`;
            path5Loop.setAttribute('d', d);
            path5Loop.setAttribute('stroke', getThemeColor('blue'));
            path5Loop.style.opacity = '1';
        } else if (path5Loop) {
            path5Loop.style.opacity = '0';
        }

        // 6. Salida: Compromiso de plazo -> Matriz de riesgo -> Registro (Flujo naranja y rojo / Criterios de riesgo y Protocolo de alerta roja)
        if (isPhase03Completed && (currentTheme === 'orange' || currentTheme === 'red') && cardCompromiso && cardMatriz && cardRegistro) {
            const compRect = cardCompromiso.getBoundingClientRect();
            const matrizRect = cardMatriz.getBoundingClientRect();
            const regRect = cardRegistro.getBoundingClientRect();

            // Compromiso a Matriz de riesgo
            if (pathCompMatriz && cardCompromiso.classList.contains('is-selected')) {
                const xMid1 = (compRect.left + compRect.right) / 2 - svgRect.left;
                const yBottom1 = compRect.bottom - svgRect.top;
                const yTop1 = matrizRect.top - svgRect.top;
                pathCompMatriz.setAttribute('d', `M ${xMid1} ${yBottom1} L ${xMid1} ${yTop1}`);
                pathCompMatriz.setAttribute('stroke', getThemeColor(currentTheme));
                pathCompMatriz.style.opacity = '1';
            } else if (pathCompMatriz) {
                pathCompMatriz.style.opacity = '0';
            }

            // Matriz de riesgo a Registro y trazabilidad
            if (pathMatrizRegistro && cardMatriz.classList.contains('is-selected')) {
                const xMid2 = (matrizRect.left + matrizRect.right) / 2 - svgRect.left;
                const yBottom2 = matrizRect.bottom - svgRect.top;
                const yTop2 = regRect.top - svgRect.top;
                pathMatrizRegistro.setAttribute('d', `M ${xMid2} ${yBottom2} L ${xMid2} ${yTop2}`);
                pathMatrizRegistro.setAttribute('stroke', getThemeColor(currentTheme));
                pathMatrizRegistro.style.opacity = '1';
            } else if (pathMatrizRegistro) {
                pathMatrizRegistro.style.opacity = '0';
            }
        } else {
            if (pathCompMatriz) pathCompMatriz.style.opacity = '0';
            if (pathMatrizRegistro) pathMatrizRegistro.style.opacity = '0';
        }
    }

    // Paso 1: Selección en Fase 00 (Inputs)
    inputCards.forEach(card => {
        card.addEventListener('click', function () {
            inputCards.forEach(c => {
                if (c === card) {
                    c.classList.add('is-selected');
                    c.classList.remove('is-disabled');
                } else {
                    c.classList.remove('is-selected');
                    c.classList.add('is-disabled');
                }
            });

            // Activar Fase 01 y tarjeta de Escucha con atención
            phase01.classList.remove('is-disabled');
            cardEscucha.classList.remove('is-disabled');

            // Activar Fase 02 (Clasificación temática)
            phase02.classList.remove('is-disabled');
            clasifCards.forEach(c => {
                c.classList.remove('is-disabled');
                c.classList.add('is-selectable');
                c.classList.add(c.getAttribute('data-theme'));
            });

            updateAllConnectors();
        });
    });

    // Paso 2: Selección en Fase 02 (Clasificación temática)
    clasifCards.forEach(card => {
        card.addEventListener('click', function () {
            if (card.classList.contains('is-disabled') && !card.classList.contains('is-selectable')) return;

            clasifCards.forEach(c => {
                const theme = c.getAttribute('data-theme');
                if (c === card) {
                    c.classList.add('is-selected');
                    c.classList.remove('is-disabled');
                    c.classList.add(theme);
                } else {
                    c.classList.remove('is-selected', 'blue', 'orange', 'red');
                    c.classList.add('is-disabled');
                }
            });

            currentTheme = card.getAttribute('data-theme');

            // Activar Fase 03 y solo la tarjeta de evaluación pertinente
            phase03.classList.remove('is-disabled');
            evalCards.forEach(ec => {
                const theme = ec.getAttribute('data-theme');
                if (theme === currentTheme) {
                    ec.classList.remove('is-disabled');
                    ec.classList.add(theme);
                } else {
                    ec.classList.add('is-disabled');
                    ec.classList.remove('blue', 'orange', 'red');
                }
            });

            // Evaluar si la nueva clasificación ya tiene todos los campos llenos o debe desactivar salida
            evaluatePhase03State();

            updateAllConnectors();
        });
    });

    // Paso 3 & 4: Detección estricta de llenado de TODOS los campos en Fase 03
    function checkPhase03Completion() {
        if (!currentTheme) return false;

        if (currentTheme === 'blue') {
            const hasRel = !!document.querySelector('input[name="eval_relaciones"]:checked');
            const hasProg = !!document.querySelector('input[name="eval_programas"]:checked');
            const hasRec = !!document.querySelector('input[name="eval_recursos"]:checked');
            const hasDecision = !!document.querySelector('.card[data-theme="blue"] .btn-decision.is-selected');
            return hasRel && hasProg && hasRec && hasDecision;
        }

        if (currentTheme === 'orange') {
            const hasOrigen = !!document.querySelector('input[name="riesgo_origen"]:checked');
            const hasPersonas = !!document.querySelector('input[name="riesgo_personas"]:checked');
            const hasNovedad = !!document.querySelector('input[name="riesgo_novedad"]:checked');
            const hasSalud = !!document.querySelector('input[name="riesgo_salud"]:checked');
            const hasUrgencia = !!document.querySelector('input[name="riesgo_urgencia"]:checked');
            return hasOrigen && hasPersonas && hasNovedad && hasSalud && hasUrgencia;
        }

        if (currentTheme === 'red') {
            const hasAction = !!document.querySelector('.card[data-theme="red"] .btn-action.is-selected');
            return hasAction;
        }

        return false;
    }

    function evaluatePhase03State() {
        if (checkPhase03Completion()) {
            activatePhase04();
        } else {
            deactivatePhase04();
        }
    }

    function deactivatePhase04() {
        isPhase03Completed = false;
        phase04.classList.add('is-disabled');
        cardCompromiso.classList.add('is-disabled');
        cardCompromiso.classList.remove('blue', 'orange', 'red', 'is-selected', 'is-selectable');
        cardMatriz.classList.add('is-disabled');
        cardMatriz.classList.remove('blue', 'orange', 'red', 'is-selected', 'is-selectable');
        cardRegistro.classList.add('is-disabled');
        if (path45) {
            path45.style.opacity = '0';
        }
        if (path5Loop) {
            path5Loop.style.opacity = '0';
        }
        if (pathCompMatriz) {
            pathCompMatriz.style.opacity = '0';
        }
        if (pathMatrizRegistro) {
            pathMatrizRegistro.style.opacity = '0';
        }
        updateAllConnectors();
    }

    function activatePhase04() {
        if (!currentTheme) return;
        isPhase03Completed = true;

        // Desbloquear Fase 04 (Salida y registro)
        phase04.classList.remove('is-disabled');

        // 1. Compromiso de plazo: siempre se activa y toma el color de la clasificación elegida
        cardCompromiso.classList.remove('blue', 'orange', 'red', 'is-disabled');
        cardCompromiso.classList.add(currentTheme);
        cardCompromiso.classList.add('is-selectable', 'is-selected');

        // 2. Matriz de riesgo: se activa cuando corresponde a Queja (orange) o Crisis (red)
        if (currentTheme === 'orange' || currentTheme === 'red') {
            cardMatriz.classList.remove('is-disabled');
            cardMatriz.classList.add(currentTheme, 'is-selectable', 'is-selected');
        } else {
            cardMatriz.classList.add('is-disabled');
            cardMatriz.classList.remove('blue', 'orange', 'red', 'is-selectable', 'is-selected');
        }

        // 3. Registro y trazabilidad: siempre se activa
        cardRegistro.classList.remove('is-disabled');

        updateAllConnectors();
    }

    // Selección interactiva de Compromiso de plazo
    if (cardCompromiso) {
        cardCompromiso.addEventListener('click', function () {
            if (cardCompromiso.classList.contains('is-disabled')) return;
            cardCompromiso.classList.toggle('is-selected');
            updateAllConnectors();
        });
    }

    // Selección interactiva de Matriz de riesgo
    if (cardMatriz) {
        cardMatriz.addEventListener('click', function () {
            if (cardMatriz.classList.contains('is-disabled')) return;
            cardMatriz.classList.toggle('is-selected');
            updateAllConnectors();
        });
    }

    // Escuchar cambios en los inputs y botones de evaluación
    evalCards.forEach(ec => {
        ec.addEventListener('change', function () {
            evaluatePhase03State();
        });

        ec.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                // Alternar selección activa del botón
                const siblings = btn.parentElement.querySelectorAll('button');
                siblings.forEach(s => s.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                evaluatePhase03State();
            });
        });
    });

    window.addEventListener('resize', updateAllConnectors);
    window.addEventListener('scroll', updateAllConnectors);
});