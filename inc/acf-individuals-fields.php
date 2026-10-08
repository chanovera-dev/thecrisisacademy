<?php
/**
 * Register ACF/SCF Field Groups for Individuals Landing Page.
 *
 * This file contains field group registrations for all modular sections
 * of the Individuals landing page (templates/individuals.php).
 *
 * All comments and DocBlocks are in English.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF field group for Hero section in Individuals template.
 */
function thecrisisacademy_register_individuals_hero_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_hero',
			'title'                 => __( 'Hero (Particulares)', 'thecrisisacademy' ),
			'fields'                => array(
				array(
					'key'           => 'field_hero_preheading',
					'label'         => __( 'Subtítulo Superior (Pretext Reveal)', 'thecrisisacademy' ),
					'name'          => 'hero_preheading',
					'type'          => 'text',
					'instructions'  => __( 'Etiqueta superior en formato badge/sub-heading.', 'thecrisisacademy' ),
					'default_value' => 'Especialización en Comunicación para Manejo de Crisis',
				),
				array(
					'key'           => 'field_hero_title',
					'label'         => __( 'Título Principal (H1)', 'thecrisisacademy' ),
					'name'          => 'hero_title',
					'type'          => 'textarea',
					'rows'          => 3,
					'instructions'  => __( 'Título principal con animación spring bounce.', 'thecrisisacademy' ),
					'default_value' => 'Domina la gestión de crisis en la era de la IA y protege lo que más importa: tu reputación',
				),
				array(
					'key'           => 'field_hero_description',
					'label'         => __( 'Descripción / Párrafo', 'thecrisisacademy' ),
					'name'          => 'hero_description',
					'type'          => 'textarea',
					'rows'          => 3,
					'instructions'  => __( 'Párrafo explicativo debajo del título principal.', 'thecrisisacademy' ),
					'default_value' => 'Conoce cómo manejar el Framework de Respuesta Inmediata ante Crisis y Escándalos. El mismo sistema que usan empresas internacionales',
				),
				array(
					'key'           => 'field_hero_canvas_tags',
					'label'         => __( 'Palabras Clave del Fondo (#hero-canvas)', 'thecrisisacademy' ),
					'name'          => 'hero_canvas_tags',
					'type'          => 'textarea',
					'rows'          => 3,
					'instructions'  => __( 'Palabras clave separadas por comas para las partículas del canvas (ej: #Negligencia, #Escándalo, #Fraude). Dejar vacío para usar las predeterminadas.', 'thecrisisacademy' ),
					'default_value' => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_hero_acf_fields' );

/**
 * Register ACF field group for About section in Individuals template.
 */
function thecrisisacademy_register_individuals_about_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$icon_choices = function_exists( 'thecrisisacademy_get_trouble_icon_options' )
		? thecrisisacademy_get_trouble_icon_options()
		: array(
			'radar'          => 'Radar (Riesgos y Tendencias)',
			'chart'          => 'Gráfica (Parámetros y Métricas)',
			'cpu'            => 'Procesador / IA (Digital e Inteligencia Artificial)',
			'target'         => 'Diana (Estrategia)',
			'share-2'        => 'Redes Sociales (Respuesta Ágil)',
			'mic'            => 'Micrófono (Vocerías)',
			'shield-alert'   => 'Escudo Alerta (Ejercicio Inmersivo)',
			'alert-circle'   => 'Círculo de Alerta (Comunicación)',
			'alert-triangle' => 'Triángulo Alerta (Consecuencias)',
			'users'          => 'Equipo / Dirección',
			'activity'       => 'Pulso / Operaciones',
			'clock'          => 'Reloj / Tiempo',
			'monitor'        => 'Pantalla / Medios',
			'star'           => 'Estrella / Narrativa',
			'file-text'      => 'Documento / Legal',
		);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_about',
			'title'                 => __( 'Acerca de / Módulos (About)', 'thecrisisacademy' ),
			'fields'                => array(
				// Tab 01: ¿Qué hacemos?
				array(
					'key'   => 'field_about_tab_overview',
					'label' => __( '01. ¿Qué hacemos?', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_about_gallery',
					'label'         => __( 'Galería de Diapositivas (Slideshow Izquierda)', 'thecrisisacademy' ),
					'name'          => 'about_gallery',
					'type'          => 'gallery',
					'instructions'  => __( 'Imágenes que se muestran en el carrusel de la columna izquierda.', 'thecrisisacademy' ),
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'default_value' => array( 161, 162, 163 ),
				),
				array(
					'key'           => 'field_about_preheading',
					'label'         => __( 'Subtítulo Superior (Pretext Reveal)', 'thecrisisacademy' ),
					'name'          => 'about_preheading',
					'type'          => 'text',
					'default_value' => '¿Qué hacemos?',
				),
				array(
					'key'           => 'field_about_title',
					'label'         => __( 'Título de la Sección', 'thecrisisacademy' ),
					'name'          => 'about_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Entrenamos para proteger un activo crucial: la reputación',
				),
				array(
					'key'           => 'field_about_subtitle',
					'label'         => __( 'Subtítulo / Misión', 'thecrisisacademy' ),
					'name'          => 'about_subtitle',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'The Crisis Academy es una academia especializada en entrenamiento estratégico para el manejo de crisis reputacionales, comunicación de riesgos y control de narrativa.',
				),
				array(
					'key'           => 'field_about_description',
					'label'         => __( 'Descripción / Párrafo', 'thecrisisacademy' ),
					'name'          => 'about_description',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Formamos a equipos de crisis, áreas de comunicación, directivos y voceros para actuar con método, rapidez y precisión cuando más se necesita.',
				),

				// Tab 02: Módulos de Especialización
				array(
					'key'   => 'field_about_tab_modules',
					'label' => __( '02. Módulos de Especialización', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_about_modules_title',
					'label'         => __( 'Título de los Módulos (Encima del Stepper)', 'thecrisisacademy' ),
					'name'          => 'about_modules_title',
					'type'          => 'text',
					'default_value' => 'Módulos de especialización',
				),
				array(
					'key'           => 'field_about_modules',
					'label'         => __( 'Módulos de Especialización (Timeline 3D Deck)', 'thecrisisacademy' ),
					'name'          => 'about_modules',
					'type'          => 'repeater',
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Módulo', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_about_modules_rows(),
					'sub_fields'    => array(
						array(
							'key'           => 'field_about_module_icon',
							'label'         => __( 'Icono del Módulo', 'thecrisisacademy' ),
							'name'          => 'icon',
							'type'          => 'select',
							'choices'       => $icon_choices,
							'default_value' => 'radar',
						),
						array(
							'key'   => 'field_about_module_period',
							'label' => __( 'Período / Indicador (ej: 01 • Radar de Riesgos)', 'thecrisisacademy' ),
							'name'  => 'period',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_about_module_tag',
							'label' => __( 'Etiqueta / Tag (ej: Tendencias 2026)', 'thecrisisacademy' ),
							'name'  => 'tag',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_about_module_short_title',
							'label' => __( 'Título Corto (para el Stepper de la izquierda)', 'thecrisisacademy' ),
							'name'  => 'short_title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_about_module_title',
							'label' => __( 'Título Completo de la Tarjeta 3D', 'thecrisisacademy' ),
							'name'  => 'title',
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'   => 'field_about_module_desc',
							'label' => __( 'Descripción de la Tarjeta', 'thecrisisacademy' ),
							'name'  => 'description',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_about_acf_fields' );

/**
 * Get default rows for About specialization modules.
 *
 * @return array Default module rows with subfield keys and names.
 */
function thecrisisacademy_get_default_about_modules_rows() {
	return array(
		array(
			'field_about_module_icon'        => 'radar',
			'icon'                           => 'radar',
			'field_about_module_period'      => '01 • Radar de Riesgos',
			'period'                         => '01 • Radar de Riesgos',
			'field_about_module_tag'         => 'Tendencias 2026',
			'tag'                            => 'Tendencias 2026',
			'field_about_module_short_title' => 'Investigación y estudios de crisis',
			'short_title'                    => 'Investigación y estudios de crisis',
			'field_about_module_title'       => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
			'title'                          => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
			'field_about_module_desc'        => 'Análisis profundo de incidentes recientes y anticipación de escenarios de riesgo reputacional adaptados al entorno actual y a las amenazas emergentes.',
			'description'                    => 'Análisis profundo de incidentes recientes y anticipación de escenarios de riesgo reputacional adaptados al entorno actual y a las amenazas emergentes.',
		),
		array(
			'field_about_module_icon'        => 'chart',
			'icon'                           => 'chart',
			'field_about_module_period'      => '02 • Medición',
			'period'                         => '02 • Medición',
			'field_about_module_tag'         => 'Métricas & Control',
			'tag'                            => 'Métricas & Control',
			'field_about_module_short_title' => 'Herramientas y parámetros de medición',
			'short_title'                    => 'Herramientas y parámetros de medición',
			'field_about_module_title'       => 'Herramientas y parámetros de medición de una crisis y su respuesta',
			'title'                          => 'Herramientas y parámetros de medición de una crisis y su respuesta',
			'field_about_module_desc'        => 'Establecimiento de indicadores cuantitativos y cualitativos para evaluar el impacto del incidente, la velocidad de reacción y la efectividad de la respuesta.',
			'description'                    => 'Establecimiento de indicadores cuantitativos y cualitativos para evaluar el impacto del incidente, la velocidad de reacción y la efectividad de la respuesta.',
		),
		array(
			'field_about_module_icon'        => 'cpu',
			'icon'                           => 'cpu',
			'field_about_module_period'      => '03 • IA y Digital',
			'period'                         => '03 • IA y Digital',
			'field_about_module_tag'         => 'IA & Nuevos Medios',
			'tag'                            => 'IA & Nuevos Medios',
			'field_about_module_short_title' => 'Entorno mediático y digital: rol de la IA',
			'short_title'                    => 'Entorno mediático y digital: rol de la IA',
			'field_about_module_title'       => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
			'title'                          => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
			'field_about_module_desc'        => 'Evaluación de la desinformación masiva, deepfakes y uso de IA en la amplificación, análisis y monitoreo predictivo de crisis modernas.',
			'description'                    => 'Evaluación de la desinformación masiva, deepfakes y uso de IA en la amplificación, análisis y monitoreo predictivo de crisis modernas.',
		),
		array(
			'field_about_module_icon'        => 'target',
			'icon'                           => 'target',
			'field_about_module_period'      => '04 • Estrategia',
			'period'                         => '04 • Estrategia',
			'field_about_module_tag'         => 'Estrategia 4.0',
			'tag'                            => 'Estrategia 4.0',
			'field_about_module_short_title' => 'Comunicación estratégica de crisis 4.0',
			'short_title'                    => 'Comunicación estratégica de crisis 4.0',
			'field_about_module_title'       => 'Comunicación estratégica para manejo de crisis 4.0',
			'title'                          => 'Comunicación estratégica para manejo de crisis 4.0',
			'field_about_module_desc'        => 'Diseño de mensajes clave hiperdirigidos, comunicados ágiles y posicionamiento corporativo multicanal bajo situaciones de extrema presión.',
			'description'                    => 'Diseño de mensajes clave hiperdirigidos, comunicados ágiles y posicionamiento corporativo multicanal bajo situaciones de extrema presión.',
		),
		array(
			'field_about_module_icon'        => 'share-2',
			'icon'                           => 'share-2',
			'field_about_module_period'      => '05 • Respuesta Ágil',
			'period'                         => '05 • Respuesta Ágil',
			'field_about_module_tag'         => 'Redes Sociales',
			'tag'                            => 'Redes Sociales',
			'field_about_module_short_title' => 'Manejo ágil en redes sociales',
			'short_title'                    => 'Manejo ágil en redes sociales',
			'field_about_module_title'       => 'Procesos para un manejo ágil de crisis en redes sociales',
			'title'                          => 'Procesos para un manejo ágil de crisis en redes sociales',
			'field_about_module_desc'        => 'Protocolos de contención inmediata en plataformas digitales, gestión de comunidades y desaceleración de tendencias negativas virales.',
			'description'                    => 'Protocolos de contención inmediata en plataformas digitales, gestión de comunidades y desaceleración de tendencias negativas virales.',
		),
		array(
			'field_about_module_icon'        => 'mic',
			'icon'                           => 'mic',
			'field_about_module_period'      => '06 • Portavoces',
			'period'                         => '06 • Portavoces',
			'field_about_module_tag'         => 'Vocerías Oficiales',
			'tag'                            => 'Vocerías Oficiales',
			'field_about_module_short_title' => 'Control de narrativa y vocerías',
			'short_title'                    => 'Control de narrativa y vocerías',
			'field_about_module_title'       => 'Control de narrativa y arquitectura de vocerías',
			'title'                          => 'Control de narrativa y arquitectura de vocerías',
			'field_about_module_desc'        => 'Definición de portavoces oficiales, lineamientos de conducta ante la prensa y técnicas avanzadas de control del relato público.',
			'description'                    => 'Definición de portavoces oficiales, lineamientos de conducta ante la prensa y técnicas avanzadas de control del relato público.',
		),
		array(
			'field_about_module_icon'        => 'shield-alert',
			'icon'                           => 'shield-alert',
			'field_about_module_period'      => '07 • Práctica Real',
			'period'                         => '07 • Práctica Real',
			'field_about_module_tag'         => 'Ejercicio Inmersivo',
			'tag'                            => 'Ejercicio Inmersivo',
			'field_about_module_short_title' => 'Simulacro de alta intensidad',
			'short_title'                    => 'Simulacro de alta intensidad',
			'field_about_module_title'       => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis de reputación',
			'title'                          => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis de reputación',
			'field_about_module_desc'        => 'Ejercicio inmersivo en tiempo real con periodistas simulados e interacciones hostiles para auditar la resistencia y eficacia de los comités.',
			'description'                    => 'Ejercicio inmersivo en tiempo real con periodistas simulados e interacciones hostiles para auditar la resistencia y eficacia de los comités.',
		),
	);
}

/**
 * Get default rows for Signals repeater container.
 *
 * @return array Default signal rows with subfield keys and names.
 */
function thecrisisacademy_get_default_signals_rows() {
	return array(
		array(
			'field_signals_item_number'       => '75',
			'signal_item_number'              => '75',
			'field_signals_item_label'        => '',
			'signal_item_label'               => '',
			'field_signals_item_info'         => '<p>de las crisis mostraron señales previas <strong>que nadie detectó</strong></p>',
			'signal_item_info'                => '<p>de las crisis mostraron señales previas <strong>que nadie detectó</strong></p>',
			'field_signals_item_icon'         => null,
			'signal_item_icon'                => null,
			'field_signals_item_source_label' => 'Institute for Crisis Management (ICM)',
			'signal_item_source_label'        => 'Institute for Crisis Management (ICM)',
			'field_signals_item_source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
			'signal_item_source_url'          => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
		array(
			'field_signals_item_number'       => '11',
			'signal_item_number'              => '11',
			'field_signals_item_label'        => '',
			'signal_item_label'               => '',
			'field_signals_item_info'         => '<p>de pérdida del valor del mercado en <strong>solo 5 días por una mala respuesta</strong></p>',
			'signal_item_info'                => '<p>de pérdida del valor del mercado en <strong>solo 5 días por una mala respuesta</strong></p>',
			'field_signals_item_icon'         => null,
			'signal_item_icon'                => null,
			'field_signals_item_source_label' => 'PwC + Oxford Metrica',
			'signal_item_source_label'        => 'PwC + Oxford Metrica',
			'field_signals_item_source_url'   => 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/',
			'signal_item_source_url'          => 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/',
		),
		array(
			'field_signals_item_number'       => '',
			'signal_item_number'              => '',
			'field_signals_item_label'        => 'Hoy una crisis puede escalar en minutos',
			'signal_item_label'               => 'Hoy una crisis puede escalar en minutos',
			'field_signals_item_info'         => '<p>por IA, redes sociales y desinformación</p>',
			'signal_item_info'                => '<p>por IA, redes sociales y desinformación</p>',
			'field_signals_item_icon'         => null,
			'signal_item_icon'                => null,
			'field_signals_item_source_label' => 'Institute for Crisis Management (ICM)',
			'signal_item_source_label'        => 'Institute for Crisis Management (ICM)',
			'field_signals_item_source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
			'signal_item_source_url'          => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
	);
}

/**
 * Register ACF field group for Signals section in Individuals template.
 */
function thecrisisacademy_register_individuals_signals_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_signals',
			'title'                 => __( 'Señales de Alerta (Signals)', 'thecrisisacademy' ),
			'fields'                => array(
				array(
					'key'           => 'field_signals_title',
					'label'         => __( 'Título de la Sección', 'thecrisisacademy' ),
					'name'          => 'signals_title',
					'type'          => 'wysiwyg',
					'instructions'  => __( 'Título principal con animación reveal.', 'thecrisisacademy' ),
					'default_value' => '<h2 class="title-section title-reveal">La mayoría de las crisis <strong>sí dieron señales</strong> antes de explotar</h2>',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 0,
					'delay'         => 0,
				),
				array(
					'key'           => 'field_signals_container',
					'label'         => __( 'Indicadores / Señales', 'thecrisisacademy' ),
					'name'          => 'signals_container',
					'type'          => 'repeater',
					'instructions'  => __( 'Añade los indicadores estadísticos y señales de alerta temprana.', 'thecrisisacademy' ),
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Señal', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_signals_rows(),
					'sub_fields'    => array(
						array(
							'key'          => 'field_signals_item_number',
							'label'        => __( 'Número / Porcentaje', 'thecrisisacademy' ),
							'name'         => 'signal_item_number',
							'type'         => 'text',
							'instructions' => __( 'Valor numérico para el contador animado (ej: 75 o 11). Dejar vacío si es una señal cualitativa.', 'thecrisisacademy' ),
						),
						array(
							'key'          => 'field_signals_item_label',
							'label'        => __( 'Etiqueta de la Señal', 'thecrisisacademy' ),
							'name'         => 'signal_item_label',
							'type'         => 'text',
							'instructions' => __( 'Texto destacado (ej: "Hoy una crisis puede escalar en minutos").', 'thecrisisacademy' ),
						),
						array(
							'key'          => 'field_signals_item_info',
							'label'        => __( 'Descripción / Información', 'thecrisisacademy' ),
							'name'         => 'signal_item_info',
							'type'         => 'wysiwyg',
							'instructions' => __( 'Detalle explicativo de la estadística o señal.', 'thecrisisacademy' ),
							'tabs'         => 'all',
							'toolbar'      => 'basic',
							'media_upload' => 0,
							'delay'        => 0,
						),
						array(
							'key'           => 'field_signals_item_icon',
							'label'         => __( 'Icono Opcional', 'thecrisisacademy' ),
							'name'          => 'signal_item_icon',
							'type'          => 'image',
							'instructions'  => __( 'Icono central para el radar en señales sin porcentaje.', 'thecrisisacademy' ),
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						array(
							'key'          => 'field_signals_item_source_label',
							'label'        => __( 'Etiqueta de la Fuente (opcional)', 'thecrisisacademy' ),
							'name'         => 'signal_item_source_label',
							'type'         => 'text',
							'instructions' => __( 'Nombre de la institución o estudio (ej: "Institute for Crisis Management (ICM)" o "PwC + Oxford Metrica").', 'thecrisisacademy' ),
						),
						array(
							'key'          => 'field_signals_item_source_url',
							'label'        => __( 'Enlace de la Fuente (opcional)', 'thecrisisacademy' ),
							'name'         => 'signal_item_source_url',
							'type'         => 'url',
							'instructions' => __( 'URL directa del estudio o reporte de referencia.', 'thecrisisacademy' ),
						),
					),
				),
				array(
					'key'           => 'field_signals_subtitle',
					'label'         => __( 'Subtítulo / Conclusión', 'thecrisisacademy' ),
					'name'          => 'signals_subtitle',
					'type'          => 'wysiwyg',
					'instructions'  => __( 'Frase de cierre o conclusión al pie de la sección.', 'thecrisisacademy' ),
					'default_value' => '<h3 class="subtitle-section">Improvisar frente a una crisis no es un error operativo, es <strong>negligencia reputacional.</strong></h3>',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 0,
					'delay'         => 0,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 4,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_signals_acf_fields' );

/**
 * Default getter functions for certification repeaters.
 */
function thecrisisacademy_get_default_cert_01_items_rows() {
	return array(
		array( 'field_cert_01_item_text' => 'Pérdida de tiempo crítico', 'text' => 'Pérdida de tiempo crítico' ),
		array( 'field_cert_01_item_text' => 'Respuestas improvisadas', 'text' => 'Respuestas improvisadas' ),
		array( 'field_cert_01_item_text' => 'Daño reputacional', 'text' => 'Daño reputacional' ),
		array( 'field_cert_01_item_text' => 'Mensajes contradictorios', 'text' => 'Mensajes contradictorios' ),
	);
}

function thecrisisacademy_get_default_cert_02_steps_rows() {
	return array(
		array(
			'field_cert_02_step_title' => 'Diagnóstico',
			'title'                    => 'Diagnóstico',
			'field_cert_02_step_desc'  => 'Detectamos las necesidades de la institución y definimos objetivos.',
			'description'              => 'Detectamos las necesidades de la institución y definimos objetivos.',
		),
		array(
			'field_cert_02_step_title' => '6 módulos especializados',
			'title'                    => '6 módulos especializados',
			'field_cert_02_step_desc'  => 'Contenido actualizado, casos reales y tendencias.',
			'description'              => 'Contenido actualizado, casos reales y tendencias.',
		),
		array(
			'field_cert_02_step_title' => 'Simulación de crisis',
			'title'                    => 'Simulación de crisis',
			'field_cert_02_step_desc'  => 'Escenarios de alta intensidad en War Room.',
			'description'              => 'Escenarios de alta intensidad en War Room.',
		),
		array(
			'field_cert_02_step_title' => 'Evaluación y ScoreCard',
			'title'                    => 'Evaluación y ScoreCard',
			'field_cert_02_step_desc'  => 'Medición del desempeño con KPIs: URR, MPR y TTR.',
			'description'              => 'Medición del desempeño con KPIs: URR, MPR y TTR.',
		),
		array(
			'field_cert_02_step_title' => 'Certificación',
			'title'                    => 'Certificación',
			'field_cert_02_step_desc'  => 'Demuestra tu aprendizaje y recibe tu certificación profesional.',
			'description'              => 'Demuestra tu aprendizaje y recibe tu certificación profesional.',
		),
	);
}

function thecrisisacademy_get_default_cert_03_formats_rows() {
	return array(
		array(
			'field_cert_03_format_icon'  => 'online',
			'icon'                       => 'online',
			'field_cert_03_format_title' => 'En línea',
			'title'                      => 'En línea',
			'field_cert_03_format_desc'  => 'Cúrsalo en tiempo real.',
			'description'                => 'Cúrsalo en tiempo real.',
		),
		array(
			'field_cert_03_format_icon'  => 'presencial',
			'icon'                       => 'presencial',
			'field_cert_03_format_title' => 'Presencial',
			'title'                      => 'Presencial',
			'field_cert_03_format_desc'  => 'También disponible en formato presencial intensivo.',
			'description'                => 'También disponible en formato presencial intensivo.',
		),
	);
}

function thecrisisacademy_get_default_cert_04_points_rows() {
	return array(
		array( 'field_cert_04_point_text' => 'Grupos reducidos garantizados', 'text' => 'Grupos reducidos garantizados' ),
		array( 'field_cert_04_point_text' => 'Avalado internacionalmente', 'text' => 'Avalado internacionalmente' ),
		array( 'field_cert_04_point_text' => 'Instructores expertos en activo', 'text' => 'Instructores expertos en activo' ),
	);
}

/**
 * Register ACF field group for Certification section in Individuals template.
 */
function thecrisisacademy_register_individuals_certification_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_certification',
			'title'                 => __( 'Certificación / Ruta (Certification)', 'thecrisisacademy' ),
			'fields'                => array(
				// Tab 00: Introducción
				array(
					'key'   => 'field_cert_tab_intro',
					'label' => __( '00. Introducción', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_cert_intro_subheading',
					'label'         => __( 'Subtítulo Superior (Intro)', 'thecrisisacademy' ),
					'name'          => 'cert_intro_subheading',
					'type'          => 'text',
					'default_value' => 'Entrenamiento especializado',
				),
				array(
					'key'           => 'field_cert_intro_title',
					'label'         => __( 'Título de la Sección (Intro)', 'thecrisisacademy' ),
					'name'          => 'cert_intro_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'La ruta definitiva para convertir a tu equipo en expertos en gestión de crisis',
				),
				array(
					'key'           => 'field_cert_intro_lead',
					'label'         => __( 'Texto Lead / Descripción (Intro)', 'thecrisisacademy' ),
					'name'          => 'cert_intro_lead',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Cada crisis sin protocolo cuesta reputación, clientes y tiempo que nunca recuperarás.',
				),

				// Tab 01: El momento crítico
				array(
					'key'   => 'field_cert_tab_01',
					'label' => __( '01. Momento Crítico', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_cert_01_number',
					'label'         => __( 'Número del Bloque', 'thecrisisacademy' ),
					'name'          => 'cert_01_number',
					'type'          => 'text',
					'default_value' => '01',
				),
				array(
					'key'           => 'field_cert_01_header_text',
					'label'         => __( 'Texto del Encabezado', 'thecrisisacademy' ),
					'name'          => 'cert_01_header_text',
					'type'          => 'text',
					'default_value' => 'El momento crítico',
				),
				array(
					'key'           => 'field_cert_01_eyebrow',
					'label'         => __( 'Etiqueta Superior (Eyebrow)', 'thecrisisacademy' ),
					'name'          => 'cert_01_eyebrow',
					'type'          => 'text',
					'default_value' => 'Cuando todo cambia',
				),
				array(
					'key'           => 'field_cert_01_title',
					'label'         => __( 'Título', 'thecrisisacademy' ),
					'name'          => 'cert_01_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'En una crisis, cada decisión cuenta.',
				),
				array(
					'key'           => 'field_cert_01_lead',
					'label'         => __( 'Texto Lead', 'thecrisisacademy' ),
					'name'          => 'cert_01_lead',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Sin preparación, el tiempo se pierde, las respuestas se improvisan y la comunicación se fragmenta.',
				),
				array(
					'key'           => 'field_cert_01_items',
					'label'         => __( 'Consecuencias Críticas', 'thecrisisacademy' ),
					'name'          => 'cert_01_items',
					'type'          => 'repeater',
					'layout'        => 'table',
					'button_label'  => __( 'Añadir Consecuencia', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_cert_01_items_rows(),
					'sub_fields'    => array(
						array(
							'key'   => 'field_cert_01_item_text',
							'label' => __( 'Texto del Punto', 'thecrisisacademy' ),
							'name'  => 'text',
							'type'  => 'text',
						),
					),
				),

				// Tab 02: La preparación
				array(
					'key'   => 'field_cert_tab_02',
					'label' => __( '02. Preparación', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_cert_02_number',
					'label'         => __( 'Número del Bloque', 'thecrisisacademy' ),
					'name'          => 'cert_02_number',
					'type'          => 'text',
					'default_value' => '02',
				),
				array(
					'key'           => 'field_cert_02_header_text',
					'label'         => __( 'Texto del Encabezado', 'thecrisisacademy' ),
					'name'          => 'cert_02_header_text',
					'type'          => 'text',
					'default_value' => 'La preparación',
				),
				array(
					'key'           => 'field_cert_02_eyebrow',
					'label'         => __( 'Etiqueta Superior (Eyebrow)', 'thecrisisacademy' ),
					'name'          => 'cert_02_eyebrow',
					'type'          => 'text',
					'default_value' => 'Tu proceso de certificación',
				),
				array(
					'key'           => 'field_cert_02_title',
					'label'         => __( 'Título', 'thecrisisacademy' ),
					'name'          => 'cert_02_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'La respuesta no se improvisa. Se entrena.',
				),
				array(
					'key'           => 'field_cert_02_lead',
					'label'         => __( 'Texto Lead', 'thecrisisacademy' ),
					'name'          => 'cert_02_lead',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Una ruta práctica para pasar del diagnóstico a la acción y medir cómo responde el equipo.',
				),
				array(
					'key'           => 'field_cert_02_steps',
					'label'         => __( 'Pasos de la Ruta / Proceso', 'thecrisisacademy' ),
					'name'          => 'cert_02_steps',
					'type'          => 'repeater',
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Paso', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_cert_02_steps_rows(),
					'sub_fields'    => array(
						array(
							'key'   => 'field_cert_02_step_title',
							'label' => __( 'Título del Paso', 'thecrisisacademy' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_cert_02_step_desc',
							'label' => __( 'Descripción del Paso', 'thecrisisacademy' ),
							'name'  => 'description',
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),

				// Tab 03: El formato
				array(
					'key'   => 'field_cert_tab_03',
					'label' => __( '03. Formato', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_cert_03_number',
					'label'         => __( 'Número del Bloque', 'thecrisisacademy' ),
					'name'          => 'cert_03_number',
					'type'          => 'text',
					'default_value' => '03',
				),
				array(
					'key'           => 'field_cert_03_header_text',
					'label'         => __( 'Texto del Encabezado', 'thecrisisacademy' ),
					'name'          => 'cert_03_header_text',
					'type'          => 'text',
					'default_value' => 'El formato',
				),
				array(
					'key'           => 'field_cert_03_eyebrow',
					'label'         => __( 'Etiqueta Superior (Eyebrow)', 'thecrisisacademy' ),
					'name'          => 'cert_03_eyebrow',
					'type'          => 'text',
					'default_value' => 'Una ruta a tu medida',
				),
				array(
					'key'           => 'field_cert_03_title',
					'label'         => __( 'Título', 'thecrisisacademy' ),
					'name'          => 'cert_03_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Aprende como mejor funciona para ti.',
				),
				array(
					'key'           => 'field_cert_03_lead',
					'label'         => __( 'Texto Lead', 'thecrisisacademy' ),
					'name'          => 'cert_03_lead',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Cursa los módulos de manera individual según tus necesidades o completa la ruta para obtener una Constancia Oficial.',
				),
				array(
					'key'           => 'field_cert_03_formats',
					'label'         => __( 'Tarjetas de Formato', 'thecrisisacademy' ),
					'name'          => 'cert_03_formats',
					'type'          => 'repeater',
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Formato', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_cert_03_formats_rows(),
					'sub_fields'    => array(
						array(
							'key'           => 'field_cert_03_format_icon',
							'label'         => __( 'Icono del Formato', 'thecrisisacademy' ),
							'name'          => 'icon',
							'type'          => 'select',
							'choices'       => array(
								'online'     => __( 'En línea (Monitor / Pantalla)', 'thecrisisacademy' ),
								'presencial' => __( 'Presencial (Personas / Aula)', 'thecrisisacademy' ),
							),
							'default_value' => 'online',
						),
						array(
							'key'   => 'field_cert_03_format_title',
							'label' => __( 'Título del Formato', 'thecrisisacademy' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_cert_03_format_desc',
							'label' => __( 'Descripción del Formato', 'thecrisisacademy' ),
							'name'  => 'description',
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),

				// Tab 04: Cierre / Certificado
				array(
					'key'   => 'field_cert_tab_04',
					'label' => __( '04. Cierre', 'thecrisisacademy' ),
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_cert_04_number',
					'label'         => __( 'Número del Bloque', 'thecrisisacademy' ),
					'name'          => 'cert_04_number',
					'type'          => 'text',
					'default_value' => '04',
				),
				array(
					'key'           => 'field_cert_04_header_text',
					'label'         => __( 'Texto del Encabezado', 'thecrisisacademy' ),
					'name'          => 'cert_04_header_text',
					'type'          => 'text',
					'default_value' => 'El siguiente capítulo',
				),
				array(
					'key'           => 'field_cert_04_title',
					'label'         => __( 'Título', 'thecrisisacademy' ),
					'name'          => 'cert_04_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Obtén tu Certificado de Especialización en Comunicación de Crisis.',
				),
				array(
					'key'           => 'field_cert_04_points',
					'label'         => __( 'Puntos Clave (.points-slideshow)', 'thecrisisacademy' ),
					'name'          => 'cert_04_points',
					'type'          => 'repeater',
					'layout'        => 'table',
					'button_label'  => __( 'Añadir Punto Clave', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_cert_04_points_rows(),
					'sub_fields'    => array(
						array(
							'key'   => 'field_cert_04_point_text',
							'label' => __( 'Texto del Punto', 'thecrisisacademy' ),
							'name'  => 'text',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'           => 'field_cert_04_button_text',
					'label'         => __( 'Texto del Botón', 'thecrisisacademy' ),
					'name'          => 'cert_04_button_text',
					'type'          => 'text',
					'default_value' => 'Inscribirme ahora',
				),
				array(
					'key'           => 'field_cert_04_button_url',
					'label'         => __( 'Enlace del Botón', 'thecrisisacademy' ),
					'name'          => 'cert_04_button_url',
					'type'          => 'text',
					'default_value' => '#cta',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 3,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_certification_acf_fields' );

/**
 * Get default rows for How Works accordion solutions.
 *
 * @return array Default rows for How Works repeater.
 */
function thecrisisacademy_get_default_how_works_items_rows() {
	return array(
		array(
			'field_how_works_item_dept'         => 'arr',
			'department'                        => 'arr',
			'field_how_works_item_radar_code'   => 'ARR',
			'radar_code'                        => 'ARR',
			'field_how_works_item_number'       => '01',
			'number'                            => '01',
			'field_how_works_item_title'        => 'Auditoría de Riesgos Reputacionales',
			'title'                             => 'Auditoría de Riesgos Reputacionales',
			'field_how_works_item_desc'         => 'Análisis de vulnerabilidades en tus planes y protocolos de comunicación',
			'description'                       => 'Análisis de vulnerabilidades en tus planes y protocolos de comunicación',
			'field_how_works_item_bullets'      => "Revisión de escenarios\nMatriz de riesgos intangibles\nRecomendaciones de mejora",
			'bullets'                           => "Revisión de escenarios\nMatriz de riesgos intangibles\nRecomendaciones de mejora",
			'field_how_works_item_button_label' => 'Más info',
			'button_label'                      => 'Más info',
			'field_how_works_item_lb_target'    => 'how-works-arr',
			'lightbox_target'                   => 'how-works-arr',
		),
		array(
			'field_how_works_item_dept'         => 'mpc',
			'department'                        => 'mpc',
			'field_how_works_item_radar_code'   => 'MPC',
			'radar_code'                        => 'MPC',
			'field_how_works_item_number'       => '02',
			'number'                            => '02',
			'field_how_works_item_title'        => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'title'                             => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'field_how_works_item_desc'         => 'Instrumentos tácticos y actualizados para los primeros 60 minutos hasta 12 horas',
			'description'                       => 'Instrumentos tácticos y actualizados para los primeros 60 minutos hasta 12 horas',
			'field_how_works_item_bullets'      => "Playbooks tácticos\nGuía de respuesta inmediata\nManual de riesgo reputacional",
			'bullets'                           => "Playbooks tácticos\nGuía de respuesta inmediata\nManual de riesgo reputacional",
			'field_how_works_item_button_label' => 'Más info',
			'button_label'                      => 'Más info',
			'field_how_works_item_lb_target'    => 'how-works-mpc',
			'lightbox_target'                   => 'how-works-mpc',
		),
		array(
			'field_how_works_item_dept'         => 'pi6m',
			'department'                        => 'pi6m',
			'field_how_works_item_radar_code'   => 'FEC',
			'radar_code'                        => 'FEC',
			'field_how_works_item_number'       => '03',
			'number'                            => '03',
			'field_how_works_item_title'        => 'Formación de Especialistas y Comités',
			'title'                             => 'Formación de Especialistas y Comités',
			'field_how_works_item_desc'         => 'Programa integral de 6 módulos, simulación de alta intensidad profesional.',
			'description'                       => 'Programa integral de 6 módulos, simulación de alta intensidad profesional.',
			'field_how_works_item_bullets'      => "Simulación y War Room\nEvaluación y ScoreCard",
			'bullets'                           => "Simulación y War Room\nEvaluación y ScoreCard",
			'field_how_works_item_button_label' => 'Ver módulos',
			'button_label'                      => 'Ver módulos',
			'field_how_works_item_lb_target'    => 'how-works-pi6m',
			'lightbox_target'                   => 'how-works-pi6m',
		),
	);
}

/**
 * Register ACF field group for How Works section in Individuals template.
 */
function thecrisisacademy_register_individuals_how_works_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_how_works',
			'title'                 => __( 'Cómo Funciona (How Works)', 'thecrisisacademy' ),
			'fields'                => array(
				array(
					'key'           => 'field_how_works_preheading',
					'label'         => __( 'Subtítulo Superior (Pretext)', 'thecrisisacademy' ),
					'name'          => 'how_works_preheading',
					'type'          => 'text',
					'default_value' => '¿Cómo funciona?',
				),
				array(
					'key'           => 'field_how_works_title',
					'label'         => __( 'Título Principal', 'thecrisisacademy' ),
					'name'          => 'how_works_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Tres soluciones para fortalecer tu preparación ante una crisis',
				),
				array(
					'key'           => 'field_how_works_items',
					'label'         => __( 'Soluciones / Acordeón Interactivo', 'thecrisisacademy' ),
					'name'          => 'how_works_items',
					'type'          => 'repeater',
					'instructions'  => __( 'Soluciones que se muestran en el radar y en el acordeón interactivo (.accordion-interactive-list).', 'thecrisisacademy' ),
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Solución', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_how_works_items_rows(),
					'sub_fields'    => array(
						array(
							'key'          => 'field_how_works_item_dept',
							'label'        => __( 'Identificador / Slug (ej: arr, mpc, pi6m)', 'thecrisisacademy' ),
							'name'         => 'department',
							'type'         => 'text',
							'instructions' => __( 'Identificador único usado para enlazar el radar y el lightbox.', 'thecrisisacademy' ),
						),
						array(
							'key'   => 'field_how_works_item_radar_code',
							'label' => __( 'Siglas en Radar (ej: ARR, MPC, FEC)', 'thecrisisacademy' ),
							'name'  => 'radar_code',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_how_works_item_number',
							'label' => __( 'Número (ej: 01, 02, 03)', 'thecrisisacademy' ),
							'name'  => 'number',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_how_works_item_title',
							'label' => __( 'Título de la Solución', 'thecrisisacademy' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_how_works_item_desc',
							'label' => __( 'Descripción', 'thecrisisacademy' ),
							'name'  => 'description',
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'          => 'field_how_works_item_bullets',
							'label'        => __( 'Puntos Clave / Viñetas (un punto por línea)', 'thecrisisacademy' ),
							'name'         => 'bullets',
							'type'         => 'textarea',
							'rows'         => 3,
							'instructions' => __( 'Escribe un punto clave por línea. Se renderizarán como lista de viñetas.', 'thecrisisacademy' ),
						),
						array(
							'key'           => 'field_how_works_item_button_label',
							'label'         => __( 'Texto del Botón', 'thecrisisacademy' ),
							'name'          => 'button_label',
							'type'          => 'text',
							'default_value' => 'Más info',
						),
						array(
							'key'          => 'field_how_works_item_lb_target',
							'label'        => __( 'ID del Lightbox Pane (ej: how-works-arr)', 'thecrisisacademy' ),
							'name'         => 'lightbox_target',
							'type'         => 'text',
							'instructions' => __( 'Debe coincidir con el ID del panel en Lightbox Panes.', 'thecrisisacademy' ),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 5,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_how_works_acf_fields' );

/**
 * Get default rows for How Works lightbox panes.
 *
 * @return array Default rows for How Works panes repeater.
 */
function thecrisisacademy_get_default_how_works_panes_rows() {
	return array(
		array(
			'field_how_works_pane_id'          => 'how-works-arr',
			'pane_id'                          => 'how-works-arr',
			'field_how_works_pane_title'       => 'Auditoría de Riesgos Reputacionales',
			'pane_title'                       => 'Auditoría de Riesgos Reputacionales',
			'field_how_works_pane_art_num'     => '01 · ARR',
			'article_number'                   => '01 · ARR',
			'field_how_works_pane_art_title'   => 'Auditoría de Riesgos Reputacionales',
			'article_title'                    => 'Auditoría de Riesgos Reputacionales',
			'field_how_works_pane_art_content' => '<p>El nuevo entorno de comunicación obliga a actuar de manera inmediata para proteger un activo intangible tan valioso como la reputación ante el impacto de una crisis. Esa respuesta de comunicación estratégica, sin embargo, no se encuentra reflejada en ningún manual o está desfasada.</p><p>Los planes de continuidad de negocio o los manuales de respuesta operativa consideran una variedad de escenarios y probabilidades de ocurrencia. Pero ignoran algunos riesgos que están afectando a los negocios: percepciones erróneas, declaraciones inadecuadas, rumores o acusaciones. Y, sobre todo, no consideran los escándalos o crisis en redes sociales.</p><p>Por ello, diseñamos el servicio de auditoría de riesgos reputacionales. Este análisis de vulnerabilidades en los planes de continuidad de negocio revisa la matriz de riesgo, los escenarios intangibles y las herramientas de respuesta. Se cotejan contra las mejores prácticas y se plantean recomendaciones de mejora que pueden implementarse inmediatamente.</p><p class="lightbox-article-highlight">¿Quieres saber si tu Manual de Crisis, sus escenarios y respuestas están actualizados?</p>',
			'article_content'                  => '<p>El nuevo entorno de comunicación obliga a actuar de manera inmediata para proteger un activo intangible tan valioso como la reputación ante el impacto de una crisis. Esa respuesta de comunicación estratégica, sin embargo, no se encuentra reflejada en ningún manual o está desfasada.</p><p>Los planes de continuidad de negocio o los manuales de respuesta operativa consideran una variedad de escenarios y probabilidades de ocurrencia. Pero ignoran algunos riesgos que están afectando a los negocios: percepciones erróneas, declaraciones inadecuadas, rumores o acusaciones. Y, sobre todo, no consideran los escándalos o crisis en redes sociales.</p><p>Por ello, diseñamos el servicio de auditoría de riesgos reputacionales. Este análisis de vulnerabilidades en los planes de continuidad de negocio revisa la matriz de riesgo, los escenarios intangibles y las herramientas de respuesta. Se cotejan contra las mejores prácticas y se plantean recomendaciones de mejora que pueden implementarse inmediatamente.</p><p class="lightbox-article-highlight">¿Quieres saber si tu Manual de Crisis, sus escenarios y respuestas están actualizados?</p>',
		),
		array(
			'field_how_works_pane_id'          => 'how-works-mpc',
			'pane_id'                          => 'how-works-mpc',
			'field_how_works_pane_title'       => 'Manuales y Playbooks',
			'pane_title'                       => 'Manuales y Playbooks',
			'field_how_works_pane_art_num'     => '02 · MPC',
			'article_number'                   => '02 · MPC',
			'field_how_works_pane_art_title'   => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'article_title'                    => 'Manuales y Playbooks de Comunicación para Manejo de Crisis',
			'field_how_works_pane_art_content' => '<p>Si un manual de crisis no se ha actualizado en los últimos 12 meses, es un documento muerto. La dinámica del entorno informativo, los nuevos riesgos, los ataques sintéticos (creados, alimentados o manipulados por la IA) obligan a adaptar con frecuencia las estrategias de mitigación de impacto. Para resolver este problema, hemos creado guías de acción táctica para los primeros 60 minutos hasta 12 horas, que es el periodo clave en que debe atenderse una crisis con la máxima precisión.</p><p>A partir de la redacción de más de 100 guías y manuales de crisis y la asesoría y acompañamiento a empresas e instituciones para atender sus emergencias, hemos diseñado instrumentos pensando en la experiencia del usuario.</p><p><strong>Playbooks tácticos:</strong> Para una respuesta efectiva a incidentes críticos, es fundamental tener en un documento simple y ágil las acciones de comunicación, herramientas y tiempos límite. Cada playbook se adapta a la industria o sector y se presenta a manera de una app (o web) responsiva.</p><p><strong>Guía de Respuesta Inmediata:</strong> Una guía diseñada específicamente para que aún en una emergencia se garantice la coherencia con el actuar previo de la institución, sus políticas de comunicación y el apego a su propósito y valores.</p><p><strong>Manual de Riesgo Reputacional:</strong> El papel de este documento es compilar los escenarios de máximo riesgo reputacional, incluir acciones para la detección oportuna de crisis latentes y asegurarse de documentar aquellas experiencias que se hayan enfrentado. Es la herramienta que ayuda a la institución a que los incidentes se conviertan en aprendizaje y continuidad. Se prepara absolutamente a medida de cada empresa.</p>',
			'article_content'                  => '<p>Si un manual de crisis no se ha actualizado en los últimos 12 meses, es un documento muerto. La dinámica del entorno informativo, los nuevos riesgos, los ataques sintéticos (creados, alimentados o manipulados por la IA) obligan a adaptar con frecuencia las estrategias de mitigación de impacto. Para resolver este problema, hemos creado guías de acción táctica para los primeros 60 minutos hasta 12 horas, que es el periodo clave en que debe atenderse una crisis con la máxima precisión.</p><p>A partir de la redacción de más de 100 guías y manuales de crisis y la asesoría y acompañamiento a empresas e instituciones para atender sus emergencias, hemos diseñado instrumentos pensando en la experiencia del usuario.</p><p><strong>Playbooks tácticos:</strong> Para una respuesta efectiva a incidentes críticos, es fundamental tener en un documento simple y ágil las acciones de comunicación, herramientas y tiempos límite. Cada playbook se adapta a la industria o sector y se presenta a manera de una app (o web) responsiva.</p><p><strong>Guía de Respuesta Inmediata:</strong> Una guía diseñada específicamente para que aún en una emergencia se garantice la coherencia con el actuar previo de la institución, sus políticas de comunicación y el apego a su propósito y valores.</p><p><strong>Manual de Riesgo Reputacional:</strong> El papel de este documento es compilar los escenarios de máximo riesgo reputacional, incluir acciones para la detección oportuna de crisis latentes y asegurarse de documentar aquellas experiencias que se hayan enfrentado. Es la herramienta que ayuda a la institución a que los incidentes se conviertan en aprendizaje y continuidad. Se prepara absolutamente a medida de cada empresa.</p>',
		),
		array(
			'field_how_works_pane_id'          => 'how-works-pi6m',
			'pane_id'                          => 'how-works-pi6m',
			'field_how_works_pane_title'       => 'Formación de Especialistas y Comités',
			'pane_title'                       => 'Formación de Especialistas y Comités',
			'field_how_works_pane_art_num'     => '03 · FEC',
			'article_number'                   => '03 · FEC',
			'field_how_works_pane_art_title'   => 'Formación de Especialistas y Comités',
			'article_title'                    => 'Formación de Especialistas y Comités',
			'field_how_works_pane_art_content' => '<h3>Módulos</h3><ol class="lightbox-article-modules"><li>Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales</li><li>Herramientas y parámetros de medición de una crisis y su respuesta</li><li>Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial</li><li>Comunicación estratégica para manejo de crisis 4.0</li><li>Procesos para un manejo ágil de crisis en redes sociales</li><li>Control de narrativa y arquitectura de vocerías</li></ol><p>Los módulos de formación pueden cursarse de manera individual. La Certificación se obtiene al cursar los 6 módulos, la simulación y demostrar el aprendizaje adquirido a través de una evaluación rigurosa.</p><p>Para las personas responsables de dar entrevistas o declaraciones en situaciones de crisis o escándalos, recomendamos el taller de Técnicas de Vocería para Manejo de Crisis.</p><p>Diseñamos una capacitación que abarca tanto los nuevos riesgos en la era de la inteligencia artificial generativa y las fake news como los escenarios más frecuentes. Analizamos estudios de caso y proporcionamos información actualizada sobre tendencias. Compartimos los protocolos y herramientas que han sido más efectivas para atender la demanda de información que se genera en una crisis. Y nos basamos en la investigación de los líderes como Ian Mitroff, Paul Benoit.</p><p><strong>Simulación de crisis de alta intensidad y war room.</strong> Dentro de la formación y certificación se incluye una experiencia donde el participante se enfrenta a escenarios reales de alta intensidad (fraude financiero, desastre ambiental, ciberdelitos, afectación a consumidores o comunidades) con inputs secuenciados (videos, audios, llamadas).</p><p><strong>Scorecard de Manejo de Crisis:</strong> Análisis del desempeño y toma de decisiones basado en KPIs: URR (Uso de Radar de Riesgos), MPR (Manejo de Protocolo de Respuesta), TTR (Time to Respond).</p>',
			'article_content'                  => '<h3>Módulos</h3><ol class="lightbox-article-modules"><li>Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales</li><li>Herramientas y parámetros de medición de una crisis y su respuesta</li><li>Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial</li><li>Comunicación estratégica para manejo de crisis 4.0</li><li>Procesos para un manejo ágil de crisis en redes sociales</li><li>Control de narrativa y arquitectura de vocerías</li></ol><p>Los módulos de formación pueden cursarse de manera individual. La Certificación se obtiene al cursar los 6 módulos, la simulación y demostrar el aprendizaje adquirido a través de una evaluación rigurosa.</p><p>Para las personas responsables de dar entrevistas o declaraciones en situaciones de crisis o escándalos, recomendamos el taller de Técnicas de Vocería para Manejo de Crisis.</p><p>Diseñamos una capacitación que abarca tanto los nuevos riesgos en la era de la inteligencia artificial generativa y las fake news como los escenarios más frecuentes. Analizamos estudios de caso y proporcionamos información actualizada sobre tendencias. Compartimos los protocolos y herramientas que han sido más efectivas para atender la demanda de información que se genera en una crisis. Y nos basamos en la investigación de los líderes como Ian Mitroff, Paul Benoit.</p><p><strong>Simulación de crisis de alta intensidad y war room.</strong> Dentro de la formación y certificación se incluye una experiencia donde el participante se enfrenta a escenarios reales de alta intensidad (fraude financiero, desastre ambiental, ciberdelitos, afectación a consumidores o comunidades) con inputs secuenciados (videos, audios, llamadas).</p><p><strong>Scorecard de Manejo de Crisis:</strong> Análisis del desempeño y toma de decisiones basado en KPIs: URR (Uso de Radar de Riesgos), MPR (Manejo de Protocolo de Respuesta), TTR (Time to Respond).</p>',
		),
	);
}

/**
 * Register ACF field group for How Works Lightbox Panes in Individuals template.
 */
function thecrisisacademy_register_individuals_how_works_panes_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_how_works_panes',
			'title'                 => __( 'Contenido Lightbox (How Works Panes)', 'thecrisisacademy' ),
			'fields'                => array(
				array(
					'key'           => 'field_how_works_panes',
					'label'         => __( 'Paneles Informativos del Lightbox', 'thecrisisacademy' ),
					'name'          => 'how_works_panes',
					'type'          => 'repeater',
					'instructions'  => __( 'Cada fila corresponde a un panel que se despliega en el modal lightbox corporativo al pulsar "Más info" o "Ver módulos".', 'thecrisisacademy' ),
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Panel', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_how_works_panes_rows(),
					'sub_fields'    => array(
						array(
							'key'          => 'field_how_works_pane_id',
							'label'        => __( 'Identificador del Panel (ej: how-works-arr, how-works-mpc, how-works-pi6m)', 'thecrisisacademy' ),
							'name'         => 'pane_id',
							'type'         => 'text',
							'instructions' => __( 'Coincide con data-open-lightbox del botón.', 'thecrisisacademy' ),
						),
						array(
							'key'   => 'field_how_works_pane_title',
							'label' => __( 'Título en la barra superior del modal (data-title)', 'thecrisisacademy' ),
							'name'  => 'pane_title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_how_works_pane_art_num',
							'label' => __( 'Insignia / Número (ej: 01 · ARR, 02 · MPC, 03 · FEC)', 'thecrisisacademy' ),
							'name'  => 'article_number',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_how_works_pane_art_title',
							'label' => __( 'Título del Artículo (H2)', 'thecrisisacademy' ),
							'name'  => 'article_title',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_how_works_pane_art_content',
							'label'        => __( 'Contenido Detallado del Artículo', 'thecrisisacademy' ),
							'name'         => 'article_content',
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'full',
							'media_upload' => 1,
							'delay'        => 0,
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 6,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_how_works_panes_acf_fields' );

/**
 * Get default rows for Simulation stages repeater.
 *
 * @return array Default rows for Simulation stages.
 */
function thecrisisacademy_get_default_simulation_stages_rows() {
	return array(
		array(
			'field_sim_stage_id'        => 'radar',
			'id'                        => 'radar',
			'field_sim_stage_label'     => 'Radar de riesgos',
			'label'                     => 'Radar de riesgos',
			'field_sim_stage_title'     => 'Detecta la crisis antes de que estalle',
			'title'                     => 'Detecta la crisis antes de que estalle',
			'field_sim_stage_desc'      => 'Monitorea señales débiles, menciones y alertas tempranas para clasificar el nivel de amenaza en tiempo real.',
			'desc'                      => 'Monitorea señales débiles, menciones y alertas tempranas para clasificar el nivel de amenaza en tiempo real.',
			'field_sim_stage_image'     => content_url( '/uploads/2026/05/radar.webp' ),
			'image'                     => content_url( '/uploads/2026/05/radar.webp' ),
			'field_sim_stage_alt'       => 'Radar de riesgos del simulador de crisis',
			'alt'                       => 'Radar de riesgos del simulador de crisis',
			'field_sim_stage_kpi'       => 'URR',
			'kpi'                       => 'URR',
			'field_sim_stage_kpi_label' => 'Uso de Radar de Riesgos',
			'kpi_label'                 => 'Uso de Radar de Riesgos',
		),
		array(
			'field_sim_stage_id'        => 'stakeholders',
			'id'                        => 'stakeholders',
			'field_sim_stage_label'     => 'Mapa de stakeholders',
			'label'                     => 'Mapa de stakeholders',
			'field_sim_stage_title'     => 'Prioriza a quién hablarle primero',
			'title'                     => 'Prioriza a quién hablarle primero',
			'field_sim_stage_desc'      => 'Identifica a las audiencias críticas, su nivel de influencia y el mensaje que cada una necesita escuchar.',
			'desc'                      => 'Identifica a las audiencias críticas, su nivel de influencia y el mensaje que cada una necesita escuchar.',
			'field_sim_stage_image'     => content_url( '/uploads/2026/05/stakeholders-map.webp' ),
			'image'                     => content_url( '/uploads/2026/05/stakeholders-map.webp' ),
			'field_sim_stage_alt'       => 'Mapa de stakeholders del simulador de crisis',
			'alt'                       => 'Mapa de stakeholders del simulador de crisis',
			'field_sim_stage_kpi'       => 'MPR',
			'kpi'                       => 'MPR',
			'field_sim_stage_kpi_label' => 'Manejo de Protocolo de Respuesta',
			'kpi_label'                 => 'Manejo de Protocolo de Respuesta',
		),
		array(
			'field_sim_stage_id'        => 'war-room',
			'id'                        => 'war-room',
			'field_sim_stage_label'     => 'War Room',
			'label'                     => 'War Room',
			'field_sim_stage_title'     => 'Decide bajo presión real',
			'title'                     => 'Decide bajo presión real',
			'field_sim_stage_desc'      => 'Inputs secuenciados —videos, audios y llamadas— ponen a prueba la coordinación y la velocidad de tu comité.',
			'desc'                      => 'Inputs secuenciados —videos, audios y llamadas— ponen a prueba la coordinación y la velocidad de tu comité.',
			'field_sim_stage_image'     => content_url( '/uploads/2026/05/war-room-1.webp' ),
			'image'                     => content_url( '/uploads/2026/05/war-room-1.webp' ),
			'field_sim_stage_alt'       => 'War Room del simulador de crisis',
			'alt'                       => 'War Room del simulador de crisis',
			'field_sim_stage_kpi'       => 'TTR',
			'kpi'                       => 'TTR',
			'field_sim_stage_kpi_label' => 'Time to Respond',
			'kpi_label'                 => 'Time to Respond',
		),
	);
}

/**
 * Register ACF field group for Simulation section in Individuals template.
 */
function thecrisisacademy_register_individuals_simulation_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_individuals_simulation',
			'title'                 => __( 'Simulador de Crisis (Simulation)', 'thecrisisacademy' ),
			'fields'                => array(
				array(
					'key'           => 'field_simulation_preheading',
					'label'         => __( 'Subtítulo Superior (Pretext Reveal)', 'thecrisisacademy' ),
					'name'          => 'simulation_preheading',
					'type'          => 'text',
					'default_value' => 'Simulador de crisis',
				),
				array(
					'key'           => 'field_simulation_title',
					'label'         => __( 'Título Principal', 'thecrisisacademy' ),
					'name'          => 'simulation_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Experimenta la presión en tiempo real y descubre si tu equipo está preparado',
				),
				array(
					'key'           => 'field_simulation_intro',
					'label'         => __( 'Descripción / Introducción', 'thecrisisacademy' ),
					'name'          => 'simulation_intro',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Tres etapas, un mismo reloj. Recorre el ciclo completo de una crisis y mide cómo responde tu equipo cuando cada minuto cuenta.',
				),
				array(
					'key'           => 'field_simulation_console_status',
					'label'         => __( 'Estado en Barra de Consola', 'thecrisisacademy' ),
					'name'          => 'simulation_console_status',
					'type'          => 'text',
					'default_value' => 'Simulación en vivo',
				),
				array(
					'key'           => 'field_simulation_cta_url',
					'label'         => __( 'URL del Botón CTA', 'thecrisisacademy' ),
					'name'          => 'simulation_cta_url',
					'type'          => 'text',
					'instructions'  => __( 'Enlace de destino del botón Simular crisis (ej: /simulador-de-crisis/).', 'thecrisisacademy' ),
					'default_value' => '/simulador-de-crisis/',
				),
				array(
					'key'           => 'field_simulation_cta_label',
					'label'         => __( 'Texto del Botón CTA', 'thecrisisacademy' ),
					'name'          => 'simulation_cta_label',
					'type'          => 'text',
					'default_value' => 'Simular crisis',
				),
				array(
					'key'           => 'field_simulation_cta_lightbox',
					'label'         => __( 'Identificador Lightbox (data-open-lightbox)', 'thecrisisacademy' ),
					'name'          => 'simulation_cta_lightbox',
					'type'          => 'text',
					'instructions'  => __( 'Identificador del modal lightbox que se abre al pulsar el botón.', 'thecrisisacademy' ),
					'default_value' => 'crisis-simulator',
				),
				array(
					'key'           => 'field_simulation_stages',
					'label'         => __( 'Etapas del Simulador (Stages)', 'thecrisisacademy' ),
					'name'          => 'simulation_stages',
					'type'          => 'repeater',
					'instructions'  => __( 'Pestañas interactivas y pantallas de la consola del simulador.', 'thecrisisacademy' ),
					'layout'        => 'block',
					'button_label'  => __( 'Añadir Etapa', 'thecrisisacademy' ),
					'default_value' => thecrisisacademy_get_default_simulation_stages_rows(),
					'sub_fields'    => array(
						array(
							'key'          => 'field_sim_stage_id',
							'label'        => __( 'Identificador / Slug (ej: radar, stakeholders, war-room)', 'thecrisisacademy' ),
							'name'         => 'id',
							'type'         => 'text',
							'instructions' => __( 'Identificador único usado para vincular la pestaña con el panel de la consola.', 'thecrisisacademy' ),
						),
						array(
							'key'   => 'field_sim_stage_label',
							'label' => __( 'Etiqueta de la Pestaña', 'thecrisisacademy' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sim_stage_title',
							'label' => __( 'Título de la Etapa', 'thecrisisacademy' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sim_stage_desc',
							'label' => __( 'Descripción', 'thecrisisacademy' ),
							'name'  => 'desc',
							'type'  => 'textarea',
							'rows'  => 2,
						),
						array(
							'key'           => 'field_sim_stage_image',
							'label'         => __( 'Captura / Imagen de la Consola', 'thecrisisacademy' ),
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'library'       => 'all',
						),
						array(
							'key'   => 'field_sim_stage_alt',
							'label' => __( 'Texto Alternativo de la Imagen (Alt)', 'thecrisisacademy' ),
							'name'  => 'alt',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_sim_stage_kpi',
							'label'        => __( 'Sigla del KPI (ej: URR, MPR, TTR)', 'thecrisisacademy' ),
							'name'         => 'kpi',
							'type'         => 'text',
							'instructions' => __( 'Acrónimo que se muestra en el visor HUD sobre la pantalla.', 'thecrisisacademy' ),
						),
						array(
							'key'          => 'field_sim_stage_kpi_label',
							'label'        => __( 'Nombre del KPI (ej: Uso de Radar de Riesgos)', 'thecrisisacademy' ),
							'name'         => 'kpi_label',
							'type'         => 'text',
							'instructions' => __( 'Descripción completa del indicador en el HUD.', 'thecrisisacademy' ),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/individuals.php',
					),
				),
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/crisisacademy-homepage.php',
					),
				),
			),
			'menu_order'            => 7,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'hide_on_screen'        => array(
				'the_content',
			),
		)
	);
}
add_action( 'acf/init', 'thecrisisacademy_register_individuals_simulation_acf_fields' );

/**
 * Render predefined SVG icon for Certification formats
 *
 * @param string $icon_key Icon identifier (online, presencial)
 * @return string SVG markup
 */
function thecrisisacademy_get_certification_format_icon( $icon_key = 'online' ) {
	if ( 'presencial' === $icon_key ) {
		return '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
	}
	return '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>';
}

/**
 * Filter ACF repeater and gallery values to provide default rows when unpopulated.
 */
function thecrisisacademy_acf_load_default_value( $value, $post_id, $field ) {
	if ( ! empty( $value ) ) {
		return $value;
	}

	$field_name = $field['name'] ?? '';

	switch ( $field_name ) {
		case 'about_modules':
			return thecrisisacademy_get_default_about_modules_rows();
		case 'about_gallery':
			return array( 161, 162, 163 );
		case 'cert_01_items':
			return thecrisisacademy_get_default_cert_01_items_rows();
		case 'cert_02_steps':
			return thecrisisacademy_get_default_cert_02_steps_rows();
		case 'cert_03_formats':
			return thecrisisacademy_get_default_cert_03_formats_rows();
		case 'cert_04_points':
			return thecrisisacademy_get_default_cert_04_points_rows();
		case 'signals_container':
			return thecrisisacademy_get_default_signals_rows();
		case 'how_works_items':
			return thecrisisacademy_get_default_how_works_items_rows();
		case 'how_works_panes':
			return thecrisisacademy_get_default_how_works_panes_rows();
		case 'simulation_stages':
			return thecrisisacademy_get_default_simulation_stages_rows();
	}

	return $value;
}
add_filter( 'acf/load_value/name=about_modules', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_about_modules', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=about_gallery', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_about_gallery', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=signals_container', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_signals_container', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=cert_01_items', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_cert_01_items', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=cert_02_steps', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_cert_02_steps', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=cert_03_formats', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_cert_03_formats', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=cert_04_points', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_cert_04_points', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=how_works_items', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_how_works_items', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=how_works_panes', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_how_works_panes', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/name=simulation_stages', 'thecrisisacademy_acf_load_default_value', 10, 3 );
add_filter( 'acf/load_value/key=field_simulation_stages', 'thecrisisacademy_acf_load_default_value', 10, 3 );


