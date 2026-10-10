<?php
/**
 * Individuals Custom Field Groups (Native ACF-alternative)
 *
 * Implements native WordPress meta boxes and repeaters for the Particulares landing page
 * (templates/individuals.php) without requiring the Advanced Custom Fields / Secure Custom Fields plugin.
 *
 * All comments and DocBlocks are in English.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   0. ACF Compatibility Fallback Bridge
   ========================================================================== */

if ( ! function_exists( 'get_field' ) ) {
	/**
	 * Fallback get_field function when ACF / SCF is not active.
	 *
	 * @param string   $selector Field name or meta key.
	 * @param int|bool $post_id  Post ID (defaults to current post).
	 * @return mixed
	 */
	function get_field( $selector, $post_id = false, $format_value = true ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		if ( ! $post_id ) {
			return null;
		}

		$val = get_post_meta( $post_id, $selector, true );
		if ( '' === $val || false === $val || null === $val ) {
			$val = get_post_meta( $post_id, '_' . $selector, true );
		}

		return ( '' !== $val && false !== $val ) ? $val : null;
	}
}

if ( ! function_exists( 'have_rows' ) ) {
	global $tca_repeater_stack;
	$tca_repeater_stack = array();

	/**
	 * Fallback have_rows function when ACF / SCF is not active.
	 *
	 * @param string   $selector Repeater field name.
	 * @param int|bool $post_id  Post ID.
	 * @return bool
	 */
	function have_rows( $selector, $post_id = false ) {
		global $tca_repeater_stack;
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! isset( $tca_repeater_stack[ $selector ] ) ) {
			$rows = get_field( $selector, $post_id );
			if ( ! is_array( $rows ) ) {
				$rows = array();
			}
			$tca_repeater_stack[ $selector ] = array(
				'rows'  => array_values( $rows ),
				'index' => -1,
			);
		}

		$stack = &$tca_repeater_stack[ $selector ];
		if ( isset( $stack['rows'][ $stack['index'] + 1 ] ) ) {
			return true;
		}

		unset( $tca_repeater_stack[ $selector ] );
		return false;
	}

	/**
	 * Fallback the_row function when ACF / SCF is not active.
	 *
	 * @return array|bool
	 */
	function the_row() {
		global $tca_repeater_stack;
		$keys = array_keys( $tca_repeater_stack );
		if ( empty( $keys ) ) {
			return false;
		}

		$current_key = end( $keys );
		$stack       = &$tca_repeater_stack[ $current_key ];
		$stack['index']++;

		return $stack['rows'][ $stack['index'] ] ?? false;
	}

	/**
	 * Fallback get_sub_field function when ACF / SCF is not active.
	 *
	 * @param string $selector Sub-field name.
	 * @return mixed
	 */
	function get_sub_field( $selector ) {
		global $tca_repeater_stack;
		$keys = array_keys( $tca_repeater_stack );
		if ( empty( $keys ) ) {
			return null;
		}

		$current_key = end( $keys );
		$stack       = $tca_repeater_stack[ $current_key ];
		$row         = $stack['rows'][ $stack['index'] ] ?? array();

		if ( isset( $row[ $selector ] ) ) {
			return $row[ $selector ];
		}

		// Also check subfield alias if prefixed
		foreach ( $row as $k => $v ) {
			if ( $k === $selector || str_ends_with( $k, '_' . $selector ) ) {
				return $v;
			}
		}

		return null;
	}
}

/* ==========================================================================
   1. Default Rows & Helper Functions
   ========================================================================== */

/**
 * Return default local theme images map for the About gallery section.
 *
 * @return array Map of attachment IDs to local filenames in assets/img/about/
 */
function thecrisisacademy_get_about_local_gallery_map() {
	return array(
		161 => 'mapa-de-stakeholders.webp',
		162 => 'radar-de-amenazas.webp',
		163 => 'war-room.webp',
	);
}

/**
 * Filter attachment URL to use local theme asset if it matches the default about gallery images.
 */
function thecrisisacademy_filter_about_attachment_url( $url, $post_id ) {
	$map = thecrisisacademy_get_about_local_gallery_map();
	if ( isset( $map[ $post_id ] ) ) {
		return get_stylesheet_directory_uri() . '/assets/img/about/' . $map[ $post_id ];
	}
	return $url;
}
add_filter( 'wp_get_attachment_url', 'thecrisisacademy_filter_about_attachment_url', 10, 2 );

/**
 * Filter image downsize to serve the local theme image for default about gallery attachments.
 */
function thecrisisacademy_filter_about_image_downsize( $downsize, $id, $size ) {
	$map = thecrisisacademy_get_about_local_gallery_map();
	if ( isset( $map[ $id ] ) ) {
		$url = get_stylesheet_directory_uri() . '/assets/img/about/' . $map[ $id ];
		return array( $url, 600, 450, true );
	}
	return $downsize;
}
add_filter( 'image_downsize', 'thecrisisacademy_filter_about_image_downsize', 10, 3 );

/**
 * Filter attachment data prepared for JavaScript (used in WP Admin / Media modal).
 */
function thecrisisacademy_filter_about_attachment_for_js( $response, $attachment, $meta ) {
	$map = thecrisisacademy_get_about_local_gallery_map();
	$id  = $attachment->ID ?? 0;
	if ( isset( $map[ $id ] ) ) {
		$url = get_stylesheet_directory_uri() . '/assets/img/about/' . $map[ $id ];
		$response['url'] = $url;
		if ( ! empty( $response['sizes'] ) && is_array( $response['sizes'] ) ) {
			foreach ( $response['sizes'] as $sz => &$data ) {
				$data['url'] = $url;
			}
		} else {
			$response['sizes'] = array(
				'full'      => array( 'url' => $url, 'width' => 600, 'height' => 450, 'orientation' => 'landscape' ),
				'medium'    => array( 'url' => $url, 'width' => 600, 'height' => 450, 'orientation' => 'landscape' ),
				'thumbnail' => array( 'url' => $url, 'width' => 600, 'height' => 450, 'orientation' => 'landscape' ),
			);
		}
	}
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'thecrisisacademy_filter_about_attachment_for_js', 10, 3 );

/**
 * Get default rows for About specialization modules.
 *
 * @return array Default module rows.
 */
function thecrisisacademy_get_default_about_modules_rows() {
	return array(
		array(
			'icon'        => 'radar',
			'period'      => '01 • Radar de Riesgos',
			'tag'         => 'Tendencias 2026',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Investigación y estudios de crisis',
			'title'       => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
			'description' => 'Análisis profundo de incidentes recientes y anticipación de escenarios de riesgo reputacional adaptados al entorno actual y a las amenazas emergentes.',
		),
		array(
			'icon'        => 'chart',
			'period'      => '02 • Medición',
			'tag'         => 'Métricas & Control',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Herramientas y parámetros de medición',
			'title'       => 'Herramientas y parámetros de medición de una crisis y su respuesta',
			'description' => 'Establecimiento de indicadores cuantitativos y cualitativos para evaluar el impacto del incidente, la velocidad de reacción y la efectividad de la respuesta.',
		),
		array(
			'icon'        => 'cpu',
			'period'      => '03 • IA y Digital',
			'tag'         => 'IA & Nuevos Medios',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Entorno mediático y digital: rol de la IA',
			'title'       => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
			'description' => 'Evaluación de la desinformación masiva, deepfakes y uso de IA en la amplificación, análisis y monitoreo predictivo de crisis modernas.',
		),
		array(
			'icon'        => 'target',
			'period'      => '04 • Estrategia',
			'tag'         => 'Estrategia 4.0',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Comunicación estratégica de crisis 4.0',
			'title'       => 'Comunicación estratégica para manejo de crisis 4.0',
			'description' => 'Diseño de mensajes clave hiperdirigidos, comunicados ágiles y posicionamiento corporativo multicanal bajo situaciones de extrema presión.',
		),
		array(
			'icon'        => 'share-2',
			'period'      => '05 • Respuesta Ágil',
			'tag'         => 'Redes Sociales',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Manejo ágil en redes sociales',
			'title'       => 'Procesos para un manejo ágil de crisis en redes sociales',
			'description' => 'Protocolos de contención inmediata en plataformas digitales, gestión de comunidades y desaceleración de tendencias negativas virales.',
		),
		array(
			'icon'        => 'mic',
			'period'      => '06 • Portavoces',
			'tag'         => 'Vocerías Oficiales',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Control de narrativa y vocerías',
			'title'       => 'Control de narrativa y arquitectura de vocerías',
			'description' => 'Definición de portavoces oficiales, lineamientos de conducta ante la prensa y técnicas avanzadas de control del relato público.',
		),
		array(
			'icon'        => 'shield-alert',
			'period'      => '07 • Práctica Real',
			'tag'         => 'Ejercicio Inmersivo',
			'tag_class'   => 'alert-tag',
			'short_title' => 'Simulacro de alta intensidad',
			'title'       => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis de reputación',
			'description' => 'Ejercicio inmersivo en tiempo real con periodistas simulados e interacciones hostiles para auditar la resistencia y eficacia de los comités.',
		),
	);
}

/**
 * Get default rows for Signals repeater container.
 *
 * @return array Default signal rows.
 */
