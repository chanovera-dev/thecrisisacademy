<?php
/**
 * Template Name: Gestión Social Mac
 *
 * Corporate landing page template for The Crisis Academy.
 *
 * @package TheCrisisAcademy
 */

get_header(); ?>

    <style>
        h1, h2, h3, h4, p, ul {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        .section-header.center {
            text-align: center;
            margin-bottom: 2rem;

            & .page-title {
                font-size: 2rem;
                margin: 0;
            }

            & .page-subtitle {
                font-size: 1.5rem;
                margin: 0;
            }
        }

        .flux {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            border: 1px solid #cbd5e1;
            border-radius: var(--badge-border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            position: relative;

            .is-chromium & {
                corner-shape: squircle;
            }

            & .flux-connector-svg {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 10;
            }

            & .phase {
                display: grid;
                grid-template-rows: auto 1fr;
                transition: opacity 0.3s ease;

                &.is-disabled {
                    opacity: 0.5;

                    & .phase-title {
                        opacity: 0.5;
                    }
                }

                &.phase-00 {
                    background-color: #f8fafc;
                }

                &.phase-01 {
                    background-color: #f1f5f9;
                }

                &.phase-02 {
                    background-color: #e2e8f0;
                }

                &.phase-03 {
                    background-color: #f1f5f9;
                }

                &.phase-04 {
                    background-color: #e2e8f0;
                }

                & > .phase-title {
                    padding: .4rem 1rem;
                    border-bottom: 1px solid #d4d9e1;
                    transition: opacity 0.3s ease;

                    & h3 {
                        font-size: 1rem;
                        text-align: center;
                        color: #515f72;

                    }
                }

                & .phase-body {
                    padding: 1rem;
                    display: grid;
                    place-content: center;
                    gap: 1rem;

                    & .card {
                        background-color: #fff;
                        border-radius: var(--badge-border-radius);
                        box-shadow: var(--shadow-card);
                        border: 1px solid #cbd5e1;
                        overflow: hidden;
                        transition: opacity 0.3s ease, filter 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;

                        &.is-selectable {
                            cursor: pointer;

                            &:hover {
                                box-shadow: var(--shadow-card-hover);
                                border-color: #94a3b8;
                            }
                        }

                        &.is-selected {
                            box-shadow: 0 0 0 2px #3b82f6, 0 4px 12px rgba(59, 130, 246, 0.22);
                            border-color: #2563eb;
                            opacity: 1 !important;
                            filter: none !important;
                            pointer-events: auto !important;

                            &.blue {
                                box-shadow: var(--shadow-card), 0 0 0 2px #2563eb, 0 4px 12px rgba(37, 99, 235, 0.25);
                                border-color: #1d4ed8;
                            }

                            &.orange {
                                box-shadow: var(--shadow-card), 0 0 0 2px #ea580c, 0 4px 12px rgba(234, 88, 12, 0.25);
                                border-color: #c2410c;
                            }

                            &.red {
                                box-shadow: var(--shadow-card), 0 0 0 2px #dc2626, 0 4px 12px rgba(220, 38, 38, 0.25);
                                border-color: #b91c1c;
                            }
                        }

                        &.is-disabled {
                            opacity: 0.75;
                            filter: grayscale(0.6);
                            pointer-events: none;
                            user-select: none;
                            box-shadow: none !important;
                        }

                        &.blue:not(.is-disabled) {
                            color: #607bca;
                            background-color: #dbeafe;
                            border-color: #bfdbfe;
                        }

                        &.orange:not(.is-disabled) {
                            color: #712910;
                            background-color: #ffedd5;
                            border-color: #f8d9b5;
                        }

                        &.red:not(.is-disabled) {
                            color: #941a1a;
                            background-color: #fee2e2;
                            border-color: #f6b2b5;
                        }

                        & .card-header,
                        & .card-body,
                        & .card-footer {
                            padding: .7rem 1rem;
                        }

                        & .card-header {
                            & h4 {
                                font-size: 1.1rem;
                                
                            }
                        }

                        & .card-body {
                            padding-top: 0;
                            font-size: .9rem;

                            & .tag {
                                font-size: .8rem;
                                padding: .1rem .4rem;
                                border-radius: var(--minibadge-border-radius);
                                border: 1px solid #cbd5e1;
                                background-color: #f8fafc;
                                margin-bottom: .7rem;

                                .is-chromium & {
                                    corner-shape: squircle;
                                }

                                &.warning {
                                    color: #961a1a;
                                    background-color: #fee2e2;
                                    border-color: #fcaaaa;
                                }

                                &.attention {
                                    color: #216d3d;
                                    background-color: #dcfce7;
                                    border-color: #8cefb1;
                                }
                            }

                            & ul {
                                padding-left: 0;
                                list-style: none;

                                & li {
                                    padding: .4rem 0;

                                    & p {
                                        margin-bottom: .35rem;
                                        line-height: 1.35;
                                    }
                                    
                                    &:not(:last-child) {
                                        border-bottom: 1px solid #cbd5e1;

                                        .blue & {
                                            border-color: #aebff2;
                                        }

                                        .orange & {
                                            border-color: #f8d9b5;
                                        }
                                    }
                                }
                            }

                            & .options {
                                display: flex;
                                gap: .35rem;
                                flex-wrap: wrap;

                                & .option-chip {
                                    cursor: pointer;
                                    user-select: none;
                                    margin: 0;

                                    & input {
                                        position: absolute;
                                        opacity: 0;
                                        pointer-events: none;
                                    }

                                    & span {
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        padding: .2rem .55rem;
                                        font-size: .78rem;
                                        font-weight: 500;
                                        background: #ffffff42;
                                        border: 1px solid #cbd5e1;
                                        border-radius: var(--minibadge-border-radius);
                                        color: #334155;
                                        transition: all .15s ease;

                                        .is-chromium & {
                                            corner-shape: squircle;
                                        }

                                        .orange & {
                                            border-color: #f8d9b5;
                                        }

                                        .blue & {
                                            border-color: #bfdbfe;
                                        }

                                        .is-chromium & {
                                            corner-shape: squircle;
                                        }
                                    }

                                    &:hover span {
                                        background: #f8fafc;
                                        border-color: #94a3b8;
                                    }

                                    &:has(input:checked) span {
                                        color: #ffffff;
                                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);

                                        .blue & {
                                            background-color: #2563eb;
                                            border-color: #1d4ed8;
                                        }

                                        .orange & {
                                            background-color: #ea580c;
                                            border-color: #c2410c;
                                        }
                                    }
                                }

                                &.scale-options {
                                    flex-direction: column;
                                    gap: .2rem;
                                    width: 100%;

                                    & .scale-steps {
                                        display: grid;
                                        grid-template-columns: repeat(5, 1fr);
                                        gap: .3rem;

                                        & .scale-step {
                                            cursor: pointer;
                                            user-select: none;
                                            margin: 0;

                                            & input {
                                                position: absolute;
                                                opacity: 0;
                                                pointer-events: none;
                                            }

                                            & span {
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                height: 26px;
                                                font-size: .8rem;
                                                font-weight: 600;
                                                background: #ffffff42;
                                                border: 1px solid #cbd5e1;
                                                border-radius: var(--minibadge-border-radius);
                                                color: #475569;
                                                transition: all .15s ease;

                                                .is-chromium & {
                                                    corn-shape: squircle;
                                                }

                                                .orange & {
                                                    border-color: #f8d9b5;
                                                }

                                                .blue & {
                                                    border-color: #bfdbfe;
                                                }

                                                .is-chromium & {
                                                    corner-shape: squircle;
                                                }
                                            }

                                            &:hover span {
                                                background: #f8fafc;
                                                border-color: #94a3b8;
                                            }

                                            &:has(input:checked) span {
                                                color: #ffffff;
                                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);

                                                .blue & {
                                                    background-color: #2563eb;
                                                    border-color: #1d4ed8;
                                                }

                                                .orange & {
                                                    background-color: #ea580c;
                                                    border-color: #c2410c;
                                                }
                                            }
                                        }
                                    }

                                    & .scale-labels {
                                        display: flex;
                                        justify-content: space-between;
                                        font-size: .68rem;
                                        color: #64748b;
                                        padding: 0 .1rem;
                                        font-weight: 500;

                                        .orange & {
                                            color: #9a492e;
                                        }

                                        .blue & {
                                            color: #4b6cb7;
                                        }
                                    }
                                }
                            }
                        }

                        & .card-footer {
                            font-size: .8rem;
                            font-weight: 500;
                            background-color: #f1f5f9;
                            color: #4a586b;

                            .blue & {
                                color: #607bca;
                                background-color: #cee3fd;
                            }

                            .orange & {
                                color: #712910;
                                background-color: #ffe8c9;
                            }

                            .red & {
                                color: #961a1a;
                                background-color: #fed8d8ff;
                            }

                            & button {
                                font-size: .8rem;
                                border: 1px solid #cbd5e1;
                                background-color: #ffffff42;
                                border-radius: var(--minibadge-border-radius);
                                transition: all .15s ease;
                                height: 37px;

                                .is-chromium & {
                                    corner-shape: squircle;
                                }

                                .blue & {
                                    color: #1e40af;
                                    border-color: #bfdbfe;
                                }

                                .orange & {
                                    color: #712910;
                                    border-color: #f8d9b5;
                                }

                                .red & {
                                    color: #7f1d1d;
                                    border-color: #fecaca;
                                }
                            }

                            &:has(button) {
                                display: flex;
                                gap: .5rem;

                                & button {
                                    flex: 1;
                                    text-wrap: nowrap;
                                    cursor: pointer;
                                    transition: all .15s ease;

                                    &.is-selected {
                                        background-color: #2563eb;
                                        color: #ffffff;
                                        border-color: #1d4ed8;
                                        font-weight: 600;
                                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);

                                        .red & {
                                            background-color: #dc2626;
                                            border-color: #b91c1c;
                                        }

                                        .blue & {
                                            background-color: #2563eb;
                                            border-color: #1d4ed8;
                                        }
                                    }
                                }
                            }
                        }

                        .is-chromium & {
                            corner-shape: squircle;
                        }
                    }

                }
            }
        }
    </style>

    <section class="block">
        <div class="content">
            <header class="section-header center">
                <h1 class="page-title">Proceso de Comunicación y Gestión Social para Coordinadores MAC</h1>
                <p class="page-subtitle">Flujo lógico, ramificaciones de decisión, criterios de riesgo y protocolos de acción institucional</p>
            </header>
            <div class="flux">
                <svg class="flux-connector-svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="flux-line-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="100%" stop-color="#6366f1" />
                        </linearGradient>
                    </defs>
                    <path id="flux-path-1-2" class="flux-connector-path" d="" fill="none" stroke="url(#flux-line-gradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease;" />
                    <path id="flux-path-2-3" class="flux-connector-path" d="" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                    <path id="flux-path-3-4" class="flux-connector-path" d="" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                    <path id="flux-path-4-5" class="flux-connector-path" d="" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                    <path id="flux-path-5-loop" class="flux-connector-path" d="" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                    <path id="flux-path-5-comp-matriz" class="flux-connector-path" d="" fill="none" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                    <path id="flux-path-5-matriz-registro" class="flux-connector-path" d="" fill="none" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0; transition: opacity 0.3s ease, stroke 0.3s ease;" />
                </svg>

                <div class="phase phase-00">
                    <header class="phase-title">
                        <h3>1. Fase de entrada (Inputs)</h3>
                    </header>
                    <div class="phase-body">
                        <div class="card is-selectable" data-step="input" data-input-id="observacion">
                            <header class="card-header">
                                <h4>Observación y detección de necesidades</h4>
                            </header>
                            <div class="card-body">
                                Identificación proactiva de necesidades en campo realizada por el equipo coordinador MAC.
                            </div>
                            <footer class="card-footer">
                                <span class="">Origen territorial</span>
                            </footer>
                        </div>
                        <div class="card is-selectable" data-step="input" data-input-id="acercamiento">
                            <header class="card-header">
                                <h4>Acercamiento de las comunidades o líderes</h4>
                            </header>
                            <div class="card-body">
                                Iniciativa directa de representantes comunitarios, líderes de opinión, ejidos o vecinos organizados.
                            </div>
                            <footer class="card-footer">
                                <span>Iniciativa social</span>
                            </footer>
                        </div>
                        <div class="card is-selectable" data-step="input" data-input-id="autoridad">
                            <header class="card-header">
                                <h4>Petición de una autoridad</h4>
                            </header>
                            <div class="card-body">
                                Solicitud formal, oficio o planteamiento emitido por dependencias públicas o entidades reguladoras.
                            </div>
                            <footer class="card-footer">
                                <span>Instancia pública</span>
                            </footer>
                        </div>
                    </div>
                </div>
                <div class="phase phase-01 is-disabled">
                    <header class="phase-title">
                        <h3>2. Escucha con atención</h3>
                    </header>
                    <div class="phase-body">
                        <div class="card is-disabled" id="card-escucha">
                            <header class="card-header">
                                <h4>Escucha con atención</h4>
                            </header>
                            <div class="card-body">
                                <div class="tag attention">
                                    "La escucha no genera ningún compromiso"
                                </div>
                                El coordinador escucha activamente, documenta hechos y contexto sin asumir compromisos verbales inmediatos.
                            </div>
                            <footer class="card-footer">
                                <p><b>Registro obligatorio:</b> Coordinador, Interlocutor, Comunidad, Contacto, Hechos, Fecha.</p>
                            </footer>
                        </div>
                    </div>
                </div>
                <div class="phase phase-02 is-disabled">
                    <header class="phase-title">
                        <h3>3. Clasificación temática</h3>
                    </header>
                    <div class="phase-body">
                        <div class="card is-disabled" data-step="clasificacion" data-theme="blue">
                            <header class="card-header">
                                <h4>Solicitud, petición o propuesta</h4>
                            </header>
                            <div class="card-body">
                                Peticiones de apoyo, proyectos comunitarios, donaciones, mantenimiento o mejoras sociales.
                            </div>
                        </div>
                        <div class="card is-disabled" data-step="clasificacion" data-theme="orange">
                            <header class="card-header">
                                <h4>Queja, problema o exigencia</h4>
                            </header>
                            <div class="card-body">
                                Inconformidades operativas, molestias por ruido, emisiones o impactos directos por ruido, emisiones o impactos directos hacia la planta.
                            </div>
                        </div>
                        <div class="card is-disabled" data-step="clasificacion" data-theme="red">
                            <header class="card-header">
                                <h4>Crisis</h4>
                            </header>
                            <div class="card-body">
                                Bloqueos inminentes, incidentes de seguridad física, paros de planta o contingencias mayores.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="phase phase-03 is-disabled">
                    <header class="phase-title">
                        <h3>4. Evaluación y decisión</h3>
                    </header>
                    <div class="phase-body">
                        <div class="card is-disabled" data-step="evaluacion" data-theme="blue">
                            <header class="card-header">
                                <h4>Evaluación de variables</h4>
                            </header>
                            <div class="card-body">
                                <ul>
                                    <li class="item">
                                        <p>Beneficio en relaciones con comunidad o actores</p>
                                        <div class="options scale-options">
                                            <div class="scale-steps">
                                                <label class="scale-step"><input type="radio" name="eval_relaciones" value="1"><span>1</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_relaciones" value="2"><span>2</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_relaciones" value="3"><span>3</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_relaciones" value="4"><span>4</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_relaciones" value="5"><span>5</span></label>
                                            </div>
                                            <div class="scale-labels">
                                                <span>Nada (0)</span>
                                                <span>Alto</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>Está alineado a los programas actuales</p>
                                        <div class="options scale-options">
                                            <div class="scale-steps">
                                                <label class="scale-step"><input type="radio" name="eval_programas" value="1"><span>1</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_programas" value="2"><span>2</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_programas" value="3"><span>3</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_programas" value="4"><span>4</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_programas" value="5"><span>5</span></label>
                                            </div>
                                            <div class="scale-labels">
                                                <span>Nada</span>
                                                <span>Mucho</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>Implica recursos</p>
                                        <div class="options scale-options">
                                            <div class="scale-steps">
                                                <label class="scale-step"><input type="radio" name="eval_recursos" value="1"><span>1</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_recursos" value="2"><span>2</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_recursos" value="3"><span>3</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_recursos" value="4"><span>4</span></label>
                                                <label class="scale-step"><input type="radio" name="eval_recursos" value="5"><span>5</span></label>
                                            </div>
                                            <div class="scale-labels">
                                                <span>Nada</span>
                                                <span>Bastante</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <footer class="card-footer">
                                <button type="button" class="btn-decision" data-decision="ok">Tomarla</button>
                                <button type="button" class="btn-decision" data-decision="no">Rechazarla</button>
                            </footer>
                        </div>
                        <div class="card is-disabled" data-step="evaluacion" data-theme="orange">
                            <header class="card-header">
                                <h4>Criterios de riesgo</h4>
                            </header>
                            <div class="card-body">
                                <ul>
                                    <li class="item">
                                        <p>¿Relacionado u originado por la planta?</p>
                                        <div class="options">
                                            <label class="option-chip"><input type="radio" name="riesgo_origen" value="no"><span>No</span></label>
                                            <label class="option-chip"><input type="radio" name="riesgo_origen" value="si"><span>Sí</span></label>
                                            <label class="option-chip"><input type="radio" name="riesgo_origen" value="nose"><span>No sé</span></label>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>¿Es una sola persona o un grupo de personas las que presentan el input?</p>
                                        <div class="options">
                                            <label class="option-chip"><input type="radio" name="riesgo_personas" value="persona"><span>Persona</span></label>
                                            <label class="option-chip"><input type="radio" name="riesgo_personas" value="grupo"><span>Grupo</span></label>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>¿Es un tema nuevo o habíamos recibido quejas antes?</p>
                                        <div class="options">
                                            <label class="option-chip"><input type="radio" name="riesgo_novedad" value="nuevo"><span>Es nuevo</span></label>
                                            <label class="option-chip"><input type="radio" name="riesgo_novedad" value="sabido"><span>Ya se sabía</span></label>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>¿Hay riesgos para la salud o seguridad de la población?</p>
                                        <div class="options">
                                            <label class="option-chip"><input type="radio" name="riesgo_salud" value="no"><span>No</span></label>
                                            <label class="option-chip"><input type="radio" name="riesgo_salud" value="si"><span>Sí</span></label>
                                        </div>
                                    </li>
                                    <li class="item">
                                        <p>¿Qué nivel de urgencia expresan o percibes?</p>
                                        <div class="options scale-options">
                                            <div class="scale-steps">
                                                <label class="scale-step"><input type="radio" name="riesgo_urgencia" value="1"><span>1</span></label>
                                                <label class="scale-step"><input type="radio" name="riesgo_urgencia" value="2"><span>2</span></label>
                                                <label class="scale-step"><input type="radio" name="riesgo_urgencia" value="3"><span>3</span></label>
                                                <label class="scale-step"><input type="radio" name="riesgo_urgencia" value="4"><span>4</span></label>
                                                <label class="scale-step"><input type="radio" name="riesgo_urgencia" value="5"><span>5</span></label>
                                            </div>
                                            <div class="scale-labels">
                                                <span>Nada urgente</span>
                                                <span>Muy urgente</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <footer class="card-footer">
                                <p><b>Matriz de riesgo:</b> Clasificación de severidad y plan de mitigación.</p>
                            </footer>
                        </div>
                        <div class="card is-disabled" data-step="evaluacion" data-theme="red">
                            <header class="card-header">
                                <h4>Protocolo de alerta roja</h4>
                            </header>
                            <div class="card-body">
                                Reporte inmediato a Rocío Flor Fermín
                            </div>
                            <footer class="card-footer">
                                <button type="button" class="btn-action">Whatsapp</button>
                                <button type="button" class="btn-action">Llamada</button>
                                <button type="button" class="btn-action">e-mail</button>
                            </footer>
                        </div>
                    </div>
                </div>
                <div class="phase phase-04 is-disabled">
                    <header class="phase-title">
                        <h3>5. Salida y registro</h3>
                    </header>
                    <div class="phase-body">
                        <div class="card is-disabled" id="card-compromiso">
                            <div class="card-header">
                                <h4>Compromiso de plazo</h4>
                            </div>
                            <div class="card-body">
                                <p>Llenar el reporte y <b>avisar al interlocutor en <u>máximo 5 días.</u></b></p>
                            </div>
                        </div>
                        <div class="card is-disabled" id="card-matriz">
                            <div class="card-header">
                                <h4>Matriz de riesgo</h4>
                            </div>
                            <div class="card-body">
                                <p>Clasificación de severidad y definición de medidas de contención técnica o comunitaria.</p>
                            </div>
                        </div>
                        <div class="card strong is-disabled" id="card-registro">
                            <header class="card-header">
                                <h4>Registro y trazabilidad</h4>
                            </header>
                            <div class="card-body">
                                <ul>
                                    <li class="item"><b>Base de datos:</b> Se guarda cada ciclo con folio correlativo ńico institucional</li>
                                    <li class="item"><b>Impresión a PDF:</b> Entrega e impresión inmediata del informe oficial con todas las respuestas</li>
                                </ul>
                            </div>
                            <footer class="card-footer">
                                Folio MAC-YYYY-MMDD
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
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
    </script>

<?php get_footer();