<?php
/**
 * Template Name: Gestión Social Mac
 *
 * Corporate landing page template for The Crisis Academy.
 *
 * @package TheCrisisAcademy
 */

get_header(); ?>

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

<?php get_footer();