function thecrisisacademy_get_default_signals_rows() {
	return array(
		array(
			'number'       => '75',
			'label'        => '',
			'info'         => '<p>de las crisis mostraron señales previas <strong>que nadie detectó</strong></p>',
			'icon'         => '',
			'source_label' => 'Institute for Crisis Management (ICM)',
			'source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
		array(
			'number'       => '11',
			'label'        => '',
			'info'         => '<p>de pérdida del valor del mercado en <strong>solo 5 días por una mala respuesta</strong></p>',
			'icon'         => '',
			'source_label' => 'PwC + Oxford Metrica',
			'source_url'   => 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/',
		),
		array(
			'number'       => '',
			'label'        => 'Hoy una crisis puede escalar en minutos',
			'info'         => '<p>por IA, redes sociales y desinformación</p>',
			'icon'         => '',
			'source_label' => 'Institute for Crisis Management (ICM)',
			'source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
	);
}

/**
 * Default getter functions for certification repeaters.
 */
function thecrisisacademy_get_default_cert_01_items_rows() {
	return array(
		array( 'text' => 'Pérdida de tiempo crítico' ),
		array( 'text' => 'Respuestas improvisadas' ),
		array( 'text' => 'Daño reputacional' ),
		array( 'text' => 'Mensajes contradictorios' ),
	);
}

function thecrisisacademy_get_default_cert_02_steps_rows() {
	return array(
		array(
			'title'       => 'Diagnóstico',
			'description' => 'Detectamos las necesidades de la institución y definimos objetivos.',
		),
		array(
			'title'       => '6 módulos especializados',
			'description' => 'Contenido actualizado, casos reales y tendencias.',
		),
		array(
			'title'       => 'Simulación de crisis',
			'description' => 'Escenarios de alta intensidad en War Room.',
		),
		array(
			'title'       => 'Evaluación y ScoreCard',
			'description' => 'Medición del desempeño con KPIs: URR, MPR y TTR.',
		),
		array(
			'title'       => 'Certificación',
			'description' => 'Demuestra tu aprendizaje y recibe tu certificación profesional.',
		),
	);
}

function thecrisisacademy_get_default_cert_03_formats_rows() {
	return array(
		array(
			'icon'        => 'online',
			'title'       => 'En línea',
			'description' => 'Cúrsalo en tiempo real.',
		),
		array(
			'icon'        => 'presencial',
			'title'       => 'Presencial',
			'description' => 'También disponible en formato presencial intensivo.',
		),
	);
}

function thecrisisacademy_get_default_cert_04_points_rows() {
	return array(
		array( 'text' => 'Grupos reducidos garantizados' ),
		array( 'text' => 'Avalado internacionalmente' ),
		array( 'text' => 'Instructores expertos en activo' ),
	);
}

/**
 * Return SVG icon markup for certification formats.
 *
 * @param string $icon_key Format icon identifier.
 * @return string SVG icon markup.
 */
function thecrisisacademy_get_certification_format_icon( $icon_key = 'online' ) {
	switch ( $icon_key ) {
		case 'presencial':
			return '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="cert-format-svg" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
		case 'online':
		default:
			return '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="cert-format-svg" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>';
	}
}

/**
 * Get default rows for How Works accordion solutions.
 *
 * @return array Default rows for How Works repeater.
 */
function thecrisisacademy_get_default_how_works_items_rows() {
	return array(
		array(
			'department'      => 'arr',
			'radar_code'      => 'ARR',
			'number'          => '01',
			'title'           => 'Auditoría de Riesgos Reputacionales',
			'description'     => 'Análisis de vulnerabilidades en tus planes y protocolos de comunicación',
			'bullets'         => "Revisión de escenarios\nMatriz de riesgos intangibles\nRecomendaciones de mejora",
			'button_label'    => 'Más info',
			'lightbox_target' => 'how-works-arr',
		),
		array(
			'department'      => 'mpc',
			'radar_code'      => 'MPC',
			'number'          => '02',
			'title'           => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'description'     => 'Instrumentos tácticos y actualizados para los primeros 60 minutos hasta 12 horas',
			'bullets'         => "Playbooks tácticos\nGuía de respuesta inmediata\nManual de riesgo reputacional",
			'button_label'    => 'Más info',
			'lightbox_target' => 'how-works-mpc',
		),
		array(
			'department'      => 'fec',
			'radar_code'      => 'FEC',
			'number'          => '03',
			'title'           => 'Formación de Especialistas y Comités',
			'description'     => 'Entrenamiento intensivo en gestión de crisis y control de narrativa para directivos y equipos',
			'bullets'         => "Simulacros inmersivos en War Room\nArquitectura de vocerías y media training\nProtocolos ágiles para redes sociales",
			'button_label'    => 'Más info',
			'lightbox_target' => 'how-works-pi6m',
		),
	);
}

/**
 * Get default rows for How Works panes repeater.
 *
 * @return array Default rows for How Works panes repeater.
 */
function thecrisisacademy_get_default_how_works_panes_rows() {
	return array(
		array(
			'pane_id'         => 'how-works-arr',
			'pane_title'      => 'Auditoría de Riesgos Reputacionales',
			'article_number'  => '01 · ARR',
			'article_title'   => 'Auditoría de Riesgos Reputacionales',
			'article_content' => '<p>El nuevo entorno de comunicación obliga a actuar de manera inmediata para proteger un activo intangible tan valioso como la reputación ante el impacto de una crisis. Esa respuesta de comunicación estratégica, sin embargo, no se encuentra reflejada en ningún manual o está desfasada.</p><p>Los planes de continuidad de negocio o los manuales de respuesta operativa consideran una variedad de escenarios y probabilidades de ocurrencia. Pero ignoran algunos riesgos que están afectando a los negocios: percepciones erróneas, declaraciones inadecuadas, rumores o acusaciones. Y, sobre todo, no consideran los escándalos o crisis en redes sociales.</p><p>Por ello, diseñamos el servicio de auditoría de riesgos reputacionales. Este análisis de vulnerabilidades en los planes de continuidad de negocio revisa la matriz de riesgo, los escenarios intangibles y las herramientas de respuesta. Se cotejan contra las mejores prácticas y se plantean recomendaciones de mejora que pueden implementarse inmediatamente.</p><p class="lightbox-article-highlight">¿Quieres saber si tu Manual de Crisis, sus escenarios y respuestas están actualizados?</p>',
		),
		array(
			'pane_id'         => 'how-works-mpc',
			'pane_title'      => 'Manuales y Playbooks',
			'article_number'  => '02 · MPC',
			'article_title'   => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'article_content' => '<p>Si un manual de crisis no se ha actualizado en los últimos 12 meses, es un documento muerto. La dinámica del entorno informativo, los nuevos riesgos, los ataques sintéticos (creados, alimentados o manipulados por la IA) obligan a adaptar con frecuencia las estrategias de mitigación de impacto. Para resolver este problema, hemos creado guías de acción táctica para los primeros 60 minutos hasta 12 horas, que es el periodo clave en que debe atenderse una crisis con la máxima precisión.</p><p>A partir de la redacción de más de 100 guías y manuales de crisis y la asesoría y acompañamiento a empresas e instituciones para atender sus emergencias, hemos diseñado instrumentos pensando en la experiencia del usuario.</p><p><strong>Playbooks tácticos:</strong> Para una respuesta efectiva a incidentes críticos, es fundamental tener en un documento simple y ágil las acciones de comunicación, herramientas y tiempos límite. Cada playbook se adapta a la industria o sector y se presenta a manera de una app (o web) responsiva.</p><p><strong>Guía de Respuesta Inmediata:</strong> Una guía diseñada específicamente para que aún en una emergencia se garantice la coherencia con el actuar previo de la institución, sus políticas de comunicación y el apego a su propósito y valores.</p><p><strong>Manual de Riesgo Reputacional:</strong> El papel de este documento es compilar los escenarios de máximo riesgo reputacional, incluir acciones para la detección oportuna de crisis latentes y asegurarse de documentar aquellas experiencias que se hayan enfrentado. Es la herramienta que ayuda a la institución a que los incidentes se conviertan en aprendizaje y continuidad. Se prepara absolutamente a medida de cada empresa.</p>',
		),
		array(
			'pane_id'         => 'how-works-pi6m',
			'pane_title'      => 'Formación de Especialistas y Comités',
			'article_number'  => '03 · FEC',
			'article_title'   => 'Formación de Especialistas y Comités de Crisis',
			'article_content' => '<p>La mejor estrategia sobre papel falla si el equipo responsable no ha sido entrenado bajo condiciones reales de presión. Los simulacros tradicionales, con escenarios hipotéticos que se resuelven en un salón de juntas con café, ya no preparan a nadie para la velocidad y hostilidad del ecosistema actual.</p><p>Por ello, nuestro programa de formación combina método riguroso con simulación inmersiva. Diseñamos entrenamientos ejecutivos para comités de crisis, áreas de comunicación y directivos que necesitan dominar el Framework de Respuesta Inmediata.</p><p><strong>War Room en tiempo real:</strong> Ponemos a prueba a los participantes frente a periodistas simulados, interacciones hostiles en redes, filtraciones y situaciones límite generadas dinámicamente.</p><p><strong>Arquitectura de vocerías:</strong> Alineamos el relato público, definimos portavoces oficiales y entrenamos la postura, el tono y la claridad bajo presión extrema.</p><p><strong>KPIs de desempeño:</strong> Medimos la velocidad de reacción (TTR), el uso del radar de riesgos (URR) y el apego al protocolo (MPR), entregando un diagnóstico claro del nivel de preparación de la organización.</p>',
		),
	);
}

/**
 * Get default rows for Simulation stages repeater.
 *
 * @return array Default rows for Simulation stages.
 */
function thecrisisacademy_get_default_simulation_stages_rows() {
	$theme_uri = set_url_scheme( get_stylesheet_directory_uri() );
	return array(
		array(
			'id'        => 'radar',
			'label'     => 'Radar de riesgos',
			'title'     => 'Detecta la crisis antes de que estalle',
			'desc'      => 'Monitorea señales débiles, menciones y alertas tempranas para clasificar el nivel de amenaza en tiempo real.',
			'image'     => $theme_uri . '/assets/img/simulator/radar.webp',
			'alt'       => 'Radar de riesgos del simulador de crisis',
			'kpi'       => 'URR',
			'kpi_label' => 'Uso de Radar de Riesgos',
		),
		array(
			'id'        => 'stakeholders',
			'label'     => 'Mapa de stakeholders',
			'title'     => 'Prioriza a quién hablarle primero',
			'desc'      => 'Identifica a las audiencias críticas, su nivel de influencia y el mensaje que cada una necesita escuchar.',
			'image'     => $theme_uri . '/assets/img/simulator/stakeholders-map.webp',
			'alt'       => 'Mapa de stakeholders del simulador de crisis',
			'kpi'       => 'MPR',
			'kpi_label' => 'Manejo de Protocolo de Respuesta',
		),
		array(
			'id'        => 'war-room',
			'label'     => 'War room y simulación activa',
			'title'     => 'Toma decisiones bajo fuego cruzado',
			'desc'      => 'Enfrenta periodistas simulados, tendencias virales y filtraciones en una consola de respuesta en tiempo real.',
			'image'     => $theme_uri . '/assets/img/simulator/war-room-1.webp',
			'alt'       => 'War room y simulación activa del simulador de crisis',
			'kpi'       => 'TTR',
			'kpi_label' => 'Tiempo de Reacción y Contención',
		),
	);
}

/* ==========================================================================
   2. Getters with Defaults for Individuals Sections
   ========================================================================== */

function thecrisisacademy_get_individuals_hero_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	return array(
		'preheading'  => get_post_meta( $post_id, 'hero_preheading', true ) ?: 'Especialización en Comunicación para Manejo de Crisis',
		'title'       => get_post_meta( $post_id, 'hero_title', true ) ?: 'Domina la gestión de crisis en la era de la IA y protege lo que más importa: tu reputación',
		'description' => get_post_meta( $post_id, 'hero_description', true ) ?: 'Conoce cómo manejar el Framework de Respuesta Inmediata ante Crisis y Escándalos. El mismo sistema que usan empresas internacionales',
		'canvas_tags' => get_post_meta( $post_id, 'hero_canvas_tags', true ) ?: '',
	);
}

function thecrisisacademy_get_individuals_about_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$modules = get_post_meta( $post_id, 'about_modules', true );
	if ( empty( $modules ) || ! is_array( $modules ) ) {
		$modules = thecrisisacademy_get_default_about_modules_rows();
	}
	$gallery = get_post_meta( $post_id, 'about_gallery', true );
	if ( is_string( $gallery ) && '' !== trim( $gallery ) ) {
		$gallery = array_values( array_filter( array_map( 'intval', explode( ',', $gallery ) ) ) );
	}
	if ( empty( $gallery ) || ! is_array( $gallery ) ) {
		$gallery = array( 161, 162, 163 );
	}
	return array(
		'gallery'       => $gallery,
		'preheading'    => get_post_meta( $post_id, 'about_preheading', true ) ?: '¿Qué hacemos?',
		'title'         => get_post_meta( $post_id, 'about_title', true ) ?: 'Entrenamos para proteger un activo crucial: la reputación',
		'subtitle'      => get_post_meta( $post_id, 'about_subtitle', true ) ?: 'The Crisis Academy es una academia especializada en entrenamiento estratégico para el manejo de crisis reputacionales, comunicación de riesgos y control de narrativa.',
		'description'   => get_post_meta( $post_id, 'about_description', true ) ?: 'Formamos a equipos de crisis, áreas de comunicación, directivos y voceros para actuar con método, rapidez y precisión cuando más se necesita.',
		'modules_title' => get_post_meta( $post_id, 'about_modules_title', true ) ?: 'Módulos de especialización',
		'modules'       => $modules,
	);
}

function thecrisisacademy_get_individuals_cert_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$c01_items = get_post_meta( $post_id, 'cert_01_items', true );
	if ( empty( $c01_items ) || ! is_array( $c01_items ) ) {
		$c01_items = thecrisisacademy_get_default_cert_01_items_rows();
	}
	$c02_steps = get_post_meta( $post_id, 'cert_02_steps', true );
	if ( empty( $c02_steps ) || ! is_array( $c02_steps ) ) {
		$c02_steps = thecrisisacademy_get_default_cert_02_steps_rows();
	}
	$c03_formats = get_post_meta( $post_id, 'cert_03_formats', true );
	if ( empty( $c03_formats ) || ! is_array( $c03_formats ) ) {
		$c03_formats = thecrisisacademy_get_default_cert_03_formats_rows();
	}
	$c04_points = get_post_meta( $post_id, 'cert_04_points', true );
	if ( empty( $c04_points ) || ! is_array( $c04_points ) ) {
		$c04_points = thecrisisacademy_get_default_cert_04_points_rows();
	}

	return array(
		'intro_subheading' => get_post_meta( $post_id, 'cert_intro_subheading', true ) ?: 'Entrenamiento especializado',
		'intro_title'      => get_post_meta( $post_id, 'cert_intro_title', true ) ?: 'La ruta definitiva para convertir a tu equipo en expertos en gestión de crisis',
		'intro_lead'       => get_post_meta( $post_id, 'cert_intro_lead', true ) ?: 'Cada crisis sin protocolo cuesta reputación, clientes y tiempo que nunca recuperarás.',
		'c01_number'       => get_post_meta( $post_id, 'cert_01_number', true ) ?: '01',
		'c01_header'       => get_post_meta( $post_id, 'cert_01_header_text', true ) ?: 'El momento crítico',
		'c01_eyebrow'      => get_post_meta( $post_id, 'cert_01_eyebrow', true ) ?: 'Cuando todo cambia',
		'c01_title'        => get_post_meta( $post_id, 'cert_01_title', true ) ?: 'En una crisis, cada decisión cuenta.',
		'c01_lead'         => get_post_meta( $post_id, 'cert_01_lead', true ) ?: 'Sin preparación, el tiempo se pierde, las respuestas se improvisan y la comunicación se fragmenta.',
		'c01_items'        => $c01_items,
		'c02_number'       => get_post_meta( $post_id, 'cert_02_number', true ) ?: '02',
		'c02_header'       => get_post_meta( $post_id, 'cert_02_header_text', true ) ?: 'La preparación',
		'c02_eyebrow'      => get_post_meta( $post_id, 'cert_02_eyebrow', true ) ?: 'Tu proceso de certificación',
		'c02_title'        => get_post_meta( $post_id, 'cert_02_title', true ) ?: 'La respuesta no se improvisa. Se entrena.',
		'c02_lead'         => get_post_meta( $post_id, 'cert_02_lead', true ) ?: 'Una ruta práctica para pasar del diagnóstico a la acción y medir cómo responde el equipo.',
		'c02_steps'        => $c02_steps,
		'c03_number'       => get_post_meta( $post_id, 'cert_03_number', true ) ?: '03',
		'c03_header'       => get_post_meta( $post_id, 'cert_03_header_text', true ) ?: 'El formato',
		'c03_eyebrow'      => get_post_meta( $post_id, 'cert_03_eyebrow', true ) ?: 'Una ruta a tu medida',
		'c03_title'        => get_post_meta( $post_id, 'cert_03_title', true ) ?: 'Aprende como mejor funciona para ti.',
		'c03_lead'         => get_post_meta( $post_id, 'cert_03_lead', true ) ?: 'Cursa los módulos de manera individual según tus necesidades o completa la ruta para obtener una Constancia Oficial.',
		'c03_formats'      => $c03_formats,
		'c04_number'       => get_post_meta( $post_id, 'cert_04_number', true ) ?: '04',
		'c04_header'       => get_post_meta( $post_id, 'cert_04_header_text', true ) ?: 'El siguiente capítulo',
		'c04_title'        => get_post_meta( $post_id, 'cert_04_title', true ) ?: 'Obtén tu Certificado de Especialización en Comunicación de Crisis.',
		'c04_points'       => $c04_points,
		'c04_btn_text'     => get_post_meta( $post_id, 'cert_04_button_text', true ) ?: 'Inscribirme ahora',
		'c04_btn_url'      => get_post_meta( $post_id, 'cert_04_button_url', true ) ?: '#cta',
	);
}

function thecrisisacademy_get_individuals_signals_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$container = get_post_meta( $post_id, 'signals_container', true );
	if ( empty( $container ) || ! is_array( $container ) ) {
		$container = thecrisisacademy_get_default_signals_rows();
	}
	return array(
		'title'     => get_post_meta( $post_id, 'signals_title', true ) ?: '<h2 class="title-section title-reveal">La mayoría de las crisis <strong>sí dieron señales</strong> antes de explotar</h2>',
		'subtitle'  => get_post_meta( $post_id, 'signals_subtitle', true ) ?: '<h3 class="subtitle-section title-reveal">Improvisar frente a una crisis no es un error operativo, es <strong>negligencia reputacional.</strong></h3>',
		'container' => $container,
	);
}

function thecrisisacademy_get_individuals_how_works_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$items = get_post_meta( $post_id, 'how_works_items', true );
	if ( empty( $items ) || ! is_array( $items ) ) {
		$items = thecrisisacademy_get_default_how_works_items_rows();
	}
	$panes = get_post_meta( $post_id, 'how_works_panes', true );
	if ( empty( $panes ) || ! is_array( $panes ) ) {
		$panes = thecrisisacademy_get_default_how_works_panes_rows();
	}
	return array(
		'preheading' => get_post_meta( $post_id, 'how_works_preheading', true ) ?: '¿Cómo funciona?',
		'title'      => get_post_meta( $post_id, 'how_works_title', true ) ?: 'Tres soluciones para fortalecer tu preparación ante una crisis',
		'items'      => $items,
		'panes'      => $panes,
	);
}

function thecrisisacademy_get_individuals_simulation_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$stages = get_post_meta( $post_id, 'simulation_stages', true );
	if ( empty( $stages ) || ! is_array( $stages ) ) {
		$stages = thecrisisacademy_get_default_simulation_stages_rows();
	} else {
		$theme_uri = set_url_scheme( get_stylesheet_directory_uri() );
		$fallback_map = array(
			'radar'        => $theme_uri . '/assets/img/simulator/radar.webp',
			'stakeholders' => $theme_uri . '/assets/img/simulator/stakeholders-map.webp',
			'war-room'     => $theme_uri . '/assets/img/simulator/war-room-1.webp',
		);
		foreach ( $stages as &$stg ) {
			$img      = $stg['image'] ?? '';
			$stage_id = $stg['id'] ?? '';
			// If image is empty or points to the old legacy demo uploads path, replace with local theme asset
			if ( empty( $img ) || ( is_string( $img ) && strpos( $img, '/uploads/2026/05/' ) !== false ) ) {
				if ( isset( $fallback_map[ $stage_id ] ) ) {
					$stg['image'] = $fallback_map[ $stage_id ];
				} elseif ( is_string( $img ) && strpos( $img, 'radar.webp' ) !== false ) {
					$stg['image'] = $fallback_map['radar'];
				} elseif ( is_string( $img ) && strpos( $img, 'stakeholders-map.webp' ) !== false ) {
					$stg['image'] = $fallback_map['stakeholders'];
				} elseif ( is_string( $img ) && strpos( $img, 'war-room' ) !== false ) {
					$stg['image'] = $fallback_map['war-room'];
				}
			}
			if ( ! empty( $stg['image'] ) && is_string( $stg['image'] ) ) {
				$stg['image'] = set_url_scheme( $stg['image'] );
			}
		}
		unset( $stg );
	}
	return array(
		'preheading'     => get_post_meta( $post_id, 'simulation_preheading', true ) ?: 'Simulador de crisis',
		'title'          => get_post_meta( $post_id, 'simulation_title', true ) ?: 'Experimenta la presión en tiempo real y descubre si tu equipo está preparado',
		'intro'          => get_post_meta( $post_id, 'simulation_intro', true ) ?: 'Tres etapas, un mismo reloj. Recorre el ciclo completo de una crisis y mide cómo responde tu equipo cuando cada minuto cuenta.',
		'console_status' => get_post_meta( $post_id, 'simulation_console_status', true ) ?: 'Simulación en vivo',
		'cta_url'        => get_post_meta( $post_id, 'simulation_cta_url', true ) ?: home_url( '/simulador-de-crisis/' ),
		'cta_label'      => get_post_meta( $post_id, 'simulation_cta_label', true ) ?: 'Simular crisis',
		'cta_lightbox'   => get_post_meta( $post_id, 'simulation_cta_lightbox', true ) ?: 'crisis-simulator',
		'stages'         => $stages,
	);
}

/* ==========================================================================
   3. Meta Boxes Registration for Particulares
   ========================================================================== */

/**
 * Register native meta boxes for the Individuals landing page template.
 *
 * @param string  $post_type Current post type.
 * @param WP_Post $post      Current post object.
 */
function thecrisisacademy_register_individuals_metaboxes( $post_type, $post ) {
	if ( 'page' !== $post_type || ! $post instanceof WP_Post ) {
		return;
	}

	$template       = get_post_meta( $post->ID, '_wp_page_template', true );
	$is_individuals = ( 'templates/individuals.php' === $template );

	if ( ! $is_individuals ) {
		return;
	}

	// 1. Hero
	add_meta_box(
		'individuals_hero_metabox',
		'⚡ Sección Hero (Particulares) — Título y Canvas',
		'thecrisisacademy_render_individuals_hero_metabox',
		'page',
		'normal',
		'high'
	);

	// 2. About
	add_meta_box(
		'individuals_about_metabox',
		'📘 Sección Acerca de / Módulos (About) — Galería y Módulos 3D',
		'thecrisisacademy_render_individuals_about_metabox',
		'page',
		'normal',
		'high'
	);

	// 3. Certification
	add_meta_box(
		'individuals_certification_metabox',
		'🎓 Sección Certificación — Proceso, Formatos y Puntos Clave',
		'thecrisisacademy_render_individuals_certification_metabox',
		'page',
		'normal',
		'default'
	);

	// 4. Signals
	add_meta_box(
		'individuals_signals_metabox',
		'🚨 Sección Señales de Alerta (Signals) — Indicadores y Métricas',
		'thecrisisacademy_render_individuals_signals_metabox',
		'page',
		'normal',
		'default'
	);

	// 5. How Works
	add_meta_box(
		'individuals_how_works_metabox',
		'⚙️ Sección Cómo Funciona — Soluciones y Paneles Lightbox',
		'thecrisisacademy_render_individuals_how_works_metabox',
		'page',
		'normal',
		'default'
	);

	// 6. Simulation
	add_meta_box(
		'individuals_simulation_metabox',
		'🎮 Sección Simulador de Crisis — Consola y Etapas',
		'thecrisisacademy_render_individuals_simulation_metabox',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_individuals_metaboxes', 10, 2 );

/* ==========================================================================
   4. Meta Box Render Functions
   ========================================================================== */

/**
 * Shared CSS for Individuals metaboxes
 */
function thecrisisacademy_render_individuals_metabox_styles() {
	static $styles_rendered = false;
	if ( $styles_rendered ) {
		return;
	}
	$styles_rendered = true;
	?>
	<style>
		.tca-ind-wrap {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
			padding: 10px 0;
		}
		.tca-ind-field {
			margin-bottom: 20px;
		}
		.tca-ind-label {
			display: block;
			font-weight: 600;
			font-size: 13px;
			margin-bottom: 6px;
			color: #1e293b;
		}
		.tca-ind-desc {
			font-size: 12px;
			color: #64748b;
			margin: 4px 0 0;
		}
		.tca-ind-row {
			background: #f8fafc;
			border: 1px solid #e2e8f0;
			border-radius: 6px;
			padding: 14px;
			margin-bottom: 12px;
			position: relative;
		}
		.tca-ind-row-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10px;
			font-weight: 600;
			font-size: 13px;
			color: #334155;
		}
		.tca-ind-del-btn {
			background: #fee2e2;
			color: #b91c1c;
			border: 1px solid #fca5a5;
			border-radius: 4px;
			padding: 3px 8px;
			font-size: 11px;
			cursor: pointer;
		}
		.tca-ind-del-btn:hover {
			background: #fecaca;
		}
		.tca-ind-grid-2 {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 12px;
		}
		.tca-ind-grid-3 {
			display: grid;
			grid-template-columns: 1fr 1fr 1fr;
			gap: 12px;
		}
		.tca-gallery-box {
			background: #f8fafc;
			border: 1px solid #cbd5e1;
			border-radius: 8px;
			padding: 16px;
		}
		.tca-gallery-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
			gap: 12px;
			margin: 14px 0;
			min-height: 80px;
		}
		.tca-gallery-item {
			position: relative;
			background: #fff;
			border: 1px solid #cbd5e1;
			border-radius: 6px;
			overflow: hidden;
			box-shadow: 0 1px 3px rgba(0,0,0,0.06);
			cursor: grab;
			transition: transform 0.15s, box-shadow 0.15s;
		}
		.tca-gallery-item:hover {
			box-shadow: 0 4px 10px rgba(0,0,0,0.12);
			transform: translateY(-2px);
		}
		.tca-gallery-item.dragging {
			opacity: 0.5;
			border: 2px dashed #3b82f6;
		}
		.tca-gallery-thumb-wrap {
			width: 100%;
			height: 100px;
			background: #0f172a;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
		}
		.tca-gallery-thumb-wrap img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
		}
		.tca-gallery-item-footer {
			padding: 6px 8px;
			font-size: 11px;
			color: #475569;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			background: #f8fafc;
			border-top: 1px solid #f1f5f9;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.tca-gallery-item-actions {
			display: flex;
			gap: 2px;
		}
		.tca-gallery-move-btn {
			background: none;
			border: none;
			color: #64748b;
			padding: 2px 4px;
			font-size: 11px;
			cursor: pointer;
			border-radius: 3px;
		}
		.tca-gallery-move-btn:hover {
			background: #e2e8f0;
			color: #1e293b;
		}
		.tca-gallery-remove-btn {
			position: absolute;
			top: 4px;
			right: 4px;
			width: 22px;
			height: 22px;
			background: rgba(220, 38, 38, 0.9);
			color: #fff;
			border: none;
			border-radius: 50%;
			font-size: 14px;
			line-height: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			opacity: 0.85;
			transition: opacity 0.15s, transform 0.15s;
			z-index: 2;
		}
		.tca-gallery-remove-btn:hover {
			opacity: 1;
			transform: scale(1.1);
			background: #dc2626;
		}
		.tca-gallery-empty-state {
			grid-column: 1 / -1;
			padding: 25px 15px;
			text-align: center;
			border: 2px dashed #cbd5e1;
			border-radius: 6px;
			color: #64748b;
			background: #fff;
		}
		.tca-gallery-toolbar {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			align-items: center;
		}
		.tca-ind-tabs {
			display: flex;
			gap: 4px;
			border-bottom: 1px solid #cbd5e1;
			margin-bottom: 16px;
			padding-bottom: 0;
		}
		.tca-ind-tabs .nav-tab {
			cursor: pointer;
			background: #f1f5f9;
			border: 1px solid #cbd5e1;
			border-bottom: none;
			color: #475569;
			padding: 8px 14px;
			font-size: 13px;
			font-weight: 600;
			border-radius: 6px 6px 0 0;
			transition: background 0.15s, color 0.15s;
		}
		.tca-ind-tabs .nav-tab:hover {
			background: #e2e8f0;
			color: #1e293b;
		}
		.tca-ind-tabs .nav-tab.nav-tab-active {
			background: #ffffff;
			color: #1e40af;
			border-color: #cbd5e1;
			border-bottom: 1px solid #ffffff;
			margin-bottom: -1px;
		}
		.tca-tab-pane {
			display: none;
		}
		.tca-tab-pane.is-active {
			display: block;
		}

		/* Button & Dashicons Alignment Fix */
		.tca-ind-wrap .button,
		.tca-gallery-toolbar .button,
		.postbox .inside .button.button-primary,
		.postbox .inside .button.button-secondary {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			gap: 6px !important;
			vertical-align: middle !important;
			line-height: 1.2 !important;
			min-height: 32px !important;
			height: auto !important;
			padding: 4px 12px !important;
		}
		.tca-ind-wrap .button .dashicons,
		.tca-gallery-toolbar .button .dashicons,
		.postbox .inside .button.button-primary .dashicons,
		.postbox .inside .button.button-secondary .dashicons {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			font-size: 18px !important;
			width: 18px !important;
			height: 18px !important;
			line-height: 1 !important;
			margin: 0 !important;
			padding: 0 !important;
			vertical-align: middle !important;
			position: static !important;
			top: auto !important;
		}
		@media (max-width: 782px) {
			.tca-ind-grid-2, .tca-ind-grid-3 {
				grid-template-columns: 1fr;
			}
		}
	</style>
	<?php
}

/**
 * 1. Hero Metabox Render
 */
function thecrisisacademy_render_individuals_hero_metabox( $post ) {
	wp_nonce_field( 'individuals_metabox_save', 'individuals_metabox_nonce' );
	thecrisisacademy_render_individuals_metabox_styles();
	$data = thecrisisacademy_get_individuals_hero_data( $post->ID );
	?>
	<div class="tca-ind-wrap">
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_hero_preheading">Subtítulo Superior (Pretext Reveal)</label>
			<input type="text" id="ind_hero_preheading" name="ind_hero[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" />
			<p class="tca-ind-desc">Etiqueta badge superior con animación scramble.</p>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_hero_title">Título Principal (H1)</label>
			<textarea id="ind_hero_title" name="ind_hero[title]" rows="3" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			<p class="tca-ind-desc">Título principal con animación spring bounce.</p>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_hero_description">Descripción / Párrafo</label>
			<textarea id="ind_hero_description" name="ind_hero[description]" rows="3" class="large-text"><?php echo esc_textarea( $data['description'] ); ?></textarea>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_hero_canvas_tags">Palabras Clave del Fondo (#hero-canvas)</label>
			<textarea id="ind_hero_canvas_tags" name="ind_hero[canvas_tags]" rows="2" class="large-text" placeholder="Ej. #Crisis, #Reputación, #Escándalo, #IA"><?php echo esc_textarea( $data['canvas_tags'] ); ?></textarea>
			<p class="tca-ind-desc">Separadas por comas. Dejar vacío para usar las predeterminadas.</p>
		</div>
	</div>
	<?php
}

/**
 * 2. About Metabox Render
 */
function thecrisisacademy_render_individuals_about_metabox( $post ) {
	if ( function_exists( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
	thecrisisacademy_render_individuals_metabox_styles();
	$data = thecrisisacademy_get_individuals_about_data( $post->ID );
	$icon_options = function_exists( 'thecrisisacademy_get_trouble_icon_options' ) ? thecrisisacademy_get_trouble_icon_options() : array(
		'radar'        => 'Radar (Riesgos)',
		'chart'        => 'Gráfica (Métricas)',
		'cpu'          => 'Procesador / IA',
		'target'       => 'Diana (Estrategia)',
		'share-2'      => 'Redes Sociales',
		'mic'          => 'Micrófono (Vocería)',
		'shield-alert' => 'Escudo (Simulacro)',
	);
	?>
	<div class="tca-ind-wrap">
		<!-- Tabs Navigation -->
		<nav class="nav-tab-wrapper tca-ind-tabs" role="tablist">
			<button type="button" class="nav-tab nav-tab-active" data-tab="ind-about-tab-overview" role="tab" aria-selected="true">
				🖼️ Galería y Textos
			</button>
			<button type="button" class="nav-tab" data-tab="ind-about-tab-modules" role="tab" aria-selected="false">
				📘 Módulos de Especialización
			</button>
		</nav>

		<!-- PESTAÑA 1: Galería y Textos Principales -->
		<div id="ind-about-tab-overview" class="tca-tab-pane is-active" role="tabpanel">
			<!-- Gallery Images -->
			<div class="tca-ind-field tca-gallery-box">
				<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
					<div>
						<label class="tca-ind-label" style="font-size:14px; margin-bottom:2px;">🖼️ Galería de Diapositivas (Carrusel Izquierdo)</label>
						<p class="tca-ind-desc">
							Arrastra las imágenes para reordenar o usa los botones. Por defecto se incluyen las 3 ilustraciones locales de la academia.
						</p>
					</div>
					<span class="tca-gallery-count" id="ind_gallery_counter" style="background:#e2e8f0; color:#334155; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600;">
						<?php echo count( (array) $data['gallery'] ); ?> diapositivas
					</span>
				</div>

				<div class="tca-gallery-grid" id="ind_about_gallery_grid">
					<?php
					$gallery_ids = is_array( $data['gallery'] ) ? $data['gallery'] : ( ! empty( $data['gallery'] ) ? explode( ',', (string) $data['gallery'] ) : array() );
					$local_map   = thecrisisacademy_get_about_local_gallery_map();
					$theme_uri   = get_stylesheet_directory_uri();

					if ( ! empty( $gallery_ids ) ) :
						foreach ( $gallery_ids as $img_id ) :
							$img_id = (int) $img_id;
							if ( $img_id <= 0 ) continue;

							$img_url   = '';
							$img_title = '';
							if ( isset( $local_map[ $img_id ] ) ) {
								$img_url   = $theme_uri . '/assets/img/about/' . $local_map[ $img_id ];
								$img_title = $local_map[ $img_id ];
							} else {
								$img_url   = wp_get_attachment_image_url( $img_id, 'medium' ) ?: wp_get_attachment_url( $img_id );
								$img_title = get_the_title( $img_id ) ?: ( 'Adjunto #' . $img_id );
							}
							if ( empty( $img_url ) ) {
								$img_url = $theme_uri . '/assets/img/about/mapa-de-stakeholders.webp';
							}
					?>
						<div class="tca-gallery-item" data-id="<?php echo esc_attr( $img_id ); ?>" draggable="true">
							<button type="button" class="tca-gallery-remove-btn" title="Eliminar de la galería" aria-label="Eliminar">&times;</button>
							<div class="tca-gallery-thumb-wrap">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_title ); ?>" />
							</div>
							<div class="tca-gallery-item-footer">
								<span style="max-width:70px; overflow:hidden; text-overflow:ellipsis;" title="<?php echo esc_attr( $img_title ); ?>"><?php echo esc_html( $img_title ); ?></span>
								<div class="tca-gallery-item-actions">
									<button type="button" class="tca-gallery-move-btn tca-move-left" title="Mover a la izquierda">&larr;</button>
									<button type="button" class="tca-gallery-move-btn tca-move-right" title="Mover a la derecha">&rarr;</button>
								</div>
							</div>
						</div>
					<?php
						endforeach;
					endif;
					?>
					<div class="tca-gallery-empty-state" id="ind_gallery_empty" style="<?php echo ! empty( $gallery_ids ) ? 'display:none;' : ''; ?>">
						<span class="dashicons dashicons-format-gallery" style="font-size:32px; width:32px; height:32px; color:#94a3b8; margin-bottom:8px;"></span>
						<p style="margin:4px 0 0; font-size:13px;">No hay imágenes en la galería.</p>
						<p style="margin:2px 0 0; font-size:12px; color:#94a3b8;">Haz clic en <strong>"Añadir a la galería"</strong> o <strong>"Restaurar 3 imágenes base"</strong>.</p>
					</div>
				</div>

				<div class="tca-gallery-toolbar">
					<button type="button" class="button button-primary" id="ind_add_gallery_btn">
						<span class="dashicons dashicons-plus-alt"></span>
						<span>Añadir a la galería</span>
					</button>
					<button type="button" class="button button-secondary" id="ind_restore_default_gallery_btn">
						<span class="dashicons dashicons-image-rotate"></span>
						<span>Restaurar 3 imágenes base</span>
					</button>
					<button type="button" class="button button-link-delete" id="ind_clear_gallery_btn" style="margin-left:auto;">
						Vaciar galería
					</button>
				</div>

				<input type="hidden" name="ind_about[gallery_ids]" id="ind_about_gallery_ids" value="<?php echo esc_attr( is_array( $data['gallery'] ) ? implode( ',', $data['gallery'] ) : $data['gallery'] ); ?>" />
			</div>

			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_about_preheading">Subtítulo Superior</label>
				<input type="text" id="ind_about_preheading" name="ind_about[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" />
			</div>

			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_about_title">Título Principal de la Sección</label>
				<textarea id="ind_about_title" name="ind_about[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			</div>

			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_about_subtitle">Subtítulo de la Sección</label>
				<textarea id="ind_about_subtitle" name="ind_about[subtitle]" rows="2" class="large-text"><?php echo esc_textarea( $data['subtitle'] ); ?></textarea>
			</div>

			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_about_description">Párrafo Descriptivo</label>
				<textarea id="ind_about_description" name="ind_about[description]" rows="3" class="large-text"><?php echo esc_textarea( $data['description'] ); ?></textarea>
			</div>
		</div>

		<!-- PESTAÑA 2: Módulos de Especialización -->
		<div id="ind-about-tab-modules" class="tca-tab-pane" role="tabpanel">
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_about_modules_title">Título de Módulos</label>
				<input type="text" id="ind_about_modules_title" name="ind_about[modules_title]" value="<?php echo esc_attr( $data['modules_title'] ); ?>" class="large-text" />
			</div>

			<!-- Modules Repeater -->
			<div class="tca-ind-field">
				<label class="tca-ind-label">Módulos de Especialización (Tarjetas 3D)</label>
				<div id="ind-modules-container">
					<?php foreach ( $data['modules'] as $idx => $mod ) : ?>
						<div class="tca-ind-row">
							<div class="tca-ind-row-header">
								<span>Módulo #<?php echo esc_html( $idx + 1 ); ?>: <?php echo esc_html( $mod['short_title'] ?? '' ); ?></span>
								<button type="button" class="tca-ind-del-btn" onclick="this.closest('.tca-ind-row').remove();">Eliminar</button>
							</div>
							<div class="tca-ind-grid-3">
								<div>
									<label class="tca-ind-desc">Icono</label>
									<select name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][icon]" style="width:100%;">
										<?php foreach ( $icon_options as $k => $label ) : ?>
											<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $mod['icon'] ?? '', $k ); ?>><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label class="tca-ind-desc">Período / Etapa</label>
									<input type="text" name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][period]" value="<?php echo esc_attr( $mod['period'] ?? '' ); ?>" class="widefat" />
								</div>
								<div>
									<label class="tca-ind-desc">Etiqueta (Badge)</label>
									<input type="text" name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][tag]" value="<?php echo esc_attr( $mod['tag'] ?? '' ); ?>" class="widefat" />
								</div>
							</div>
							<div class="tca-ind-grid-2" style="margin-top:8px;">
								<div>
									<label class="tca-ind-desc">Título Corto (Pestaña)</label>
									<input type="text" name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][short_title]" value="<?php echo esc_attr( $mod['short_title'] ?? '' ); ?>" class="widefat" />
								</div>
								<div>
									<label class="tca-ind-desc">Título Completo</label>
									<input type="text" name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][title]" value="<?php echo esc_attr( $mod['title'] ?? '' ); ?>" class="widefat" />
								</div>
							</div>
							<div style="margin-top:8px;">
								<label class="tca-ind-desc">Descripción</label>
								<textarea name="ind_about[modules][<?php echo esc_attr( $idx ); ?>][description]" rows="2" class="widefat"><?php echo esc_textarea( $mod['description'] ?? '' ); ?></textarea>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button button-secondary" id="ind-add-module-btn">+ Añadir Módulo</button>
			</div>
		</div>
	</div>

	<script>
		// Tab Switcher
		document.querySelectorAll('.tca-ind-tabs .nav-tab').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				var parent = this.closest('.tca-ind-wrap');
				if (!parent) return;
				var targetId = this.getAttribute('data-tab');
				parent.querySelectorAll('.tca-ind-tabs .nav-tab').forEach(function(b) {
					b.classList.remove('nav-tab-active');
					b.setAttribute('aria-selected', 'false');
				});
				this.classList.add('nav-tab-active');
				this.setAttribute('aria-selected', 'true');
				parent.querySelectorAll('.tca-tab-pane').forEach(function(pane) {
					pane.classList.remove('is-active');
				});
				var targetPane = document.getElementById(targetId);
				if (targetPane) {
					targetPane.classList.add('is-active');
				}
			});
		});

		// Gallery Manager
		(function() {
			var grid = document.getElementById('ind_about_gallery_grid');
			var hiddenInput = document.getElementById('ind_about_gallery_ids');
			var emptyState = document.getElementById('ind_gallery_empty');
			var counter = document.getElementById('ind_gallery_counter');
			var addBtn = document.getElementById('ind_add_gallery_btn');
			var restoreBtn = document.getElementById('ind_restore_default_gallery_btn');
			var clearBtn = document.getElementById('ind_clear_gallery_btn');

			if (!grid || !hiddenInput) return;

			function syncGallery() {
				var items = grid.querySelectorAll('.tca-gallery-item');
				var ids = [];
				items.forEach(function(item) {
					var id = item.getAttribute('data-id');
					if (id) ids.push(id);
				});
				hiddenInput.value = ids.join(',');
				if (counter) counter.textContent = ids.length + ' diapositivas';
				if (emptyState) {
					emptyState.style.display = ids.length === 0 ? 'block' : 'none';
				}
			}

			grid.addEventListener('click', function(e) {
				var removeBtn = e.target.closest('.tca-gallery-remove-btn');
				if (removeBtn) {
					e.preventDefault();
					var item = removeBtn.closest('.tca-gallery-item');
					if (item) {
						item.remove();
						syncGallery();
					}
					return;
				}

				var moveLeft = e.target.closest('.tca-move-left');
				if (moveLeft) {
					e.preventDefault();
					var item = moveLeft.closest('.tca-gallery-item');
					var prev = item ? item.previousElementSibling : null;
					if (prev && prev !== emptyState && prev.classList.contains('tca-gallery-item')) {
						grid.insertBefore(item, prev);
						syncGallery();
					}
					return;
				}

				var moveRight = e.target.closest('.tca-move-right');
				if (moveRight) {
					e.preventDefault();
					var item = moveRight.closest('.tca-gallery-item');
					var next = item ? item.nextElementSibling : null;
					if (next && next !== emptyState && next.classList.contains('tca-gallery-item')) {
						grid.insertBefore(next, item);
						syncGallery();
					}
					return;
				}
			});

			// Drag & Drop
			var draggedItem = null;
			grid.addEventListener('dragstart', function(e) {
				var item = e.target.closest('.tca-gallery-item');
				if (item) {
					draggedItem = item;
					item.classList.add('dragging');
					if (e.dataTransfer) {
						e.dataTransfer.effectAllowed = 'move';
					}
				}
			});
			grid.addEventListener('dragend', function(e) {
				if (draggedItem) {
					draggedItem.classList.remove('dragging');
					draggedItem = null;
					syncGallery();
				}
			});
			grid.addEventListener('dragover', function(e) {
				e.preventDefault();
				var targetItem = e.target.closest('.tca-gallery-item');
				if (targetItem && targetItem !== draggedItem) {
					var rect = targetItem.getBoundingClientRect();
					var next = (e.clientX - rect.left) / (rect.right - rect.left) > 0.5;
					grid.insertBefore(draggedItem, next ? targetItem.nextSibling : targetItem);
				}
			});

			function createItemNode(id, url, title) {
				var div = document.createElement('div');
				div.className = 'tca-gallery-item';
				div.setAttribute('data-id', id);
				div.setAttribute('draggable', 'true');
				div.innerHTML = '<button type="button" class="tca-gallery-remove-btn" title="Eliminar de la galería" aria-label="Eliminar">&times;</button>' +
					'<div class="tca-gallery-thumb-wrap">' +
					'<img src="' + url + '" alt="' + (title || '') + '" />' +
					'</div>' +
					'<div class="tca-gallery-item-footer">' +
					'<span style="max-width:70px; overflow:hidden; text-overflow:ellipsis;" title="' + (title || '') + '">' + (title || 'Imagen') + '</span>' +
					'<div class="tca-gallery-item-actions">' +
					'<button type="button" class="tca-gallery-move-btn tca-move-left" title="Mover a la izquierda">&larr;</button>' +
					'<button type="button" class="tca-gallery-move-btn tca-move-right" title="Mover a la derecha">&rarr;</button>' +
					'</div>' +
					'</div>';
				return div;
			}

			if (addBtn) {
				addBtn.addEventListener('click', function(e) {
					e.preventDefault();
					if (typeof wp === 'undefined' || !wp.media) {
						alert('La biblioteca de medios no está disponible.');
						return;
					}
					var frame = wp.media({
						title: 'Añadir imágenes a la Galería de Diapositivas',
						button: { text: 'Añadir a la galería' },
						multiple: 'add',
						library: { type: 'image' }
					});
					frame.on('select', function() {
						var selection = frame.state().get('selection');
						selection.each(function(attachment) {
							var att = attachment.toJSON();
							var imgUrl = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : (att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url);
							var title = att.title || att.filename || ('Adjunto #' + att.id);
							var node = createItemNode(att.id, imgUrl, title);
							grid.insertBefore(node, emptyState);
						});
						syncGallery();
					});
					frame.open();
				});
			}

			if (restoreBtn) {
				restoreBtn.addEventListener('click', function(e) {
					e.preventDefault();
					if (!confirm('¿Restaurar las 3 imágenes por defecto de la academia?')) return;
					grid.querySelectorAll('.tca-gallery-item').forEach(function(el) { el.remove(); });
					var themeUri = <?php echo json_encode( get_stylesheet_directory_uri() ); ?>;
					var defaults = [
						{ id: 161, url: themeUri + '/assets/img/about/mapa-de-stakeholders.webp', title: 'mapa-de-stakeholders.webp' },
						{ id: 162, url: themeUri + '/assets/img/about/radar-de-amenazas.webp', title: 'radar-de-amenazas.webp' },
						{ id: 163, url: themeUri + '/assets/img/about/war-room.webp', title: 'war-room.webp' }
					];
					defaults.forEach(function(d) {
						grid.insertBefore(createItemNode(d.id, d.url, d.title), emptyState);
					});
					syncGallery();
				});
			}

			if (clearBtn) {
				clearBtn.addEventListener('click', function(e) {
					e.preventDefault();
					if (!confirm('¿Estás seguro de vaciar todas las imágenes de la galería?')) return;
					grid.querySelectorAll('.tca-gallery-item').forEach(function(el) { el.remove(); });
					syncGallery();
				});
			}
		})();

		// Modules Repeater
		document.getElementById('ind-add-module-btn')?.addEventListener('click', function() {
			var container = document.getElementById('ind-modules-container');
			var idx = container.children.length;
			var row = document.createElement('div');
			row.className = 'tca-ind-row';
			row.innerHTML = '<div class="tca-ind-row-header"><span>Nuevo Módulo</span><button type="button" class="tca-ind-del-btn" onclick="this.closest(\x27.tca-ind-row\x27).remove();">Eliminar</button></div>' +
				'<div class="tca-ind-grid-3">' +
				'<div><label class="tca-ind-desc">Icono</label><select name="ind_about[modules][' + idx + '][icon]" style="width:100%;"><option value="radar">Radar</option><option value="chart">Gráfica</option><option value="cpu">Procesador</option><option value="target">Diana</option><option value="share-2">Redes</option><option value="mic">Vocería</option><option value="shield-alert">Escudo</option></select></div>' +
				'<div><label class="tca-ind-desc">Período</label><input type="text" name="ind_about[modules][' + idx + '][period]" placeholder="08 • Práctica" class="widefat" /></div>' +
				'<div><label class="tca-ind-desc">Etiqueta</label><input type="text" name="ind_about[modules][' + idx + '][tag]" placeholder="Especializado" class="widefat" /></div>' +
				'</div>' +
				'<div class="tca-ind-grid-2" style="margin-top:8px;">' +
				'<div><label class="tca-ind-desc">Título Corto</label><input type="text" name="ind_about[modules][' + idx + '][short_title]" class="widefat" /></div>' +
				'<div><label class="tca-ind-desc">Título Completo</label><input type="text" name="ind_about[modules][' + idx + '][title]" class="widefat" /></div>' +
				'</div>' +
				'<div style="margin-top:8px;"><label class="tca-ind-desc">Descripción</label><textarea name="ind_about[modules][' + idx + '][description]" rows="2" class="widefat"></textarea></div>';
			container.appendChild(row);
		});
	</script>
	<?php
}

/**
 * 3. Certification Metabox Render
 */
function thecrisisacademy_render_individuals_certification_metabox( $post ) {
	thecrisisacademy_render_individuals_metabox_styles();
	$data = thecrisisacademy_get_individuals_cert_data( $post->ID );
	?>
	<div class="tca-ind-wrap">
		<!-- 00. Intro -->
		<h4 style="margin:0 0 10px; font-size:14px; color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">00. Introducción de Certificación</h4>
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_cert_intro_subheading">Subtítulo</label>
				<input type="text" id="ind_cert_intro_subheading" name="ind_cert[intro_subheading]" value="<?php echo esc_attr( $data['intro_subheading'] ); ?>" class="large-text" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_cert_intro_lead">Párrafo Destacado (Lead)</label>
				<input type="text" id="ind_cert_intro_lead" name="ind_cert[intro_lead]" value="<?php echo esc_attr( $data['intro_lead'] ); ?>" class="large-text" />
			</div>
		</div>
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_cert_intro_title">Título Principal</label>
			<textarea id="ind_cert_intro_title" name="ind_cert[intro_title]" rows="2" class="large-text"><?php echo esc_textarea( $data['intro_title'] ); ?></textarea>
		</div>

		<!-- 01. El Momento Crítico -->
		<h4 style="margin:20px 0 10px; font-size:14px; color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">01. El Momento Crítico</h4>
		<div class="tca-ind-grid-3">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Número</label>
				<input type="text" name="ind_cert[c01_number]" value="<?php echo esc_attr( $data['c01_number'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Encabezado</label>
				<input type="text" name="ind_cert[c01_header]" value="<?php echo esc_attr( $data['c01_header'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Eyebrow</label>
				<input type="text" name="ind_cert[c01_eyebrow]" value="<?php echo esc_attr( $data['c01_eyebrow'] ); ?>" class="widefat" />
			</div>
		</div>
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Título</label>
				<textarea name="ind_cert[c01_title]" rows="2" class="widefat"><?php echo esc_textarea( $data['c01_title'] ); ?></textarea>
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Párrafo</label>
				<textarea name="ind_cert[c01_lead]" rows="2" class="widefat"><?php echo esc_textarea( $data['c01_lead'] ); ?></textarea>
			</div>
		</div>
		<div class="tca-ind-field">
			<label class="tca-ind-label">Puntos Críticos (uno por línea)</label>
			<textarea name="ind_cert[c01_items_text]" rows="4" class="large-text"><?php
				$lines = array();
				foreach ( $data['c01_items'] as $item ) {
					$lines[] = is_array( $item ) ? ( $item['text'] ?? '' ) : $item;
				}
				echo esc_textarea( implode( "\n", array_filter( $lines ) ) );
			?></textarea>
		</div>

		<!-- 02. La Preparación -->
		<h4 style="margin:20px 0 10px; font-size:14px; color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">02. La Preparación</h4>
		<div class="tca-ind-grid-3">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Número</label>
				<input type="text" name="ind_cert[c02_number]" value="<?php echo esc_attr( $data['c02_number'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Encabezado</label>
				<input type="text" name="ind_cert[c02_header]" value="<?php echo esc_attr( $data['c02_header'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Eyebrow</label>
				<input type="text" name="ind_cert[c02_eyebrow]" value="<?php echo esc_attr( $data['c02_eyebrow'] ); ?>" class="widefat" />
			</div>
		</div>
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Título</label>
				<textarea name="ind_cert[c02_title]" rows="2" class="widefat"><?php echo esc_textarea( $data['c02_title'] ); ?></textarea>
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Párrafo</label>
				<textarea name="ind_cert[c02_lead]" rows="2" class="widefat"><?php echo esc_textarea( $data['c02_lead'] ); ?></textarea>
			</div>
		</div>

		<!-- 03. El Formato -->
		<h4 style="margin:20px 0 10px; font-size:14px; color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">03. El Formato</h4>
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Título</label>
				<textarea name="ind_cert[c03_title]" rows="2" class="widefat"><?php echo esc_textarea( $data['c03_title'] ); ?></textarea>
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Párrafo</label>
				<textarea name="ind_cert[c03_lead]" rows="2" class="widefat"><?php echo esc_textarea( $data['c03_lead'] ); ?></textarea>
			</div>
		</div>

		<!-- 04. El Siguiente Capítulo -->
		<h4 style="margin:20px 0 10px; font-size:14px; color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">04. El Siguiente Capítulo</h4>
		<div class="tca-ind-field">
			<label class="tca-ind-label">Título</label>
			<textarea name="ind_cert[c04_title]" rows="2" class="large-text"><?php echo esc_textarea( $data['c04_title'] ); ?></textarea>
		</div>
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label">Texto del Botón</label>
				<input type="text" name="ind_cert[c04_btn_text]" value="<?php echo esc_attr( $data['c04_btn_text'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label">Enlace URL del Botón</label>
				<input type="text" name="ind_cert[c04_btn_url]" value="<?php echo esc_attr( $data['c04_btn_url'] ); ?>" class="widefat" />
			</div>
		</div>
		<div class="tca-ind-field">
			<label class="tca-ind-label">Puntos Clave Finales (uno por línea)</label>
			<textarea name="ind_cert[c04_points_text]" rows="3" class="large-text"><?php
				$p_lines = array();
				foreach ( $data['c04_points'] as $p ) {
					$p_lines[] = is_array( $p ) ? ( $p['text'] ?? '' ) : $p;
				}
				echo esc_textarea( implode( "\n", array_filter( $p_lines ) ) );
			?></textarea>
		</div>
	</div>
	<?php
}

/**
 * 4. Signals Metabox Render
 */
function thecrisisacademy_render_individuals_signals_metabox( $post ) {
	thecrisisacademy_render_individuals_metabox_styles();
	$data = thecrisisacademy_get_individuals_signals_data( $post->ID );
	?>
	<div class="tca-ind-wrap">
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_signals_title">Título Principal (HTML permitido)</label>
			<textarea id="ind_signals_title" name="ind_signals[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_signals_subtitle">Subtítulo (HTML permitido)</label>
			<textarea id="ind_signals_subtitle" name="ind_signals[subtitle]" rows="2" class="large-text"><?php echo esc_textarea( $data['subtitle'] ); ?></textarea>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label">Indicadores de Señales</label>
			<div id="ind-signals-container">
				<?php foreach ( $data['container'] as $idx => $sig ) : ?>
					<div class="tca-ind-row">
						<div class="tca-ind-row-header">
							<span>Indicador #<?php echo esc_html( $idx + 1 ); ?></span>
							<button type="button" class="tca-ind-del-btn" onclick="this.closest('.tca-ind-row').remove();">Eliminar</button>
						</div>
						<div class="tca-ind-grid-3">
							<div>
								<label class="tca-ind-desc">Cifra / Número</label>
								<input type="text" name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][number]" value="<?php echo esc_attr( $sig['number'] ?? '' ); ?>" class="widefat" placeholder="75" />
							</div>
							<div>
								<label class="tca-ind-desc">Sufijo / Etiqueta</label>
								<input type="text" name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][label]" value="<?php echo esc_attr( $sig['label'] ?? '' ); ?>" class="widefat" placeholder="%, MIN" />
							</div>
							<div>
								<label class="tca-ind-desc">Icono (opcional)</label>
								<input type="text" name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][icon]" value="<?php echo esc_attr( $sig['icon'] ?? '' ); ?>" class="widefat" placeholder="cpu, alert-triangle" />
							</div>
						</div>
						<div style="margin-top:8px;">
							<label class="tca-ind-desc">Texto Descriptivo</label>
							<textarea name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][info]" rows="2" class="widefat"><?php echo esc_textarea( $sig['info'] ?? '' ); ?></textarea>
						</div>
						<div class="tca-ind-grid-2" style="margin-top:8px;">
							<div>
								<label class="tca-ind-desc">Fuente (Nombre)</label>
								<input type="text" name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][source_label]" value="<?php echo esc_attr( $sig['source_label'] ?? '' ); ?>" class="widefat" />
							</div>
							<div>
								<label class="tca-ind-desc">Enlace de la Fuente</label>
								<input type="text" name="ind_signals[items][<?php echo esc_attr( $idx ); ?>][source_url]" value="<?php echo esc_attr( $sig['source_url'] ?? '' ); ?>" class="widefat" />
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * 5. How Works Metabox Render
 */
function thecrisisacademy_render_individuals_how_works_metabox( $post ) {
	thecrisisacademy_render_individuals_metabox_styles();
	$data = thecrisisacademy_get_individuals_how_works_data( $post->ID );
	?>
	<div class="tca-ind-wrap">
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_hw_preheading">Subtítulo Superior</label>
				<input type="text" id="ind_hw_preheading" name="ind_how_works[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_hw_title">Título Principal</label>
				<textarea id="ind_hw_title" name="ind_how_works[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			</div>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label">Soluciones / Servicios de la Sección</label>
			<div id="ind-hw-items-container">
				<?php foreach ( $data['items'] as $idx => $item ) : ?>
					<div class="tca-ind-row">
						<div class="tca-ind-row-header">
							<span>Solución #<?php echo esc_html( $idx + 1 ); ?>: <?php echo esc_html( $item['title'] ?? '' ); ?></span>
							<button type="button" class="tca-ind-del-btn" onclick="this.closest('.tca-ind-row').remove();">Eliminar</button>
						</div>
						<div class="tca-ind-grid-3">
							<div>
								<label class="tca-ind-desc">Número</label>
								<input type="text" name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][number]" value="<?php echo esc_attr( $item['number'] ?? '' ); ?>" class="widefat" />
							</div>
							<div>
								<label class="tca-ind-desc">Código Radar</label>
								<input type="text" name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][radar_code]" value="<?php echo esc_attr( $item['radar_code'] ?? '' ); ?>" class="widefat" />
							</div>
							<div>
								<label class="tca-ind-desc">ID Lightbox Destino</label>
								<input type="text" name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][lightbox_target]" value="<?php echo esc_attr( $item['lightbox_target'] ?? '' ); ?>" class="widefat" />
							</div>
						</div>
						<div style="margin-top:8px;">
							<label class="tca-ind-desc">Título</label>
							<input type="text" name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>" class="widefat" />
						</div>
						<div style="margin-top:8px;">
							<label class="tca-ind-desc">Descripción</label>
							<textarea name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][description]" rows="2" class="widefat"><?php echo esc_textarea( $item['description'] ?? '' ); ?></textarea>
						</div>
						<div style="margin-top:8px;">
							<label class="tca-ind-desc">Viñetas / Bullets (uno por línea)</label>
							<textarea name="ind_how_works[items][<?php echo esc_attr( $idx ); ?>][bullets]" rows="3" class="widefat"><?php echo esc_textarea( $item['bullets'] ?? '' ); ?></textarea>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * 6. Simulation Metabox Render
 */
function thecrisisacademy_render_individuals_simulation_metabox( $post ) {
	thecrisisacademy_render_individuals_metabox_styles();
	$data           = thecrisisacademy_get_individuals_simulation_data( $post->ID );
	$theme_uri      = get_stylesheet_directory_uri();
	$default_images = array(
		'radar'        => $theme_uri . '/assets/img/simulator/radar.webp',
		'stakeholders' => $theme_uri . '/assets/img/simulator/stakeholders-map.webp',
		'war-room'     => $theme_uri . '/assets/img/simulator/war-room-1.webp',
	);
	?>
	<div class="tca-ind-wrap">
		<div class="tca-ind-grid-2">
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_sim_preheading">Subtítulo</label>
				<input type="text" id="ind_sim_preheading" name="ind_sim[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_sim_console_status">Estado de Consola</label>
				<input type="text" id="ind_sim_console_status" name="ind_sim[console_status]" value="<?php echo esc_attr( $data['console_status'] ); ?>" class="large-text" />
			</div>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_sim_title">Título Principal</label>
			<textarea id="ind_sim_title" name="ind_sim[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label" for="ind_sim_intro">Texto Introductorio</label>
			<textarea id="ind_sim_intro" name="ind_sim[intro]" rows="2" class="large-text"><?php echo esc_textarea( $data['intro'] ); ?></textarea>
		</div>

		<div class="tca-ind-grid-3">
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_sim_cta_label">Texto del Botón CTA</label>
				<input type="text" id="ind_sim_cta_label" name="ind_sim[cta_label]" value="<?php echo esc_attr( $data['cta_label'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_sim_cta_url">Enlace del Botón</label>
				<input type="text" id="ind_sim_cta_url" name="ind_sim[cta_url]" value="<?php echo esc_attr( $data['cta_url'] ); ?>" class="widefat" />
			</div>
			<div class="tca-ind-field">
				<label class="tca-ind-label" for="ind_sim_cta_lightbox">ID Lightbox (data-open-lightbox)</label>
				<input type="text" id="ind_sim_cta_lightbox" name="ind_sim[cta_lightbox]" value="<?php echo esc_attr( $data['cta_lightbox'] ); ?>" class="widefat" />
			</div>
		</div>

		<div class="tca-ind-field">
			<label class="tca-ind-label">Etapas del Simulador</label>
			<p class="tca-ind-desc" style="margin-bottom:12px;">Configura cada etapa con su KPI asociado e imagen descriptiva (puedes seleccionarla desde la biblioteca de medios de WordPress o usar las imágenes base del tema).</p>
			<div id="ind-sim-stages-container">
				<?php foreach ( $data['stages'] as $idx => $stg ) :
					$stage_id    = $stg['id'] ?? '';
					$current_img = $stg['image'] ?? '';
					$current_alt = $stg['alt'] ?? '';
					$default_url = $default_images[ $stage_id ] ?? '';
					if ( empty( $default_url ) ) {
						$keys = array_keys( $default_images );
						if ( isset( $keys[ $idx ] ) ) {
							$default_url = $default_images[ $keys[ $idx ] ];
						}
					}
				?>
					<div class="tca-ind-row" data-stage-index="<?php echo esc_attr( $idx ); ?>">
						<div class="tca-ind-row-header">
							<span>Etapa: <strong class="tca-stage-header-label"><?php echo esc_html( $stg['label'] ?? ( 'Etapa #' . ( $idx + 1 ) ) ); ?></strong></span>
							<button type="button" class="tca-ind-del-btn" onclick="this.closest('.tca-ind-row').remove();">Eliminar</button>
						</div>
						<div class="tca-ind-grid-3">
							<div>
								<label class="tca-ind-desc">ID</label>
								<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][id]" value="<?php echo esc_attr( $stage_id ); ?>" class="widefat tca-stage-id-input" />
							</div>
							<div>
								<label class="tca-ind-desc">Etiqueta / Pestaña</label>
								<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][label]" value="<?php echo esc_attr( $stg['label'] ?? '' ); ?>" class="widefat tca-stage-label-input" />
							</div>
							<div>
								<label class="tca-ind-desc">KPI (Métrica)</label>
								<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][kpi]" value="<?php echo esc_attr( $stg['kpi'] ?? '' ); ?>" class="widefat" />
							</div>
						</div>
						<div class="tca-ind-grid-2" style="margin-top:8px;">
							<div>
								<label class="tca-ind-desc">Título de la Etapa</label>
								<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][title]" value="<?php echo esc_attr( $stg['title'] ?? '' ); ?>" class="widefat" />
							</div>
							<div>
								<label class="tca-ind-desc">Etiqueta del KPI</label>
								<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][kpi_label]" value="<?php echo esc_attr( $stg['kpi_label'] ?? '' ); ?>" class="widefat" />
							</div>
						</div>
						<div style="margin-top:8px;">
							<label class="tca-ind-desc">Descripción</label>
							<textarea name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][desc]" rows="2" class="widefat"><?php echo esc_textarea( $stg['desc'] ?? '' ); ?></textarea>
						</div>

						<!-- Media Selector Card -->
						<div class="tca-stage-media-card" style="margin-top:12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:12px;">
							<label class="tca-ind-desc" style="font-weight:600; color:#334155; margin-bottom:8px; display:block;">
								<span class="dashicons dashicons-format-image" style="vertical-align:text-bottom; margin-right:4px;"></span>
								Imagen de la Etapa (Simulador)
							</label>
							<div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">
								<div class="tca-stage-preview-box" style="width:160px; height:95px; background:#0f172a; border:1px solid #94a3b8; border-radius:6px; overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
									<img class="tca-stage-preview-img" src="<?php echo esc_url( $current_img ); ?>" alt="<?php echo esc_attr( $current_alt ); ?>" style="width:100%; height:100%; object-fit:cover; display:<?php echo ! empty( $current_img ) ? 'block' : 'none'; ?>;" />
									<div class="tca-stage-no-img" style="color:#94a3b8; font-size:11px; text-align:center; padding:6px; display:<?php echo empty( $current_img ) ? 'block' : 'none'; ?>;">
										<span class="dashicons dashicons-camera" style="font-size:24px; width:24px; height:24px; display:block; margin:0 auto 4px;"></span>
										Sin imagen
									</div>
								</div>

								<div style="flex:1; min-width:240px;">
									<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
										<button type="button" class="button button-primary tca-select-stage-media-btn">
											<span class="dashicons dashicons-images-alt2"></span>
											<span>Seleccionar de la galería</span>
										</button>
										<?php if ( ! empty( $default_url ) ) : ?>
											<button type="button" class="button tca-restore-stage-media-btn" data-default-url="<?php echo esc_attr( $default_url ); ?>" title="Restaurar la imagen base local de esta etapa">
												<span class="dashicons dashicons-undo"></span>
												<span>Imagen base</span>
											</button>
										<?php endif; ?>
										<button type="button" class="button tca-remove-stage-media-btn" style="color:#b91c1c;">
											<span class="dashicons dashicons-trash"></span>
											<span>Quitar</span>
										</button>
									</div>

									<div class="tca-ind-grid-2">
										<div>
											<label class="tca-ind-desc">URL de Imagen</label>
											<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][image]" value="<?php echo esc_attr( $current_img ); ?>" class="widefat tca-stage-img-url" placeholder="https://... o ruta local" />
										</div>
										<div>
											<label class="tca-ind-desc">Texto Alt de Imagen</label>
											<input type="text" name="ind_sim[stages][<?php echo esc_attr( $idx ); ?>][alt]" value="<?php echo esc_attr( $current_alt ); ?>" class="widefat tca-stage-img-alt" placeholder="Descripción de la imagen" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div style="margin-top:12px; display:flex; gap:8px;">
				<button type="button" class="button" id="ind-add-stage-btn">
					<span class="dashicons dashicons-plus-alt"></span>
					<span>Añadir Etapa</span>
				</button>
				<button type="button" class="button" id="ind-restore-stages-btn">
					<span class="dashicons dashicons-image-rotate"></span>
					<span>Restaurar 3 etapas base</span>
				</button>
			</div>
		</div>
	</div>

	<script>
	(function() {
		var container = document.getElementById('ind-sim-stages-container');
		if (!container) return;

		var defaultImages = <?php echo json_encode( $default_images ); ?>;

		// Delegate media picker, restore and remove
		container.addEventListener('click', function(e) {
			var selectBtn = e.target.closest('.tca-select-stage-media-btn');
			if (selectBtn) {
				e.preventDefault();
				if (typeof wp === 'undefined' || !wp.media) {
					alert('La biblioteca de medios no está disponible.');
					return;
				}
				var card = selectBtn.closest('.tca-stage-media-card');
				var urlInput = card.querySelector('.tca-stage-img-url');
				var altInput = card.querySelector('.tca-stage-img-alt');
				var previewImg = card.querySelector('.tca-stage-preview-img');
				var noImg = card.querySelector('.tca-stage-no-img');

				var frame = wp.media({
					title: 'Seleccionar imagen para la etapa del simulador',
					button: { text: 'Usar esta imagen' },
					multiple: false,
					library: { type: 'image' }
				});

				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					var imgUrl = attachment.url;
					if (urlInput) urlInput.value = imgUrl;
					if (previewImg) {
						previewImg.src = imgUrl;
						previewImg.style.display = 'block';
					}
					if (noImg) noImg.style.display = 'none';
					if (altInput && !altInput.value && attachment.alt) {
						altInput.value = attachment.alt;
					}
				});

				frame.open();
				return;
			}

			var restoreBtn = e.target.closest('.tca-restore-stage-media-btn');
			if (restoreBtn) {
				e.preventDefault();
				var defaultUrl = restoreBtn.getAttribute('data-default-url');
				if (!defaultUrl) return;
				var card = restoreBtn.closest('.tca-stage-media-card');
				var urlInput = card.querySelector('.tca-stage-img-url');
				var previewImg = card.querySelector('.tca-stage-preview-img');
				var noImg = card.querySelector('.tca-stage-no-img');

				if (urlInput) urlInput.value = defaultUrl;
				if (previewImg) {
					previewImg.src = defaultUrl;
					previewImg.style.display = 'block';
				}
				if (noImg) noImg.style.display = 'none';
				return;
			}

			var removeBtn = e.target.closest('.tca-remove-stage-media-btn');
			if (removeBtn) {
				e.preventDefault();
				var card = removeBtn.closest('.tca-stage-media-card');
				var urlInput = card.querySelector('.tca-stage-img-url');
				var previewImg = card.querySelector('.tca-stage-preview-img');
				var noImg = card.querySelector('.tca-stage-no-img');

				if (urlInput) urlInput.value = '';
				if (previewImg) {
					previewImg.src = '';
					previewImg.style.display = 'none';
				}
				if (noImg) noImg.style.display = 'block';
				return;
			}
		});

		// Dynamic update if URL text changes manually
		container.addEventListener('input', function(e) {
			if (e.target.matches('.tca-stage-img-url')) {
				var val = e.target.value.trim();
				var card = e.target.closest('.tca-stage-media-card');
				var previewImg = card.querySelector('.tca-stage-preview-img');
				var noImg = card.querySelector('.tca-stage-no-img');
				if (val) {
					previewImg.src = val;
					previewImg.style.display = 'block';
					noImg.style.display = 'none';
				} else {
					previewImg.src = '';
					previewImg.style.display = 'none';
					noImg.style.display = 'block';
				}
			}
			if (e.target.matches('.tca-stage-label-input')) {
				var headerLabel = e.target.closest('.tca-ind-row').querySelector('.tca-stage-header-label');
				if (headerLabel) headerLabel.textContent = e.target.value || 'Nueva Etapa';
			}
		});

		// Add new stage button
		var addBtn = document.getElementById('ind-add-stage-btn');
		if (addBtn) {
			addBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var idx = container.querySelectorAll('.tca-ind-row').length;
				var row = document.createElement('div');
				row.className = 'tca-ind-row';
				row.setAttribute('data-stage-index', idx);
				row.innerHTML = '<div class="tca-ind-row-header">' +
					'<span>Etapa: <strong class="tca-stage-header-label">Nueva Etapa</strong></span>' +
					'<button type="button" class="tca-ind-del-btn" onclick="this.closest(\x27.tca-ind-row\x27).remove();">Eliminar</button>' +
					'</div>' +
					'<div class="tca-ind-grid-3">' +
					'<div><label class="tca-ind-desc">ID</label><input type="text" name="ind_sim[stages][' + idx + '][id]" value="stage-' + (idx + 1) + '" class="widefat tca-stage-id-input" /></div>' +
					'<div><label class="tca-ind-desc">Etiqueta / Pestaña</label><input type="text" name="ind_sim[stages][' + idx + '][label]" value="" placeholder="Etapa ' + (idx + 1) + '" class="widefat tca-stage-label-input" /></div>' +
					'<div><label class="tca-ind-desc">KPI (Métrica)</label><input type="text" name="ind_sim[stages][' + idx + '][kpi]" value="" placeholder="KPI" class="widefat" /></div>' +
					'</div>' +
					'<div class="tca-ind-grid-2" style="margin-top:8px;">' +
					'<div><label class="tca-ind-desc">Título de la Etapa</label><input type="text" name="ind_sim[stages][' + idx + '][title]" value="" class="widefat" /></div>' +
					'<div><label class="tca-ind-desc">Etiqueta del KPI</label><input type="text" name="ind_sim[stages][' + idx + '][kpi_label]" value="" class="widefat" /></div>' +
					'</div>' +
					'<div style="margin-top:8px;"><label class="tca-ind-desc">Descripción</label><textarea name="ind_sim[stages][' + idx + '][desc]" rows="2" class="widefat"></textarea></div>' +
					'<div class="tca-stage-media-card" style="margin-top:12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:12px;">' +
					'<label class="tca-ind-desc" style="font-weight:600; color:#334155; margin-bottom:8px; display:block;"><span class="dashicons dashicons-format-image" style="vertical-align:text-bottom; margin-right:4px;"></span>Imagen de la Etapa (Simulador)</label>' +
					'<div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">' +
					'<div class="tca-stage-preview-box" style="width:160px; height:95px; background:#0f172a; border:1px solid #94a3b8; border-radius:6px; overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0;">' +
					'<img class="tca-stage-preview-img" src="" alt="" style="width:100%; height:100%; object-fit:cover; display:none;" />' +
					'<div class="tca-stage-no-img" style="color:#94a3b8; font-size:11px; text-align:center; padding:6px; display:block;"><span class="dashicons dashicons-camera" style="font-size:24px; width:24px; height:24px; display:block; margin:0 auto 4px;"></span>Sin imagen</div>' +
					'</div>' +
					'<div style="flex:1; min-width:240px;">' +
					'<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">' +
					'<button type="button" class="button button-primary tca-select-stage-media-btn"><span class="dashicons dashicons-images-alt2"></span><span>Seleccionar de la galería</span></button>' +
					'<button type="button" class="button tca-remove-stage-media-btn" style="color:#b91c1c;"><span class="dashicons dashicons-trash"></span><span>Quitar</span></button>' +
					'</div>' +
					'<div class="tca-ind-grid-2">' +
					'<div><label class="tca-ind-desc">URL de Imagen</label><input type="text" name="ind_sim[stages][' + idx + '][image]" value="" class="widefat tca-stage-img-url" placeholder="https://..." /></div>' +
					'<div><label class="tca-ind-desc">Texto Alt de Imagen</label><input type="text" name="ind_sim[stages][' + idx + '][alt]" value="" class="widefat tca-stage-img-alt" placeholder="Descripción de la imagen" /></div>' +
					'</div>' +
					'</div>' +
					'</div>' +
					'</div>';
				container.appendChild(row);
			});
		}

		// Restore base 3 stages button
		var restoreStagesBtn = document.getElementById('ind-restore-stages-btn');
		if (restoreStagesBtn) {
			restoreStagesBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (!confirm('¿Restaurar las 3 etapas base originales con sus imágenes locales?')) return;
				container.innerHTML = '';
				var baseStages = [
					{
						id: 'radar',
						label: 'Radar de riesgos',
						title: 'Detecta la crisis antes de que estalle',
						desc: 'Monitorea señales débiles, menciones y alertas tempranas para clasificar el nivel de amenaza en tiempo real.',
						image: defaultImages.radar,
						alt: 'Radar de riesgos del simulador de crisis',
						kpi: 'URR',
						kpi_label: 'Uso de Radar de Riesgos'
					},
					{
						id: 'stakeholders',
						label: 'Mapa de stakeholders',
						title: 'Prioriza a quién hablarle primero',
						desc: 'Identifica a las audiencias críticas, su nivel de influencia y el mensaje que cada una necesita escuchar.',
						image: defaultImages.stakeholders,
						alt: 'Mapa de stakeholders del simulador de crisis',
						kpi: 'MPR',
						kpi_label: 'Manejo de Protocolo de Respuesta'
					},
					{
						id: 'war-room',
						label: 'War room y simulación activa',
						title: 'Toma decisiones bajo fuego cruzado',
						desc: 'Enfrenta periodistas simulados, tendencias virales y filtraciones en una consola de respuesta en tiempo real.',
						image: defaultImages['war-room'],
						alt: 'War room y simulación activa del simulador de crisis',
						kpi: 'TTR',
						kpi_label: 'Tiempo de Reacción y Contención'
					}
				];
				baseStages.forEach(function(stg, idx) {
					var row = document.createElement('div');
					row.className = 'tca-ind-row';
					row.setAttribute('data-stage-index', idx);
					row.innerHTML = '<div class="tca-ind-row-header">' +
						'<span>Etapa: <strong class="tca-stage-header-label">' + stg.label + '</strong></span>' +
						'<button type="button" class="tca-ind-del-btn" onclick="this.closest(\x27.tca-ind-row\x27).remove();">Eliminar</button>' +
						'</div>' +
						'<div class="tca-ind-grid-3">' +
						'<div><label class="tca-ind-desc">ID</label><input type="text" name="ind_sim[stages][' + idx + '][id]" value="' + stg.id + '" class="widefat tca-stage-id-input" /></div>' +
						'<div><label class="tca-ind-desc">Etiqueta / Pestaña</label><input type="text" name="ind_sim[stages][' + idx + '][label]" value="' + stg.label + '" class="widefat tca-stage-label-input" /></div>' +
						'<div><label class="tca-ind-desc">KPI (Métrica)</label><input type="text" name="ind_sim[stages][' + idx + '][kpi]" value="' + stg.kpi + '" class="widefat" /></div>' +
						'</div>' +
						'<div class="tca-ind-grid-2" style="margin-top:8px;">' +
						'<div><label class="tca-ind-desc">Título de la Etapa</label><input type="text" name="ind_sim[stages][' + idx + '][title]" value="' + stg.title + '" class="widefat" /></div>' +
						'<div><label class="tca-ind-desc">Etiqueta del KPI</label><input type="text" name="ind_sim[stages][' + idx + '][kpi_label]" value="' + stg.kpi_label + '" class="widefat" /></div>' +
						'</div>' +
						'<div style="margin-top:8px;"><label class="tca-ind-desc">Descripción</label><textarea name="ind_sim[stages][' + idx + '][desc]" rows="2" class="widefat">' + stg.desc + '</textarea></div>' +
						'<div class="tca-stage-media-card" style="margin-top:12px; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:12px;">' +
						'<label class="tca-ind-desc" style="font-weight:600; color:#334155; margin-bottom:8px; display:block;"><span class="dashicons dashicons-format-image" style="vertical-align:text-bottom; margin-right:4px;"></span>Imagen de la Etapa (Simulador)</label>' +
						'<div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">' +
						'<div class="tca-stage-preview-box" style="width:160px; height:95px; background:#0f172a; border:1px solid #94a3b8; border-radius:6px; overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0;">' +
						'<img class="tca-stage-preview-img" src="' + stg.image + '" alt="' + stg.alt + '" style="width:100%; height:100%; object-fit:cover; display:block;" />' +
						'<div class="tca-stage-no-img" style="color:#94a3b8; font-size:11px; text-align:center; padding:6px; display:none;"><span class="dashicons dashicons-camera" style="font-size:24px; width:24px; height:24px; display:block; margin:0 auto 4px;"></span>Sin imagen</div>' +
						'</div>' +
						'<div style="flex:1; min-width:240px;">' +
						'<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">' +
						'<button type="button" class="button button-primary tca-select-stage-media-btn"><span class="dashicons dashicons-images-alt2"></span><span>Seleccionar de la galería</span></button>' +
						'<button type="button" class="button tca-restore-stage-media-btn" data-default-url="' + stg.image + '"><span class="dashicons dashicons-undo"></span><span>Imagen base</span></button>' +
						'<button type="button" class="button tca-remove-stage-media-btn" style="color:#b91c1c;"><span class="dashicons dashicons-trash"></span><span>Quitar</span></button>' +
						'</div>' +
						'<div class="tca-ind-grid-2">' +
						'<div><label class="tca-ind-desc">URL de Imagen</label><input type="text" name="ind_sim[stages][' + idx + '][image]" value="' + stg.image + '" class="widefat tca-stage-img-url" placeholder="https://..." /></div>' +
						'<div><label class="tca-ind-desc">Texto Alt de Imagen</label><input type="text" name="ind_sim[stages][' + idx + '][alt]" value="' + stg.alt + '" class="widefat tca-stage-img-alt" placeholder="Descripción de la imagen" /></div>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'</div>';
					container.appendChild(row);
				});
			});
		}
	})();
	</script>
	<?php
}

/* ==========================================================================
   5. Save Handler for Particulares Metaboxes
   ========================================================================== */

function thecrisisacademy_save_individuals_metaboxes( $post_id, $post ) {
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['individuals_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['individuals_metabox_nonce'], 'individuals_metabox_save' ) ) {
		return;
	}

	// 1. Hero
	if ( isset( $_POST['ind_hero'] ) && is_array( $_POST['ind_hero'] ) ) {
		$h = $_POST['ind_hero'];
		update_post_meta( $post_id, 'hero_preheading', sanitize_text_field( $h['preheading'] ?? '' ) );
		update_post_meta( $post_id, 'hero_title', wp_kses_post( $h['title'] ?? '' ) );
		update_post_meta( $post_id, 'hero_description', wp_kses_post( $h['description'] ?? '' ) );
		update_post_meta( $post_id, 'hero_canvas_tags', sanitize_textarea_field( $h['canvas_tags'] ?? '' ) );
	}

	// 2. About
	if ( isset( $_POST['ind_about'] ) && is_array( $_POST['ind_about'] ) ) {
		$a = $_POST['ind_about'];
		update_post_meta( $post_id, 'about_preheading', sanitize_text_field( $a['preheading'] ?? '' ) );
		update_post_meta( $post_id, 'about_title', wp_kses_post( $a['title'] ?? '' ) );
		update_post_meta( $post_id, 'about_subtitle', wp_kses_post( $a['subtitle'] ?? '' ) );
		update_post_meta( $post_id, 'about_description', wp_kses_post( $a['description'] ?? '' ) );
		update_post_meta( $post_id, 'about_modules_title', sanitize_text_field( $a['modules_title'] ?? '' ) );

		if ( isset( $a['gallery_ids'] ) ) {
			$raw_ids = array_filter( array_map( 'intval', explode( ',', $a['gallery_ids'] ) ) );
			update_post_meta( $post_id, 'about_gallery', $raw_ids );
		}

		if ( isset( $a['modules'] ) && is_array( $a['modules'] ) ) {
			$clean_modules = array();
			foreach ( $a['modules'] as $mod ) {
				$clean_modules[] = array(
					'icon'        => sanitize_key( $mod['icon'] ?? 'radar' ),
					'period'      => sanitize_text_field( $mod['period'] ?? '' ),
					'tag'         => sanitize_text_field( $mod['tag'] ?? '' ),
					'tag_class'   => 'alert-tag',
					'short_title' => sanitize_text_field( $mod['short_title'] ?? '' ),
					'title'       => sanitize_text_field( $mod['title'] ?? '' ),
					'description' => sanitize_textarea_field( $mod['description'] ?? '' ),
				);
			}
			update_post_meta( $post_id, 'about_modules', $clean_modules );
		}
	}

	// 3. Certification
	if ( isset( $_POST['ind_cert'] ) && is_array( $_POST['ind_cert'] ) ) {
		$c = $_POST['ind_cert'];
		update_post_meta( $post_id, 'cert_intro_subheading', sanitize_text_field( $c['intro_subheading'] ?? '' ) );
		update_post_meta( $post_id, 'cert_intro_title', wp_kses_post( $c['intro_title'] ?? '' ) );
		update_post_meta( $post_id, 'cert_intro_lead', wp_kses_post( $c['intro_lead'] ?? '' ) );

		update_post_meta( $post_id, 'cert_01_number', sanitize_text_field( $c['c01_number'] ?? '01' ) );
		update_post_meta( $post_id, 'cert_01_header_text', sanitize_text_field( $c['c01_header'] ?? '' ) );
		update_post_meta( $post_id, 'cert_01_eyebrow', sanitize_text_field( $c['c01_eyebrow'] ?? '' ) );
		update_post_meta( $post_id, 'cert_01_title', wp_kses_post( $c['c01_title'] ?? '' ) );
		update_post_meta( $post_id, 'cert_01_lead', wp_kses_post( $c['c01_lead'] ?? '' ) );
		if ( isset( $c['c01_items_text'] ) ) {
			$lines = array_filter( array_map( 'trim', explode( "\n", $c['c01_items_text'] ) ) );
			$items = array();
			foreach ( $lines as $line ) {
				$items[] = array( 'text' => $line );
			}
			update_post_meta( $post_id, 'cert_01_items', $items );
		}

		update_post_meta( $post_id, 'cert_02_number', sanitize_text_field( $c['c02_number'] ?? '02' ) );
		update_post_meta( $post_id, 'cert_02_header_text', sanitize_text_field( $c['c02_header'] ?? '' ) );
		update_post_meta( $post_id, 'cert_02_eyebrow', sanitize_text_field( $c['c02_eyebrow'] ?? '' ) );
		update_post_meta( $post_id, 'cert_02_title', wp_kses_post( $c['c02_title'] ?? '' ) );
		update_post_meta( $post_id, 'cert_02_lead', wp_kses_post( $c['c02_lead'] ?? '' ) );

		update_post_meta( $post_id, 'cert_03_title', wp_kses_post( $c['c03_title'] ?? '' ) );
		update_post_meta( $post_id, 'cert_03_lead', wp_kses_post( $c['c03_lead'] ?? '' ) );

		update_post_meta( $post_id, 'cert_04_title', wp_kses_post( $c['c04_title'] ?? '' ) );
		update_post_meta( $post_id, 'cert_04_button_text', sanitize_text_field( $c['c04_btn_text'] ?? '' ) );
		update_post_meta( $post_id, 'cert_04_button_url', esc_url_raw( $c['c04_btn_url'] ?? '' ) );
		if ( isset( $c['c04_points_text'] ) ) {
			$p_lines = array_filter( array_map( 'trim', explode( "\n", $c['c04_points_text'] ) ) );
			$points  = array();
			foreach ( $p_lines as $pl ) {
				$points[] = array( 'text' => $pl );
			}
			update_post_meta( $post_id, 'cert_04_points', $points );
		}
	}

	// 4. Signals
	if ( isset( $_POST['ind_signals'] ) && is_array( $_POST['ind_signals'] ) ) {
		$s = $_POST['ind_signals'];
		update_post_meta( $post_id, 'signals_title', wp_kses_post( $s['title'] ?? '' ) );
		update_post_meta( $post_id, 'signals_subtitle', wp_kses_post( $s['subtitle'] ?? '' ) );
		if ( isset( $s['items'] ) && is_array( $s['items'] ) ) {
			$clean_signals = array();
			foreach ( $s['items'] as $it ) {
				$clean_signals[] = array(
					'number'       => sanitize_text_field( $it['number'] ?? '' ),
					'label'        => sanitize_text_field( $it['label'] ?? '' ),
					'icon'         => sanitize_key( $it['icon'] ?? '' ),
					'info'         => wp_kses_post( $it['info'] ?? '' ),
					'source_label' => sanitize_text_field( $it['source_label'] ?? '' ),
					'source_url'   => esc_url_raw( $it['source_url'] ?? '' ),
				);
			}
			update_post_meta( $post_id, 'signals_container', $clean_signals );
		}
	}

	// 5. How Works
	if ( isset( $_POST['ind_how_works'] ) && is_array( $_POST['ind_how_works'] ) ) {
		$hw = $_POST['ind_how_works'];
		update_post_meta( $post_id, 'how_works_preheading', sanitize_text_field( $hw['preheading'] ?? '' ) );
		update_post_meta( $post_id, 'how_works_title', wp_kses_post( $hw['title'] ?? '' ) );
		if ( isset( $hw['items'] ) && is_array( $hw['items'] ) ) {
			$clean_items = array();
			foreach ( $hw['items'] as $it ) {
				$clean_items[] = array(
					'number'          => sanitize_text_field( $it['number'] ?? '' ),
					'radar_code'      => sanitize_text_field( $it['radar_code'] ?? '' ),
					'department'      => sanitize_key( $it['radar_code'] ?? 'arr' ),
					'title'           => sanitize_text_field( $it['title'] ?? '' ),
					'description'     => sanitize_textarea_field( $it['description'] ?? '' ),
					'bullets'         => sanitize_textarea_field( $it['bullets'] ?? '' ),
					'button_label'    => 'Más info',
					'lightbox_target' => sanitize_key( $it['lightbox_target'] ?? '' ),
				);
			}
			update_post_meta( $post_id, 'how_works_items', $clean_items );
		}
	}

	// 6. Simulation
	if ( isset( $_POST['ind_sim'] ) && is_array( $_POST['ind_sim'] ) ) {
		$sim = $_POST['ind_sim'];
		update_post_meta( $post_id, 'simulation_preheading', sanitize_text_field( $sim['preheading'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_title', wp_kses_post( $sim['title'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_intro', sanitize_textarea_field( $sim['intro'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_console_status', sanitize_text_field( $sim['console_status'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_cta_label', sanitize_text_field( $sim['cta_label'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_cta_url', esc_url_raw( $sim['cta_url'] ?? '' ) );
		update_post_meta( $post_id, 'simulation_cta_lightbox', sanitize_key( $sim['cta_lightbox'] ?? '' ) );

		if ( isset( $sim['stages'] ) && is_array( $sim['stages'] ) ) {
			$clean_stages = array();
			foreach ( $sim['stages'] as $stg ) {
				$clean_stages[] = array(
					'id'        => sanitize_key( $stg['id'] ?? '' ),
					'label'     => sanitize_text_field( $stg['label'] ?? '' ),
					'title'     => sanitize_text_field( $stg['title'] ?? '' ),
					'desc'      => sanitize_textarea_field( $stg['desc'] ?? '' ),
					'image'     => esc_url_raw( $stg['image'] ?? '' ),
					'alt'       => sanitize_text_field( $stg['alt'] ?? '' ),
					'kpi'       => sanitize_text_field( $stg['kpi'] ?? '' ),
					'kpi_label' => sanitize_text_field( $stg['kpi_label'] ?? '' ),
				);
			}
			update_post_meta( $post_id, 'simulation_stages', $clean_stages );
		}
	}
}
add_action( 'save_post', 'thecrisisacademy_save_individuals_metaboxes', 10, 2 );
