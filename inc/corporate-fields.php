<?php
/**
 * Corporate Custom Field Groups (Native ACF-alternative)
 *
 * Implements native WordPress meta boxes and repeaters without ACF plugin dependency.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper to get the primary Corporate page ID.
 *
 * @return int Page ID or 0.
 */
function thecrisisacademy_get_corporate_page_id() {
	static $corp_id = null;
	if ( null !== $corp_id ) {
		return $corp_id;
	}

	$page = get_page_by_path( 'corporativo', OBJECT, 'page' );
	if ( ! $page ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'templates/corporate.php',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$corp_id = ! empty( $query->posts ) ? (int) $query->posts[0] : 0;
	} else {
		$corp_id = (int) $page->ID;
	}

	return $corp_id;
}

/**
 * Get all landing page IDs that share corporate sections (Corporate and Individuals).
 *
 * @return array Array of post IDs.
 */
function thecrisisacademy_get_shared_landing_page_ids() {
	$page_ids = array();
	$corp_page = get_page_by_path( 'corporativo', OBJECT, 'page' );
	if ( $corp_page ) {
		$page_ids[] = (int) $corp_page->ID;
	}
	$indiv_page = get_page_by_path( 'particulares', OBJECT, 'page' );
	if ( $indiv_page ) {
		$page_ids[] = (int) $indiv_page->ID;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => '_wp_page_template',
					'value'   => array( 'templates/corporate.php', 'templates/individuals.php' ),
					'compare' => 'IN',
				),
			),
		)
	);

	if ( ! empty( $query->posts ) ) {
		foreach ( $query->posts as $pid ) {
			$pid = (int) $pid;
			if ( ! in_array( $pid, $page_ids, true ) ) {
				$page_ids[] = $pid;
			}
		}
	}

	return $page_ids;
}

/**
 * Synchronize shared corporate section meta keys across Corporate and Individuals landing pages.
 *
 * @param int $source_post_id Post ID that was saved.
 */
function thecrisisacademy_sync_shared_corporate_meta( $source_post_id ) {
	static $syncing = false;
	if ( $syncing ) {
		return;
	}
	$syncing = true;

	$shared_keys = array(
		// CTA
		'_corporate_cta_preheading',
		'_corporate_cta_title',
		'_corporate_cta_description',
		'_corporate_cta_whatsapp_phone',
		'_corporate_cta_form_name_label',
		'_corporate_cta_form_email_label',
		'_corporate_cta_form_phone_label',
		'_corporate_cta_form_msg_label',
		'_corporate_cta_button_text',
		'_corporate_cta_microcopy_text',
		'_corporate_cta_success_title',
		'_corporate_cta_success_desc',
		'_corporate_cta_reset_btn_text',
		'_corporate_cta_points',
		// Upcoming Events
		'_corporate_upcoming_events_preheading',
		'_corporate_upcoming_events_title',
		// News
		'_corporate_news_preheading',
		'_corporate_news_title',
		'_corporate_news_description',
		'_corporate_news_posts_per_page',
		'_corporate_news_orderby',
		'_corporate_news_order',
		'_corporate_news_show_filters',
		'_corporate_news_show_canvas',
		'_corporate_news_show_button',
		'_corporate_news_filter_all_label',
		'_corporate_news_button_text',
		'_corporate_news_button_url',
		'_corporate_news_button_target',
		// FAQ
		'_corporate_faq_preheading',
		'_corporate_faq_title',
		'_corporate_faq_cta_title',
		'_corporate_faq_cta_desc',
		'_corporate_faq_cta_btn_text',
		'_corporate_faq_cta_btn_url',
		'_corporate_faq_cta_btn_target',
		'_corporate_faq_posts_per_page',
		'_corporate_faq_orderby',
		'_corporate_faq_order',
		'_corporate_faq_first_open',
	);

	$target_ids = thecrisisacademy_get_shared_landing_page_ids();

	foreach ( $target_ids as $target_id ) {
		if ( (int) $target_id === (int) $source_post_id ) {
			continue;
		}

		foreach ( $shared_keys as $key ) {
			$val = get_post_meta( $source_post_id, $key, true );
			if ( '' !== $val && false !== $val ) {
				update_post_meta( $target_id, $key, $val );
			}
		}
	}

	$syncing = false;
}

/**
 * Get default fallback data for Hero section
 *
 * @return array
 */
function thecrisisacademy_get_hero_defaults() {
	return array(
		'preheading'         => 'Comunicación y Manejo de Crisis • Programa In-Company',
		'title'              => 'Tu empresa tiene 60 minutos. ¿Está preparada para responder?',
		'points'             => array(
			'Una crisis mal manejada no destruye solo la reputación, destruye las metas del negocio.',
			'En los primeros 60 minutos una crisis se contiene... o se sale de control. Después solo se administra el daño.',
		),
		'data_block_content' => "<h3>Las cifras lo demuestran.</h3>\n<p>Las empresas sin preparación pierden más del <strong>11% de su valor de mercado</strong> en los primeros días de una crisis.</p>\n<p>Y las organizaciones que responden durante la primera hora reducen significativamente el daño reputacional frente a quienes reaccionan tarde.</p>",
		'source_label'       => 'PwC + Oxford Metrica',
		'source_url'         => 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/',
		'canvas_tags'        => array(
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
			'#CaídaDeReputación', '#EscándaloFiscal',
		),
	);
}

/**
 * Retrieve sanitized Hero data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_hero_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_hero_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading  = get_post_meta( $post_id, '_corporate_hero_preheading', true );
	$title       = get_post_meta( $post_id, '_corporate_hero_title', true );
	$points      = get_post_meta( $post_id, '_corporate_hero_points', true );
	$content     = get_post_meta( $post_id, '_corporate_hero_data_block_content', true );
	$source_lbl  = get_post_meta( $post_id, '_corporate_hero_source_label', true );
	$source_url  = get_post_meta( $post_id, '_corporate_hero_source_url', true );
	$canvas_tags = get_post_meta( $post_id, '_corporate_hero_canvas_tags', true );

	return array(
		'preheading'         => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'              => '' !== $title && false !== $title ? $title : $defaults['title'],
		'points'             => is_array( $points ) && ! empty( $points ) ? $points : $defaults['points'],
		'data_block_content' => '' !== $content && false !== $content ? $content : $defaults['data_block_content'],
		'source_label'       => '' !== $source_lbl && false !== $source_lbl ? $source_lbl : $defaults['source_label'],
		'source_url'         => '' !== $source_url && false !== $source_url ? $source_url : $defaults['source_url'],
		'canvas_tags'        => is_array( $canvas_tags ) && ! empty( $canvas_tags ) ? $canvas_tags : $defaults['canvas_tags'],
	);
}

/**
 * Render predefined SVG icon for Trouble timeline
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @param float  $stroke   Stroke width
 * @return string
 */
function thecrisisacademy_get_trouble_icon_svg( $icon_key, $width = 20, $height = 20, $stroke = 2 ) {
	$paths = array(
		'alert-circle'   => '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
		'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
		'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
		'activity'       => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>',
		'clock'          => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
		'monitor'        => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>',
		'star'           => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
		'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
		'radar'          => '<path d="M19.07 4.93A10 10 0 0 0 4.93 19.07"></path><path d="M16.24 7.76A6 6 0 0 0 7.76 16.24"></path><path d="M12 12l4-4"></path><circle cx="12" cy="12" r="2"></circle>',
		'chart'          => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
		'gauge'          => '<path d="m12 14 4-4"></path><path d="M3.34 19a10 10 0 1 1 17.32 0"></path>',
		'cpu'            => '<rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line>',
		'target'         => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
		'share-2'        => '<circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>',
		'mic'            => '<path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" x2="12" y1="19" y2="22"></line>',
		'shield-alert'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
		'flame'          => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>',
	);

	$inner = $paths[ $icon_key ] ?? $paths['alert-circle'];
	return sprintf(
		'<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		(int) $width,
		(int) $height,
		esc_attr( (string) $stroke ),
		$inner
	);
}

/**
 * Get available predefined icons for Trouble timeline
 *
 * @return array
 */
function thecrisisacademy_get_trouble_icon_options() {
	return array(
		'alert-circle'   => 'Círculo de Alerta (Comunicación)',
		'users'          => 'Equipo / Dirección',
		'file-text'      => 'Documento / Legal',
		'activity'       => 'Pulso / Operaciones',
		'clock'          => 'Reloj / Tiempo',
		'monitor'        => 'Pantalla / Medios',
		'star'           => 'Estrella / Narrativa',
		'alert-triangle' => 'Triángulo Alerta (Consecuencias)',
		'radar'          => 'Radar (Riesgos y Tendencias)',
		'chart'          => 'Gráfica (Parámetros y Métricas)',
		'gauge'          => 'Medidor (Impacto y Respuesta)',
		'cpu'            => 'Procesador / IA (Digital e Inteligencia Artificial)',
		'target'         => 'Diana / Estrategia (Comunicación 4.0)',
		'share-2'        => 'Red / Nodos (Redes Sociales y Viralidad)',
		'mic'            => 'Micrófono (Vocerías y Portavoces)',
		'shield-alert'   => 'Escudo de Alerta (Simulacro y Resistencia)',
		'flame'          => 'Fuego / Alta Intensidad (Simulacro Hostil)',
	);
}

/**
 * Get default fallback data for Trouble section
 *
 * @return array
 */
function thecrisisacademy_get_trouble_defaults() {
	return array(
		'preheading' => 'El problema',
		'title'      => "Muchas empresas descubren que sus protocolos son del siglo pasado... <br><em>cuando ya es demasiado tarde.</em>",
		'steps'      => array(
			array(
				'icon'        => 'alert-circle',
				'department'  => 'Comunicación',
				'tag'         => 'Parálisis Inicial',
				'tag_class'   => 'alert-tag',
				'title'       => 'Espera instrucciones que nunca llegan',
				'description' => 'El equipo de comunicación externa e interna está atado de manos. Sin una vocería designada ni autorización para emitir un <em>holding statement</em> inmediato, la orden de facto es esperar. Cada minuto de silencio oficial es interpretado como indiferencia, descontrol o admisión de culpa.',
				'points'      => array(
					'Vocerías bloqueadas por falta de protocolos de autorización rápida.',
					'La prensa y clientes llenan el vacío de información con conjeturas.',
					'Pérdida irreversible de la iniciativa en los primeros 15 minutos.',
				),
			),
			array(
				'icon'        => 'users',
				'department'  => 'Dirección',
				'tag'         => 'Niebla de Mando',
				'tag_class'   => 'alert-tag',
				'title'       => 'Requiere información verificada en medio del caos',
				'description' => 'La alta gerencia convoca comités de urgencia a puerta cerrada exigiendo reportes 100% corroborados antes de pronunciarse. En una crisis real, la certeza técnica absoluta tarda horas o días. Esta parálisis por análisis desconecta a los líderes de la velocidad del mundo exterior.',
				'points'      => array(
					'Búsqueda infructuosa de certezas técnicas en la primera hora.',
					'Falta de roles entrenados para operar bajo estrés extremo.',
					'La dirección actúa de espaldas a la velocidad de las redes y medios.',
				),
			),
			array(
				'icon'        => 'file-text',
				'department'  => 'Legal',
				'tag'         => 'Blindaje Inútil',
				'tag_class'   => 'alert-tag',
				'title'       => 'Quiere revisar cada coma antes de cualquier acción',
				'description' => 'El departamento legal prioriza blindar a la empresa ante futuras contingencias regulatorias y demandas, congelando comunicados y vetando disculpas o empatía con los afectados. Al intentar ganar un juicio futuro en tribunales, pierden el juicio presente de la opinión pública.',
				'points'      => array(
					'Borradores impersonales y fríos que dilatan horas la respuesta.',
					'Miedo a admitir hechos básicos, interpretado como soberbia.',
					'Incompatibilidad entre el tiempo procesal y el tiempo mediático.',
				),
			),
			array(
				'icon'        => 'activity',
				'department'  => 'Operaciones',
				'tag'         => 'Desconexión Táctica',
				'tag_class'   => 'alert-tag',
				'title'       => 'Ya tomó decisiones sin consultar a nadie',
				'description' => 'Bajo la presión del incidente en el terreno (planta, sucursal o centro de datos), los mandos operativos improvisan medidas para solucionar el fallo técnico y responden a testigos o autoridades. Al no haber un canal unificado, la respuesta local choca frontalmente con la postura corporativa.',
				'points'      => array(
					'Acciones sobre la marcha sin coordinación con el comité directivo.',
					'Filtraciones de audios, videos o testimonios de empleados.',
					'Contradicciones públicas entre la realidad de campo y los comunicados.',
				),
			),
			array(
				'icon'        => 'clock',
				'department'  => 'El resto del mundo',
				'tag'         => 'Velocidad Digital',
				'tag_class'   => 'alert-tag',
				'title'       => 'Mientras tanto... el reloj no se detiene',
				'description' => 'El ecosistema digital no espera a que termine la junta directiva ni a que los abogados den luz verde. En las redes e internet, un vacío de información oficial es llenado en minutos por rumores, quejas de usuarios enfurecidos y opiniones de competidores oportunistas.',
				'points'      => array(
					'Crecimiento viral exponencial de la conversación cada 15 minutos.',
					'Algoritmos que premian el conflicto y la indignación popular.',
					'Los grupos de interés construyen su propio veredicto sin la empresa.',
				),
			),
			array(
				'icon'        => 'monitor',
				'department'  => 'Medios',
				'tag'         => 'Cobertura Hostil',
				'tag_class'   => 'alert-tag',
				'title'       => 'Ya comenzaron a publicar su versión',
				'description' => 'Los medios de comunicación y portales del sector lanzan alertas informativas basándose en fuentes no contrastadas o testimonios indignados. La frase más destructiva para la reputación de una empresa se instala como titular: <em>"La compañía fue consultada pero rechazó emitir declaraciones"</em>.',
				'points'      => array(
					'Titulares armados con versiones de terceros sin contrapeso.',
					'El silencio corporativo convertido en la prueba de culpabilidad.',
					'Pérdida del derecho de réplica en el momento de mayor audiencia.',
				),
			),
			array(
				'icon'        => 'star',
				'department'  => 'Narrativa',
				'tag'         => 'Pérdida del Relato',
				'tag_class'   => 'alert-tag',
				'title'       => 'Ya cambió y ya no les pertenece',
				'description' => 'El debate social ya no trata de entender qué incidente ocurrió, sino de cuestionar por qué la empresa intentó ocultarlo o actuó con negligencia. A partir de este punto, cualquier comunicado suena a justificación forzada: la marca pasa de ser vista como víctima a ser considerada culpable.',
				'points'      => array(
					'El marco ético y la credibilidad quedan secuestrados por terceros.',
					'Efectividad de comunicados tardíos reducida a menos del 20%.',
					'Inversionistas y clientes corporativos comienzan a exigir explicaciones.',
				),
			),
			array(
				'icon'        => 'alert-triangle',
				'department'  => 'Consecuencias',
				'tag'         => 'Crisis Mayor',
				'tag_class'   => 'critical-tag',
				'title'       => 'Una situación manejable se vuelve crisis mayor',
				'description' => 'Lo que inició como una incidencia operativa controlable termina en fuga de clientes estratégicos, desplome del valor de mercado, renuncia de directivos y costosas investigaciones regulatorias. Todo por protocolos obsoletos que no supieron responder en los primeros 60 minutos.',
				'points'      => array(
					'Pérdida superior al 11% del valor de mercado en los primeros días.',
					'Coste de reparación y demandas 100x mayor a la preparación.',
					'Daño duradero en la confianza de clientes, empleados y socios.',
				),
			),
		),
	);
}

/**
 * Retrieve sanitized Trouble data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_trouble_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_trouble_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading = get_post_meta( $post_id, '_corporate_trouble_preheading', true );
	$title      = get_post_meta( $post_id, '_corporate_trouble_title', true );
	$steps      = get_post_meta( $post_id, '_corporate_trouble_steps', true );

	return array(
		'preheading' => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'      => '' !== $title && false !== $title ? $title : $defaults['title'],
		'steps'      => is_array( $steps ) && ! empty( $steps ) ? $steps : $defaults['steps'],
	);
}

/**
 * Get default fallback data for Hearings section
 *
 * @return array
 */
function thecrisisacademy_get_hearings_defaults() {
	return array(
		'preheading' => 'Impacto 360°',
		'title'      => 'Una crisis de reputación no es solo un asunto de comunicación.',
		'items'      => array(
			array(
				'slug'        => 'comms',
				'radar_label' => 'COMMS',
				'title'       => 'COMMS / PR',
				'badge'       => 'Primera Línea',
				'description' => 'Tu área será la primera en responder y la más expuesta ante los medios y la opinión pública. Necesita protocolos de contención inmediata en los primeros minutos.',
			),
			array(
				'slug'        => 'hr',
				'radar_label' => 'RH',
				'title'       => 'RH / D.O.',
				'badge'       => 'Cultura',
				'description' => 'Una crisis pone a prueba la cultura, la comunicación interna y la percepción de los colaboradores. Mantener la alineación y la calma del equipo humano evita filtraciones internas dañinas.',
			),
			array(
				'slug'        => 'csuite',
				'radar_label' => 'C-SUITE',
				'title'       => 'C-SUITE / CEO / CFO',
				'badge'       => 'Estrategia',
				'description' => 'Las decisiones tomadas bajo presión afectarán la continuidad, el valor y el futuro completo del negocio. El comité directivo debe actuar con un plan unificado y claro.',
			),
			array(
				'slug'        => 'ops',
				'radar_label' => 'OPS',
				'title'       => 'OPERACIONES & SEGURIDAD',
				'badge'       => 'Continuidad',
				'description' => 'La crisis operativa y la reputacional ocurren juntas. La respuesta técnica en el campo define la gravedad del impacto y la velocidad de la recuperación.',
			),
		),
	);
}

/**
 * Retrieve sanitized Hearings data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_hearings_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_hearings_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading = get_post_meta( $post_id, '_corporate_hearings_preheading', true );
	$title      = get_post_meta( $post_id, '_corporate_hearings_title', true );
	$items      = get_post_meta( $post_id, '_corporate_hearings_items', true );

	return array(
		'preheading' => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'      => '' !== $title && false !== $title ? $title : $defaults['title'],
		'items'      => is_array( $items ) && ! empty( $items ) ? $items : $defaults['items'],
	);
}

/**
 * Render predefined SVG icon for Founder methodology
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @return string
 */
function thecrisisacademy_get_founder_icon_svg( $icon_key, $width = 22, $height = 22 ) {
	$paths = array(
		'book'   => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5z"></path>',
		'layers' => '<rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line>',
		'chart'  => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
		'award'  => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
		'cpu'    => '<rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line>',
	);

	$inner = $paths[ $icon_key ] ?? $paths['book'];
	return sprintf(
		'<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		(int) $width,
		(int) $height,
		$inner
	);
}

/**
 * Get available predefined icons for Founder methodology
 *
 * @return array
 */
function thecrisisacademy_get_founder_icon_options() {
	return array(
		'book'   => 'Libro (Investigación Académica)',
		'layers' => 'Capas / Servidores (Simulación Inmersiva)',
		'chart'  => 'Gráfica de Barras (Métricas y KPIs)',
		'award'  => 'Medalla (Excelencia y Acreditación)',
		'cpu'    => 'Chip / Procesador (Tecnología y Datos)',
	);
}

/**
 * Get default fallback data for Founder section
 *
 * @return array
 */
function thecrisisacademy_get_founder_defaults() {
	return array(
		'photo_url'          => 'https://thecrisisacademy.com/wp-content/themes/crisisacademy/assets/img/carolina-eslava.webp',
		'photo_alt'          => 'Carolina Eslava - Fundadora',
		'preheading'         => 'Liderazgo Académico',
		'name'               => 'Carolina Eslava',
		'role'               => 'Fundadora & Directora de The Crisis Academy',
		'quote'              => '25 años formando y preparando comités de crisis en multinacionales frente a escenarios de alta complejidad operativa y mediática.',
		'methodology_title'  => 'Metodología Basada en Investigación Científica',
		'methodology_items'  => array(
			array(
				'icon'        => 'book',
				'title'       => 'Ian Mitroff, Timothy Coombs & William Benoit',
				'description' => 'Modelos teóricos consolidados de contención de crisis y estrategias de restauración de imagen corporativa.',
			),
			array(
				'icon'        => 'layers',
				'title'       => 'Learning Sciences & Simulación',
				'description' => 'Práctica inmersiva activa de toma de decisiones bajo presión y fatiga cognitiva acelerada.',
			),
			array(
				'icon'        => 'chart',
				'title'       => 'Métricas y KPIs Cualitativos',
				'description' => 'Práctica inmersiva activa de toma de decisiones bajo presión y fatiga cognitiva acelerada.',
			),
		),
		'stat_number'        => '+2,000',
		'stat_label'         => 'Ejecutivos entrenados bajo simulación de crisis activa en toda la región.',
	);
}

/**
 * Retrieve sanitized Founder data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_founder_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_founder_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$photo_url         = get_post_meta( $post_id, '_corporate_founder_photo_url', true );
	$photo_alt         = get_post_meta( $post_id, '_corporate_founder_photo_alt', true );
	$preheading        = get_post_meta( $post_id, '_corporate_founder_preheading', true );
	$name              = get_post_meta( $post_id, '_corporate_founder_name', true );
	$role              = get_post_meta( $post_id, '_corporate_founder_role', true );
	$quote             = get_post_meta( $post_id, '_corporate_founder_quote', true );
	$methodology_title = get_post_meta( $post_id, '_corporate_founder_methodology_title', true );
	$methodology_items = get_post_meta( $post_id, '_corporate_founder_methodology_items', true );
	$stat_number       = get_post_meta( $post_id, '_corporate_founder_stat_number', true );
	$stat_label        = get_post_meta( $post_id, '_corporate_founder_stat_label', true );

	return array(
		'photo_url'         => '' !== $photo_url && false !== $photo_url ? $photo_url : $defaults['photo_url'],
		'photo_alt'         => '' !== $photo_alt && false !== $photo_alt ? $photo_alt : $defaults['photo_alt'],
		'preheading'        => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'name'              => '' !== $name && false !== $name ? $name : $defaults['name'],
		'role'              => '' !== $role && false !== $role ? $role : $defaults['role'],
		'quote'             => '' !== $quote && false !== $quote ? $quote : $defaults['quote'],
		'methodology_title' => '' !== $methodology_title && false !== $methodology_title ? $methodology_title : $defaults['methodology_title'],
		'methodology_items' => is_array( $methodology_items ) && ! empty( $methodology_items ) ? $methodology_items : $defaults['methodology_items'],
		'stat_number'       => '' !== $stat_number && false !== $stat_number ? $stat_number : $defaults['stat_number'],
		'stat_label'        => '' !== $stat_label && false !== $stat_label ? $stat_label : $defaults['stat_label'],
	);
}

/**
 * Render predefined SVG icon for Program phases
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @param float  $stroke   Stroke width
 * @return string
 */
function thecrisisacademy_get_program_icon_svg( $icon_key, $width = 48, $height = 48, $stroke = 1.8 ) {
	$paths = array(
		'radar'          => '<circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path><path d="M12 6a6 6 0 0 1 6 6"></path>',
		'layers'         => '<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline>',
		'grid'           => '<rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line>',
		'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
		'message-circle' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>',
		'mic'            => '<path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v1a7 7 0 0 1-14 0v-1"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line>',
		'activity'       => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>',
		'shield'         => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
		'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
		'trending-up'    => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline>',
	);

	$inner = isset( $paths[ $icon_key ] ) ? $paths[ $icon_key ] : $paths['radar'];

	return sprintf(
		'<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%.1f" stroke-linecap="round" stroke-linejoin="round">%s</svg>',
		absint( $width ),
		absint( $height ),
		floatval( $stroke ),
		$inner
	);
}

/**
 * Get icon options for Program phases
 *
 * @return array
 */
function thecrisisacademy_get_program_icon_options() {
	return array(
		'radar'          => 'Radar / Círculos Concéntricos (Investigación)',
		'layers'         => 'Capas Superpuestas (Métricas y Parámetros)',
		'grid'           => 'Matriz / Chip (IA & Entorno Digital)',
		'file-text'      => 'Documento Estratégico (Estrategia y Mensajes)',
		'message-circle' => 'Burbuja de Diálogo (Redes Sociales)',
		'mic'            => 'Micrófono (Vocerías y Declaraciones)',
		'activity'       => 'Pulso / Electro (Simulacro y Acción)',
		'shield'         => 'Escudo (Protección y Blindaje)',
		'users'          => 'Equipo / Personas (Comité de Crisis)',
		'trending-up'    => 'Tendencia Alcista (Recuperación)',
	);
}

/**
 * Get default fallback data for Program section
 *
 * @return array
 */
function thecrisisacademy_get_program_defaults() {
	$theme_uri = get_stylesheet_directory_uri();
	return array(
		'preheading'         => 'Crisis Readiness Program™',
		'title'              => 'No desarrollamos conocimientos.<br><span class="color">Desarrollamos capacidad institucional.</span>',
		'before_label'       => 'Antes del Programa',
		'after_label'        => 'Después del Programa',
		'before_title'       => 'No existe protocolo',
		'before_description' => 'Falta de gobernanza clara, comités desorganizados, respuestas lentas, pánico operativo y alta vulnerabilidad ante cualquier contingencia pública.',
		'after_title'        => 'La organización responde como un solo equipo',
		'after_description'  => 'Toma de decisiones coordinada, flujos de escalamiento inmediatos, vocerías alineadas y mitigación real del daño reputacional en minutos.',
		'steps'              => array(
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-1.webp',
				'photo_alt'   => 'Investigación y estudios de crisis - Radar de riesgos',
				'icon'        => 'radar',
				'short_title' => 'Investigación y estudios de crisis',
				'title'       => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
				'description' => 'Análisis profundo de incidentes recientes y anticipación de escenarios de riesgo reputacional adaptados al entorno actual.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-2.webp',
				'photo_alt'   => 'Herramientas y parámetros de medición de una crisis y su respuesta',
				'icon'        => 'layers',
				'short_title' => 'Herramientas y parámetros de medición',
				'title'       => 'Herramientas y parámetros de medición de una crisis y su respuesta',
				'description' => 'Establecimiento de indicadores cuantitativos y cualitativos para evaluar el impacto del incidente y la efectividad de la respuesta.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-3.webp',
				'photo_alt'   => 'Entorno mediático y digital - El nuevo rol de la Inteligencia Artificial',
				'icon'        => 'grid',
				'short_title' => 'Entorno IA & Digital',
				'title'       => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
				'description' => 'Evaluación de la desinformación masiva, deepfakes y uso de IA en la amplificación y monitoreo de crisis modernas.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-4.webp',
				'photo_alt'   => 'Comunicación estratégica para manejo de crisis 4.0',
				'icon'        => 'file-text',
				'short_title' => 'Comunicación Estratégica',
				'title'       => 'Comunicación estratégica para manejo de crisis 4.0',
				'description' => 'Diseño de mensajes clave hiperdirigidos, comunicados ágiles y posicionamiento corporativo multicanal bajo presión.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-5.webp',
				'photo_alt'   => 'Procesos para un manejo ágil de crisis en redes sociales',
				'icon'        => 'message-circle',
				'short_title' => 'Gestión Ágil en Redes',
				'title'       => 'Procesos para un manejo ágil de crisis en redes sociales',
				'description' => 'Protocolos de contención inmediata en plataformas digitales, gestión de comunidades y desaceleración de tendencias negativas.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-6.webp',
				'photo_alt'   => 'Control de narrativa y arquitectura de vocerías',
				'icon'        => 'mic',
				'short_title' => 'Control de Narrativa',
				'title'       => 'Control de narrativa y arquitectura de vocerías',
				'description' => 'Definición de portavoces oficiales, lineamientos de conducta ante la prensa y técnicas de control del relato público.',
			),
			array(
				'photo_url'   => $theme_uri . '/assets/img/program/phase-7.webp',
				'photo_alt'   => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis',
				'icon'        => 'activity',
				'short_title' => 'Simulacro de Alta Intensidad',
				'title'       => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis de reputación',
				'description' => 'Ejercicio inmersivo en tiempo real con periodistas simulados e interacciones hostiles para auditar la resistencia de los comités.',
			),
		),
	);
}

/**
 * Retrieve sanitized Program data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_program_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_program_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading         = get_post_meta( $post_id, '_corporate_program_preheading', true );
	$title              = get_post_meta( $post_id, '_corporate_program_title', true );
	$before_label       = get_post_meta( $post_id, '_corporate_program_before_label', true );
	$after_label        = get_post_meta( $post_id, '_corporate_program_after_label', true );
	$before_title       = get_post_meta( $post_id, '_corporate_program_before_title', true );
	$before_description = get_post_meta( $post_id, '_corporate_program_before_description', true );
	$after_title        = get_post_meta( $post_id, '_corporate_program_after_title', true );
	$after_description  = get_post_meta( $post_id, '_corporate_program_after_description', true );
	$steps              = get_post_meta( $post_id, '_corporate_program_steps', true );

	return array(
		'preheading'         => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'              => '' !== $title && false !== $title ? $title : $defaults['title'],
		'before_label'       => '' !== $before_label && false !== $before_label ? $before_label : $defaults['before_label'],
		'after_label'        => '' !== $after_label && false !== $after_label ? $after_label : $defaults['after_label'],
		'before_title'       => '' !== $before_title && false !== $before_title ? $before_title : $defaults['before_title'],
		'before_description' => '' !== $before_description && false !== $before_description ? $before_description : $defaults['before_description'],
		'after_title'        => '' !== $after_title && false !== $after_title ? $after_title : $defaults['after_title'],
		'after_description'  => '' !== $after_description && false !== $after_description ? $after_description : $defaults['after_description'],
		'steps'              => is_array( $steps ) && ! empty( $steps ) ? $steps : $defaults['steps'],
	);
}

/**
 * Render predefined SVG icon for Simulation vulnerabilities
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @param float  $stroke   Stroke width
 * @return string
 */
function thecrisisacademy_get_simulation_icon_svg( $icon_key, $width = 22, $height = 22, $stroke = 3 ) {
	$paths = array(
		'x'              => '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>',
		'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
		'alert-circle'   => '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
		'shield-alert'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>',
		'slash'          => '<circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>',
		'check'          => '<polyline points="20 6 9 17 4 12"></polyline>',
		'clock'          => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
		'users'          => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
	);

	$inner = isset( $paths[ $icon_key ] ) ? $paths[ $icon_key ] : $paths['x'];
	$stroke_val = ( 'x' === $icon_key ) ? $stroke : ( $stroke > 2.2 ? 2 : $stroke );

	return sprintf(
		'<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="%.1f" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		absint( $width ),
		absint( $height ),
		floatval( $stroke_val ),
		$inner
	);
}

/**
 * Get icon options for Simulation vulnerabilities
 *
 * @return array
 */
function thecrisisacademy_get_simulation_icon_options() {
	return array(
		'x'              => 'Cruz / Fallo (✕)',
		'alert-triangle' => 'Triángulo de Advertencia (⚠)',
		'alert-circle'   => 'Círculo de Alerta (Exclamación)',
		'shield-alert'   => 'Escudo Vulnerado (Seguridad)',
		'slash'          => 'Prohibido / Bloqueado (⊘)',
		'clock'          => 'Reloj / Tiempo Crítico',
		'users'          => 'Personas / Equipo',
		'check'          => 'Check / Verificado (✓)',
	);
}

/**
 * Get default fallback data for Simulation section
 *
 * @return array
 */
function thecrisisacademy_get_simulation_defaults() {
	return array(
		'preheading'    => 'Entrenamiento Inmersivo',
		'title'         => 'Antes de comenzar,<br><span class="color">casi siempre encontramos los mismos problemas.</span>',
		'description'   => 'Las organizaciones suelen creer que están preparadas hasta que se enfrentan a un incidente real. Nuestro simulador interactivo recrea la presión de una crisis digital y mediática para evaluar y fortalecer su protocolo de respuesta en minutos.',
		'cta_text'      => 'Probar Simulador de Crisis',
		'cta_url'       => site_url() . '/simulador-de-crisis/',
		'cta_lightbox'  => 'crisis-simulator',
		'panel_title'   => 'Auditoria de seguridad',
		'items'         => array(
			array(
				'icon'        => 'x',
				'title'       => 'No existe un protocolo compartido',
				'description' => 'Los manuales teóricos estáticos se quedan en el papel ante un incidente en tiempo real.',
			),
			array(
				'icon'        => 'x',
				'title'       => 'Cada área entiende una crisis diferente',
				'description' => 'Falta de alineación semántica y objetivos contradictorios entre comités y departamentos clave.',
			),
			array(
				'icon'        => 'x',
				'title'       => 'Nadie sabe quién toma la decisión final',
				'description' => 'Vacíos de liderazgo corporativo y comités de crisis paralizados por la burocracia interna.',
			),
			array(
				'icon'        => 'x',
				'title'       => 'El comité nunca ha practicado junto',
				'description' => 'La primera vez que coordinan una respuesta no debería ser en medio de un ataque reputacional.',
			),
			array(
				'icon'        => 'x',
				'title'       => 'Los voceros nunca han recibido presión real',
				'description' => 'Portavoces sin entrenamiento práctico para responder ante el asedio agresivo de medios digitales.',
			),
		),
	);
}

/**
 * Retrieve sanitized Simulation data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_simulation_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_simulation_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading   = get_post_meta( $post_id, '_corporate_sim_preheading', true );
	$title        = get_post_meta( $post_id, '_corporate_sim_title', true );
	$description  = get_post_meta( $post_id, '_corporate_sim_description', true );
	$cta_text     = get_post_meta( $post_id, '_corporate_sim_cta_text', true );
	$cta_url      = get_post_meta( $post_id, '_corporate_sim_cta_url', true );
	$cta_lightbox = get_post_meta( $post_id, '_corporate_sim_cta_lightbox', true );
	$panel_title  = get_post_meta( $post_id, '_corporate_sim_panel_title', true );
	$items        = get_post_meta( $post_id, '_corporate_sim_items', true );

	return array(
		'preheading'   => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'        => '' !== $title && false !== $title ? $title : $defaults['title'],
		'description'  => '' !== $description && false !== $description ? $description : $defaults['description'],
		'cta_text'     => '' !== $cta_text && false !== $cta_text ? $cta_text : $defaults['cta_text'],
		'cta_url'      => '' !== $cta_url && false !== $cta_url ? $cta_url : $defaults['cta_url'],
		'cta_lightbox' => '' !== $cta_lightbox && false !== $cta_lightbox ? $cta_lightbox : $defaults['cta_lightbox'],
		'panel_title'  => '' !== $panel_title && false !== $panel_title ? $panel_title : $defaults['panel_title'],
		'items'        => is_array( $items ) && ! empty( $items ) ? $items : $defaults['items'],
	);
}

/**
 * Render predefined SVG icon for Diff (Why Us) slides
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @param float  $stroke   Stroke width
 * @return string
 */
function thecrisisacademy_get_diff_icon_svg( $icon_key, $width = 22, $height = 22, $stroke = 2 ) {
	$paths = array(
		'book-open'   => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>',
		'zap'         => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>',
		'target'      => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
		'cpu'         => '<rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line>',
		'globe'       => '<circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>',
		'shield'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
		'award'       => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
		'trending-up' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline>',
		'users'       => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
		'activity'    => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>',
	);

	$inner = isset( $paths[ $icon_key ] ) ? $paths[ $icon_key ] : $paths['book-open'];

	return sprintf(
		'<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="%.1f" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		absint( $width ),
		absint( $height ),
		floatval( $stroke ),
		$inner
	);
}

/**
 * Get icon options for Diff slides
 *
 * @return array
 */
function thecrisisacademy_get_diff_icon_options() {
	return array(
		'book-open'   => 'Libro Abierto (Investigación)',
		'zap'         => 'Rayo (Simulación / Intensidad)',
		'target'      => 'Diana / Radar (KPIs / Métricas)',
		'cpu'         => 'Procesador / Chip (Inteligencia Artificial)',
		'globe'       => 'Planeta / Globo (Multinacional)',
		'shield'      => 'Escudo (Protección y Blindaje)',
		'award'       => 'Medalla (Excelencia y Acreditación)',
		'trending-up' => 'Tendencia Alcista (Crecimiento)',
		'users'       => 'Personas (Comités / Liderazgo)',
		'activity'    => 'Pulso (Respuesta Activa)',
	);
}

/**
 * Get default fallback data for Diff section
 *
 * @return array
 */
function thecrisisacademy_get_diff_defaults() {
	return array(
		'preheading' => 'Por qué nosotros',
		'title'      => 'Por qué organizaciones líderes<br><span class="color">trabajan con nosotros</span>',
		'autoplay'   => '14000',
		'slides'     => array(
			array(
				'icon'        => 'book-open',
				'title'       => 'Investigación aplicada',
				'description' => 'Estudiamos casos reales de desinformación e incidentes digitales para crear dinámicas vigentes y contextuales.',
			),
			array(
				'icon'        => 'zap',
				'title'       => 'Simulación de alta intensidad',
				'description' => 'Entrenamientos interactivos bajo estrés reputacional real para evaluar el desempeño de su equipo bajo máxima presión.',
			),
			array(
				'icon'        => 'target',
				'title'       => 'KPIs individuales',
				'description' => 'Métricas cuantitativas del desempeño y tiempos de reacción de los equipos de comités de crisis en simulacros prácticos.',
			),
			array(
				'icon'        => 'cpu',
				'title'       => 'IA integrada',
				'description' => 'Monitoreo de opinión y simulación potenciados por inteligencia artificial para emular la velocidad de la viralización moderna.',
			),
			array(
				'icon'        => 'globe',
				'title'       => 'Experiencia multinacional',
				'description' => 'Metodología probada en comités de crisis regionales de corporaciones en múltiples países de América Latina.',
			),
			array(
				'icon'        => 'shield',
				'title'       => 'Especialización corporativa',
				'description' => 'Enfoque exclusivo en la protección reputacional, toma de decisiones ejecutivas y continuidad operativa del negocio.',
			),
		),
	);
}

/**
 * Retrieve sanitized Diff data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_diff_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_diff_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading = get_post_meta( $post_id, '_corporate_diff_preheading', true );
	$title      = get_post_meta( $post_id, '_corporate_diff_title', true );
	$autoplay   = get_post_meta( $post_id, '_corporate_diff_autoplay', true );
	$slides     = get_post_meta( $post_id, '_corporate_diff_slides', true );

	return array(
		'preheading' => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'      => '' !== $title && false !== $title ? $title : $defaults['title'],
		'autoplay'   => '' !== $autoplay && false !== $autoplay ? $autoplay : $defaults['autoplay'],
		'slides'     => is_array( $slides ) && ! empty( $slides ) ? $slides : $defaults['slides'],
	);
}

/**
 * Get default fallback data for Testimonies section
 *
 * @return array
 */
function thecrisisacademy_get_testimonies_defaults() {
	$theme_uri = get_stylesheet_directory_uri();
	return array(
		'preheading' => 'Testimonios',
		'title'      => 'Voces de líderes que ya<br><span class="color">se han entrenado con nosotros</span>',
		'items'      => array(
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/alejandro-ruiz.jpg',
				'avatar_alt' => 'Alejandro Ruiz',
				'name'       => 'Alejandro Ruiz',
				'role'       => 'Director de Reputación Corporativa — Grupo Financiero',
				'text'       => 'El simulador de alta intensidad puso a prueba a todo nuestro comité directivo. Nos obligó a tomar decisiones rápidas y a darnos cuenta de las fallas que teníamos en nuestros protocolos anteriores.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/sofia-mendoza.jpg',
				'avatar_alt' => 'Sofía Mendoza',
				'name'       => 'Sofía Mendoza',
				'role'       => 'Gerente de Comunicación Externa — Empresa de Consumo',
				'text'       => 'Una experiencia reveladora. La presión mediática simulada se siente tan real que los portavoces realmente entienden la importancia de cada declaración pública.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/carlos-villanueva.jpg',
				'avatar_alt' => 'Carlos Villanueva',
				'name'       => 'Carlos Villanueva',
				'role'       => 'VP de Operaciones y Continuidad — Telecomunicaciones',
				'text'       => 'Gracias a los KPIs individuales pudimos medir el tiempo de respuesta real y la efectividad de cada área. Excelente para auditar la resistencia de la empresa.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/diana-torres.jpg',
				'avatar_alt' => 'Diana Patricia Torres',
				'name'       => 'Diana Patricia Torres',
				'role'       => 'Directora de Asuntos Públicos — Consorcio Energético',
				'text'       => 'La integración de Inteligencia Artificial para emular la velocidad de las redes sociales hace que este entrenamiento sea único en el mercado. Indispensable hoy en día.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/mariano-silva.jpg',
				'avatar_alt' => 'Mariano de Silva',
				'name'       => 'Mariano de Silva',
				'role'       => 'Director General de Asuntos Corporativos LATAM',
				'text'       => 'La metodología y la experiencia del equipo de instructores aportó un valor tremendo a nuestro protocolo regional. 100% recomendado para empresas multinacionales.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/gabriela-esparza.jpg',
				'avatar_alt' => 'Gabriela Esparza',
				'name'       => 'Gabriela Esparza',
				'role'       => 'VP de Relaciones Institucionales — Logística Global',
				'text'       => 'La especialización corporativa de este programa nos dio herramientas prácticas e inmediatas para asegurar la continuidad de nuestra operación bajo escenarios críticos.',
			),
			array(
				'avatar_url' => $theme_uri . '/assets/img/avatars/roberto-lujan.jpg',
				'avatar_alt' => 'Roberto Luján',
				'name'       => 'Roberto Luján',
				'role'       => 'Director de Seguridad de la Información — Fintech',
				'text'       => 'No es una capacitación teórica ordinaria; es un ejercicio inmersivo de control de daños que prepara al equipo para reaccionar con absoluta frialdad ante lo inesperado.',
			),
		),
	);
}

/**
 * Retrieve sanitized Testimonies data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_testimonies_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_testimonies_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading = get_post_meta( $post_id, '_corporate_testimonies_preheading', true );
	$title      = get_post_meta( $post_id, '_corporate_testimonies_title', true );
	$items      = get_post_meta( $post_id, '_corporate_testimonies_items', true );
	return array(
		'preheading' => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'      => '' !== $title && false !== $title ? $title : $defaults['title'],
		'items'      => is_array( $items ) && ! empty( $items ) ? $items : $defaults['items'],
	);
}

/**
 * Render predefined SVG icon for Thought section columns
 *
 * @param string $icon_key Icon identifier
 * @param int    $width    Width in pixels
 * @param int    $height   Height in pixels
 * @param float  $stroke   Stroke width
 * @return string
 */
function thecrisisacademy_get_thought_icon_svg( $icon_key, $width = 22, $height = 22, $stroke = 2 ) {
	$paths = array(
		'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
		'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
		'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
		'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
		'target'    => '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>',
		'award'     => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
	);

	$inner = $paths[ $icon_key ] ?? $paths['users'];
	return sprintf(
		'<svg viewBox="0 0 24 24" width="%d" height="%d" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		(int) $width,
		(int) $height,
		esc_attr( (string) $stroke ),
		$inner
	);
}

/**
 * Get available predefined icons for Thought section columns
 *
 * @return array
 */
function thecrisisacademy_get_thought_icon_options() {
	return array(
		'users'     => 'Usuarios / Audiencia (Perfiles)',
		'briefcase' => 'Maletín / Empresa (Sectores)',
		'star'      => 'Estrella / Propósito (Manifiesto)',
		'shield'    => 'Escudo / Protección',
		'target'    => 'Diana / Criterios',
		'award'     => 'Premio / Liderazgo',
	);
}

/**
 * Get default fallback data for Thought section
 *
 * @return array
 */
function thecrisisacademy_get_thought_defaults() {
	return array(
		'preheading'          => 'Criterios de Idoneidad',
		'title'               => '¿Es este programa para tu organización?',
		'description'         => 'Diseñado para liderazgos corporativos, comités de alta dirección y entornos expuestos a alto riesgo reputacional.',
		// Col 1: Perfiles
		'profiles_tag'        => 'Audiencia',
		'profiles_title'      => 'Perfiles',
		'profiles_icon'       => 'users',
		'profiles_items'      => array(
			'Empresas con más de 200 colaboradores.',
			'Equipos de comunicación corporativa.',
			'Comités de crisis.',
			'Empresas reguladas.',
		),
		// Col 2: Sectores
		'sectors_tag'         => 'Industrias',
		'sectors_title'       => 'Sectores Clave',
		'sectors_icon'        => 'briefcase',
		'sectors_items'       => array(
			'Manufactura',
			'Energía',
			'Consumo',
			'Financiero',
			'Salud',
			'Tecnología',
		),
		// Col 3: Manifiesto
		'manifesto_tag'       => 'Declaración',
		'manifesto_title'     => 'Propósito',
		'manifesto_icon'      => 'star',
		'manifesto_lead'      => 'No es un curso para aprender teoría',
		'manifesto_statement' => 'Es un entrenamiento para organizaciones que <strong>no pueden improvisar</strong> cuando ocurre una crisis.',
	);
}

/**
 * Retrieve sanitized Thought data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_thought_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_thought_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	$preheading          = get_post_meta( $post_id, '_corporate_thought_preheading', true );
	$title               = get_post_meta( $post_id, '_corporate_thought_title', true );
	$description         = get_post_meta( $post_id, '_corporate_thought_description', true );

	$profiles_tag        = get_post_meta( $post_id, '_corporate_thought_profiles_tag', true );
	$profiles_title      = get_post_meta( $post_id, '_corporate_thought_profiles_title', true );
	$profiles_icon       = get_post_meta( $post_id, '_corporate_thought_profiles_icon', true );
	$profiles_items      = get_post_meta( $post_id, '_corporate_thought_profiles_items', true );

	$sectors_tag         = get_post_meta( $post_id, '_corporate_thought_sectors_tag', true );
	$sectors_title       = get_post_meta( $post_id, '_corporate_thought_sectors_title', true );
	$sectors_icon        = get_post_meta( $post_id, '_corporate_thought_sectors_icon', true );
	$sectors_items       = get_post_meta( $post_id, '_corporate_thought_sectors_items', true );

	$manifesto_tag       = get_post_meta( $post_id, '_corporate_thought_manifesto_tag', true );
	$manifesto_title     = get_post_meta( $post_id, '_corporate_thought_manifesto_title', true );
	$manifesto_icon      = get_post_meta( $post_id, '_corporate_thought_manifesto_icon', true );
	$manifesto_lead      = get_post_meta( $post_id, '_corporate_thought_manifesto_lead', true );
	$manifesto_statement = get_post_meta( $post_id, '_corporate_thought_manifesto_statement', true );

	return array(
		'preheading'          => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'               => '' !== $title && false !== $title ? $title : $defaults['title'],
		'description'         => '' !== $description && false !== $description ? $description : $defaults['description'],

		'profiles_tag'        => '' !== $profiles_tag && false !== $profiles_tag ? $profiles_tag : $defaults['profiles_tag'],
		'profiles_title'      => '' !== $profiles_title && false !== $profiles_title ? $profiles_title : $defaults['profiles_title'],
		'profiles_icon'       => '' !== $profiles_icon && false !== $profiles_icon ? $profiles_icon : $defaults['profiles_icon'],
		'profiles_items'      => is_array( $profiles_items ) && ! empty( $profiles_items ) ? $profiles_items : $defaults['profiles_items'],

		'sectors_tag'         => '' !== $sectors_tag && false !== $sectors_tag ? $sectors_tag : $defaults['sectors_tag'],
		'sectors_title'       => '' !== $sectors_title && false !== $sectors_title ? $sectors_title : $defaults['sectors_title'],
		'sectors_icon'        => '' !== $sectors_icon && false !== $sectors_icon ? $sectors_icon : $defaults['sectors_icon'],
		'sectors_items'       => is_array( $sectors_items ) && ! empty( $sectors_items ) ? $sectors_items : $defaults['sectors_items'],

		'manifesto_tag'       => '' !== $manifesto_tag && false !== $manifesto_tag ? $manifesto_tag : $defaults['manifesto_tag'],
		'manifesto_title'     => '' !== $manifesto_title && false !== $manifesto_title ? $manifesto_title : $defaults['manifesto_title'],
		'manifesto_icon'      => '' !== $manifesto_icon && false !== $manifesto_icon ? $manifesto_icon : $defaults['manifesto_icon'],
		'manifesto_lead'      => '' !== $manifesto_lead && false !== $manifesto_lead ? $manifesto_lead : $defaults['manifesto_lead'],
		'manifesto_statement' => '' !== $manifesto_statement && false !== $manifesto_statement ? $manifesto_statement : $defaults['manifesto_statement'],
	);
}

/**
 * Get default fallback data for CTA section
 *
 * @return array
 */
function thecrisisacademy_get_cta_defaults() {
	return array(
		'preheading'        => 'Tu reputación en las mejores manos',
		'title'             => 'Anticípate, prepárate y gestiona profesionalmente cualquier crisis.',
		'description'       => 'No dejes el futuro de tu organización al azar. Agenda una llamada con nuestros expertos y descubre cómo podemos fortalecer tu resiliencia corporativa.',
		'points'            => array(
			'Atención personalizada para cada empresa',
			'Más de 10 años de experiencia comprobada',
			'Metodología internacional certificada',
		),
		'whatsapp_phone'    => '525543910088',
		'form_name_label'   => 'Nombre completo',
		'form_email_label'  => 'Correo electrónico',
		'form_phone_label'  => 'Teléfono / WhatsApp',
		'form_msg_label'    => '¿Qué quieres aprender específicamente?',
		'button_text'       => 'Enviar mensaje',
		'microcopy_text'    => 'Sin compromisos · Respuesta en menos de 24 h',
		'success_title'     => '¡Preparado!',
		'success_desc'      => 'Se ha abierto WhatsApp con tu mensaje pre-cargado.',
		'reset_btn_text'    => 'Llenar nuevo formulario',
	);
}

/**
 * Retrieve sanitized CTA data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_cta_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_cta_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	// Fallback to Corporate page if current page has no custom title set
	$check_title = get_post_meta( $post_id, '_corporate_cta_title', true );
	if ( ( '' === $check_title || false === $check_title ) && function_exists( 'thecrisisacademy_get_corporate_page_id' ) ) {
		$corp_id = thecrisisacademy_get_corporate_page_id();
		if ( $corp_id && (int) $corp_id !== (int) $post_id ) {
			$corp_title = get_post_meta( $corp_id, '_corporate_cta_title', true );
			if ( '' !== $corp_title && false !== $corp_title ) {
				$post_id = $corp_id;
			}
		}
	}

	$preheading       = get_post_meta( $post_id, '_corporate_cta_preheading', true );
	$title            = get_post_meta( $post_id, '_corporate_cta_title', true );
	$description      = get_post_meta( $post_id, '_corporate_cta_description', true );
	$points           = get_post_meta( $post_id, '_corporate_cta_points', true );
	$whatsapp_phone   = get_post_meta( $post_id, '_corporate_cta_whatsapp_phone', true );
	$form_name_label  = get_post_meta( $post_id, '_corporate_cta_form_name_label', true );
	$form_email_label = get_post_meta( $post_id, '_corporate_cta_form_email_label', true );
	$form_phone_label = get_post_meta( $post_id, '_corporate_cta_form_phone_label', true );
	$form_msg_label   = get_post_meta( $post_id, '_corporate_cta_form_msg_label', true );
	$button_text      = get_post_meta( $post_id, '_corporate_cta_button_text', true );
	$microcopy_text   = get_post_meta( $post_id, '_corporate_cta_microcopy_text', true );
	$success_title    = get_post_meta( $post_id, '_corporate_cta_success_title', true );
	$success_desc     = get_post_meta( $post_id, '_corporate_cta_success_desc', true );
	$reset_btn_text   = get_post_meta( $post_id, '_corporate_cta_reset_btn_text', true );

	return array(
		'preheading'        => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'             => '' !== $title && false !== $title ? $title : $defaults['title'],
		'description'       => '' !== $description && false !== $description ? $description : $defaults['description'],
		'points'            => is_array( $points ) && ! empty( $points ) ? $points : $defaults['points'],
		'whatsapp_phone'    => '' !== $whatsapp_phone && false !== $whatsapp_phone ? $whatsapp_phone : $defaults['whatsapp_phone'],
		'form_name_label'   => '' !== $form_name_label && false !== $form_name_label ? $form_name_label : $defaults['form_name_label'],
		'form_email_label'  => '' !== $form_email_label && false !== $form_email_label ? $form_email_label : $defaults['form_email_label'],
		'form_phone_label'  => '' !== $form_phone_label && false !== $form_phone_label ? $form_phone_label : $defaults['form_phone_label'],
		'form_msg_label'    => '' !== $form_msg_label && false !== $form_msg_label ? $form_msg_label : $defaults['form_msg_label'],
		'button_text'       => '' !== $button_text && false !== $button_text ? $button_text : $defaults['button_text'],
		'microcopy_text'    => '' !== $microcopy_text && false !== $microcopy_text ? $microcopy_text : $defaults['microcopy_text'],
		'success_title'     => '' !== $success_title && false !== $success_title ? $success_title : $defaults['success_title'],
		'success_desc'      => '' !== $success_desc && false !== $success_desc ? $success_desc : $defaults['success_desc'],
		'reset_btn_text'    => '' !== $reset_btn_text && false !== $reset_btn_text ? $reset_btn_text : $defaults['reset_btn_text'],
	);
}

/**
 * Get default fallback data for Upcoming Events section header
 *
 * @return array
 */
function thecrisisacademy_get_upcoming_events_defaults() {
	return array(
		'preheading' => 'Agenda 2026',
		'title'      => 'Próximos eventos',
	);
}

/**
 * Retrieve sanitized Upcoming Events section data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_upcoming_events_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_upcoming_events_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	// Fallback to Corporate page if current page has no custom title set
	$check_title = get_post_meta( $post_id, '_corporate_upcoming_events_title', true );
	if ( ( '' === $check_title || false === $check_title ) && function_exists( 'thecrisisacademy_get_corporate_page_id' ) ) {
		$corp_id = thecrisisacademy_get_corporate_page_id();
		if ( $corp_id && (int) $corp_id !== (int) $post_id ) {
			$corp_title = get_post_meta( $corp_id, '_corporate_upcoming_events_title', true );
			if ( '' !== $corp_title && false !== $corp_title ) {
				$post_id = $corp_id;
			}
		}
	}

	$preheading = get_post_meta( $post_id, '_corporate_upcoming_events_preheading', true );
	$title      = get_post_meta( $post_id, '_corporate_upcoming_events_title', true );

	return array(
		'preheading' => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'      => '' !== $title && false !== $title ? $title : $defaults['title'],
	);
}

/**
 * Get default fallback data for News section
 *
 * @return array
 */
function thecrisisacademy_get_news_defaults() {
	return array(
		'preheading'       => 'Centro de Inteligencia',
		'title'            => 'Actualidad Global y Análisis',
		'description'      => 'Mantente informado de los últimos eventos, análisis e impacto de crisis y escándalos.',
		'posts_per_page'   => 8,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'show_filters'     => '1',
		'filter_all_label' => 'Todos',
		'show_canvas'      => '1',
		'show_button'      => '1',
		'button_text'      => 'Ver todas las noticias',
		'button_url'       => '',
		'button_target'    => '0',
	);
}

/**
 * Retrieve sanitized News section data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_news_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_news_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	// Fallback to Corporate page if current page has no custom title set
	$check_title = get_post_meta( $post_id, '_corporate_news_title', true );
	if ( ( '' === $check_title || false === $check_title ) && function_exists( 'thecrisisacademy_get_corporate_page_id' ) ) {
		$corp_id = thecrisisacademy_get_corporate_page_id();
		if ( $corp_id && (int) $corp_id !== (int) $post_id ) {
			$corp_title = get_post_meta( $corp_id, '_corporate_news_title', true );
			if ( '' !== $corp_title && false !== $corp_title ) {
				$post_id = $corp_id;
			}
		}
	}

	$preheading       = get_post_meta( $post_id, '_corporate_news_preheading', true );
	$title            = get_post_meta( $post_id, '_corporate_news_title', true );
	$description      = get_post_meta( $post_id, '_corporate_news_description', true );
	$posts_per_page   = get_post_meta( $post_id, '_corporate_news_posts_per_page', true );
	$orderby          = get_post_meta( $post_id, '_corporate_news_orderby', true );
	$order            = get_post_meta( $post_id, '_corporate_news_order', true );
	$show_filters     = get_post_meta( $post_id, '_corporate_news_show_filters', true );
	$filter_all_label = get_post_meta( $post_id, '_corporate_news_filter_all_label', true );
	$show_canvas      = get_post_meta( $post_id, '_corporate_news_show_canvas', true );
	$show_button      = get_post_meta( $post_id, '_corporate_news_show_button', true );
	$button_text      = get_post_meta( $post_id, '_corporate_news_button_text', true );
	$button_url       = get_post_meta( $post_id, '_corporate_news_button_url', true );
	$button_target    = get_post_meta( $post_id, '_corporate_news_button_target', true );

	return array(
		'preheading'       => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'            => '' !== $title && false !== $title ? $title : $defaults['title'],
		'description'      => '' !== $description && false !== $description ? $description : $defaults['description'],
		'posts_per_page'   => '' !== $posts_per_page && false !== $posts_per_page ? absint( $posts_per_page ) : $defaults['posts_per_page'],
		'orderby'          => '' !== $orderby && false !== $orderby ? $orderby : $defaults['orderby'],
		'order'            => '' !== $order && false !== $order ? $order : $defaults['order'],
		'show_filters'     => '' !== $show_filters && false !== $show_filters ? $show_filters : $defaults['show_filters'],
		'filter_all_label' => '' !== $filter_all_label && false !== $filter_all_label ? $filter_all_label : $defaults['filter_all_label'],
		'show_canvas'      => '' !== $show_canvas && false !== $show_canvas ? $show_canvas : $defaults['show_canvas'],
		'show_button'      => '' !== $show_button && false !== $show_button ? $show_button : $defaults['show_button'],
		'button_text'      => '' !== $button_text && false !== $button_text ? $button_text : $defaults['button_text'],
		'button_url'       => '' !== $button_url && false !== $button_url ? $button_url : $defaults['button_url'],
		'button_target'    => '' !== $button_target && false !== $button_target ? $button_target : $defaults['button_target'],
	);
}

/**
 * Get default fallback data for FAQ section
 *
 * @return array
 */
function thecrisisacademy_get_faq_defaults() {
	return array(
		'preheading'     => 'FAQ',
		'title'          => 'Preguntas más frecuentes',
		'cta_title'      => 'Agenda una llamada de 15 min',
		'cta_desc'       => 'Si tienes dudas, agenda una videollamada gratuita de 15 minutos antes de suscribirte a un plan.',
		'cta_btn_text'   => 'Reservar Llamada Gratuita',
		'cta_btn_url'    => '#cta',
		'cta_btn_target' => '0',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'first_open'     => '1',
	);
}

/**
 * Retrieve sanitized FAQ section data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_faq_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_faq_defaults();
	if ( ! $post_id ) {
		return $defaults;
	}

	// Fallback to Corporate page if current page has no custom title set
	$check_title = get_post_meta( $post_id, '_corporate_faq_title', true );
	if ( ( '' === $check_title || false === $check_title ) && function_exists( 'thecrisisacademy_get_corporate_page_id' ) ) {
		$corp_id = thecrisisacademy_get_corporate_page_id();
		if ( $corp_id && (int) $corp_id !== (int) $post_id ) {
			$corp_title = get_post_meta( $corp_id, '_corporate_faq_title', true );
			if ( '' !== $corp_title && false !== $corp_title ) {
				$post_id = $corp_id;
			}
		}
	}

	$preheading     = get_post_meta( $post_id, '_corporate_faq_preheading', true );
	$title          = get_post_meta( $post_id, '_corporate_faq_title', true );
	$cta_title      = get_post_meta( $post_id, '_corporate_faq_cta_title', true );
	$cta_desc       = get_post_meta( $post_id, '_corporate_faq_cta_desc', true );
	$cta_btn_text   = get_post_meta( $post_id, '_corporate_faq_cta_btn_text', true );
	$cta_btn_url    = get_post_meta( $post_id, '_corporate_faq_cta_btn_url', true );
	$cta_btn_target = get_post_meta( $post_id, '_corporate_faq_cta_btn_target', true );
	$posts_per_page = get_post_meta( $post_id, '_corporate_faq_posts_per_page', true );
	$orderby        = get_post_meta( $post_id, '_corporate_faq_orderby', true );
	$order          = get_post_meta( $post_id, '_corporate_faq_order', true );
	$first_open     = get_post_meta( $post_id, '_corporate_faq_first_open', true );

	return array(
		'preheading'     => '' !== $preheading && false !== $preheading ? $preheading : $defaults['preheading'],
		'title'          => '' !== $title && false !== $title ? $title : $defaults['title'],
		'cta_title'      => '' !== $cta_title && false !== $cta_title ? $cta_title : $defaults['cta_title'],
		'cta_desc'       => '' !== $cta_desc && false !== $cta_desc ? $cta_desc : $defaults['cta_desc'],
		'cta_btn_text'   => '' !== $cta_btn_text && false !== $cta_btn_text ? $cta_btn_text : $defaults['cta_btn_text'],
		'cta_btn_url'    => '' !== $cta_btn_url && false !== $cta_btn_url ? $cta_btn_url : $defaults['cta_btn_url'],
		'cta_btn_target' => '' !== $cta_btn_target && false !== $cta_btn_target ? $cta_btn_target : $defaults['cta_btn_target'],
		'posts_per_page' => '' !== $posts_per_page && false !== $posts_per_page ? intval( $posts_per_page ) : $defaults['posts_per_page'],
		'orderby'        => '' !== $orderby && false !== $orderby ? $orderby : $defaults['orderby'],
		'order'          => '' !== $order && false !== $order ? $order : $defaults['order'],
		'first_open'     => '' !== $first_open && false !== $first_open ? $first_open : $defaults['first_open'],
	);
}

/**
 * Register Meta Boxes for Corporate Page Template
 *
 * @param string  $post_type Post type
 * @param WP_Post $post      Post object
 */
function thecrisisacademy_register_corporate_metaboxes( $post_type, $post ) {
	if ( 'page' !== $post_type ) {
		return;
	}

	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	$is_front = (int) get_option( 'page_on_front' ) === $post->ID;

	$is_corporate   = ( 'templates/corporate.php' === $template || $is_front );
	$is_individuals = ( 'templates/individuals.php' === $template );

	// Only show on corporate or individuals templates
	if ( ! $is_corporate && ! $is_individuals ) {
		return;
	}

	// Full Corporate-only sections
	if ( $is_corporate ) {
		add_meta_box(
			'corporate_hero_metabox',
			'⚡ Sección Hero — Configuración de Campos y Repetidor',
			'thecrisisacademy_render_hero_metabox',
			'page',
			'normal',
			'high'
		);

		add_meta_box(
			'corporate_trouble_metabox',
			'🚨 Sección El Problema (Trouble) — Línea de Tiempo y Tarjetas 3D',
			'thecrisisacademy_render_trouble_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_hearings_metabox',
			'🎯 Sección Impacto 360° (Hearings) — Radar y Acordeón Interactivo',
			'thecrisisacademy_render_hearings_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_founder_metabox',
			'👩‍🏫 Sección Fundadora (Founder) — Perfil, Cita, Metodología y Cifras',
			'thecrisisacademy_render_founder_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_program_metabox',
			'📘 Sección Programa (Program) — Comparación Antes/Después y Fases del Proceso',
			'thecrisisacademy_render_program_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_simulation_metabox',
			'🎮 Sección Simulador (Simulation) — Panel de Auditoría y Acordeón',
			'thecrisisacademy_render_simulation_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_diff_metabox',
			'💎 Sección Por qué Nosotros (Diff) — Carrusel 3D Flip',
			'thecrisisacademy_render_diff_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_testimonies_metabox',
			'💬 Sección Testimonios (Testimonies) — Carrusel Coverflow 3D',
			'thecrisisacademy_render_testimonies_metabox',
			'page',
			'normal',
			'default'
		);

		add_meta_box(
			'corporate_thought_metabox',
			'🧭 Sección Criterios e Idoneidad (Thought) — Perfiles, Sectores y Propósito',
			'thecrisisacademy_render_thought_metabox',
			'page',
			'normal',
			'default'
		);
	}

	// Shared sections (both Corporate and Individuals)
	add_meta_box(
		'corporate_cta_metabox',
		'📱 Sección Llamado a la Acción (CTA) — WhatsApp y Puntos Clave',
		'thecrisisacademy_render_cta_metabox',
		'page',
		'normal',
		'default'
	);

	add_meta_box(
		'corporate_upcoming_events_metabox',
		'📅 Sección Próximos Eventos (Upcoming Events) — Agenda y Estado CPT',
		'thecrisisacademy_render_upcoming_events_metabox',
		'page',
		'normal',
		'default'
	);

	add_meta_box(
		'corporate_news_metabox',
		'📰 Sección Noticias (News) — Encabezado, Filtros y Configuración CPT',
		'thecrisisacademy_render_news_metabox',
		'page',
		'normal',
		'default'
	);

	add_meta_box(
		'corporate_faq_metabox',
		'❓ Sección Preguntas Frecuentes (FAQ) — Encabezado, Tarjeta CTA y Acordeón',
		'thecrisisacademy_render_faq_metabox',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_corporate_metaboxes', 10, 2 );

/**
 * Enqueue WordPress media scripts in post editor
 *
 * @param string $hook Admin page hook
 */
function thecrisisacademy_admin_media_scripts( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'thecrisisacademy_admin_media_scripts' );

/**
 * Render Hero Meta Box in WordPress Admin
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_hero_metabox( $post ) {
	wp_nonce_field( 'corporate_hero_metabox_save', 'corporate_hero_nonce' );

	$data = thecrisisacademy_get_hero_data( $post->ID );
	?>
	<div class="tca-metabox-wrapper">
		<style>
			.tca-metabox-wrapper {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
				padding: 10px 0;
			}
			.tca-field-group {
				margin-bottom: 24px;
				padding-bottom: 20px;
				border-bottom: 1px solid #e2e8f0;
			}
			.tca-field-group:last-child {
				border-bottom: none;
				margin-bottom: 0;
				padding-bottom: 0;
			}
			.tca-field-title {
				font-size: 14px;
				font-weight: 700;
				color: #1e293b;
				margin-bottom: 6px;
				display: block;
			}
			.tca-field-desc {
				font-size: 12px;
				color: #64748b;
				margin-top: 4px;
				margin-bottom: 8px;
			}
			.tca-repeater-container {
				background: #f8fafc;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				padding: 14px;
			}
			.tca-repeater-list {
				display: flex;
				flex-direction: column;
				gap: 10px;
				margin-bottom: 14px;
			}
			.tca-repeater-row {
				display: flex;
				align-items: stretch;
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 4px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.04);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-repeater-row:hover {
				border-color: #94a3b8;
			}
			.tca-row-drag-handle {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 38px;
				background: #f1f5f9;
				color: #64748b;
				cursor: grab;
				user-select: none;
				border-right: 1px solid #e2e8f0;
				border-top-left-radius: 3px;
				border-bottom-left-radius: 3px;
			}
			.tca-row-drag-handle:active {
				cursor: grabbing;
			}
			.tca-row-content {
				flex: 1;
				padding: 8px 12px;
			}
			.tca-row-content textarea {
				width: 100%;
				border: 1px solid #cbd5e1;
				border-radius: 3px;
				padding: 8px 10px;
				font-size: 13px;
				line-height: 1.5;
				resize: vertical;
				box-sizing: border-box;
			}
			.tca-row-content textarea:focus {
				border-color: #0079ff;
				box-shadow: 0 0 0 1px #0079ff;
				outline: none;
			}
			.tca-row-actions {
				display: flex;
				flex-direction: column;
				justify-content: center;
				gap: 4px;
				padding: 6px 8px;
				background: #f8fafc;
				border-left: 1px solid #e2e8f0;
				border-top-right-radius: 3px;
				border-bottom-right-radius: 3px;
			}
			.tca-btn-icon {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 3px;
				cursor: pointer;
				padding: 2px 6px;
				font-size: 11px;
				line-height: 1;
				color: #475569;
			}
			.tca-btn-icon:hover {
				background: #f1f5f9;
				color: #0f172a;
			}
			.tca-btn-delete {
				color: #dc2626 !important;
				border-color: #fecaca !important;
			}
			.tca-btn-delete:hover {
				background: #fee2e2 !important;
				color: #991b1b !important;
			}
			.tca-grid-2 {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 16px;
			}
			@media (max-width: 782px) {
				.tca-grid-2 {
					grid-template-columns: 1fr;
				}
			}
		</style>

		<!-- 1. Preheading / Sub-heading -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_hero_preheading">Subtítulo Superior (Pretext Reveal)</label>
			<input type="text" id="tca_hero_preheading" name="corporate_hero[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" placeholder="Ej. Comunicación y Manejo de Crisis • Programa In-Company" />
			<p class="tca-field-desc">Texto con animación de decodificación aleatoria (scramble effect).</p>
		</div>

		<!-- 2. Main Title -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_hero_title">Título Principal (Title Reveal)</label>
			<textarea id="tca_hero_title" name="corporate_hero[title]" rows="2" class="large-text" placeholder="Ej. Tu empresa tiene 60 minutos. ¿Está preparada para responder?"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			<p class="tca-field-desc">Aparece escalonado palabra por palabra (soporta etiquetas básicas como <code>&lt;br&gt;</code>, <code>&lt;span class="color"&gt;</code> o <code>&lt;em&gt;</code>).</p>
		</div>

		<!-- 3. Points Slideshow Repeater -->
		<div class="tca-field-group">
			<label class="tca-field-title">Puntos Clave (.points-slideshow — Repetidor)</label>
			<p class="tca-field-desc">Slideshow interactivo con transición de puntos clave y navegación por indicadores.</p>

			<div class="tca-repeater-container" id="hero-points-repeater">
				<div class="tca-repeater-list">
					<?php foreach ( $data['points'] as $index => $point ) : ?>
						<div class="tca-repeater-row" draggable="true">
							<div class="tca-row-drag-handle" title="Arrastrar para reordenar">&#9776;</div>
							<div class="tca-row-content">
								<textarea name="corporate_hero[points][]" rows="2" placeholder="Introduce el texto del punto clave..."><?php echo esc_textarea( is_array( $point ) ? ( $point['text'] ?? '' ) : $point ); ?></textarea>
							</div>
							<div class="tca-row-actions">
								<button type="button" class="tca-btn-icon tca-move-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-move-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-remove-row" title="Eliminar este punto">&times;</button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-point-btn">+ Añadir Nuevo Punto Clave</button>

				<!-- Row Template -->
				<template id="tca-point-row-template">
					<div class="tca-repeater-row" draggable="true">
						<div class="tca-row-drag-handle" title="Arrastrar para reordenar">&#9776;</div>
						<div class="tca-row-content">
							<textarea name="corporate_hero[points][]" rows="2" placeholder="Introduce el texto del punto clave..."></textarea>
						</div>
						<div class="tca-row-actions">
							<button type="button" class="tca-btn-icon tca-move-up" title="Mover arriba">&uarr;</button>
							<button type="button" class="tca-btn-icon tca-move-down" title="Mover abajo">&darr;</button>
							<button type="button" class="tca-btn-icon tca-btn-delete tca-remove-row" title="Eliminar este punto">&times;</button>
						</div>
					</div>
				</template>
			</div>
		</div>

		<!-- 4. Data Block Content (WYSIWYG) -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="herodatablockcontent">Contenido del Bloque de Datos (.data-block — HTML / Texto)</label>
			<p class="tca-field-desc">Texto informativo superior del panel 3D. Puedes utilizar títulos &lt;h3&gt;, párrafos &lt;p&gt;, negritas &lt;strong&gt; y enlaces.</p>
			
			<textarea id="herodatablockcontent" name="corporate_hero[data_block_content]" rows="6" class="large-text code" style="font-family: monospace; font-size: 13px; line-height: 1.5; padding: 10px; border-radius: 4px;"><?php echo esc_textarea( $data['data_block_content'] ); ?></textarea>
		</div>

		<!-- 5. Data Block Source -->
		<div class="tca-field-group">
			<label class="tca-field-title">Fuente del Gráfico de Crisis (.data-block)</label>
			<div class="tca-grid-2">
				<div>
					<label for="tca_hero_source_label" style="display:block; font-size:12px; margin-bottom:4px;">Texto de la Fuente</label>
					<input type="text" id="tca_hero_source_label" name="corporate_hero[source_label]" value="<?php echo esc_attr( $data['source_label'] ); ?>" class="large-text" placeholder="Ej. PwC + Oxford Metrica" />
				</div>
				<div>
					<label for="tca_hero_source_url" style="display:block; font-size:12px; margin-bottom:4px;">Enlace URL de la Fuente</label>
					<input type="url" id="tca_hero_source_url" name="corporate_hero[source_url]" value="<?php echo esc_attr( $data['source_url'] ); ?>" class="large-text" placeholder="https://..." />
				</div>
			</div>
			<p class="tca-field-desc">Se muestra en la esquina inferior izquierda bajo el gráfico de simulación de caída de mercado.</p>
		</div>

		<!-- 6. Canvas Crisis Keywords -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_hero_canvas_tags">Palabras Clave del Fondo (#hero-canvas)</label>
			<textarea id="tca_hero_canvas_tags" name="corporate_hero[canvas_tags]" rows="4" class="large-text" placeholder="Ej. #Negligencia, #Escándalo, #Fraude, #FugaDeDatos..."><?php echo esc_textarea( implode( ', ', $data['canvas_tags'] ) ); ?></textarea>
			<p class="tca-field-desc">Palabras clave y hashtags que flotan, colisionan y detonan en el fondo animado del Hero. Puedes separarlas por comas o saltos de línea (si una palabra no incluye <code>#</code>, se añadirá automáticamente).</p>
		</div>

	</div>

	<!-- JavaScript for Repeater interaction and Drag & Drop -->
	<script>
	(function() {
		const repeater = document.getElementById('hero-points-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-repeater-list');
		const addBtn = repeater.querySelector('.tca-add-point-btn');
		const template = document.getElementById('tca-point-row-template');

		// Add new row
		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			const textareas = list.querySelectorAll('textarea');
			textareas[textareas.length - 1].focus();
			bindRowEvents();
		});

		// Delegate row controls
		list.addEventListener('click', function(e) {
			const row = e.target.closest('.tca-repeater-row');
			if (!row) return;

			if (e.target.closest('.tca-remove-row')) {
				if (list.querySelectorAll('.tca-repeater-row').length > 1) {
					row.remove();
				} else {
					alert('Debe haber al menos un punto clave en el slideshow.');
				}
			} else if (e.target.closest('.tca-move-up')) {
				if (row.previousElementSibling) {
					list.insertBefore(row, row.previousElementSibling);
				}
			} else if (e.target.closest('.tca-move-down')) {
				if (row.nextElementSibling) {
					list.insertBefore(row.nextElementSibling, row);
				}
			}
		});

		// HTML5 Drag and Drop Reordering
		let draggedRow = null;

		function bindRowEvents() {
			list.querySelectorAll('.tca-repeater-row').forEach(row => {
				row.ondragstart = function(e) {
					draggedRow = row;
					e.dataTransfer.effectAllowed = 'move';
					row.style.opacity = '0.5';
				};
				row.ondragend = function() {
					if (draggedRow) draggedRow.style.opacity = '1';
					draggedRow = null;
				};
				row.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				row.ondrop = function(e) {
					e.preventDefault();
					if (draggedRow && draggedRow !== row) {
						const allRows = Array.from(list.children);
						const fromIndex = allRows.indexOf(draggedRow);
						const toIndex = allRows.indexOf(row);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedRow, row.nextSibling);
						} else {
							list.insertBefore(draggedRow, row);
						}
					}
				};
			});
		}

		bindRowEvents();
	})();
	</script>
	<?php
}

/**
 * Render Trouble Meta Box in WordPress Admin
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_trouble_metabox( $post ) {
	wp_nonce_field( 'corporate_trouble_metabox_save', 'corporate_trouble_nonce' );

	$data         = thecrisisacademy_get_trouble_data( $post->ID );
	$icon_options = thecrisisacademy_get_trouble_icon_options();
	?>
	<div class="tca-metabox-wrapper tca-trouble-metabox">
		<style>
			.tca-trouble-metabox .tca-step-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 12px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-trouble-metabox .tca-step-card:hover {
				border-color: #94a3b8;
			}
			.tca-trouble-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-trouble-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-trouble-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-trouble-metabox .tca-step-badge {
				background: #0f172a;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
				letter-spacing: 0.5px;
			}
			.tca-trouble-metabox .tca-step-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-trouble-metabox .tca-step-tag-pill {
				background: #fee2e2;
				color: #991b1b;
				font-size: 11px;
				padding: 2px 8px;
				border-radius: 12px;
				font-weight: 600;
			}
			.tca-trouble-metabox .tca-card-head-actions {
				display: flex;
				align-items: center;
				gap: 6px;
			}
			.tca-trouble-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 14px;
			}
			.tca-trouble-metabox .tca-card-body.is-collapsed {
				display: none;
			}
			.tca-trouble-metabox .tca-grid-3 {
				display: grid;
				grid-template-columns: 1.2fr 1.2fr 1fr 1fr;
				gap: 12px;
			}
			@media (max-width: 960px) {
				.tca-trouble-metabox .tca-grid-3 {
					grid-template-columns: 1fr 1fr;
				}
			}
			@media (max-width: 600px) {
				.tca-trouble-metabox .tca-grid-3 {
					grid-template-columns: 1fr;
				}
			}
			.tca-trouble-toolbar {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 12px;
			}
		</style>

		<!-- 1. Preheading -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_trouble_preheading">Subtítulo Superior (Pretext Reveal)</label>
			<input type="text" id="tca_trouble_preheading" name="corporate_trouble[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" placeholder="Ej. El problema" />
			<p class="tca-field-desc">Texto pequeño superior sobre el título principal de la sección.</p>
		</div>

		<!-- 2. Main Title -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_trouble_title">Título Principal (Title Reveal)</label>
			<textarea id="tca_trouble_title" name="corporate_trouble[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			<p class="tca-field-desc">Soporta formato básico como <code>&lt;br&gt;</code>, <code>&lt;em&gt;</code> o <code>&lt;strong&gt;</code>.</p>
		</div>

		<!-- 3. Repeater: Hitos de la Crisis -->
		<div class="tca-field-group">
			<div class="tca-trouble-toolbar">
				<div>
					<label class="tca-field-title" style="margin-bottom:2px;">Hitos de la Crisis (Línea de Tiempo + Tarjetas 3D)</label>
					<p class="tca-field-desc" style="margin-bottom:0;">Un solo repetidor alimenta el <em>stepper</em> (resumen) a la izquierda y las <em>tarjetas 3D</em> (extendido) a la derecha.</p>
				</div>
				<div style="display:flex; gap:6px;">
					<button type="button" class="button tca-collapse-all-btn">Colapsar todos</button>
					<button type="button" class="button tca-expand-all-btn">Expandir todos</button>
				</div>
			</div>

			<div class="tca-repeater-container" id="trouble-steps-repeater">
				<div class="tca-trouble-steps-list">
					<?php foreach ( $data['steps'] as $idx => $step ) : 
						$step_num     = sprintf( '%02d', $idx + 1 );
						$points_text  = is_array( $step['points'] ) ? implode( "\n", $step['points'] ) : '';
						$tag_class    = ! empty( $step['tag_class'] ) ? $step['tag_class'] : 'alert-tag';
					?>
						<div class="tca-step-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-step-badge">Fase <?php echo esc_html( $step_num ); ?></span>
									<span class="tca-step-summary"><?php echo esc_html( $step['department'] . ' — ' . $step['title'] ); ?></span>
									<span class="tca-step-tag-pill"><?php echo esc_html( $step['tag'] ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-trouble-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-trouble-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-trouble-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-trouble-remove" title="Eliminar este hito">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<div class="tca-grid-3">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Área / Departamento</label>
										<input type="text" name="corporate_trouble_department[]" value="<?php echo esc_attr( $step['department'] ); ?>" class="widefat" placeholder="Ej. Comunicación" required />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono del Hito</label>
										<select name="corporate_trouble_icon[]" class="widefat">
											<?php foreach ( $icon_options as $val => $lbl ) : ?>
												<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $step['icon'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta de Alerta</label>
										<input type="text" name="corporate_trouble_tag[]" value="<?php echo esc_attr( $step['tag'] ); ?>" class="widefat" placeholder="Ej. Parálisis Inicial" />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nivel de Severidad</label>
										<select name="corporate_trouble_tag_class[]" class="widefat">
											<option value="alert-tag" <?php selected( $tag_class, 'alert-tag' ); ?>>Alerta (Naranja/Rojo suave)</option>
											<option value="critical-tag" <?php selected( $tag_class, 'critical-tag' ); ?>>Crítica (Rojo intenso)</option>
										</select>
									</div>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Hito (Compartido: Stepper + Tarjeta 3D)</label>
									<input type="text" name="corporate_trouble_title[]" value="<?php echo esc_attr( $step['title'] ); ?>" class="widefat" placeholder="Ej. Espera instrucciones que nunca llegan" required />
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Extendida (Cuerpo de la Tarjeta)</label>
									<textarea name="corporate_trouble_description[]" rows="3" class="widefat" placeholder="Detalle exhaustivo de lo que ocurre en esta fase..."><?php echo esc_textarea( $step['description'] ); ?></textarea>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Puntos Clave (.points-slideshow de la tarjeta)</label>
									<textarea name="corporate_trouble_points[]" rows="3" class="widefat" placeholder="Escribe un punto clave por línea..."><?php echo esc_textarea( $points_text ); ?></textarea>
									<p class="tca-field-desc">Escribe cada viñeta en una línea separada. Se rotarán automáticamente en el carrusel de la tarjeta.</p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-trouble-step-btn" style="margin-top:10px;">+ Añadir Nuevo Hito de Crisis</button>

				<!-- Template for new row -->
				<template id="tca-trouble-step-template">
					<div class="tca-step-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<span class="tca-step-badge">Fase Nueva</span>
								<span class="tca-step-summary">Nuevo hito — Completa los campos</span>
								<span class="tca-step-tag-pill">Alerta</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-trouble-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-trouble-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-trouble-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-trouble-remove" title="Eliminar este hito">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<div class="tca-grid-3">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Área / Departamento</label>
									<input type="text" name="corporate_trouble_department[]" value="" class="widefat" placeholder="Ej. Comunicación" required />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono del Hito</label>
									<select name="corporate_trouble_icon[]" class="widefat">
										<?php foreach ( $icon_options as $val => $lbl ) : ?>
											<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $lbl ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta de Alerta</label>
									<input type="text" name="corporate_trouble_tag[]" value="Nueva Alerta" class="widefat" placeholder="Ej. Parálisis Inicial" />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nivel de Severidad</label>
									<select name="corporate_trouble_tag_class[]" class="widefat">
										<option value="alert-tag">Alerta (Naranja/Rojo suave)</option>
										<option value="critical-tag">Crítica (Rojo intenso)</option>
									</select>
								</div>
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Hito (Compartido: Stepper + Tarjeta 3D)</label>
								<input type="text" name="corporate_trouble_title[]" value="" class="widefat" placeholder="Ej. Título del hito..." required />
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Extendida (Cuerpo de la Tarjeta)</label>
								<textarea name="corporate_trouble_description[]" rows="3" class="widefat" placeholder="Detalle exhaustivo de lo que ocurre en esta fase..."></textarea>
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Puntos Clave (.points-slideshow de la tarjeta)</label>
								<textarea name="corporate_trouble_points[]" rows="3" class="widefat" placeholder="Escribe un punto clave por línea..."></textarea>
								<p class="tca-field-desc">Escribe cada viñeta en una línea separada. Se rotarán automáticamente en el carrusel de la tarjeta.</p>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>

	</div>

	<!-- JavaScript for Trouble Repeater -->
	<script>
	(function() {
		const repeater = document.getElementById('trouble-steps-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-trouble-steps-list');
		const addBtn = repeater.querySelector('.tca-add-trouble-step-btn');
		const template = document.getElementById('tca-trouble-step-template');
		const collapseAllBtn = document.querySelector('.tca-collapse-all-btn');
		const expandAllBtn = document.querySelector('.tca-expand-all-btn');

		function updateBadges() {
			list.querySelectorAll('.tca-step-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-step-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Fase ' + numStr;
				}
			});
		}

		if (collapseAllBtn) {
			collapseAllBtn.addEventListener('click', function() {
				list.querySelectorAll('.tca-card-body').forEach(b => b.classList.add('is-collapsed'));
			});
		}
		if (expandAllBtn) {
			expandAllBtn.addEventListener('click', function() {
				list.querySelectorAll('.tca-card-body').forEach(b => b.classList.remove('is-collapsed'));
			});
		}

		// Add new step
		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_trouble_department[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		// Delegate row controls
		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-step-card');
			if (!card) return;

			if (e.target.closest('.tca-trouble-remove')) {
				if (list.querySelectorAll('.tca-step-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar este hito de la crisis?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos un hito en la línea de tiempo.');
				}
			} else if (e.target.closest('.tca-trouble-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-trouble-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-trouble-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-trouble-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		// Live update summary header on typing
		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-step-card');
			if (!card) return;

			const deptInput = card.querySelector('input[name="corporate_trouble_department[]"]');
			const titleInput = card.querySelector('input[name="corporate_trouble_title[]"]');
			const tagInput = card.querySelector('input[name="corporate_trouble_tag[]"]');

			const summary = card.querySelector('.tca-step-summary');
			const tagPill = card.querySelector('.tca-step-tag-pill');

			if (summary && deptInput && titleInput) {
				const d = deptInput.value.trim() || 'Área';
				const t = titleInput.value.trim() || 'Título del hito';
				summary.textContent = d + ' — ' + t;
			}
			if (tagPill && tagInput) {
				tagPill.textContent = tagInput.value.trim() || 'Alerta';
			}
		});

		// Drag & Drop
		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-step-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Hearings Meta Box in WordPress Admin
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_hearings_metabox( $post ) {
	wp_nonce_field( 'corporate_hearings_metabox_save', 'corporate_hearings_nonce' );

	$data = thecrisisacademy_get_hearings_data( $post->ID );
	?>
	<div class="tca-metabox-wrapper tca-hearings-metabox">
		<style>
			.tca-hearings-metabox .tca-accordion-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 12px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-hearings-metabox .tca-accordion-card:hover {
				border-color: #94a3b8;
			}
			.tca-hearings-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-hearings-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-hearings-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-hearings-metabox .tca-area-badge {
				background: #0284c7;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
				letter-spacing: 0.5px;
			}
			.tca-hearings-metabox .tca-area-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-hearings-metabox .tca-radar-pill {
				background: #e0f2fe;
				color: #0369a1;
				font-size: 11px;
				padding: 2px 8px;
				border-radius: 12px;
				font-weight: 700;
			}
			.tca-hearings-metabox .tca-card-head-actions {
				display: flex;
				align-items: center;
				gap: 6px;
			}
			.tca-hearings-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 14px;
			}
			.tca-hearings-metabox .tca-card-body.is-collapsed {
				display: none;
			}
			.tca-hearings-metabox .tca-grid-3 {
				display: grid;
				grid-template-columns: 1fr 1.5fr 1fr 1fr;
				gap: 12px;
			}
			@media (max-width: 960px) {
				.tca-hearings-metabox .tca-grid-3 {
					grid-template-columns: 1fr 1fr;
				}
			}
			@media (max-width: 600px) {
				.tca-hearings-metabox .tca-grid-3 {
					grid-template-columns: 1fr;
				}
			}
		</style>

		<!-- 1. Preheading -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_hearings_preheading">Subtítulo Superior (Pretext Reveal)</label>
			<input type="text" id="tca_hearings_preheading" name="corporate_hearings[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="large-text" placeholder="Ej. Impacto 360°" />
			<p class="tca-field-desc">Texto pequeño con efecto de animación sobre el título principal.</p>
		</div>

		<!-- 2. Main Title -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_hearings_title">Título Principal (Title Reveal)</label>
			<textarea id="tca_hearings_title" name="corporate_hearings[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
			<p class="tca-field-desc">Título de la sección (soporta <code>&lt;br&gt;</code>, <code>&lt;em&gt;</code>, <code>&lt;strong&gt;</code>).</p>
		</div>

		<!-- 3. Repeater: Áreas de Impacto (.accordion-interactive-list) -->
		<div class="tca-field-group">
			<label class="tca-field-title">Áreas de Impacto (.accordion-interactive-list — Repetidor)</label>
			<p class="tca-field-desc">Cada elemento alimenta una pestaña interactiva del acordeón y su cuadrante sincronizado en el radar 360°.</p>

			<div class="tca-repeater-container" id="hearings-accordion-repeater">
				<div class="tca-hearings-list">
					<?php foreach ( $data['items'] as $idx => $item ) : 
						$item_num = sprintf( '%02d', $idx + 1 );
					?>
						<div class="tca-accordion-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-area-badge">Área <?php echo esc_html( $item_num ); ?></span>
									<span class="tca-area-summary"><?php echo esc_html( $item['title'] . ( ! empty( $item['badge'] ) ? ' • ' . $item['badge'] : '' ) ); ?></span>
									<span class="tca-radar-pill"><?php echo esc_html( $item['radar_label'] ?: 'RADAR' ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-hearings-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-hearings-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-hearings-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-hearings-remove" title="Eliminar este elemento">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<div class="tca-grid-3">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Radar (Cuadrante)</label>
										<input type="text" name="corporate_hearings_radar_label[]" value="<?php echo esc_attr( $item['radar_label'] ); ?>" class="widefat" placeholder="Ej. COMMS, RH, OPS..." required />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Área (Acordeón)</label>
										<input type="text" name="corporate_hearings_title[]" value="<?php echo esc_attr( $item['title'] ); ?>" class="widefat" placeholder="Ej. COMMS / PR" required />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Distintivo / Subtítulo</label>
										<input type="text" name="corporate_hearings_badge[]" value="<?php echo esc_attr( $item['badge'] ); ?>" class="widefat" placeholder="Ej. Primera Línea" />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Identificador (Slug)</label>
										<input type="text" name="corporate_hearings_slug[]" value="<?php echo esc_attr( $item['slug'] ); ?>" class="widefat" placeholder="Ej. comms" />
									</div>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible</label>
									<textarea name="corporate_hearings_description[]" rows="3" class="widefat" placeholder="Texto descriptivo que aparece al abrir el acordeón..."><?php echo esc_textarea( $item['description'] ); ?></textarea>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-hearings-btn" style="margin-top:10px;">+ Añadir Nueva Área de Impacto</button>

				<!-- Template for new row -->
				<template id="tca-hearings-template">
					<div class="tca-accordion-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<span class="tca-area-badge">Nueva Área</span>
								<span class="tca-area-summary">Nueva Área — Completa los campos</span>
								<span class="tca-radar-pill">RADAR</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-hearings-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-hearings-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-hearings-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-hearings-remove" title="Eliminar este elemento">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<div class="tca-grid-3">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Radar (Cuadrante)</label>
									<input type="text" name="corporate_hearings_radar_label[]" value="" class="widefat" placeholder="Ej. LEGAL" required />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Área (Acordeón)</label>
									<input type="text" name="corporate_hearings_title[]" value="" class="widefat" placeholder="Ej. LEGAL & COMPLIANCE" required />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Distintivo / Subtítulo</label>
									<input type="text" name="corporate_hearings_badge[]" value="" class="widefat" placeholder="Ej. Blindaje" />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Identificador (Slug)</label>
									<input type="text" name="corporate_hearings_slug[]" value="" class="widefat" placeholder="Ej. legal" />
								</div>
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible</label>
								<textarea name="corporate_hearings_description[]" rows="3" class="widefat" placeholder="Texto descriptivo que aparece al abrir el acordeón..."></textarea>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>

	</div>

	<!-- JavaScript for Hearings Repeater -->
	<script>
	(function() {
		const repeater = document.getElementById('hearings-accordion-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-hearings-list');
		const addBtn = repeater.querySelector('.tca-add-hearings-btn');
		const template = document.getElementById('tca-hearings-template');

		function updateBadges() {
			list.querySelectorAll('.tca-accordion-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-area-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Área ' + numStr;
				}
			});
		}

		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_hearings_radar_label[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-accordion-card');
			if (!card) return;

			if (e.target.closest('.tca-hearings-remove')) {
				if (list.querySelectorAll('.tca-accordion-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar esta área de impacto?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos un área en el acordeón.');
				}
			} else if (e.target.closest('.tca-hearings-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-hearings-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card, card.nextElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-hearings-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-hearings-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-accordion-card');
			if (!card) return;

			const radarInput = card.querySelector('input[name="corporate_hearings_radar_label[]"]');
			const titleInput = card.querySelector('input[name="corporate_hearings_title[]"]');
			const badgeInput = card.querySelector('input[name="corporate_hearings_badge[]"]');

			const summary = card.querySelector('.tca-area-summary');
			const radarPill = card.querySelector('.tca-radar-pill');

			if (summary && titleInput) {
				const t = titleInput.value.trim() || 'Área';
				const b = badgeInput && badgeInput.value.trim() ? ' • ' + badgeInput.value.trim() : '';
				summary.textContent = t + b;
			}
			if (radarPill && radarInput) {
				radarPill.textContent = radarInput.value.trim() || 'RADAR';
			}
		});

		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-accordion-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Founder Meta Box in WordPress Admin
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_founder_metabox( $post ) {
	wp_nonce_field( 'corporate_founder_metabox_save', 'corporate_founder_nonce' );

	$data         = thecrisisacademy_get_founder_data( $post->ID );
	$icon_options = thecrisisacademy_get_founder_icon_options();
	?>
	<div class="tca-metabox-wrapper tca-founder-metabox">
		<style>
			.tca-founder-metabox .tca-method-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 12px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-founder-metabox .tca-method-card:hover {
				border-color: #94a3b8;
			}
			.tca-founder-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-founder-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-founder-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-founder-metabox .tca-method-badge {
				background: #475569;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
			}
			.tca-founder-metabox .tca-method-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-founder-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 14px;
			}
			.tca-founder-metabox .tca-card-body.is-collapsed {
				display: none;
			}
			.tca-founder-photo-row {
				display: flex;
				gap: 18px;
				align-items: flex-start;
			}
			.tca-founder-photo-preview-wrap {
				width: 100px;
				height: 100px;
				border-radius: 8px;
				overflow: hidden;
				border: 1px solid #cbd5e1;
				background: #f1f5f9;
				flex-shrink: 0;
			}
			.tca-founder-photo-preview-wrap img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
			.tca-founder-photo-inputs {
				flex: 1;
			}
		</style>

		<!-- 1. Portrait Photo -->
		<div class="tca-field-group">
			<label class="tca-field-title">Fotografía del Retrato (.founder-photo)</label>
			<div class="tca-founder-photo-row">
				<div class="tca-founder-photo-preview-wrap">
					<img id="tca-founder-photo-preview" src="<?php echo esc_url( $data['photo_url'] ); ?>" alt="Preview" />
				</div>
				<div class="tca-founder-photo-inputs">
					<div style="display:flex; gap:8px; margin-bottom:8px;">
						<input type="text" id="tca_founder_photo_url" name="corporate_founder[photo_url]" value="<?php echo esc_attr( $data['photo_url'] ); ?>" class="regular-text" style="flex:1;" placeholder="https://..." />
						<button type="button" class="button" id="tca-founder-upload-photo-btn">Subir / Seleccionar Imagen</button>
					</div>
					<div>
						<label for="tca_founder_photo_alt" style="display:block; font-size:12px; margin-bottom:4px;">Texto Alternativo (Alt)</label>
						<input type="text" id="tca_founder_photo_alt" name="corporate_founder[photo_alt]" value="<?php echo esc_attr( $data['photo_alt'] ); ?>" class="large-text" placeholder="Ej. Carolina Eslava - Fundadora" />
					</div>
				</div>
			</div>
		</div>

		<!-- 2. Profile Details -->
		<div class="tca-field-group">
			<div class="tca-grid-3" style="grid-template-columns: 1fr 1fr 1.5fr;">
				<div>
					<label class="tca-field-title" for="tca_founder_preheading">Subtítulo Superior</label>
					<input type="text" id="tca_founder_preheading" name="corporate_founder[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Ej. Liderazgo Académico" />
				</div>
				<div>
					<label class="tca-field-title" for="tca_founder_name">Nombre de la Fundadora</label>
					<input type="text" id="tca_founder_name" name="corporate_founder[name]" value="<?php echo esc_attr( $data['name'] ); ?>" class="widefat" placeholder="Ej. Carolina Eslava" required />
				</div>
				<div>
					<label class="tca-field-title" for="tca_founder_role">Cargo / Rol</label>
					<input type="text" id="tca_founder_role" name="corporate_founder[role]" value="<?php echo esc_attr( $data['role'] ); ?>" class="widefat" placeholder="Ej. Fundadora & Directora de The Crisis Academy" />
				</div>
			</div>
		</div>

		<!-- 3. Quote Card -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_founder_quote">Cita / Declaración de Trayectoria (.card-quote)</label>
			<textarea id="tca_founder_quote" name="corporate_founder[quote]" rows="3" class="large-text" placeholder="Texto destacado de trayectoria..."><?php echo esc_textarea( $data['quote'] ); ?></textarea>
			<p class="tca-field-desc">Aparece en la tarjeta con ícono de comillas y decorado lateral.</p>
		</div>

		<!-- 4. Methodology Title & Repeater -->
		<div class="tca-field-group">
			<label class="tca-field-title" for="tca_founder_method_title">Título del Bloque de Metodología</label>
			<input type="text" id="tca_founder_method_title" name="corporate_founder[methodology_title]" value="<?php echo esc_attr( $data['methodology_title'] ); ?>" class="large-text" placeholder="Ej. Metodología Basada en Investigación Científica" />
			<p class="tca-field-desc">Encabezado sobre las pestañas de investigación científica.</p>

			<div style="margin-top:16px;">
				<label class="tca-field-title">Pilares de Metodología (.accordion-interactive-list — Repetidor)</label>
				<p class="tca-field-desc">Pestañas interactivas con íconos que detallan los fundamentos científicos y de aprendizaje.</p>

				<div class="tca-repeater-container" id="founder-methodology-repeater">
					<div class="tca-methodology-list">
						<?php foreach ( $data['methodology_items'] as $idx => $m_item ) : 
							$m_num = sprintf( '%02d', $idx + 1 );
						?>
							<div class="tca-method-card" draggable="true">
								<div class="tca-card-head">
									<div class="tca-card-head-left">
										<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
										<span class="tca-method-badge">Pilar <?php echo esc_html( $m_num ); ?></span>
										<span class="tca-method-summary"><?php echo esc_html( $m_item['title'] ); ?></span>
									</div>
									<div class="tca-card-head-actions">
										<button type="button" class="tca-btn-icon tca-method-up" title="Mover arriba">&uarr;</button>
										<button type="button" class="tca-btn-icon tca-method-down" title="Mover abajo">&darr;</button>
										<button type="button" class="tca-btn-icon tca-method-toggle" title="Expandir/Colapsar">&#9660;</button>
										<button type="button" class="tca-btn-icon tca-btn-delete tca-method-remove" title="Eliminar este pilar">&times;</button>
									</div>
								</div>

								<div class="tca-card-body">
									<div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:12px;">
										<div>
											<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
											<select name="corporate_founder_method_icon[]" class="widefat">
												<?php foreach ( $icon_options as $val => $lbl ) : ?>
													<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m_item['icon'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div>
											<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Pilar</label>
											<input type="text" name="corporate_founder_method_title[]" value="<?php echo esc_attr( $m_item['title'] ); ?>" class="widefat" placeholder="Ej. Ian Mitroff, Timothy Coombs & William Benoit" required />
										</div>
									</div>

									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible</label>
										<textarea name="corporate_founder_method_desc[]" rows="2" class="widefat" placeholder="Descripción de la metodología..."><?php echo esc_textarea( $m_item['description'] ); ?></textarea>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<button type="button" class="button button-secondary tca-add-method-btn" style="margin-top:10px;">+ Añadir Nuevo Pilar Metodológico</button>

					<!-- Row template -->
					<template id="tca-method-template">
						<div class="tca-method-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-method-badge">Nuevo Pilar</span>
									<span class="tca-method-summary">Nuevo Pilar — Completa los campos</span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-method-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-method-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-method-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-method-remove" title="Eliminar este pilar">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:12px;">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
										<select name="corporate_founder_method_icon[]" class="widefat">
											<?php foreach ( $icon_options as $val => $lbl ) : ?>
												<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $lbl ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Pilar</label>
										<input type="text" name="corporate_founder_method_title[]" value="" class="widefat" placeholder="Ej. Título del pilar..." required />
									</div>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible</label>
									<textarea name="corporate_founder_method_desc[]" rows="2" class="widefat" placeholder="Descripción de la metodología..."></textarea>
								</div>
							</div>
						</div>
					</template>
				</div>
			</div>
		</div>

		<!-- 5. Big Callout Stat -->
		<div class="tca-field-group">
			<label class="tca-field-title">Cifra de Impacto Destacada (.founder-counter)</label>
			<div class="tca-grid-2">
				<div>
					<label for="tca_founder_stat_number" style="display:block; font-size:12px; margin-bottom:4px;">Número / Métrica</label>
					<input type="text" id="tca_founder_stat_number" name="corporate_founder[stat_number]" value="<?php echo esc_attr( $data['stat_number'] ); ?>" class="widefat" placeholder="Ej. +2,000" />
				</div>
				<div>
					<label for="tca_founder_stat_label" style="display:block; font-size:12px; margin-bottom:4px;">Etiqueta / Leyenda</label>
					<input type="text" id="tca_founder_stat_label" name="corporate_founder[stat_label]" value="<?php echo esc_attr( $data['stat_label'] ); ?>" class="widefat" placeholder="Ej. Ejecutivos entrenados bajo simulación..." />
				</div>
			</div>
		</div>

	</div>

	<!-- JavaScript for Founder Section & Media Library -->
	<script>
	(function() {
		// Media Uploader
		const uploadBtn = document.getElementById('tca-founder-upload-photo-btn');
		if (uploadBtn) {
			uploadBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (typeof wp === 'undefined' || !wp.media) {
					alert('La biblioteca de medios no está disponible.');
					return;
				}
				const customUploader = wp.media({
					title: 'Seleccionar Foto de la Fundadora',
					button: { text: 'Usar esta foto' },
					multiple: false
				}).on('select', function() {
					const attachment = customUploader.state().get('selection').first().toJSON();
					document.getElementById('tca_founder_photo_url').value = attachment.url;
					const preview = document.getElementById('tca-founder-photo-preview');
					if (preview) preview.src = attachment.url;
					const altInput = document.getElementById('tca_founder_photo_alt');
					if (altInput && !altInput.value && attachment.alt) {
						altInput.value = attachment.alt;
					}
				}).open();
			});
		}

		// Live Photo URL Preview
		const photoInput = document.getElementById('tca_founder_photo_url');
		if (photoInput) {
			photoInput.addEventListener('input', function() {
				const preview = document.getElementById('tca-founder-photo-preview');
				if (preview) preview.src = this.value;
			});
		}

		// Methodology Repeater
		const repeater = document.getElementById('founder-methodology-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-methodology-list');
		const addBtn = repeater.querySelector('.tca-add-method-btn');
		const template = document.getElementById('tca-method-template');

		function updateBadges() {
			list.querySelectorAll('.tca-method-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-method-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Pilar ' + numStr;
				}
			});
		}

		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_founder_method_title[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-method-card');
			if (!card) return;

			if (e.target.closest('.tca-method-remove')) {
				if (list.querySelectorAll('.tca-method-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar este pilar?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos un pilar metodológico.');
				}
			} else if (e.target.closest('.tca-method-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-method-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-method-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-method-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-method-card');
			if (!card) return;

			const titleInput = card.querySelector('input[name="corporate_founder_method_title[]"]');
			const summary = card.querySelector('.tca-method-summary');

			if (summary && titleInput) {
				summary.textContent = titleInput.value.trim() || 'Nuevo Pilar';
			}
		});

		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-method-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for Program Section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_program_metabox( $post ) {
	wp_nonce_field( 'corporate_program_metabox_save', 'corporate_program_nonce' );

	$data         = thecrisisacademy_get_program_data( $post->ID );
	$icon_options = thecrisisacademy_get_program_icon_options();
	?>
	<div class="tca-metabox-wrapper tca-program-metabox">
		<style>
			.tca-program-metabox .tca-program-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 14px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-program-metabox .tca-program-card:hover {
				border-color: #94a3b8;
			}
			.tca-program-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-program-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-program-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-program-metabox .tca-step-badge {
				background: #1e293b;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
				flex-shrink: 0;
			}
			.tca-program-metabox .tca-step-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-program-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 14px;
			}
			.tca-program-metabox .tca-card-body.is-collapsed {
				display: none;
			}
			.tca-step-photo-row {
				display: flex;
				gap: 16px;
				align-items: flex-start;
			}
			.tca-step-photo-preview-wrap {
				width: 120px;
				height: 68px;
				border-radius: 6px;
				overflow: hidden;
				border: 1px solid #cbd5e1;
				background: #0f172a;
				flex-shrink: 0;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.tca-step-photo-preview-wrap img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
			.tca-step-photo-inputs {
				flex: 1;
			}
		</style>

		<!-- 1. Section Header -->
		<div class="tca-field-group">
			<div class="tca-grid-2">
				<div>
					<label class="tca-field-title" for="tca_program_preheading">Subtítulo Superior (.sub-heading)</label>
					<input type="text" id="tca_program_preheading" name="corporate_program[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Crisis Readiness Program™" />
				</div>
				<div>
					<label class="tca-field-title" for="tca_program_title">Título Principal (.title-section)</label>
					<input type="text" id="tca_program_title" name="corporate_program[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="No desarrollamos conocimientos.<br><span class=&quot;color&quot;>Desarrollamos capacidad institucional.</span>" />
					<p class="tca-field-desc">Admite etiquetas HTML como <code>&lt;br&gt;</code> y <code>&lt;span class="color"&gt;</code>.</p>
				</div>
			</div>
		</div>

		<!-- 2. Before / After Comparison Cards -->
		<div class="tca-field-group">
			<label class="tca-field-title">Comparación de Estados (Antes / Después del Programa)</label>
			<div class="tca-grid-2" style="gap:20px;">
				<!-- Before State -->
				<div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:6px; padding:14px;">
					<h4 style="margin:0 0 10px 0; color:#9f1239; font-size:13px; display:flex; align-items:center; gap:6px;">
						<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#e11d48;"></span>
						Estado Anterior (.state-program--before)
					</h4>
					<div style="margin-bottom:10px;">
						<label for="tca_program_before_label" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta de la Pestaña</label>
						<input type="text" id="tca_program_before_label" name="corporate_program[before_label]" value="<?php echo esc_attr( $data['before_label'] ); ?>" class="widefat" placeholder="Antes del Programa" />
					</div>
					<div style="margin-bottom:10px;">
						<label for="tca_program_before_title" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Estado</label>
						<input type="text" id="tca_program_before_title" name="corporate_program[before_title]" value="<?php echo esc_attr( $data['before_title'] ); ?>" class="widefat" placeholder="No existe protocolo" />
					</div>
					<div>
						<label for="tca_program_before_description" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción</label>
						<textarea id="tca_program_before_description" name="corporate_program[before_description]" rows="3" class="widefat" placeholder="Falta de gobernanza clara..."><?php echo esc_textarea( $data['before_description'] ); ?></textarea>
					</div>
				</div>

				<!-- After State -->
				<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:14px;">
					<h4 style="margin:0 0 10px 0; color:#166534; font-size:13px; display:flex; align-items:center; gap:6px;">
						<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e;"></span>
						Estado Posterior (.state-program--after)
					</h4>
					<div style="margin-bottom:10px;">
						<label for="tca_program_after_label" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta de la Pestaña</label>
						<input type="text" id="tca_program_after_label" name="corporate_program[after_label]" value="<?php echo esc_attr( $data['after_label'] ); ?>" class="widefat" placeholder="Después del Programa" />
					</div>
					<div style="margin-bottom:10px;">
						<label for="tca_program_after_title" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Estado</label>
						<input type="text" id="tca_program_after_title" name="corporate_program[after_title]" value="<?php echo esc_attr( $data['after_title'] ); ?>" class="widefat" placeholder="La organización responde como un solo equipo" />
					</div>
					<div>
						<label for="tca_program_after_description" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción</label>
						<textarea id="tca_program_after_description" name="corporate_program[after_description]" rows="3" class="widefat" placeholder="Toma de decisiones coordinada..."><?php echo esc_textarea( $data['after_description'] ); ?></textarea>
					</div>
				</div>
			</div>
		</div>

		<!-- 3. Process Steps Repeater (.process-steps-track) -->
		<div class="tca-field-group">
			<label class="tca-field-title">Fases del Proceso (.process-steps-track — Repetidor del Carrusel)</label>
			<p class="tca-field-desc">Cada fase alimenta una diapositiva del carrusel con su fotografía de fondo, ícono decorativo, subtítulo, título y descripción detallada.</p>

			<div class="tca-repeater-container" id="program-steps-repeater">
				<div class="tca-program-steps-list">
					<?php foreach ( $data['steps'] as $idx => $step ) : 
						$phase_num = sprintf( '%02d', $idx + 1 );
					?>
						<div class="tca-program-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-step-badge">Fase <?php echo esc_html( $phase_num ); ?></span>
									<span class="tca-step-summary"><?php echo esc_html( ! empty( $step['short_title'] ) ? $step['short_title'] : $step['title'] ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-step-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-step-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-step-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-step-remove" title="Eliminar esta fase">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<!-- Media Photo Row -->
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Imagen de Fondo (.metric-bg-photo)</label>
									<div class="tca-step-photo-row">
										<div class="tca-step-photo-preview-wrap">
											<img class="tca-step-photo-preview" src="<?php echo esc_url( $step['photo_url'] ); ?>" alt="Preview" />
										</div>
										<div class="tca-step-photo-inputs">
											<div style="display:flex; gap:8px; margin-bottom:8px;">
												<input type="text" name="corporate_program_step_photo_url[]" value="<?php echo esc_attr( $step['photo_url'] ); ?>" class="regular-text" style="flex:1;" placeholder="https://..." />
												<button type="button" class="button tca-step-upload-photo-btn">Subir / Seleccionar Imagen</button>
											</div>
											<div>
												<input type="text" name="corporate_program_step_photo_alt[]" value="<?php echo esc_attr( $step['photo_alt'] ); ?>" class="large-text" placeholder="Texto descriptivo (Alt de la imagen)" />
											</div>
										</div>
									</div>
								</div>

								<!-- Icon & Short Title -->
								<div style="display:grid; grid-template-columns: 1fr 2fr; gap:12px;">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono de la Fase</label>
										<select name="corporate_program_step_icon[]" class="widefat">
											<?php foreach ( $icon_options as $val => $lbl ) : ?>
												<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $step['icon'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Subtítulo Corto (.metric-slide-sub-title)</label>
										<input type="text" name="corporate_program_step_short_title[]" value="<?php echo esc_attr( $step['short_title'] ); ?>" class="widefat" placeholder="Ej. Investigación y estudios de crisis" required />
									</div>
								</div>

								<!-- Full Slide Title -->
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título Completo de la Fase (.process-step-header h3)</label>
									<input type="text" name="corporate_program_step_title[]" value="<?php echo esc_attr( $step['title'] ); ?>" class="widefat" placeholder="Ej. Investigación y estudios de crisis. Radar de riesgos: tendencias 2026..." required />
								</div>

								<!-- Description -->
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Detallada (.process-step-content p)</label>
									<textarea name="corporate_program_step_desc[]" rows="2" class="widefat" placeholder="Descripción de los contenidos y objetivos de esta fase..."><?php echo esc_textarea( $step['description'] ); ?></textarea>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-step-btn" style="margin-top:10px;">+ Añadir Nueva Fase al Proceso</button>

				<!-- Step Template for Dynamic JS Addition -->
				<template id="tca-program-step-template">
					<div class="tca-program-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<span class="tca-step-badge">Nueva Fase</span>
								<span class="tca-step-summary">Nueva Fase — Completa los campos</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-step-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-step-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-step-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-step-remove" title="Eliminar esta fase">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<!-- Media Photo Row -->
							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Imagen de Fondo (.metric-bg-photo)</label>
								<div class="tca-step-photo-row">
									<div class="tca-step-photo-preview-wrap">
										<img class="tca-step-photo-preview" src="" alt="Preview" />
									</div>
									<div class="tca-step-photo-inputs">
										<div style="display:flex; gap:8px; margin-bottom:8px;">
											<input type="text" name="corporate_program_step_photo_url[]" value="" class="regular-text" style="flex:1;" placeholder="https://..." />
											<button type="button" class="button tca-step-upload-photo-btn">Subir / Seleccionar Imagen</button>
										</div>
										<div>
											<input type="text" name="corporate_program_step_photo_alt[]" value="" class="large-text" placeholder="Texto descriptivo (Alt de la imagen)" />
										</div>
									</div>
								</div>
							</div>

							<!-- Icon & Short Title -->
							<div style="display:grid; grid-template-columns: 1fr 2fr; gap:12px;">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono de la Fase</label>
									<select name="corporate_program_step_icon[]" class="widefat">
										<?php foreach ( $icon_options as $val => $lbl ) : ?>
											<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $lbl ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Subtítulo Corto (.metric-slide-sub-title)</label>
									<input type="text" name="corporate_program_step_short_title[]" value="" class="widefat" placeholder="Ej. Título corto para badge..." required />
								</div>
							</div>

							<!-- Full Slide Title -->
							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título Completo de la Fase (.process-step-header h3)</label>
								<input type="text" name="corporate_program_step_title[]" value="" class="widefat" placeholder="Ej. Título completo de la fase..." required />
							</div>

							<!-- Description -->
							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Detallada (.process-step-content p)</label>
								<textarea name="corporate_program_step_desc[]" rows="2" class="widefat" placeholder="Descripción de los contenidos y objetivos de esta fase..."></textarea>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>

	<!-- JavaScript for Program Section -->
	<script>
	(function() {
		const repeater = document.getElementById('program-steps-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-program-steps-list');
		const addBtn = repeater.querySelector('.tca-add-step-btn');
		const template = document.getElementById('tca-program-step-template');

		function updateBadges() {
			list.querySelectorAll('.tca-program-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-step-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Fase ' + numStr;
				}
			});
		}

		// Media Uploader click handler
		repeater.addEventListener('click', function(e) {
			const uploadBtn = e.target.closest('.tca-step-upload-photo-btn');
			if (!uploadBtn) return;
			e.preventDefault();

			if (typeof wp === 'undefined' || !wp.media) {
				alert('La biblioteca de medios no está disponible.');
				return;
			}

			const card = uploadBtn.closest('.tca-program-card');
			if (!card) return;

			const urlInput = card.querySelector('input[name="corporate_program_step_photo_url[]"]');
			const altInput = card.querySelector('input[name="corporate_program_step_photo_alt[]"]');
			const preview = card.querySelector('.tca-step-photo-preview');

			const customUploader = wp.media({
				title: 'Seleccionar Imagen de la Fase',
				button: { text: 'Usar esta imagen' },
				multiple: false
			}).on('select', function() {
				const attachment = customUploader.state().get('selection').first().toJSON();
				if (urlInput) urlInput.value = attachment.url;
				if (preview) preview.src = attachment.url;
				if (altInput && !altInput.value && attachment.alt) {
					altInput.value = attachment.alt;
				}
			}).open();
		});

		// Live Photo URL & Title update
		repeater.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-program-card');
			if (!card) return;

			if (e.target.name === 'corporate_program_step_photo_url[]') {
				const preview = card.querySelector('.tca-step-photo-preview');
				if (preview) preview.src = e.target.value;
			}

			if (e.target.name === 'corporate_program_step_short_title[]' || e.target.name === 'corporate_program_step_title[]') {
				const shortInput = card.querySelector('input[name="corporate_program_step_short_title[]"]');
				const fullInput = card.querySelector('input[name="corporate_program_step_title[]"]');
				const summary = card.querySelector('.tca-step-summary');
				const val = (shortInput && shortInput.value.trim()) || (fullInput && fullInput.value.trim()) || 'Nueva Fase';
				if (summary) summary.textContent = val;
			}
		});

		// Add Step
		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_program_step_short_title[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		// Card actions (remove, up, down, toggle)
		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-program-card');
			if (!card) return;

			if (e.target.closest('.tca-step-remove')) {
				if (list.querySelectorAll('.tca-program-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar esta fase?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos una fase en el proceso.');
				}
			} else if (e.target.closest('.tca-step-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-step-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-step-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-step-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		// Drag & Drop
		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-program-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for Simulation Section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_simulation_metabox( $post ) {
	wp_nonce_field( 'corporate_simulation_metabox_save', 'corporate_simulation_nonce' );

	$data         = thecrisisacademy_get_simulation_data( $post->ID );
	$icon_options = thecrisisacademy_get_simulation_icon_options();
	?>
	<div class="tca-metabox-wrapper tca-simulation-metabox">
		<style>
			.tca-simulation-metabox .tca-sim-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 12px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-simulation-metabox .tca-sim-card:hover {
				border-color: #94a3b8;
			}
			.tca-simulation-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-simulation-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-simulation-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-simulation-metabox .tca-sim-badge {
				background: #e11d48;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
				flex-shrink: 0;
			}
			.tca-simulation-metabox .tca-sim-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-simulation-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 12px;
			}
			.tca-simulation-metabox .tca-card-body.is-collapsed {
				display: none;
			}
		</style>

		<!-- 1. Section Header (Columna Izquierda) -->
		<div class="tca-field-group">
			<label class="tca-field-title">Encabezado y Mensaje de Introducción (Columna Izquierda)</label>
			<div class="tca-grid-2" style="margin-bottom:12px;">
				<div>
					<label for="tca_sim_preheading" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Subtítulo Superior (.sub-heading)</label>
					<input type="text" id="tca_sim_preheading" name="corporate_sim[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Entrenamiento Inmersivo" />
				</div>
				<div>
					<label for="tca_sim_title" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título Principal (.title-section)</label>
					<input type="text" id="tca_sim_title" name="corporate_sim[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Antes de comenzar,<br><span class=&quot;color&quot;>casi siempre encontramos los mismos problemas.</span>" />
					<p class="tca-field-desc">Admite <code>&lt;br&gt;</code> y <code>&lt;span class="color"&gt;</code>.</p>
				</div>
			</div>
			<div>
				<label for="tca_sim_description" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Párrafo Descriptivo (.simulation-intro-desc)</label>
				<textarea id="tca_sim_description" name="corporate_sim[description]" rows="3" class="widefat" placeholder="Las organizaciones suelen creer que están preparadas..."><?php echo esc_textarea( $data['description'] ); ?></textarea>
			</div>
		</div>

		<!-- 2. Botón CTA del Simulador -->
		<div class="tca-field-group">
			<label class="tca-field-title">Botón de Llamada a la Acción (.cta-wrapper .btn)</label>
			<div class="tca-grid-3">
				<div>
					<label for="tca_sim_cta_text" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Texto del Botón</label>
					<input type="text" id="tca_sim_cta_text" name="corporate_sim[cta_text]" value="<?php echo esc_attr( $data['cta_text'] ); ?>" class="widefat" placeholder="Probar Simulador de Crisis" />
				</div>
				<div>
					<label for="tca_sim_cta_url" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Enlace (URL)</label>
					<input type="text" id="tca_sim_cta_url" name="corporate_sim[cta_url]" value="<?php echo esc_attr( $data['cta_url'] ); ?>" class="widefat" placeholder="<?php echo esc_attr( site_url() . '/simulador-de-crisis/' ); ?>" />
				</div>
				<div>
					<label for="tca_sim_cta_lightbox" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">ID Lightbox (data-open-lightbox)</label>
					<input type="text" id="tca_sim_cta_lightbox" name="corporate_sim[cta_lightbox]" value="<?php echo esc_attr( $data['cta_lightbox'] ); ?>" class="widefat" placeholder="crisis-simulator" />
				</div>
			</div>
		</div>

		<!-- 3. Ventana de Auditoría y Acordeón (Columna Derecha) -->
		<div class="tca-field-group">
			<label class="tca-field-title">Ventana de Auditoría (.vulnerabilities-panel)</label>
			<div style="margin-bottom:16px;">
				<label for="tca_sim_panel_title" style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Barra Safari (.panel-title)</label>
				<input type="text" id="tca_sim_panel_title" name="corporate_sim[panel_title]" value="<?php echo esc_attr( $data['panel_title'] ); ?>" class="widefat" placeholder="Auditoria de seguridad" />
			</div>

			<label class="tca-field-title">Puntos de Vulnerabilidad (.accordion-interactive-list — Repetidor)</label>
			<p class="tca-field-desc">Puntos críticos o hallazgos detectados en la auditoría que se despliegan interactivamente al hacer clic.</p>

			<div class="tca-repeater-container" id="simulation-accordion-repeater">
				<div class="tca-sim-list">
					<?php foreach ( $data['items'] as $idx => $sim_item ) : 
						$item_num = sprintf( '%02d', $idx + 1 );
					?>
						<div class="tca-sim-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-sim-badge">Hallazgo <?php echo esc_html( $item_num ); ?></span>
									<span class="tca-sim-summary"><?php echo esc_html( $sim_item['title'] ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-sim-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-sim-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-sim-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-sim-remove" title="Eliminar este hallazgo">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<div style="display:grid; grid-template-columns: 1fr 3fr; gap:12px;">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
										<select name="corporate_sim_item_icon[]" class="widefat">
											<?php foreach ( $icon_options as $val => $lbl ) : ?>
												<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $sim_item['icon'] ?? 'x', $val ); ?>><?php echo esc_html( $lbl ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Hallazgo (.accordion-main h3)</label>
										<input type="text" name="corporate_sim_item_title[]" value="<?php echo esc_attr( $sim_item['title'] ); ?>" class="widefat" placeholder="Ej. No existe un protocolo compartido" required />
									</div>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible (.accordion-expandable p)</label>
									<textarea name="corporate_sim_item_desc[]" rows="2" class="widefat" placeholder="Explicación detallada del problema o vulnerabilidad..."><?php echo esc_textarea( $sim_item['description'] ); ?></textarea>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-sim-btn" style="margin-top:10px;">+ Añadir Nuevo Punto de Vulnerabilidad</button>

				<!-- Template for new row -->
				<template id="tca-sim-template">
					<div class="tca-sim-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<span class="tca-sim-badge">Nuevo Hallazgo</span>
								<span class="tca-sim-summary">Nuevo Hallazgo — Completa los campos</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-sim-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-sim-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-sim-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-sim-remove" title="Eliminar este hallazgo">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<div style="display:grid; grid-template-columns: 1fr 3fr; gap:12px;">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
									<select name="corporate_sim_item_icon[]" class="widefat">
										<?php foreach ( $icon_options as $val => $lbl ) : ?>
											<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $lbl ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título del Hallazgo (.accordion-main h3)</label>
									<input type="text" name="corporate_sim_item_title[]" value="" class="widefat" placeholder="Ej. Título del hallazgo..." required />
								</div>
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Expandible (.accordion-expandable p)</label>
								<textarea name="corporate_sim_item_desc[]" rows="2" class="widefat" placeholder="Explicación detallada del problema o vulnerabilidad..."></textarea>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>

	<!-- JavaScript for Simulation Section -->
	<script>
	(function() {
		const repeater = document.getElementById('simulation-accordion-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-sim-list');
		const addBtn = repeater.querySelector('.tca-add-sim-btn');
		const template = document.getElementById('tca-sim-template');

		function updateBadges() {
			list.querySelectorAll('.tca-sim-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-sim-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Hallazgo ' + numStr;
				}
			});
		}

		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_sim_item_title[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-sim-card');
			if (!card) return;

			if (e.target.closest('.tca-sim-remove')) {
				if (list.querySelectorAll('.tca-sim-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar este hallazgo?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos un hallazgo en la lista.');
				}
			} else if (e.target.closest('.tca-sim-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-sim-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-sim-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-sim-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-sim-card');
			if (!card) return;

			const titleInput = card.querySelector('input[name="corporate_sim_item_title[]"]');
			const summary = card.querySelector('.tca-sim-summary');

			if (summary && titleInput) {
				summary.textContent = titleInput.value.trim() || 'Nuevo Hallazgo';
			}
		});

		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-sim-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for Diff (Why Us) Section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_diff_metabox( $post ) {
	wp_nonce_field( 'corporate_diff_metabox_save', 'corporate_diff_nonce' );

	$data         = thecrisisacademy_get_diff_data( $post->ID );
	$icon_options = thecrisisacademy_get_diff_icon_options();
	?>
	<div class="tca-metabox-wrapper tca-diff-metabox">
		<style>
			.tca-diff-metabox .tca-diff-card {
				background: #ffffff;
				border: 1px solid #cbd5e1;
				border-radius: 6px;
				margin-bottom: 12px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.03);
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}
			.tca-diff-metabox .tca-diff-card:hover {
				border-color: #94a3b8;
			}
			.tca-diff-metabox .tca-card-head {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 10px 14px;
				background: #f8fafc;
				border-bottom: 1px solid #e2e8f0;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				cursor: pointer;
				user-select: none;
			}
			.tca-diff-metabox .tca-card-head-left {
				display: flex;
				align-items: center;
				gap: 10px;
				flex: 1;
				overflow: hidden;
			}
			.tca-diff-metabox .tca-drag-grip {
				cursor: grab;
				color: #94a3b8;
				font-size: 16px;
				line-height: 1;
			}
			.tca-diff-metabox .tca-diff-badge {
				background: #0284c7;
				color: #ffffff;
				font-size: 11px;
				font-weight: 700;
				padding: 2px 7px;
				border-radius: 4px;
				flex-shrink: 0;
			}
			.tca-diff-metabox .tca-diff-summary {
				font-weight: 600;
				color: #1e293b;
				font-size: 13px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.tca-diff-metabox .tca-card-body {
				padding: 16px;
				display: flex;
				flex-direction: column;
				gap: 12px;
			}
			.tca-diff-metabox .tca-card-body.is-collapsed {
				display: none;
			}
		</style>

		<!-- 1. Section Header -->
		<div class="tca-field-group">
			<div class="tca-grid-3" style="grid-template-columns: 1fr 2fr 1fr;">
				<div>
					<label class="tca-field-title" for="tca_diff_preheading">Subtítulo Superior (.sub-heading)</label>
					<input type="text" id="tca_diff_preheading" name="corporate_diff[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Por qué nosotros" />
				</div>
				<div>
					<label class="tca-field-title" for="tca_diff_title">Título Principal (.title-section)</label>
					<input type="text" id="tca_diff_title" name="corporate_diff[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Por qué organizaciones líderes<br><span class=&quot;color&quot;>trabajan con nosotros</span>" />
					<p class="tca-field-desc">Admite <code>&lt;br&gt;</code> y <code>&lt;span class="color"&gt;</code>.</p>
				</div>
				<div>
					<label class="tca-field-title" for="tca_diff_autoplay">Autoplay (ms)</label>
					<input type="number" id="tca_diff_autoplay" name="corporate_diff[autoplay]" value="<?php echo esc_attr( $data['autoplay'] ); ?>" class="widefat" placeholder="14000" step="500" min="2000" />
					<p class="tca-field-desc">Tiempo de rotación 3D (14000 = 14s).</p>
				</div>
			</div>
		</div>

		<!-- 2. Slides Repeater (.diff-slide-item) -->
		<div class="tca-field-group">
			<label class="tca-field-title">Diapositivas de Diferenciación (.diff-slide-item — Repetidor 3D Flip)</label>
			<p class="tca-field-desc">Tarjetas de valor y ventajas competitivas mostradas con efecto de giro 3D.</p>

			<div class="tca-repeater-container" id="diff-slides-repeater">
				<div class="tca-diff-list">
					<?php foreach ( $data['slides'] as $idx => $slide ) : 
						$slide_num = sprintf( '%02d', $idx + 1 );
					?>
						<div class="tca-diff-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<span class="tca-diff-badge">Tarjeta <?php echo esc_html( $slide_num ); ?></span>
									<span class="tca-diff-summary"><?php echo esc_html( $slide['title'] ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-diff-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-diff-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-diff-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-diff-remove" title="Eliminar esta tarjeta">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:12px;">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
										<select name="corporate_diff_slide_icon[]" class="widefat">
											<?php foreach ( $icon_options as $val => $lbl ) : ?>
												<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $slide['icon'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Ventaja (h3)</label>
										<input type="text" name="corporate_diff_slide_title[]" value="<?php echo esc_attr( $slide['title'] ); ?>" class="widefat" placeholder="Ej. Investigación aplicada" required />
									</div>
								</div>

								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Explicativa (p)</label>
									<textarea name="corporate_diff_slide_desc[]" rows="2" class="widefat" placeholder="Explicación concisa del valor diferenciador..."><?php echo esc_textarea( $slide['description'] ); ?></textarea>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-diff-btn" style="margin-top:10px;">+ Añadir Nueva Tarjeta Diferenciadora</button>

				<!-- Template for new row -->
				<template id="tca-diff-template">
					<div class="tca-diff-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<span class="tca-diff-badge">Nueva Tarjeta</span>
								<span class="tca-diff-summary">Nueva Tarjeta — Completa los campos</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-diff-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-diff-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-diff-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-diff-remove" title="Eliminar esta tarjeta">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:12px;">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono</label>
									<select name="corporate_diff_slide_icon[]" class="widefat">
										<?php foreach ( $icon_options as $val => $lbl ) : ?>
											<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $lbl ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Ventaja (h3)</label>
									<input type="text" name="corporate_diff_slide_title[]" value="" class="widefat" placeholder="Ej. Título de la tarjeta..." required />
								</div>
							</div>

							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción Explicativa (p)</label>
								<textarea name="corporate_diff_slide_desc[]" rows="2" class="widefat" placeholder="Explicación concisa del valor diferenciador..."></textarea>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>

	<!-- JavaScript for Diff Section -->
	<script>
	(function() {
		const repeater = document.getElementById('diff-slides-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-diff-list');
		const addBtn = repeater.querySelector('.tca-add-diff-btn');
		const template = document.getElementById('tca-diff-template');

		function updateBadges() {
			list.querySelectorAll('.tca-diff-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-diff-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Tarjeta ' + numStr;
				}
			});
		}

		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('input[name="corporate_diff_slide_title[]"]');
			if (input) input.focus();
			bindCardEvents();
		});

		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-diff-card');
			if (!card) return;

			if (e.target.closest('.tca-diff-remove')) {
				if (list.querySelectorAll('.tca-diff-card').length > 1) {
					if (confirm('¿Seguro que deseas eliminar esta tarjeta?')) {
						card.remove();
						updateBadges();
					}
				} else {
					alert('Debe haber al menos una tarjeta en la lista.');
				}
			} else if (e.target.closest('.tca-diff-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-diff-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-diff-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-diff-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			}
		});

		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-diff-card');
			if (!card) return;

			const titleInput = card.querySelector('input[name="corporate_diff_slide_title[]"]');
			const summary = card.querySelector('.tca-diff-summary');

			if (summary && titleInput) {
				summary.textContent = titleInput.value.trim() || 'Nueva Tarjeta';
			}
		});

		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-diff-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for Testimonies section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_testimonies_metabox( $post ) {
	wp_nonce_field( 'corporate_testimonies_metabox_save', 'corporate_testimonies_nonce' );

	$data        = thecrisisacademy_get_testimonies_data( $post->ID );
	$preheading  = $data['preheading'];
	$title       = $data['title'];
	$items       = $data['items'];
	$default_svg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="%2394a3b8"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
	?>
	<div class="tca-metabox-wrapper">
		<!-- Section Header Fields -->
		<div class="tca-field-row">
			<label class="tca-label" for="corporate_testimonies_preheading">Pre-encabezado (Sub-heading)</label>
			<input type="text" id="corporate_testimonies_preheading" name="corporate_testimonies[preheading]" value="<?php echo esc_attr( $preheading ); ?>" class="widefat" placeholder="Ej. Testimonios" />
		</div>

		<div class="tca-field-row">
			<label class="tca-label" for="corporate_testimonies_title">Título Principal de la Sección (h2)</label>
			<input type="text" id="corporate_testimonies_title" name="corporate_testimonies[title]" value="<?php echo esc_attr( $title ); ?>" class="widefat" placeholder="Ej. Voces de líderes que ya..." />
			<p class="description">Se admiten etiquetas como <code>&lt;br&gt;</code>, <code>&lt;span class="color"&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>.</p>
		</div>

		<div style="background:#eff6ff; border-left:4px solid #3b82f6; padding:12px 16px; margin:20px 0; border-radius:0 4px 4px 0;">
			<p style="margin:0; font-size:13px; color:#1e3a8a; line-height:1.5;">
				💡 <strong>Requisito del diseño:</strong> Esta sección requiere un <strong>mínimo de 7 testimonios</strong> para garantizar el flujo continuo y simétrico del carrusel 3D (coverflow). Puedes agregar todos los testimonios adicionales que desees. La foto seleccionada pertenece al perfil del testigo tanto para la fila superior de avatares como para la tarjeta.
			</p>
		</div>

		<!-- Testimonies Repeater -->
		<div class="tca-field-row" style="margin-top:20px;">
			<label class="tca-label" style="font-size:14px; margin-bottom:10px; display:block;">
				👥 Lista de Testimonios y Testigos (Repetidor - Mínimo 7 requeridos)
			</label>

			<div id="testimonies-repeater">
				<div class="tca-testi-list">
					<?php foreach ( $items as $idx => $item ) :
						$photo_url = ! empty( $item['avatar_url'] ) ? $item['avatar_url'] : '';
						$photo_alt = ! empty( $item['avatar_alt'] ) ? $item['avatar_alt'] : '';
						$name      = ! empty( $item['name'] ) ? $item['name'] : '';
						$role      = ! empty( $item['role'] ) ? $item['role'] : '';
						$text      = ! empty( $item['text'] ) ? $item['text'] : '';
						$num_str   = sprintf( '%02d', $idx + 1 );
						$preview   = $photo_url ? $photo_url : $default_svg;
					?>
						<div class="tca-testi-card" draggable="true">
							<div class="tca-card-head">
								<div class="tca-card-head-left">
									<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
									<img src="<?php echo esc_url( $preview ); ?>" class="tca-testi-thumb-preview" alt="Preview" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid #cbd5e1; margin-right:8px; display:inline-block; vertical-align:middle;">
									<span class="tca-testi-badge">Testimonio <?php echo esc_html( $num_str ); ?></span>
									<span class="tca-testi-summary"><?php echo esc_html( $name . ( $role ? ' — ' . $role : '' ) ); ?></span>
								</div>
								<div class="tca-card-head-actions">
									<button type="button" class="tca-btn-icon tca-testi-up" title="Mover arriba">&uarr;</button>
									<button type="button" class="tca-btn-icon tca-testi-down" title="Mover abajo">&darr;</button>
									<button type="button" class="tca-btn-icon tca-testi-toggle" title="Expandir/Colapsar">&#9660;</button>
									<button type="button" class="tca-btn-icon tca-btn-delete tca-testi-remove" title="Eliminar este testimonio">&times;</button>
								</div>
							</div>

							<div class="tca-card-body">
								<!-- Avatar Selection -->
								<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; margin-bottom:12px;">
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">Foto / Avatar del Testigo</label>
									<div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
										<img src="<?php echo esc_url( $preview ); ?>" class="tca-testi-photo-preview" alt="Preview" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0; background:#ffffff;">
										<div>
											<input type="hidden" name="corporate_testi_avatar_url[]" class="tca-testi-photo-url-input" value="<?php echo esc_url( $photo_url ); ?>" />
											<button type="button" class="button button-secondary tca-testi-upload-photo-btn">📷 Seleccionar / Cambiar Foto</button>
											<button type="button" class="button button-link-delete tca-testi-remove-photo-btn" style="margin-left:8px; <?php echo empty( $photo_url ) ? 'display:none;' : ''; ?>">Quitar Foto</button>
											<div style="margin-top:6px;">
												<input type="text" name="corporate_testi_avatar_alt[]" class="tca-testi-photo-alt-input" value="<?php echo esc_attr( $photo_alt ); ?>" placeholder="Texto alternativo (alt)..." style="width:260px; font-size:12px;" />
											</div>
										</div>
									</div>
								</div>

								<!-- Name & Role -->
								<div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:12px; margin-bottom:12px;">
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nombre del Testigo</label>
										<input type="text" name="corporate_testi_name[]" value="<?php echo esc_attr( $name ); ?>" class="widefat tca-testi-name-input" placeholder="Ej. Alejandro Ruiz" required />
									</div>
									<div>
										<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Cargo y Empresa</label>
										<input type="text" name="corporate_testi_role[]" value="<?php echo esc_attr( $role ); ?>" class="widefat tca-testi-role-input" placeholder="Ej. Director de Reputación Corporativa — Grupo Financiero" />
									</div>
								</div>

								<!-- Quote -->
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Cita o Testimonio</label>
									<textarea name="corporate_testi_text[]" rows="3" class="widefat" placeholder="Cita testimonial del líder participante..."><?php echo esc_textarea( $text ); ?></textarea>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="button button-secondary tca-add-testi-btn" style="margin-top:10px;">+ Añadir Nuevo Testimonio</button>

				<!-- Template for new row -->
				<template id="tca-testi-template">
					<div class="tca-testi-card" draggable="true">
						<div class="tca-card-head">
							<div class="tca-card-head-left">
								<span class="tca-drag-grip" title="Arrastrar para reordenar">&#9776;</span>
								<img src="<?php echo esc_url( $default_svg ); ?>" class="tca-testi-thumb-preview" alt="Preview" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid #cbd5e1; margin-right:8px; display:inline-block; vertical-align:middle;">
								<span class="tca-testi-badge">Nuevo Testimonio</span>
								<span class="tca-testi-summary">Nuevo Testimonio — Completa los campos</span>
							</div>
							<div class="tca-card-head-actions">
								<button type="button" class="tca-btn-icon tca-testi-up" title="Mover arriba">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-testi-down" title="Mover abajo">&darr;</button>
								<button type="button" class="tca-btn-icon tca-testi-toggle" title="Expandir/Colapsar">&#9660;</button>
								<button type="button" class="tca-btn-icon tca-btn-delete tca-testi-remove" title="Eliminar este testimonio">&times;</button>
							</div>
						</div>

						<div class="tca-card-body">
							<!-- Avatar Selection -->
							<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; margin-bottom:12px;">
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">Foto / Avatar del Testigo</label>
								<div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
									<img src="<?php echo esc_url( $default_svg ); ?>" class="tca-testi-photo-preview" alt="Preview" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0; background:#ffffff;">
									<div>
										<input type="hidden" name="corporate_testi_avatar_url[]" class="tca-testi-photo-url-input" value="" />
										<button type="button" class="button button-secondary tca-testi-upload-photo-btn">📷 Seleccionar / Cambiar Foto</button>
										<button type="button" class="button button-link-delete tca-testi-remove-photo-btn" style="margin-left:8px; display:none;">Quitar Foto</button>
										<div style="margin-top:6px;">
											<input type="text" name="corporate_testi_avatar_alt[]" class="tca-testi-photo-alt-input" value="" placeholder="Texto alternativo (alt)..." style="width:260px; font-size:12px;" />
										</div>
									</div>
								</div>
							</div>

							<!-- Name & Role -->
							<div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:12px; margin-bottom:12px;">
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nombre del Testigo</label>
									<input type="text" name="corporate_testi_name[]" value="" class="widefat tca-testi-name-input" placeholder="Ej. Nombre del Testigo" required />
								</div>
								<div>
									<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Cargo y Empresa</label>
									<input type="text" name="corporate_testi_role[]" value="" class="widefat tca-testi-role-input" placeholder="Ej. Cargo — Organización" />
								</div>
							</div>

							<!-- Quote -->
							<div>
								<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Cita o Testimonio</label>
								<textarea name="corporate_testi_text[]" rows="3" class="widefat" placeholder="Cita testimonial del líder participante..."></textarea>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>

	<!-- JavaScript for Testimonies Section -->
	<script>
	(function() {
		const repeater = document.getElementById('testimonies-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-testi-list');
		const addBtn = repeater.querySelector('.tca-add-testi-btn');
		const template = document.getElementById('tca-testi-template');
		const defaultSvg = <?php echo wp_json_encode( $default_svg ); ?>;

		function updateBadges() {
			list.querySelectorAll('.tca-testi-card').forEach((card, i) => {
				const badge = card.querySelector('.tca-testi-badge');
				if (badge) {
					const numStr = (i + 1).toString().padStart(2, '0');
					badge.textContent = 'Testimonio ' + numStr;
				}
			});
		}

		addBtn.addEventListener('click', function() {
			const clone = template.content.cloneNode(true);
			list.appendChild(clone);
			updateBadges();
			const newCard = list.lastElementChild;
			const input = newCard.querySelector('.tca-testi-name-input');
			if (input) input.focus();
			bindCardEvents();
		});

		list.addEventListener('click', function(e) {
			const card = e.target.closest('.tca-testi-card');
			if (!card) return;

			// Remove item (Strict minimum 7 check)
			if (e.target.closest('.tca-testi-remove')) {
				const totalCards = list.querySelectorAll('.tca-testi-card').length;
				if (totalCards <= 7) {
					alert('La sección de testimonios requiere un mínimo de 7 testimonios para mantener la simetría y funcionamiento del carrusel coverflow 3D. No es posible eliminar más elementos.');
					return;
				}

				if (confirm('¿Seguro que deseas eliminar este testimonio?')) {
					card.remove();
					updateBadges();
				}
			} else if (e.target.closest('.tca-testi-up')) {
				if (card.previousElementSibling) {
					list.insertBefore(card, card.previousElementSibling);
					updateBadges();
				}
			} else if (e.target.closest('.tca-testi-down')) {
				if (card.nextElementSibling) {
					list.insertBefore(card.nextElementSibling, card);
					updateBadges();
				}
			} else if (e.target.closest('.tca-testi-toggle') || e.target.closest('.tca-card-head')) {
				if (!e.target.closest('.tca-card-head-actions') || e.target.closest('.tca-testi-toggle')) {
					const body = card.querySelector('.tca-card-body');
					if (body) {
						body.classList.toggle('is-collapsed');
					}
				}
			} else if (e.target.closest('.tca-testi-upload-photo-btn')) {
				e.preventDefault();
				if (typeof wp === 'undefined' || !wp.media) {
					alert('La biblioteca de medios no está disponible.');
					return;
				}

				const urlInput = card.querySelector('.tca-testi-photo-url-input');
				const altInput = card.querySelector('.tca-testi-photo-alt-input');
				const preview = card.querySelector('.tca-testi-photo-preview');
				const thumb = card.querySelector('.tca-testi-thumb-preview');
				const removeBtn = card.querySelector('.tca-testi-remove-photo-btn');

				const customUploader = wp.media({
					title: 'Seleccionar Foto del Testigo',
					button: { text: 'Usar esta foto' },
					multiple: false,
					library: { type: 'image' }
				}).on('select', function() {
					const attachment = customUploader.state().get('selection').first().toJSON();
					if (urlInput) urlInput.value = attachment.url;
					if (preview) preview.src = attachment.url;
					if (thumb) thumb.src = attachment.url;
					if (removeBtn) removeBtn.style.display = 'inline-block';
					if (altInput && !altInput.value && (attachment.alt || attachment.title)) {
						altInput.value = attachment.alt || attachment.title;
					}
				}).open();
			} else if (e.target.closest('.tca-testi-remove-photo-btn')) {
				e.preventDefault();
				const urlInput = card.querySelector('.tca-testi-photo-url-input');
				const preview = card.querySelector('.tca-testi-photo-preview');
				const thumb = card.querySelector('.tca-testi-thumb-preview');
				const removeBtn = card.querySelector('.tca-testi-remove-photo-btn');

				if (urlInput) urlInput.value = '';
				if (preview) preview.src = defaultSvg;
				if (thumb) thumb.src = defaultSvg;
				if (removeBtn) removeBtn.style.display = 'none';
			}
		});

		list.addEventListener('input', function(e) {
			const card = e.target.closest('.tca-testi-card');
			if (!card) return;

			const nameInput = card.querySelector('.tca-testi-name-input');
			const roleInput = card.querySelector('.tca-testi-role-input');
			const summary = card.querySelector('.tca-testi-summary');

			if (summary && nameInput) {
				const nameVal = nameInput.value.trim();
				const roleVal = roleInput ? roleInput.value.trim() : '';
				summary.textContent = nameVal ? (nameVal + (roleVal ? ' — ' + roleVal : '')) : 'Nuevo Testimonio';
			}
		});

		let draggedCard = null;
		function bindCardEvents() {
			list.querySelectorAll('.tca-testi-card').forEach(card => {
				card.ondragstart = function(e) {
					draggedCard = card;
					e.dataTransfer.effectAllowed = 'move';
					card.style.opacity = '0.5';
				};
				card.ondragend = function() {
					if (draggedCard) draggedCard.style.opacity = '1';
					draggedCard = null;
					updateBadges();
				};
				card.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				card.ondrop = function(e) {
					e.preventDefault();
					if (draggedCard && draggedCard !== card) {
						const allCards = Array.from(list.children);
						const fromIndex = allCards.indexOf(draggedCard);
						const toIndex = allCards.indexOf(card);
						if (fromIndex < toIndex) {
							list.insertBefore(draggedCard, card.nextSibling);
						} else {
							list.insertBefore(draggedCard, card);
						}
						updateBadges();
					}
				};
			});
		}

		bindCardEvents();
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for Thought section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_thought_metabox( $post ) {
	wp_nonce_field( 'corporate_thought_metabox_save', 'corporate_thought_nonce' );

	$data         = thecrisisacademy_get_thought_data( $post->ID );
	$icon_options = thecrisisacademy_get_thought_icon_options();
	?>
	<div class="tca-metabox-wrapper">
		<!-- Section Header Fields -->
		<div class="tca-field-row">
			<label class="tca-label" for="corporate_thought_preheading">Pre-encabezado (Sub-heading)</label>
			<input type="text" id="corporate_thought_preheading" name="corporate_thought[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Ej. Criterios de Idoneidad" />
		</div>

		<div class="tca-field-row">
			<label class="tca-label" for="corporate_thought_title">Título Principal de la Sección (h2)</label>
			<input type="text" id="corporate_thought_title" name="corporate_thought[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Ej. ¿Es este programa para tu organización?" />
			<p class="description">Se admiten etiquetas como <code>&lt;br&gt;</code>, <code>&lt;span class="color"&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>.</p>
		</div>

		<div class="tca-field-row">
			<label class="tca-label" for="corporate_thought_description">Descripción del Encabezado (p)</label>
			<textarea id="corporate_thought_description" name="corporate_thought[description]" rows="2" class="widefat" placeholder="Ej. Diseñado para liderazgos corporativos..."><?php echo esc_textarea( $data['description'] ); ?></textarea>
		</div>

		<!-- 3 Columns Configuration Grid -->
		<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 20px; margin-top:25px;">
			
			<!-- Column 1: Perfiles -->
			<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
				<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
					<span style="font-size:18px;">👥</span>
					<strong style="font-size:14px; color:#0f172a;">Columna 1: Perfiles</strong>
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Superior (Tag)</label>
					<input type="text" name="corporate_thought[profiles_tag]" value="<?php echo esc_attr( $data['profiles_tag'] ); ?>" class="widefat" placeholder="Ej. Audiencia" />
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Columna</label>
					<input type="text" name="corporate_thought[profiles_title]" value="<?php echo esc_attr( $data['profiles_title'] ); ?>" class="widefat" placeholder="Ej. Perfiles" />
				</div>

				<div style="margin-bottom:15px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono de la Columna</label>
					<select name="corporate_thought[profiles_icon]" class="widefat">
						<?php foreach ( $icon_options as $key => $lbl ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $data['profiles_icon'], $key ); ?>><?php echo esc_html( $lbl ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">Items de Perfiles (Repetidor)</label>
				<div id="thought-profiles-repeater">
					<div class="tca-thought-profiles-list">
						<?php foreach ( $data['profiles_items'] as $p_item ) : ?>
							<div class="tca-thought-item" draggable="true" style="display:flex; align-items:center; gap:6px; padding:6px 8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
								<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
								<input type="text" name="corporate_thought_profiles[]" value="<?php echo esc_attr( $p_item ); ?>" class="widefat" style="flex:1;" placeholder="Ej. Empresas con más de 200 colaboradores." required />
								<button type="button" class="tca-btn-icon tca-thought-p-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-thought-p-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
								<button type="button" class="tca-btn-icon tca-thought-p-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button button-secondary tca-add-profile-btn" style="margin-top:6px; width:100%;">+ Añadir Perfil</button>
				</div>
			</div>

			<!-- Column 2: Sectores Clave -->
			<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
				<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
					<span style="font-size:18px;">🏭</span>
					<strong style="font-size:14px; color:#0f172a;">Columna 2: Sectores Clave</strong>
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Superior (Tag)</label>
					<input type="text" name="corporate_thought[sectors_tag]" value="<?php echo esc_attr( $data['sectors_tag'] ); ?>" class="widefat" placeholder="Ej. Industrias" />
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Columna</label>
					<input type="text" name="corporate_thought[sectors_title]" value="<?php echo esc_attr( $data['sectors_title'] ); ?>" class="widefat" placeholder="Ej. Sectores Clave" />
				</div>

				<div style="margin-bottom:15px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono de la Columna</label>
					<select name="corporate_thought[sectors_icon]" class="widefat">
						<?php foreach ( $icon_options as $key => $lbl ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $data['sectors_icon'], $key ); ?>><?php echo esc_html( $lbl ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<label style="display:block; font-size:12px; font-weight:600; margin-bottom:8px;">Sectores (Repetidor - Numerado Automático)</label>
				<div id="thought-sectors-repeater">
					<div class="tca-thought-sectors-list">
						<?php foreach ( $data['sectors_items'] as $s_idx => $s_item ) : ?>
							<div class="tca-thought-item" draggable="true" style="display:flex; align-items:center; gap:6px; padding:6px 8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
								<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
								<span class="tca-sector-num-badge" style="display:inline-block; min-width:24px; font-size:11px; font-weight:700; color:#64748b;"><?php echo sprintf( '%02d', $s_idx + 1 ); ?></span>
								<input type="text" name="corporate_thought_sectors[]" value="<?php echo esc_attr( $s_item ); ?>" class="widefat" style="flex:1;" placeholder="Ej. Manufactura" required />
								<button type="button" class="tca-btn-icon tca-thought-s-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
								<button type="button" class="tca-btn-icon tca-thought-s-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
								<button type="button" class="tca-btn-icon tca-thought-s-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button button-secondary tca-add-sector-btn" style="margin-top:6px; width:100%;">+ Añadir Sector</button>
				</div>
			</div>

			<!-- Column 3: Manifiesto -->
			<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
				<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
					<span style="font-size:18px;">⭐</span>
					<strong style="font-size:14px; color:#0f172a;">Columna 3: Propósito / Manifiesto</strong>
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Superior (Tag)</label>
					<input type="text" name="corporate_thought[manifesto_tag]" value="<?php echo esc_attr( $data['manifesto_tag'] ); ?>" class="widefat" placeholder="Ej. Declaración" />
				</div>

				<div style="margin-bottom:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de la Columna</label>
					<input type="text" name="corporate_thought[manifesto_title]" value="<?php echo esc_attr( $data['manifesto_title'] ); ?>" class="widefat" placeholder="Ej. Propósito" />
				</div>

				<div style="margin-bottom:15px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Ícono de la Columna</label>
					<select name="corporate_thought[manifesto_icon]" class="widefat">
						<?php foreach ( $icon_options as $key => $lbl ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $data['manifesto_icon'], $key ); ?>><?php echo esc_html( $lbl ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div style="margin-bottom:12px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Texto Secundario (Lead)</label>
					<input type="text" name="corporate_thought[manifesto_lead]" value="<?php echo esc_attr( $data['manifesto_lead'] ); ?>" class="widefat" placeholder="Ej. No es un curso para aprender teoría" />
				</div>

				<div>
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Declaración Principal / Énfasis</label>
					<textarea name="corporate_thought[manifesto_statement]" rows="4" class="widefat" placeholder="Ej. Es un entrenamiento para organizaciones que <strong>no pueden improvisar</strong> cuando ocurre una crisis."><?php echo esc_textarea( $data['manifesto_statement'] ); ?></textarea>
					<p class="description">Puedes usar <code>&lt;strong&gt;</code> para resaltar palabras clave con el color primario.</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Templates for new items -->
	<template id="tca-thought-profile-template">
		<div class="tca-thought-item" draggable="true" style="display:flex; align-items:center; gap:6px; padding:6px 8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
			<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
			<input type="text" name="corporate_thought_profiles[]" value="" class="widefat" style="flex:1;" placeholder="Ej. Nuevo perfil..." required />
			<button type="button" class="tca-btn-icon tca-thought-p-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
			<button type="button" class="tca-btn-icon tca-thought-p-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
			<button type="button" class="tca-btn-icon tca-thought-p-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
		</div>
	</template>

	<template id="tca-thought-sector-template">
		<div class="tca-thought-item" draggable="true" style="display:flex; align-items:center; gap:6px; padding:6px 8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
			<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
			<span class="tca-sector-num-badge" style="display:inline-block; min-width:24px; font-size:11px; font-weight:700; color:#64748b;">01</span>
			<input type="text" name="corporate_thought_sectors[]" value="" class="widefat" style="flex:1;" placeholder="Ej. Nuevo sector..." required />
			<button type="button" class="tca-btn-icon tca-thought-s-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
			<button type="button" class="tca-btn-icon tca-thought-s-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
			<button type="button" class="tca-btn-icon tca-thought-s-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
		</div>
	</template>

	<!-- Script for Thought Section Repeaters -->
	<script>
	(function() {
		// 1. Profiles Repeater
		const pRepeater = document.getElementById('thought-profiles-repeater');
		if (pRepeater) {
			const pList = pRepeater.querySelector('.tca-thought-profiles-list');
			const pAddBtn = pRepeater.querySelector('.tca-add-profile-btn');
			const pTpl = document.getElementById('tca-thought-profile-template');

			pAddBtn.addEventListener('click', function() {
				const clone = pTpl.content.cloneNode(true);
				pList.appendChild(clone);
				const newInput = pList.lastElementChild.querySelector('input');
				if (newInput) newInput.focus();
				bindGenericDrag(pList);
			});

			pList.addEventListener('click', function(e) {
				const item = e.target.closest('.tca-thought-item');
				if (!item) return;

				if (e.target.closest('.tca-thought-p-remove')) {
					if (pList.querySelectorAll('.tca-thought-item').length > 1) {
						item.remove();
					} else {
						alert('Debe haber al menos un perfil en la lista.');
					}
				} else if (e.target.closest('.tca-thought-p-up')) {
					if (item.previousElementSibling) {
						pList.insertBefore(item, item.previousElementSibling);
					}
				} else if (e.target.closest('.tca-thought-p-down')) {
					if (item.nextElementSibling) {
						pList.insertBefore(item, item.nextElementSibling);
					}
				}
			});
			bindGenericDrag(pList);
		}

		// 2. Sectors Repeater
		const sRepeater = document.getElementById('thought-sectors-repeater');
		if (sRepeater) {
			const sList = sRepeater.querySelector('.tca-thought-sectors-list');
			const sAddBtn = sRepeater.querySelector('.tca-add-sector-btn');
			const sTpl = document.getElementById('tca-thought-sector-template');

			function updateSectorBadges() {
				sList.querySelectorAll('.tca-thought-item').forEach((item, idx) => {
					const badge = item.querySelector('.tca-sector-num-badge');
					if (badge) {
						badge.textContent = (idx + 1).toString().padStart(2, '0');
					}
				});
			}

			sAddBtn.addEventListener('click', function() {
				const clone = sTpl.content.cloneNode(true);
				sList.appendChild(clone);
				updateSectorBadges();
				const newInput = sList.lastElementChild.querySelector('input');
				if (newInput) newInput.focus();
				bindGenericDrag(sList, updateSectorBadges);
			});

			sList.addEventListener('click', function(e) {
				const item = e.target.closest('.tca-thought-item');
				if (!item) return;

				if (e.target.closest('.tca-thought-s-remove')) {
					if (sList.querySelectorAll('.tca-thought-item').length > 1) {
						item.remove();
						updateSectorBadges();
					} else {
						alert('Debe haber al menos un sector en la lista.');
					}
				} else if (e.target.closest('.tca-thought-s-up')) {
					if (item.previousElementSibling) {
						sList.insertBefore(item, item.previousElementSibling);
						updateSectorBadges();
					}
				} else if (e.target.closest('.tca-thought-s-down')) {
					if (item.nextElementSibling) {
						sList.insertBefore(item, item.nextElementSibling);
						updateSectorBadges();
					}
				}
			});
			bindGenericDrag(sList, updateSectorBadges);
		}

		function bindGenericDrag(container, onReorder) {
			container.querySelectorAll('.tca-thought-item').forEach(item => {
				item.ondragstart = function(e) {
					container._dragged = item;
					e.dataTransfer.effectAllowed = 'move';
					item.style.opacity = '0.5';
				};
				item.ondragend = function() {
					if (container._dragged) container._dragged.style.opacity = '1';
					container._dragged = null;
					if (onReorder) onReorder();
				};
				item.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				item.ondrop = function(e) {
					e.preventDefault();
					if (container._dragged && container._dragged !== item) {
						const allItems = Array.from(container.children);
						const fromIndex = allItems.indexOf(container._dragged);
						const toIndex = allItems.indexOf(item);
						if (fromIndex < toIndex) {
							container.insertBefore(container._dragged, item.nextSibling);
						} else {
							container.insertBefore(container._dragged, item);
						}
						if (onReorder) onReorder();
					}
				};
			});
		}
	})();
	</script>
	<?php
}

/**
 * Render Meta Box for CTA (WhatsApp) section
 *
 * @param WP_Post $post Post object
 */
function thecrisisacademy_render_cta_metabox( $post ) {
	wp_nonce_field( 'corporate_cta_metabox_save', 'corporate_cta_nonce' );

	$data = thecrisisacademy_get_cta_data( $post->ID );
	?>
	<div class="tca-metabox-wrapper">
		<!-- Section Header & Intro -->
		<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:20px;">
			<h4 style="margin:0 0 12px 0; font-size:14px; color:#0f172a;">📢 Textos de Introducción (Columna Izquierda)</h4>
			
			<div class="tca-field-row" style="margin-bottom:12px;">
				<label class="tca-label" for="corporate_cta_preheading">Pre-encabezado (Sub-heading)</label>
				<input type="text" id="corporate_cta_preheading" name="corporate_cta[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Ej. Tu reputación en las mejores manos" />
			</div>

			<div class="tca-field-row" style="margin-bottom:12px;">
				<label class="tca-label" for="corporate_cta_title">Título Principal de la Sección (h2)</label>
				<input type="text" id="corporate_cta_title" name="corporate_cta[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Ej. Anticípate, prepárate..." />
				<p class="description">Se admiten etiquetas como <code>&lt;br&gt;</code>, <code>&lt;span class="color"&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>.</p>
			</div>

			<div class="tca-field-row" style="margin-bottom:0;">
				<label class="tca-label" for="corporate_cta_description">Descripción del CTA (p)</label>
				<textarea id="corporate_cta_description" name="corporate_cta[description]" rows="3" class="widefat" placeholder="Ej. No dejes el futuro de tu organización al azar..."><?php echo esc_textarea( $data['description'] ); ?></textarea>
			</div>
		</div>

		<!-- Points Slideshow (Repeater) -->
		<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
			<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
				<span style="font-size:18px;">✨</span>
				<strong style="font-size:14px; color:#0f172a;">Puntos Clave del Slideshow (Columna Derecha - Repetidor)</strong>
			</div>
			<p class="description" style="margin-bottom:12px;">Estos puntos rotan automáticamente sobre el formulario como argumentos de confianza y propuesta de valor.</p>

			<div id="cta-points-repeater">
				<div class="tca-cta-points-list">
					<?php foreach ( $data['points'] as $pt_text ) : ?>
						<div class="tca-cta-point-item" draggable="true" style="display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
							<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
							<span style="color:#10b981; font-weight:bold;">✔</span>
							<input type="text" name="corporate_cta_points[]" value="<?php echo esc_attr( $pt_text ); ?>" class="widefat" style="flex:1;" placeholder="Ej. Atención personalizada para cada empresa" required />
							<button type="button" class="tca-btn-icon tca-cta-pt-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
							<button type="button" class="tca-btn-icon tca-cta-pt-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
							<button type="button" class="tca-btn-icon tca-cta-pt-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button button-secondary tca-add-cta-point-btn" style="margin-top:6px;">+ Añadir Punto Clave</button>
			</div>
		</div>

		<!-- WhatsApp Form & Action Settings Grid -->
		<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
			<!-- WhatsApp & Action Area -->
			<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
				<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
					<span style="font-size:18px;">📱</span>
					<strong style="font-size:14px; color:#0f172a;">Configuración de WhatsApp</strong>
				</div>

				<div style="margin-bottom:12px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Número de Teléfono WhatsApp (Con código de país)</label>
					<input type="text" name="corporate_cta[whatsapp_phone]" value="<?php echo esc_attr( $data['whatsapp_phone'] ); ?>" class="widefat" placeholder="Ej. 525543910088" required />
					<p class="description">Sin símbolo <code>+</code>, espacios ni guiones. Ej: <code>525543910088</code> para México.</p>
				</div>

				<div style="margin-bottom:12px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Texto del Botón de Envío</label>
					<input type="text" name="corporate_cta[button_text]" value="<?php echo esc_attr( $data['button_text'] ); ?>" class="widefat" placeholder="Ej. Enviar mensaje" />
				</div>

				<div>
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Microcopy de Confianza (debajo del botón)</label>
					<input type="text" name="corporate_cta[microcopy_text]" value="<?php echo esc_attr( $data['microcopy_text'] ); ?>" class="widefat" placeholder="Ej. Sin compromisos · Respuesta en menos de 24 h" />
				</div>
			</div>

			<!-- Form Labels & Success Message -->
			<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
				<div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
					<span style="font-size:18px;">✉️</span>
					<strong style="font-size:14px; color:#0f172a;">Etiquetas del Formulario y Mensaje de Éxito</strong>
				</div>

				<div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:12px;">
					<div>
						<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Nombre</label>
						<input type="text" name="corporate_cta[form_name_label]" value="<?php echo esc_attr( $data['form_name_label'] ); ?>" class="widefat" />
					</div>
					<div>
						<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Email</label>
						<input type="text" name="corporate_cta[form_email_label]" value="<?php echo esc_attr( $data['form_email_label'] ); ?>" class="widefat" />
					</div>
				</div>

				<div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:12px;">
					<div>
						<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Teléfono</label>
						<input type="text" name="corporate_cta[form_phone_label]" value="<?php echo esc_attr( $data['form_phone_label'] ); ?>" class="widefat" />
					</div>
					<div>
						<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Etiqueta Pregunta / Interés</label>
						<input type="text" name="corporate_cta[form_msg_label]" value="<?php echo esc_attr( $data['form_msg_label'] ); ?>" class="widefat" />
					</div>
				</div>

				<div style="border-top:1px solid #e2e8f0; padding-top:10px; margin-top:10px;">
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Título de Confirmación (Éxito)</label>
					<input type="text" name="corporate_cta[success_title]" value="<?php echo esc_attr( $data['success_title'] ); ?>" class="widefat" placeholder="Ej. ¡Preparado!" style="margin-bottom:8px;" />
					
					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Descripción de Confirmación</label>
					<input type="text" name="corporate_cta[success_desc]" value="<?php echo esc_attr( $data['success_desc'] ); ?>" class="widefat" placeholder="Ej. Se ha abierto WhatsApp con tu mensaje pre-cargado." style="margin-bottom:8px;" />

					<label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Botón para Reiniciar Formulario</label>
					<input type="text" name="corporate_cta[reset_btn_text]" value="<?php echo esc_attr( $data['reset_btn_text'] ); ?>" class="widefat" placeholder="Ej. Llenar nuevo formulario" />
				</div>
			</div>
		</div>
	</div>

	<!-- Template for new CTA point -->
	<template id="tca-cta-point-template">
		<div class="tca-cta-point-item" draggable="true" style="display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:6px;">
			<span class="tca-drag-grip" title="Arrastrar para reordenar" style="cursor:grab; color:#94a3b8; font-size:14px;">&#9776;</span>
			<span style="color:#10b981; font-weight:bold;">✔</span>
			<input type="text" name="corporate_cta_points[]" value="" class="widefat" style="flex:1;" placeholder="Ej. Nuevo punto clave..." required />
			<button type="button" class="tca-btn-icon tca-cta-pt-up" title="Mover arriba" style="border:none; background:transparent; cursor:pointer;">&uarr;</button>
			<button type="button" class="tca-btn-icon tca-cta-pt-down" title="Mover abajo" style="border:none; background:transparent; cursor:pointer;">&darr;</button>
			<button type="button" class="tca-btn-icon tca-cta-pt-remove" title="Eliminar" style="border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:16px;">&times;</button>
		</div>
	</template>

	<!-- Script for CTA Points Repeater -->
	<script>
	(function() {
		const repeater = document.getElementById('cta-points-repeater');
		if (!repeater) return;

		const list = repeater.querySelector('.tca-cta-points-list');
		const addBtn = repeater.querySelector('.tca-add-cta-point-btn');
		const tpl = document.getElementById('tca-cta-point-template');

		addBtn.addEventListener('click', function() {
			const clone = tpl.content.cloneNode(true);
			list.appendChild(clone);
			const newInput = list.lastElementChild.querySelector('input');
			if (newInput) newInput.focus();
			bindCtaPointsDrag();
		});

		list.addEventListener('click', function(e) {
			const item = e.target.closest('.tca-cta-point-item');
			if (!item) return;

			if (e.target.closest('.tca-cta-pt-remove')) {
				if (list.querySelectorAll('.tca-cta-point-item').length > 1) {
					item.remove();
				} else {
					alert('Debe haber al menos un punto clave en el slideshow.');
				}
			} else if (e.target.closest('.tca-cta-pt-up')) {
				if (item.previousElementSibling) {
					list.insertBefore(item, item.previousElementSibling);
				}
			} else if (e.target.closest('.tca-cta-pt-down')) {
				if (item.nextElementSibling) {
					list.insertBefore(item, item.nextElementSibling);
				}
			}
		});

		function bindCtaPointsDrag() {
			list.querySelectorAll('.tca-cta-point-item').forEach(item => {
				item.ondragstart = function(e) {
					list._dragged = item;
					e.dataTransfer.effectAllowed = 'move';
					item.style.opacity = '0.5';
				};
				item.ondragend = function() {
					if (list._dragged) list._dragged.style.opacity = '1';
					list._dragged = null;
				};
				item.ondragover = function(e) {
					e.preventDefault();
					e.dataTransfer.dropEffect = 'move';
				};
				item.ondrop = function(e) {
					e.preventDefault();
					if (list._dragged && list._dragged !== item) {
						const allItems = Array.from(list.children);
						const fromIndex = allItems.indexOf(list._dragged);
						const toIndex = allItems.indexOf(item);
						if (fromIndex < toIndex) {
							list.insertBefore(list._dragged, item.nextSibling);
						} else {
							list.insertBefore(list._dragged, item);
						}
					}
				};
			});
		}

		bindCtaPointsDrag();
	})();
	</script>
	<?php
}

/**
 * Render Upcoming Events Metabox for Corporate Template
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_upcoming_events_metabox( $post ) {
	wp_nonce_field( 'corporate_upcoming_events_metabox_save', 'corporate_upcoming_events_nonce' );
	$data = thecrisisacademy_get_upcoming_events_data( $post->ID );

	$today = current_time( 'Y-m-d' );
	$upcoming_events = get_posts( array(
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'meta_key'       => '_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => '_event_date',
				'value'   => $today,
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	) );
	$upcoming_count = count( $upcoming_events );

	$trashed_count = count( get_posts( array(
		'post_type'      => 'event',
		'post_status'    => 'trash',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) );
	?>
	<div class="tca-upcoming-events-metabox" style="padding:15px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,sans-serif;">
		<style>
			.tca-events-status-card {
				background: #ffffff;
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px 20px;
				margin-bottom: 24px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.05);
			}
			.tca-events-status-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 12px;
				padding-bottom: 12px;
				border-bottom: 1px solid #f1f5f9;
			}
			.tca-events-badge {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				font-size: 13px;
				font-weight: 700;
				padding: 4px 12px;
				border-radius: 20px;
			}
			.tca-events-badge--active {
				background: #dcfce7;
				color: #15803d;
			}
			.tca-events-badge--hidden {
				background: #fef3c7;
				color: #b45309;
			}
			.tca-events-actions {
				display: flex;
				gap: 10px;
			}
			.tca-events-actions .button {
				display: inline-flex;
				align-items: center;
				gap: 6px;
			}
			.tca-events-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 15px;
				font-size: 13px;
			}
			.tca-events-table th {
				text-align: left;
				padding: 8px 12px;
				background: #f8fafc;
				border-bottom: 2px solid #e2e8f0;
				color: #475569;
				font-weight: 600;
			}
			.tca-events-table td {
				padding: 10px 12px;
				border-bottom: 1px solid #f1f5f9;
				color: #1e293b;
			}
			.tca-events-table tr:hover td {
				background: #f8fafc;
			}
		</style>

		<!-- Status & CPT management summary -->
		<div class="tca-events-status-card">
			<div class="tca-events-status-header">
				<div>
					<?php if ( $upcoming_count > 0 ) : ?>
						<span class="tca-events-badge tca-events-badge--active">
							<span style="font-size:10px;">🟢</span> <?php printf( esc_html__( 'Sección Visible en la Web (%d eventos próximos programados)', 'thecrisisacademy' ), $upcoming_count ); ?>
						</span>
					<?php else : ?>
						<span class="tca-events-badge tca-events-badge--hidden">
							<span style="font-size:10px;">🟡</span> <?php esc_html_e( 'Sección Oculta Automáticamente (No hay eventos futuros programados)', 'thecrisisacademy' ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="tca-events-actions">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=event' ) ); ?>" class="button button-primary">
						<span class="dashicons dashicons-calendar-alt" style="margin-top:4px;"></span>
						<?php esc_html_e( 'Gestionar Todos los Eventos', 'thecrisisacademy' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=event' ) ); ?>" class="button">
						<span class="dashicons dashicons-plus-alt" style="margin-top:4px;"></span>
						<?php esc_html_e( 'Añadir Nuevo Evento', 'thecrisisacademy' ); ?>
					</a>
				</div>
			</div>
			<p style="color:#64748b; font-size:13px; line-height:1.6; margin:0 0 12px 0;">
				<?php esc_html_e( 'Los eventos se gestionan individualmente desde el tipo de contenido "Eventos". El sistema comprueba automáticamente las fechas y traslada a la papelera los eventos cuya fecha ya haya expirado. Si no hay ningún evento programado para una fecha igual o posterior al día de hoy, la sección completa de Próximos Eventos se ocultará automáticamente en el sitio web.', 'thecrisisacademy' ); ?>
			</p>

			<?php if ( ! empty( $upcoming_events ) ) : ?>
				<h4 style="margin:16px 0 8px 0; font-size:13px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.5px;">
					<?php esc_html_e( 'Próximos eventos activos en la agenda:', 'thecrisisacademy' ); ?>
				</h4>
				<table class="tca-events-table">
					<thead>
						<tr>
							<th style="width:120px;"><?php esc_html_e( 'Fecha', 'thecrisisacademy' ); ?></th>
							<th><?php esc_html_e( 'Título del Evento', 'thecrisisacademy' ); ?></th>
							<th style="width:180px;"><?php esc_html_e( 'Ubicación', 'thecrisisacademy' ); ?></th>
							<th style="width:90px; text-align:center;"><?php esc_html_e( 'Destacado', 'thecrisisacademy' ); ?></th>
							<th style="width:80px; text-align:right;"><?php esc_html_e( 'Acción', 'thecrisisacademy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $upcoming_events as $evt ) :
							$evt_date = get_post_meta( $evt->ID, '_event_date', true );
							$evt_loc  = get_post_meta( $evt->ID, '_event_location', true );
							$evt_feat = get_post_meta( $evt->ID, '_event_featured', true );
						?>
							<tr>
								<td style="font-weight:600; color:#0284c7;">
									<?php echo esc_html( wp_date( 'd M Y', strtotime( $evt_date ) ) ); ?>
								</td>
								<td>
									<strong><?php echo esc_html( get_the_title( $evt->ID ) ); ?></strong>
								</td>
								<td style="color:#64748b;">
									<?php echo esc_html( $evt_loc ? $evt_loc : '@Campus online - Zoom' ); ?>
								</td>
								<td style="text-align:center;">
									<?php if ( '1' === $evt_feat ) : ?>
										<span style="color:#f59e0b; font-weight:700;">★ Sí</span>
									<?php else : ?>
										<span style="color:#cbd5e1;">—</span>
									<?php endif; ?>
								</td>
								<td style="text-align:right;">
									<a href="<?php echo esc_url( get_edit_post_link( $evt->ID ) ?: '' ); ?>" class="button button-small" target="_blank">
										<?php esc_html_e( 'Editar', 'thecrisisacademy' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div style="background:#fffbeb; border:1px dashed #fde68a; border-radius:6px; padding:12px 16px; margin-top:12px; color:#92400e;">
					<p style="margin:0; font-size:13px;">
						ℹ️ <?php esc_html_e( 'No hay eventos próximos en la base de datos. Haz clic en "Añadir Nuevo Evento" para programar uno.', 'thecrisisacademy' ); ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $trashed_count > 0 ) : ?>
				<p style="margin:14px 0 0 0; font-size:12px; color:#94a3b8;">
					🗑️ <?php printf( esc_html__( 'Hay %d eventos pasados en la papelera generados por la limpieza automática.', 'thecrisisacademy' ), $trashed_count ); ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=trash&post_type=event' ) ); ?>" style="color:#64748b; text-decoration:underline;">
						<?php esc_html_e( 'Ver papelera de eventos', 'thecrisisacademy' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>

		<!-- Section Header Customization -->
		<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px 20px;">
			<h4 style="margin:0 0 12px 0; font-size:14px; font-weight:700; color:#1e293b;">
				<?php esc_html_e( 'Textos del Encabezado de la Sección', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px;">
				<div>
					<label for="corporate_upcoming_events_preheading" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Pre-título (Sub-heading)', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_upcoming_events_preheading" name="corporate_upcoming_events[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Ej. Agenda 2026" />
				</div>
				<div>
					<label for="corporate_upcoming_events_title" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Título Principal de la Sección', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_upcoming_events_title" name="corporate_upcoming_events[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Ej. Próximos eventos" />
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render News Metabox for Corporate Template
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_news_metabox( $post ) {
	wp_nonce_field( 'corporate_news_metabox_save', 'corporate_news_nonce' );
	$data = thecrisisacademy_get_news_data( $post->ID );

	$news_count_obj = wp_count_posts( 'news' );
	$news_published = isset( $news_count_obj->publish ) ? (int) $news_count_obj->publish : 0;
	$news_drafts    = isset( $news_count_obj->draft ) ? (int) $news_count_obj->draft : 0;

	$recent_news = get_posts( array(
		'post_type'      => 'news',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	?>
	<div class="tca-news-metabox" style="padding:15px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,sans-serif;">
		<style>
			.tca-news-card {
				background: #ffffff;
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px 20px;
				margin-bottom: 20px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.04);
			}
			.tca-news-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 12px;
				padding-bottom: 12px;
				border-bottom: 1px solid #f1f5f9;
			}
			.tca-news-badge {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				font-size: 13px;
				font-weight: 700;
				padding: 4px 12px;
				border-radius: 20px;
			}
			.tca-news-badge--active {
				background: #dcfce7;
				color: #15803d;
			}
			.tca-news-badge--empty {
				background: #fef3c7;
				color: #b45309;
			}
			.tca-news-actions {
				display: flex;
				gap: 8px;
				flex-wrap: wrap;
			}
			.tca-news-actions .button {
				display: inline-flex;
				align-items: center;
				gap: 5px;
			}
			.tca-news-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 14px;
				font-size: 13px;
			}
			.tca-news-table th {
				text-align: left;
				padding: 8px 12px;
				background: #f8fafc;
				border-bottom: 2px solid #e2e8f0;
				color: #475569;
				font-weight: 600;
			}
			.tca-news-table td {
				padding: 9px 12px;
				border-bottom: 1px solid #f1f5f9;
				color: #1e293b;
			}
			.tca-news-table tr:hover td {
				background: #f8fafc;
			}
			.tca-field-row {
				margin-bottom: 14px;
			}
			.tca-field-label {
				display: block;
				font-weight: 600;
				font-size: 13px;
				margin-bottom: 5px;
				color: #334155;
			}
			.tca-field-desc {
				font-size: 12px;
				color: #64748b;
				margin-top: 4px;
				line-height: 1.4;
			}
		</style>

		<!-- Status & CPT management summary -->
		<div class="tca-news-card">
			<div class="tca-news-header">
				<div>
					<?php if ( $news_published > 0 ) : ?>
						<span class="tca-news-badge tca-news-badge--active">
							<span style="font-size:10px;">🟢</span> <?php printf( esc_html__( 'Sección Visible en la Web (%d noticias publicadas)', 'thecrisisacademy' ), $news_published ); ?>
						</span>
					<?php else : ?>
						<span class="tca-news-badge tca-news-badge--empty">
							<span style="font-size:10px;">🟡</span> <?php esc_html_e( 'Sin noticias publicadas (La sección mostrará mensaje de aviso)', 'thecrisisacademy' ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="tca-news-actions">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=news' ) ); ?>" class="button button-primary">
						<span class="dashicons dashicons-admin-links" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Gestionar Todas las Noticias', 'thecrisisacademy' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=news' ) ); ?>" class="button">
						<span class="dashicons dashicons-plus-alt" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Añadir Nueva Noticia', 'thecrisisacademy' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=category&post_type=news' ) ); ?>" class="button">
						<span class="dashicons dashicons-category" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Categorías', 'thecrisisacademy' ); ?>
					</a>
				</div>
			</div>
			<p style="color:#64748b; font-size:13px; line-height:1.6; margin:0 0 10px 0;">
				<?php esc_html_e( 'Las noticias se administran de forma independiente mediante el tipo de contenido "Noticias" (news). La sección cuenta con filtrado dinámico mediante AJAX por categorías, cuadrícula responsiva y animación visual de ondas.', 'thecrisisacademy' ); ?>
			</p>

			<?php if ( ! empty( $recent_news ) ) : ?>
				<h4 style="margin:14px 0 8px 0; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">
					<?php esc_html_e( 'Últimas noticias añadidas al Centro de Inteligencia:', 'thecrisisacademy' ); ?>
				</h4>
				<table class="tca-news-table">
					<thead>
						<tr>
							<th style="width:60px;"><?php esc_html_e( 'Portada', 'thecrisisacademy' ); ?></th>
							<th><?php esc_html_e( 'Título de la Noticia', 'thecrisisacademy' ); ?></th>
							<th style="width:180px;"><?php esc_html_e( 'Categorías', 'thecrisisacademy' ); ?></th>
							<th style="width:110px;"><?php esc_html_e( 'Fecha', 'thecrisisacademy' ); ?></th>
							<th style="width:80px; text-align:right;"><?php esc_html_e( 'Acción', 'thecrisisacademy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent_news as $item ) :
							$thumb_url = get_the_post_thumbnail_url( $item->ID, 'thumbnail' );
							$terms = get_the_terms( $item->ID, 'category' );
							$cat_names = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'name' ) : array();
						?>
							<tr>
								<td>
									<?php if ( $thumb_url ) : ?>
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" style="width:40px; height:28px; object-fit:cover; border-radius:4px; display:block;" />
									<?php else : ?>
										<div style="width:40px; height:28px; background:#e2e8f0; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">📰</div>
									<?php endif; ?>
								</td>
								<td>
									<strong><?php echo esc_html( get_the_title( $item->ID ) ); ?></strong>
								</td>
								<td style="color:#64748b; font-size:12px;">
									<?php echo esc_html( ! empty( $cat_names ) ? implode( ', ', $cat_names ) : '—' ); ?>
								</td>
								<td style="color:#64748b; font-size:12px;">
									<?php echo esc_html( get_the_date( 'd M Y', $item->ID ) ); ?>
								</td>
								<td style="text-align:right;">
									<a href="<?php echo esc_url( get_edit_post_link( $item->ID ) ?: '' ); ?>" class="button button-small" target="_blank">
										<?php esc_html_e( 'Editar', 'thecrisisacademy' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div style="background:#fffbeb; border:1px dashed #fde68a; border-radius:6px; padding:12px 16px; margin-top:10px; color:#92400e;">
					<p style="margin:0; font-size:13px;">
						ℹ️ <?php esc_html_e( 'Aún no hay noticias publicadas en el Centro de Inteligencia. Haz clic en "Añadir Nueva Noticia" para publicar la primera.', 'thecrisisacademy' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>

		<!-- 1. Encabezado de la Sección -->
		<div class="tca-news-card" style="background:#f8fafc;">
			<h4 style="margin:0 0 14px 0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px;">
				<span>🏷️</span> <?php esc_html_e( '1. Encabezado de la Sección', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:12px;">
				<div>
					<label for="corporate_news_preheading" class="tca-field-label">
						<?php esc_html_e( 'Pre-título (Sub-heading)', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_news_preheading" name="corporate_news[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="Ej. Centro de Inteligencia" />
					<div class="tca-field-desc"><?php esc_html_e( 'Texto pequeño sobre el título principal.', 'thecrisisacademy' ); ?></div>
				</div>
				<div>
					<label for="corporate_news_title" class="tca-field-label">
						<?php esc_html_e( 'Título Principal de la Sección', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_news_title" name="corporate_news[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Ej. Actualidad Global y Análisis" />
					<div class="tca-field-desc"><?php esc_html_e( 'Soporta etiquetas de formato HTML como &lt;br&gt;, &lt;span class="color"&gt;, &lt;em&gt;.', 'thecrisisacademy' ); ?></div>
				</div>
			</div>
			<div>
				<label for="corporate_news_description" class="tca-field-label">
					<?php esc_html_e( 'Descripción o Bajada', 'thecrisisacademy' ); ?>
				</label>
				<textarea id="corporate_news_description" name="corporate_news[description]" rows="2" class="widefat" placeholder="Ej. Mantente informado de los últimos eventos, análisis e impacto de crisis y escándalos."><?php echo esc_textarea( $data['description'] ); ?></textarea>
			</div>
		</div>

		<!-- 2. Configuración de Publicaciones y Filtros -->
		<div class="tca-news-card" style="background:#f8fafc;">
			<h4 style="margin:0 0 14px 0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px;">
				<span>⚙️</span> <?php esc_html_e( '2. Parámetros de Publicaciones y Filtro AJAX', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin-bottom:14px;">
				<div>
					<label for="corporate_news_posts_per_page" class="tca-field-label">
						<?php esc_html_e( 'Noticias por página / lote', 'thecrisisacademy' ); ?>
					</label>
					<input type="number" id="corporate_news_posts_per_page" name="corporate_news[posts_per_page]" value="<?php echo esc_attr( $data['posts_per_page'] ); ?>" min="1" max="24" step="1" class="widefat" />
					<div class="tca-field-desc"><?php esc_html_e( 'Por defecto: 8 noticias.', 'thecrisisacademy' ); ?></div>
				</div>
				<div>
					<label for="corporate_news_orderby" class="tca-field-label">
						<?php esc_html_e( 'Ordenar por', 'thecrisisacademy' ); ?>
					</label>
					<select id="corporate_news_orderby" name="corporate_news[orderby]" class="widefat">
						<option value="date" <?php selected( $data['orderby'], 'date' ); ?>><?php esc_html_e( 'Fecha de publicación (Recomendado)', 'thecrisisacademy' ); ?></option>
						<option value="title" <?php selected( $data['orderby'], 'title' ); ?>><?php esc_html_e( 'Título alfabético', 'thecrisisacademy' ); ?></option>
						<option value="modified" <?php selected( $data['orderby'], 'modified' ); ?>><?php esc_html_e( 'Última modificación', 'thecrisisacademy' ); ?></option>
						<option value="rand" <?php selected( $data['orderby'], 'rand' ); ?>><?php esc_html_e( 'Aleatorio', 'thecrisisacademy' ); ?></option>
					</select>
				</div>
				<div>
					<label for="corporate_news_order" class="tca-field-label">
						<?php esc_html_e( 'Dirección del Orden', 'thecrisisacademy' ); ?>
					</label>
					<select id="corporate_news_order" name="corporate_news[order]" class="widefat">
						<option value="DESC" <?php selected( $data['order'], 'DESC' ); ?>><?php esc_html_e( 'Descendente (Más recientes primero)', 'thecrisisacademy' ); ?></option>
						<option value="ASC" <?php selected( $data['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascendente (Más antiguas primero)', 'thecrisisacademy' ); ?></option>
					</select>
				</div>
			</div>

			<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; padding-top:12px; border-top:1px solid #e2e8f0;">
				<div>
					<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer;">
						<input type="checkbox" name="corporate_news[show_filters]" value="1" <?php checked( $data['show_filters'], '1' ); ?> />
						<span><?php esc_html_e( 'Mostrar barra de pestañas para filtrar por categoría', 'thecrisisacademy' ); ?></span>
					</label>
					<div class="tca-field-desc" style="margin-left:24px;">
						<?php esc_html_e( 'Genera automáticamente botones con las categorías activas asignadas a las noticias.', 'thecrisisacademy' ); ?>
					</div>
				</div>
				<div>
					<label for="corporate_news_filter_all_label" class="tca-field-label">
						<?php esc_html_e( 'Texto del botón "Todos"', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_news_filter_all_label" name="corporate_news[filter_all_label]" value="<?php echo esc_attr( $data['filter_all_label'] ); ?>" class="widefat" placeholder="Todos" />
				</div>
			</div>
		</div>

		<!-- 3. Botón de Acción y Efectos Visuales -->
		<div class="tca-news-card" style="background:#f8fafc; margin-bottom:0;">
			<h4 style="margin:0 0 14px 0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px;">
				<span>🔗</span> <?php esc_html_e( '3. Botón de Acción Inferior y Efectos Visuales', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:14px;">
				<div>
					<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer; margin-bottom:8px;">
						<input type="checkbox" name="corporate_news[show_button]" value="1" <?php checked( $data['show_button'], '1' ); ?> />
						<span><?php esc_html_e( 'Mostrar botón "Ver todas las noticias"', 'thecrisisacademy' ); ?></span>
					</label>
					<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer;">
						<input type="checkbox" name="corporate_news[button_target]" value="1" <?php checked( $data['button_target'], '1' ); ?> />
						<span><?php esc_html_e( 'Abrir enlace en nueva pestaña (_blank)', 'thecrisisacademy' ); ?></span>
					</label>
				</div>
				<div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
					<div>
						<label for="corporate_news_button_text" class="tca-field-label">
							<?php esc_html_e( 'Texto del Botón', 'thecrisisacademy' ); ?>
						</label>
						<input type="text" id="corporate_news_button_text" name="corporate_news[button_text]" value="<?php echo esc_attr( $data['button_text'] ); ?>" class="widefat" placeholder="Ver todas las noticias" />
					</div>
					<div>
						<label for="corporate_news_button_url" class="tca-field-label">
							<?php esc_html_e( 'URL personalizada (Opcional)', 'thecrisisacademy' ); ?>
						</label>
						<input type="text" id="corporate_news_button_url" name="corporate_news[button_url]" value="<?php echo esc_attr( $data['button_url'] ); ?>" class="widefat" placeholder="<?php echo esc_attr( get_post_type_archive_link( 'news' ) ? get_post_type_archive_link( 'news' ) : '/news/' ); ?>" />
						<div class="tca-field-desc"><?php esc_html_e( 'Si se deja vacío, dirigirá al archivo oficial de noticias.', 'thecrisisacademy' ); ?></div>
					</div>
				</div>
			</div>

			<div style="padding-top:12px; border-top:1px solid #e2e8f0;">
				<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer;">
					<input type="checkbox" name="corporate_news[show_canvas]" value="1" <?php checked( $data['show_canvas'], '1' ); ?> />
					<span><?php esc_html_e( 'Activar animación de ondas de radio en el fondo (#news-radio-waves-canvas)', 'thecrisisacademy' ); ?></span>
				</label>
				<div class="tca-field-desc" style="margin-left:24px;">
					<?php esc_html_e( 'Renderiza el lienzo dinámico con ondas de radio que da ambiente de centro de monitoreo e inteligencia.', 'thecrisisacademy' ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render FAQ Metabox for Corporate Template
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_faq_metabox( $post ) {
	wp_nonce_field( 'corporate_faq_metabox_save', 'corporate_faq_nonce' );
	$data = thecrisisacademy_get_faq_data( $post->ID );

	$faq_count_obj = wp_count_posts( 'faq' );
	$faq_published = isset( $faq_count_obj->publish ) ? (int) $faq_count_obj->publish : 0;

	$faqs_list = get_posts( array(
		'post_type'      => 'faq',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );

	$seed_url = wp_nonce_url(
		admin_url( 'admin-post.php?action=thecrisisacademy_seed_faqs&redirect_to=' . urlencode( admin_url( 'post.php?post=' . $post->ID . '&action=edit#corporate_faq_metabox' ) ) ),
		'thecrisisacademy_seed_faqs_action',
		'thecrisisacademy_seed_faqs_nonce'
	);
	?>
	<div class="tca-faq-metabox" style="padding:15px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,sans-serif;">
		<style>
			.tca-faq-card {
				background: #ffffff;
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px 20px;
				margin-bottom: 20px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.04);
			}
			.tca-faq-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 12px;
				padding-bottom: 12px;
				border-bottom: 1px solid #f1f5f9;
			}
			.tca-faq-badge {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				font-size: 13px;
				font-weight: 700;
				padding: 4px 12px;
				border-radius: 20px;
			}
			.tca-faq-badge--active {
				background: #dcfce7;
				color: #15803d;
			}
			.tca-faq-badge--empty {
				background: #fef3c7;
				color: #b45309;
			}
			.tca-faq-actions {
				display: flex;
				gap: 8px;
				flex-wrap: wrap;
			}
			.tca-faq-actions .button {
				display: inline-flex;
				align-items: center;
				gap: 5px;
			}
			.tca-faq-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 14px;
				font-size: 13px;
			}
			.tca-faq-table th {
				text-align: left;
				padding: 8px 12px;
				background: #f8fafc;
				border-bottom: 2px solid #e2e8f0;
				color: #475569;
				font-weight: 600;
			}
			.tca-faq-table td {
				padding: 9px 12px;
				border-bottom: 1px solid #f1f5f9;
				color: #1e293b;
			}
			.tca-faq-table tr:hover td {
				background: #f8fafc;
			}
		</style>

		<!-- Status & CPT management summary -->
		<div class="tca-faq-card">
			<div class="tca-faq-header">
				<div>
					<?php if ( $faq_published > 0 ) : ?>
						<span class="tca-faq-badge tca-faq-badge--active">
							<span style="font-size:10px;">🟢</span> <?php printf( esc_html__( 'Sección Activa en la Web (%d preguntas publicadas)', 'thecrisisacademy' ), $faq_published ); ?>
						</span>
					<?php else : ?>
						<span class="tca-faq-badge tca-faq-badge--empty">
							<span style="font-size:10px;">🟡</span> <?php esc_html_e( 'Mostrando 10 preguntas por defecto (Sin preguntas en CPT aún)', 'thecrisisacademy' ); ?>
						</span>
					<?php endif; ?>
				</div>
				<div class="tca-faq-actions">
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=faq' ) ); ?>" class="button button-primary">
						<span class="dashicons dashicons-editor-help" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Gestionar Todas las Preguntas', 'thecrisisacademy' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=faq' ) ); ?>" class="button">
						<span class="dashicons dashicons-plus-alt" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Añadir Nueva Pregunta', 'thecrisisacademy' ); ?>
					</a>
					<a href="<?php echo esc_url( $seed_url ); ?>" class="button button-secondary" onclick="return confirm('¿Deseas restaurar/importar las 10 preguntas iniciales en la base de datos?');">
						<span class="dashicons dashicons-download" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Importar 10 Preguntas Iniciales', 'thecrisisacademy' ); ?>
					</a>
				</div>
			</div>
			<p style="color:#64748b; font-size:13px; line-height:1.6; margin:0 0 10px 0;">
				<?php esc_html_e( 'Las preguntas del acordeón se administran individualmente desde el menú "Preguntas FAQ". Cada publicación cuenta con un campo numérico de "Orden de Visualización" para controlar exactamente su posición en el acordeón interactivo.', 'thecrisisacademy' ); ?>
			</p>

			<?php if ( ! empty( $faqs_list ) ) : ?>
				<h4 style="margin:14px 0 8px 0; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">
					<?php esc_html_e( 'Preguntas publicadas en el acordeón:', 'thecrisisacademy' ); ?>
				</h4>
				<table class="tca-faq-table">
					<thead>
						<tr>
							<th style="width:50px; text-align:center;"><?php esc_html_e( 'Pos.', 'thecrisisacademy' ); ?></th>
							<th><?php esc_html_e( 'Pregunta (Título)', 'thecrisisacademy' ); ?></th>
							<th><?php esc_html_e( 'Respuesta Previa', 'thecrisisacademy' ); ?></th>
							<th style="width:80px; text-align:right;"><?php esc_html_e( 'Acción', 'thecrisisacademy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $faqs_list as $f_item ) :
							$clean_ans = wp_strip_all_tags( $f_item->post_content );
						?>
							<tr>
								<td style="text-align:center; font-weight:700; color:#0284c7;">
									<?php echo esc_html( (string) $f_item->menu_order ); ?>
								</td>
								<td>
									<strong><?php echo esc_html( get_the_title( $f_item->ID ) ); ?></strong>
								</td>
								<td style="color:#64748b; font-size:12px;">
									<?php echo esc_html( wp_trim_words( $clean_ans, 12, '...' ) ); ?>
								</td>
								<td style="text-align:right;">
									<a href="<?php echo esc_url( get_edit_post_link( $f_item->ID ) ?: '' ); ?>" class="button button-small" target="_blank">
										<?php esc_html_e( 'Editar', 'thecrisisacademy' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div style="background:#fffbeb; border:1px dashed #fde68a; border-radius:6px; padding:12px 16px; margin-top:10px; color:#92400e;">
					<p style="margin:0; font-size:13px;">
						ℹ️ <?php esc_html_e( 'No hay preguntas cargadas en el tipo de contenido "Preguntas FAQ". Haz clic en "Importar 10 Preguntas Iniciales" para sembrarlas automáticamente en el panel.', 'thecrisisacademy' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>

		<!-- 1. Tarjeta Flotante CTA (Agenda una llamada) -->
		<div class="tca-faq-card" style="background:#f8fafc;">
			<h4 style="margin:0 0 14px 0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px;">
				<span>💬</span> <?php esc_html_e( '1. Tarjeta Destacada de Llamado a la Acción (CTA)', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:14px;">
				<div>
					<label for="corporate_faq_preheading" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Pre-título de Sección', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_faq_preheading" name="corporate_faq[preheading]" value="<?php echo esc_attr( $data['preheading'] ); ?>" class="widefat" placeholder="FAQ" />
				</div>
				<div>
					<label for="corporate_faq_title" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Título Principal de la Sección', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_faq_title" name="corporate_faq[title]" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat" placeholder="Preguntas más frecuentes" />
				</div>
			</div>

			<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px; padding-top:12px; border-top:1px solid #e2e8f0;">
				<div>
					<label for="corporate_faq_cta_title" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Título de la Tarjeta CTA', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_faq_cta_title" name="corporate_faq[cta_title]" value="<?php echo esc_attr( $data['cta_title'] ); ?>" class="widefat" placeholder="Agenda una llamada de 15 min" />
				</div>
				<div>
					<label for="corporate_faq_cta_btn_text" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Texto del Botón CTA', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_faq_cta_btn_text" name="corporate_faq[cta_btn_text]" value="<?php echo esc_attr( $data['cta_btn_text'] ); ?>" class="widefat" placeholder="Reservar Llamada Gratuita" />
				</div>
			</div>

			<div style="margin-bottom:14px;">
				<label for="corporate_faq_cta_desc" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
					<?php esc_html_e( 'Descripción del CTA', 'thecrisisacademy' ); ?>
				</label>
				<textarea id="corporate_faq_cta_desc" name="corporate_faq[cta_desc]" rows="2" class="widefat" placeholder="Si tienes dudas, agenda una videollamada gratuita..."><?php echo esc_textarea( $data['cta_desc'] ); ?></textarea>
			</div>

			<div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; padding-top:12px; border-top:1px solid #e2e8f0;">
				<div>
					<label for="corporate_faq_cta_btn_url" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Enlace del Botón CTA (URL o ancla)', 'thecrisisacademy' ); ?>
					</label>
					<input type="text" id="corporate_faq_cta_btn_url" name="corporate_faq[cta_btn_url]" value="<?php echo esc_attr( $data['cta_btn_url'] ); ?>" class="widefat" placeholder="#cta" />
				</div>
				<div style="display:flex; align-items:flex-end;">
					<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer; margin-bottom:10px;">
						<input type="checkbox" name="corporate_faq[cta_btn_target]" value="1" <?php checked( $data['cta_btn_target'], '1' ); ?> />
						<span><?php esc_html_e( 'Abrir botón en nueva pestaña', 'thecrisisacademy' ); ?></span>
					</label>
				</div>
			</div>
		</div>

		<!-- 2. Parámetros del Acordeón -->
		<div class="tca-faq-card" style="background:#f8fafc; margin-bottom:0;">
			<h4 style="margin:0 0 14px 0; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px;">
				<span>⚙️</span> <?php esc_html_e( '2. Configuración y Comportamiento del Acordeón', 'thecrisisacademy' ); ?>
			</h4>
			<div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin-bottom:14px;">
				<div>
					<label for="corporate_faq_posts_per_page" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Preguntas a mostrar (-1 para todas)', 'thecrisisacademy' ); ?>
					</label>
					<input type="number" id="corporate_faq_posts_per_page" name="corporate_faq[posts_per_page]" value="<?php echo esc_attr( (string) $data['posts_per_page'] ); ?>" min="-1" max="50" step="1" class="widefat" />
				</div>
				<div>
					<label for="corporate_faq_orderby" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Criterio de Ordenación', 'thecrisisacademy' ); ?>
					</label>
					<select id="corporate_faq_orderby" name="corporate_faq[orderby]" class="widefat">
						<option value="menu_order" <?php selected( $data['orderby'], 'menu_order' ); ?>><?php esc_html_e( 'Orden numérico personalizado (Recomendado)', 'thecrisisacademy' ); ?></option>
						<option value="date" <?php selected( $data['orderby'], 'date' ); ?>><?php esc_html_e( 'Fecha de publicación', 'thecrisisacademy' ); ?></option>
						<option value="title" <?php selected( $data['orderby'], 'title' ); ?>><?php esc_html_e( 'Título alfabético', 'thecrisisacademy' ); ?></option>
						<option value="rand" <?php selected( $data['orderby'], 'rand' ); ?>><?php esc_html_e( 'Aleatorio', 'thecrisisacademy' ); ?></option>
					</select>
				</div>
				<div>
					<label for="corporate_faq_order" style="display:block; font-weight:600; font-size:13px; margin-bottom:5px; color:#334155;">
						<?php esc_html_e( 'Dirección', 'thecrisisacademy' ); ?>
					</label>
					<select id="corporate_faq_order" name="corporate_faq[order]" class="widefat">
						<option value="ASC" <?php selected( $data['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascendente (1, 2, 3...)', 'thecrisisacademy' ); ?></option>
						<option value="DESC" <?php selected( $data['order'], 'DESC' ); ?>><?php esc_html_e( 'Descendente', 'thecrisisacademy' ); ?></option>
					</select>
				</div>
			</div>

			<div style="padding-top:12px; border-top:1px solid #e2e8f0;">
				<label style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13px; color:#334155; cursor:pointer;">
					<input type="checkbox" name="corporate_faq[first_open]" value="1" <?php checked( $data['first_open'], '1' ); ?> />
					<span><?php esc_html_e( 'Mantener la primera pregunta desplegada/activa por defecto al cargar', 'thecrisisacademy' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Save Corporate Meta Box fields
 *
 * @param int     $post_id Post ID
 * @param WP_Post $post    Post object
 */
function thecrisisacademy_save_corporate_metaboxes( $post_id, $post ) {
	// Avoid autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	// ─── 1. Hero Metabox Save ───
	if ( isset( $_POST['corporate_hero_nonce'] ) && wp_verify_nonce( $_POST['corporate_hero_nonce'], 'corporate_hero_metabox_save' ) ) {
		if ( isset( $_POST['corporate_hero'] ) && is_array( $_POST['corporate_hero'] ) ) {
			$data = $_POST['corporate_hero'];

	// 1. Preheading
	if ( isset( $data['preheading'] ) ) {
		update_post_meta( $post_id, '_corporate_hero_preheading', sanitize_text_field( $data['preheading'] ) );
	}

	// 2. Title (allow basic formatting like <br>, <span>, <em>, <strong>)
	if ( isset( $data['title'] ) ) {
		$allowed_title_tags = array(
			'br'     => array(),
			'span'   => array( 'class' => array() ),
			'em'     => array(),
			'strong' => array(),
		);
		update_post_meta( $post_id, '_corporate_hero_title', wp_kses( $data['title'], $allowed_title_tags ) );
	}

	// 3. Points Slideshow (Repeater)
	if ( isset( $data['points'] ) && is_array( $data['points'] ) ) {
		$clean_points = array();
		foreach ( $data['points'] as $point_text ) {
			$trimmed = trim( $point_text );
			if ( '' !== $trimmed ) {
				$clean_points[] = wp_kses_post( $trimmed );
			}
		}
		if ( ! empty( $clean_points ) ) {
			update_post_meta( $post_id, '_corporate_hero_points', $clean_points );
		} else {
			delete_post_meta( $post_id, '_corporate_hero_points' );
		}
	} else {
		delete_post_meta( $post_id, '_corporate_hero_points' );
	}

	// 4. Data Block Content (WYSIWYG HTML)
	if ( isset( $data['data_block_content'] ) ) {
		update_post_meta( $post_id, '_corporate_hero_data_block_content', wp_kses_post( $data['data_block_content'] ) );
	}

	// 5. Source Label and URL
	if ( isset( $data['source_label'] ) ) {
		update_post_meta( $post_id, '_corporate_hero_source_label', sanitize_text_field( $data['source_label'] ) );
	}
	if ( isset( $data['source_url'] ) ) {
		update_post_meta( $post_id, '_corporate_hero_source_url', esc_url_raw( $data['source_url'] ) );
	}

	// 6. Canvas Crisis Tags
	if ( isset( $data['canvas_tags'] ) ) {
		$raw_tags   = preg_split( '/[\r\n,]+/', $data['canvas_tags'] );
		$clean_tags = array();
		foreach ( $raw_tags as $tag ) {
			$t = trim( sanitize_text_field( $tag ) );
			if ( '' !== $t ) {
				if ( 0 !== strpos( $t, '#' ) ) {
					$t = '#' . $t;
				}
				$clean_tags[] = $t;
			}
		}
		if ( ! empty( $clean_tags ) ) {
			update_post_meta( $post_id, '_corporate_hero_canvas_tags', array_values( array_unique( $clean_tags ) ) );
		} else {
			delete_post_meta( $post_id, '_corporate_hero_canvas_tags' );
		}
	} else {
		delete_post_meta( $post_id, '_corporate_hero_canvas_tags' );
	}
	}
	}

	// ─── 2. Trouble Metabox Save ───
	if ( isset( $_POST['corporate_trouble_nonce'] ) && wp_verify_nonce( $_POST['corporate_trouble_nonce'], 'corporate_trouble_metabox_save' ) ) {
		// Preheading
		if ( isset( $_POST['corporate_trouble']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_trouble_preheading', sanitize_text_field( $_POST['corporate_trouble']['preheading'] ) );
		}

		// Title
		if ( isset( $_POST['corporate_trouble']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_trouble_title', wp_kses( $_POST['corporate_trouble']['title'], $allowed_title_tags ) );
		}

		// Steps Repeater
		if ( isset( $_POST['corporate_trouble_title'] ) && is_array( $_POST['corporate_trouble_title'] ) ) {
			$clean_steps  = array();
			$titles       = $_POST['corporate_trouble_title'];
			$departments  = $_POST['corporate_trouble_department'] ?? array();
			$icons        = $_POST['corporate_trouble_icon'] ?? array();
			$tags         = $_POST['corporate_trouble_tag'] ?? array();
			$tag_classes  = $_POST['corporate_trouble_tag_class'] ?? array();
			$descriptions = $_POST['corporate_trouble_description'] ?? array();
			$points_list  = $_POST['corporate_trouble_points'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$raw_points = $points_list[ $idx ] ?? '';
				$points_arr = array();
				if ( ! empty( $raw_points ) ) {
					$lines = preg_split( '/\r\n|\r|\n/', $raw_points );
					foreach ( $lines as $line ) {
						$trimmed_line = trim( sanitize_text_field( $line ) );
						if ( '' !== $trimmed_line ) {
							$points_arr[] = $trimmed_line;
						}
					}
				}

				$clean_steps[] = array(
					'department'  => sanitize_text_field( $departments[ $idx ] ?? '' ),
					'icon'        => sanitize_key( $icons[ $idx ] ?? 'alert-circle' ),
					'tag'         => sanitize_text_field( $tags[ $idx ] ?? '' ),
					'tag_class'   => in_array( $tag_classes[ $idx ] ?? '', array( 'alert-tag', 'critical-tag' ), true ) ? $tag_classes[ $idx ] : 'alert-tag',
					'title'       => $title,
					'description' => wp_kses( $descriptions[ $idx ] ?? '', array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
					'points'      => $points_arr,
				);
			}

			if ( ! empty( $clean_steps ) ) {
				update_post_meta( $post_id, '_corporate_trouble_steps', $clean_steps );
			} else {
				delete_post_meta( $post_id, '_corporate_trouble_steps' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_trouble_steps' );
		}
	}

	// ─── 3. Hearings Metabox Save ───
	if ( isset( $_POST['corporate_hearings_nonce'] ) && wp_verify_nonce( $_POST['corporate_hearings_nonce'], 'corporate_hearings_metabox_save' ) ) {
		// Preheading
		if ( isset( $_POST['corporate_hearings']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_hearings_preheading', sanitize_text_field( $_POST['corporate_hearings']['preheading'] ) );
		}

		// Title
		if ( isset( $_POST['corporate_hearings']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_hearings_title', wp_kses( $_POST['corporate_hearings']['title'], $allowed_title_tags ) );
		}

		// Items Repeater
		if ( isset( $_POST['corporate_hearings_title'] ) && is_array( $_POST['corporate_hearings_title'] ) ) {
			$clean_items   = array();
			$titles        = $_POST['corporate_hearings_title'];
			$radar_labels  = $_POST['corporate_hearings_radar_label'] ?? array();
			$badges        = $_POST['corporate_hearings_badge'] ?? array();
			$slugs         = $_POST['corporate_hearings_slug'] ?? array();
			$descriptions  = $_POST['corporate_hearings_description'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$raw_slug  = sanitize_title( $slugs[ $idx ] ?? '' );
				$radar_lbl = sanitize_text_field( $radar_labels[ $idx ] ?? '' );
				if ( empty( $raw_slug ) ) {
					$raw_slug = sanitize_title( $radar_lbl ?: $title );
				}

				$clean_items[] = array(
					'slug'        => $raw_slug,
					'radar_label' => $radar_lbl,
					'title'       => $title,
					'badge'       => sanitize_text_field( $badges[ $idx ] ?? '' ),
					'description' => wp_kses( $descriptions[ $idx ] ?? '', array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
				);
			}

			if ( ! empty( $clean_items ) ) {
				update_post_meta( $post_id, '_corporate_hearings_items', $clean_items );
			} else {
				delete_post_meta( $post_id, '_corporate_hearings_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_hearings_items' );
		}
	}

	// ─── 4. Founder Metabox Save ───
	if ( isset( $_POST['corporate_founder_nonce'] ) && wp_verify_nonce( $_POST['corporate_founder_nonce'], 'corporate_founder_metabox_save' ) ) {
		if ( isset( $_POST['corporate_founder']['photo_url'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_photo_url', esc_url_raw( $_POST['corporate_founder']['photo_url'] ) );
		}
		if ( isset( $_POST['corporate_founder']['photo_alt'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_photo_alt', sanitize_text_field( $_POST['corporate_founder']['photo_alt'] ) );
		}
		if ( isset( $_POST['corporate_founder']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_preheading', sanitize_text_field( $_POST['corporate_founder']['preheading'] ) );
		}
		if ( isset( $_POST['corporate_founder']['name'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_name', sanitize_text_field( $_POST['corporate_founder']['name'] ) );
		}
		if ( isset( $_POST['corporate_founder']['role'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_role', sanitize_text_field( $_POST['corporate_founder']['role'] ) );
		}
		if ( isset( $_POST['corporate_founder']['quote'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_quote', sanitize_textarea_field( $_POST['corporate_founder']['quote'] ) );
		}
		if ( isset( $_POST['corporate_founder']['methodology_title'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_methodology_title', sanitize_text_field( $_POST['corporate_founder']['methodology_title'] ) );
		}
		if ( isset( $_POST['corporate_founder']['stat_number'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_stat_number', sanitize_text_field( $_POST['corporate_founder']['stat_number'] ) );
		}
		if ( isset( $_POST['corporate_founder']['stat_label'] ) ) {
			update_post_meta( $post_id, '_corporate_founder_stat_label', sanitize_text_field( $_POST['corporate_founder']['stat_label'] ) );
		}

		// Methodology items repeater
		if ( isset( $_POST['corporate_founder_method_title'] ) && is_array( $_POST['corporate_founder_method_title'] ) ) {
			$clean_methods = array();
			$titles        = $_POST['corporate_founder_method_title'];
			$icons         = $_POST['corporate_founder_method_icon'] ?? array();
			$descriptions  = $_POST['corporate_founder_method_desc'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$clean_methods[] = array(
					'icon'        => sanitize_key( $icons[ $idx ] ?? 'book' ),
					'title'       => $title,
					'description' => wp_kses_post( $descriptions[ $idx ] ?? '' ),
				);
			}

			if ( ! empty( $clean_methods ) ) {
				update_post_meta( $post_id, '_corporate_founder_methodology_items', $clean_methods );
			} else {
				delete_post_meta( $post_id, '_corporate_founder_methodology_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_founder_methodology_items' );
		}
	}

	// ─── 5. Program Metabox Save ───
	if ( isset( $_POST['corporate_program_nonce'] ) && wp_verify_nonce( $_POST['corporate_program_nonce'], 'corporate_program_metabox_save' ) ) {
		if ( isset( $_POST['corporate_program']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_program_preheading', sanitize_text_field( $_POST['corporate_program']['preheading'] ) );
		}
		if ( isset( $_POST['corporate_program']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_program_title', wp_kses( $_POST['corporate_program']['title'], $allowed_title_tags ) );
		}
		if ( isset( $_POST['corporate_program']['before_label'] ) ) {
			update_post_meta( $post_id, '_corporate_program_before_label', sanitize_text_field( $_POST['corporate_program']['before_label'] ) );
		}
		if ( isset( $_POST['corporate_program']['after_label'] ) ) {
			update_post_meta( $post_id, '_corporate_program_after_label', sanitize_text_field( $_POST['corporate_program']['after_label'] ) );
		}
		if ( isset( $_POST['corporate_program']['before_title'] ) ) {
			update_post_meta( $post_id, '_corporate_program_before_title', sanitize_text_field( $_POST['corporate_program']['before_title'] ) );
		}
		if ( isset( $_POST['corporate_program']['before_description'] ) ) {
			update_post_meta( $post_id, '_corporate_program_before_description', sanitize_textarea_field( $_POST['corporate_program']['before_description'] ) );
		}
		if ( isset( $_POST['corporate_program']['after_title'] ) ) {
			update_post_meta( $post_id, '_corporate_program_after_title', sanitize_text_field( $_POST['corporate_program']['after_title'] ) );
		}
		if ( isset( $_POST['corporate_program']['after_description'] ) ) {
			update_post_meta( $post_id, '_corporate_program_after_description', sanitize_textarea_field( $_POST['corporate_program']['after_description'] ) );
		}

		// Process Steps Repeater
		if ( isset( $_POST['corporate_program_step_title'] ) && is_array( $_POST['corporate_program_step_title'] ) ) {
			$clean_steps = array();
			$titles      = $_POST['corporate_program_step_title'];
			$photos      = $_POST['corporate_program_step_photo_url'] ?? array();
			$alts        = $_POST['corporate_program_step_photo_alt'] ?? array();
			$icons       = $_POST['corporate_program_step_icon'] ?? array();
			$shorts      = $_POST['corporate_program_step_short_title'] ?? array();
			$descs       = $_POST['corporate_program_step_desc'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$clean_steps[] = array(
					'photo_url'   => esc_url_raw( $photos[ $idx ] ?? '' ),
					'photo_alt'   => sanitize_text_field( $alts[ $idx ] ?? '' ),
					'icon'        => sanitize_key( $icons[ $idx ] ?? 'radar' ),
					'short_title' => sanitize_text_field( $shorts[ $idx ] ?? '' ),
					'title'       => $title,
					'description' => wp_kses( $descs[ $idx ] ?? '', array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
				);
			}

			if ( ! empty( $clean_steps ) ) {
				update_post_meta( $post_id, '_corporate_program_steps', $clean_steps );
			} else {
				delete_post_meta( $post_id, '_corporate_program_steps' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_program_steps' );
		}
	}

	// ─── 6. Simulation Metabox Save ───
	if ( isset( $_POST['corporate_simulation_nonce'] ) && wp_verify_nonce( $_POST['corporate_simulation_nonce'], 'corporate_simulation_metabox_save' ) ) {
		if ( isset( $_POST['corporate_sim']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_preheading', sanitize_text_field( $_POST['corporate_sim']['preheading'] ) );
		}
		if ( isset( $_POST['corporate_sim']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_sim_title', wp_kses( $_POST['corporate_sim']['title'], $allowed_title_tags ) );
		}
		if ( isset( $_POST['corporate_sim']['description'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_description', sanitize_textarea_field( $_POST['corporate_sim']['description'] ) );
		}
		if ( isset( $_POST['corporate_sim']['cta_text'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_cta_text', sanitize_text_field( $_POST['corporate_sim']['cta_text'] ) );
		}
		if ( isset( $_POST['corporate_sim']['cta_url'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_cta_url', esc_url_raw( $_POST['corporate_sim']['cta_url'] ) );
		}
		if ( isset( $_POST['corporate_sim']['cta_lightbox'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_cta_lightbox', sanitize_text_field( $_POST['corporate_sim']['cta_lightbox'] ) );
		}
		if ( isset( $_POST['corporate_sim']['panel_title'] ) ) {
			update_post_meta( $post_id, '_corporate_sim_panel_title', sanitize_text_field( $_POST['corporate_sim']['panel_title'] ) );
		}

		// Vulnerabilities Repeater (.accordion-interactive-list)
		if ( isset( $_POST['corporate_sim_item_title'] ) && is_array( $_POST['corporate_sim_item_title'] ) ) {
			$clean_items = array();
			$titles      = $_POST['corporate_sim_item_title'];
			$icons       = $_POST['corporate_sim_item_icon'] ?? array();
			$descs       = $_POST['corporate_sim_item_desc'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$clean_items[] = array(
					'icon'        => sanitize_key( $icons[ $idx ] ?? 'x' ),
					'title'       => $title,
					'description' => wp_kses( $descs[ $idx ] ?? '', array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
				);
			}

			if ( ! empty( $clean_items ) ) {
				update_post_meta( $post_id, '_corporate_sim_items', $clean_items );
			} else {
				delete_post_meta( $post_id, '_corporate_sim_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_sim_items' );
		}
	}

	// ─── 7. Diff Metabox Save ───
	if ( isset( $_POST['corporate_diff_nonce'] ) && wp_verify_nonce( $_POST['corporate_diff_nonce'], 'corporate_diff_metabox_save' ) ) {
		if ( isset( $_POST['corporate_diff']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_diff_preheading', sanitize_text_field( $_POST['corporate_diff']['preheading'] ) );
		}
		if ( isset( $_POST['corporate_diff']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_diff_title', wp_kses( $_POST['corporate_diff']['title'], $allowed_title_tags ) );
		}
		if ( isset( $_POST['corporate_diff']['autoplay'] ) ) {
			$autoplay_val = absint( $_POST['corporate_diff']['autoplay'] );
			update_post_meta( $post_id, '_corporate_diff_autoplay', $autoplay_val > 0 ? (string) $autoplay_val : '14000' );
		}

		// Diff Slides Repeater (.diff-slide-item)
		if ( isset( $_POST['corporate_diff_slide_title'] ) && is_array( $_POST['corporate_diff_slide_title'] ) ) {
			$clean_slides = array();
			$titles       = $_POST['corporate_diff_slide_title'];
			$icons        = $_POST['corporate_diff_slide_icon'] ?? array();
			$descs        = $_POST['corporate_diff_slide_desc'] ?? array();

			foreach ( $titles as $idx => $raw_title ) {
				$title = sanitize_text_field( $raw_title );
				if ( '' === $title ) continue;

				$clean_slides[] = array(
					'icon'        => sanitize_key( $icons[ $idx ] ?? 'book-open' ),
					'title'       => $title,
					'description' => wp_kses( $descs[ $idx ] ?? '', array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
				);
			}

			if ( ! empty( $clean_slides ) ) {
				update_post_meta( $post_id, '_corporate_diff_slides', $clean_slides );
			} else {
				delete_post_meta( $post_id, '_corporate_diff_slides' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_diff_slides' );
		}
	}

	// ─── 8. Testimonies Metabox Save ───
	if ( isset( $_POST['corporate_testimonies_nonce'] ) && wp_verify_nonce( $_POST['corporate_testimonies_nonce'], 'corporate_testimonies_metabox_save' ) ) {
		if ( isset( $_POST['corporate_testimonies']['preheading'] ) ) {
			update_post_meta( $post_id, '_corporate_testimonies_preheading', sanitize_text_field( $_POST['corporate_testimonies']['preheading'] ) );
		}
		if ( isset( $_POST['corporate_testimonies']['title'] ) ) {
			$allowed_title_tags = array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array(),
				'strong' => array(),
			);
			update_post_meta( $post_id, '_corporate_testimonies_title', wp_kses( $_POST['corporate_testimonies']['title'], $allowed_title_tags ) );
		}

		// Testimonies Repeater
		if ( isset( $_POST['corporate_testi_name'] ) && is_array( $_POST['corporate_testi_name'] ) ) {
			$clean_items = array();
			$names       = $_POST['corporate_testi_name'];
			$roles       = $_POST['corporate_testi_role'] ?? array();
			$texts       = $_POST['corporate_testi_text'] ?? array();
			$avatars     = $_POST['corporate_testi_avatar_url'] ?? array();
			$alts        = $_POST['corporate_testi_avatar_alt'] ?? array();

			foreach ( $names as $idx => $raw_name ) {
				$name = sanitize_text_field( $raw_name );
				if ( '' === $name ) continue;

				$clean_items[] = array(
					'avatar_url' => esc_url_raw( $avatars[ $idx ] ?? '' ),
					'avatar_alt' => sanitize_text_field( $alts[ $idx ] ?? '' ),
					'name'       => $name,
					'role'       => sanitize_text_field( $roles[ $idx ] ?? '' ),
					'text'       => sanitize_textarea_field( $texts[ $idx ] ?? '' ),
				);
			}

			if ( ! empty( $clean_items ) ) {
				update_post_meta( $post_id, '_corporate_testimonies_items', $clean_items );
			} else {
				delete_post_meta( $post_id, '_corporate_testimonies_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_testimonies_items' );
		}
	}

	// ─── 9. Thought Metabox Save ───
	if ( isset( $_POST['corporate_thought_nonce'] ) && wp_verify_nonce( $_POST['corporate_thought_nonce'], 'corporate_thought_metabox_save' ) ) {
		if ( isset( $_POST['corporate_thought'] ) && is_array( $_POST['corporate_thought'] ) ) {
			$thought = $_POST['corporate_thought'];

			if ( isset( $thought['preheading'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_preheading', sanitize_text_field( $thought['preheading'] ) );
			}
			if ( isset( $thought['title'] ) ) {
				$allowed_title_tags = array(
					'br'     => array(),
					'span'   => array( 'class' => array() ),
					'em'     => array(),
					'strong' => array(),
				);
				update_post_meta( $post_id, '_corporate_thought_title', wp_kses( $thought['title'], $allowed_title_tags ) );
			}
			if ( isset( $thought['description'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_description', sanitize_textarea_field( $thought['description'] ) );
			}

			// Column 1: Profiles
			if ( isset( $thought['profiles_tag'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_profiles_tag', sanitize_text_field( $thought['profiles_tag'] ) );
			}
			if ( isset( $thought['profiles_title'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_profiles_title', sanitize_text_field( $thought['profiles_title'] ) );
			}
			if ( isset( $thought['profiles_icon'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_profiles_icon', sanitize_key( $thought['profiles_icon'] ) );
			}

			// Column 2: Sectors
			if ( isset( $thought['sectors_tag'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_sectors_tag', sanitize_text_field( $thought['sectors_tag'] ) );
			}
			if ( isset( $thought['sectors_title'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_sectors_title', sanitize_text_field( $thought['sectors_title'] ) );
			}
			if ( isset( $thought['sectors_icon'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_sectors_icon', sanitize_key( $thought['sectors_icon'] ) );
			}

			// Column 3: Manifesto
			if ( isset( $thought['manifesto_tag'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_manifesto_tag', sanitize_text_field( $thought['manifesto_tag'] ) );
			}
			if ( isset( $thought['manifesto_title'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_manifesto_title', sanitize_text_field( $thought['manifesto_title'] ) );
			}
			if ( isset( $thought['manifesto_icon'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_manifesto_icon', sanitize_key( $thought['manifesto_icon'] ) );
			}
			if ( isset( $thought['manifesto_lead'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_manifesto_lead', sanitize_text_field( $thought['manifesto_lead'] ) );
			}
			if ( isset( $thought['manifesto_statement'] ) ) {
				update_post_meta( $post_id, '_corporate_thought_manifesto_statement', wp_kses( $thought['manifesto_statement'], array( 'strong' => array(), 'em' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ) );
			}
		}

		// Repeater: Profiles
		if ( isset( $_POST['corporate_thought_profiles'] ) && is_array( $_POST['corporate_thought_profiles'] ) ) {
			$clean_profiles = array();
			foreach ( $_POST['corporate_thought_profiles'] as $raw_profile ) {
				$val = sanitize_text_field( $raw_profile );
				if ( '' !== $val ) {
					$clean_profiles[] = $val;
				}
			}
			if ( ! empty( $clean_profiles ) ) {
				update_post_meta( $post_id, '_corporate_thought_profiles_items', $clean_profiles );
			} else {
				delete_post_meta( $post_id, '_corporate_thought_profiles_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_thought_profiles_items' );
		}

		// Repeater: Sectors
		if ( isset( $_POST['corporate_thought_sectors'] ) && is_array( $_POST['corporate_thought_sectors'] ) ) {
			$clean_sectors = array();
			foreach ( $_POST['corporate_thought_sectors'] as $raw_sector ) {
				$val = sanitize_text_field( $raw_sector );
				if ( '' !== $val ) {
					$clean_sectors[] = $val;
				}
			}
			if ( ! empty( $clean_sectors ) ) {
				update_post_meta( $post_id, '_corporate_thought_sectors_items', $clean_sectors );
			} else {
				delete_post_meta( $post_id, '_corporate_thought_sectors_items' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_thought_sectors_items' );
		}
	}

	// ─── 10. CTA Metabox Save ───
	if ( isset( $_POST['corporate_cta_nonce'] ) && wp_verify_nonce( $_POST['corporate_cta_nonce'], 'corporate_cta_metabox_save' ) ) {
		if ( isset( $_POST['corporate_cta'] ) && is_array( $_POST['corporate_cta'] ) ) {
			$cta = $_POST['corporate_cta'];

			if ( isset( $cta['preheading'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_preheading', sanitize_text_field( $cta['preheading'] ) );
			}
			if ( isset( $cta['title'] ) ) {
				$allowed_title_tags = array(
					'br'     => array(),
					'span'   => array( 'class' => array() ),
					'em'     => array(),
					'strong' => array(),
				);
				update_post_meta( $post_id, '_corporate_cta_title', wp_kses( $cta['title'], $allowed_title_tags ) );
			}
			if ( isset( $cta['description'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_description', sanitize_textarea_field( $cta['description'] ) );
			}
			if ( isset( $cta['whatsapp_phone'] ) ) {
				$cleaned_phone = preg_replace( '/[^0-9]/', '', $cta['whatsapp_phone'] );
				update_post_meta( $post_id, '_corporate_cta_whatsapp_phone', $cleaned_phone );
			}
			if ( isset( $cta['form_name_label'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_form_name_label', sanitize_text_field( $cta['form_name_label'] ) );
			}
			if ( isset( $cta['form_email_label'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_form_email_label', sanitize_text_field( $cta['form_email_label'] ) );
			}
			if ( isset( $cta['form_phone_label'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_form_phone_label', sanitize_text_field( $cta['form_phone_label'] ) );
			}
			if ( isset( $cta['form_msg_label'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_form_msg_label', sanitize_text_field( $cta['form_msg_label'] ) );
			}
			if ( isset( $cta['button_text'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_button_text', sanitize_text_field( $cta['button_text'] ) );
			}
			if ( isset( $cta['microcopy_text'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_microcopy_text', sanitize_text_field( $cta['microcopy_text'] ) );
			}
			if ( isset( $cta['success_title'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_success_title', sanitize_text_field( $cta['success_title'] ) );
			}
			if ( isset( $cta['success_desc'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_success_desc', sanitize_text_field( $cta['success_desc'] ) );
			}
			if ( isset( $cta['reset_btn_text'] ) ) {
				update_post_meta( $post_id, '_corporate_cta_reset_btn_text', sanitize_text_field( $cta['reset_btn_text'] ) );
			}
		}

		// Repeater: Points
		if ( isset( $_POST['corporate_cta_points'] ) && is_array( $_POST['corporate_cta_points'] ) ) {
			$clean_points = array();
			foreach ( $_POST['corporate_cta_points'] as $raw_point ) {
				$val = sanitize_text_field( $raw_point );
				if ( '' !== $val ) {
					$clean_points[] = $val;
				}
			}
			if ( ! empty( $clean_points ) ) {
				update_post_meta( $post_id, '_corporate_cta_points', $clean_points );
			} else {
				delete_post_meta( $post_id, '_corporate_cta_points' );
			}
		} else {
			delete_post_meta( $post_id, '_corporate_cta_points' );
		}
	}

	// ─── 10. Upcoming Events Metabox Save ───
	if ( isset( $_POST['corporate_upcoming_events_nonce'] ) && wp_verify_nonce( $_POST['corporate_upcoming_events_nonce'], 'corporate_upcoming_events_metabox_save' ) ) {
		if ( isset( $_POST['corporate_upcoming_events'] ) && is_array( $_POST['corporate_upcoming_events'] ) ) {
			$events_settings = $_POST['corporate_upcoming_events'];

			if ( isset( $events_settings['preheading'] ) ) {
				update_post_meta( $post_id, '_corporate_upcoming_events_preheading', sanitize_text_field( $events_settings['preheading'] ) );
			}
			if ( isset( $events_settings['title'] ) ) {
				$allowed_title_tags = array(
					'br'     => array(),
					'span'   => array( 'class' => array() ),
					'em'     => array(),
					'strong' => array(),
				);
				update_post_meta( $post_id, '_corporate_upcoming_events_title', wp_kses( $events_settings['title'], $allowed_title_tags ) );
			}
		}
	}

	// ─── 11. News Metabox Save ───
	if ( isset( $_POST['corporate_news_nonce'] ) && wp_verify_nonce( $_POST['corporate_news_nonce'], 'corporate_news_metabox_save' ) ) {
		if ( isset( $_POST['corporate_news'] ) && is_array( $_POST['corporate_news'] ) ) {
			$news_settings = $_POST['corporate_news'];

			if ( isset( $news_settings['preheading'] ) ) {
				update_post_meta( $post_id, '_corporate_news_preheading', sanitize_text_field( $news_settings['preheading'] ) );
			}
			if ( isset( $news_settings['title'] ) ) {
				$allowed_title_tags = array(
					'br'     => array(),
					'span'   => array( 'class' => array() ),
					'em'     => array(),
					'strong' => array(),
				);
				update_post_meta( $post_id, '_corporate_news_title', wp_kses( $news_settings['title'], $allowed_title_tags ) );
			}
			if ( isset( $news_settings['description'] ) ) {
				update_post_meta( $post_id, '_corporate_news_description', sanitize_textarea_field( $news_settings['description'] ) );
			}
			if ( isset( $news_settings['posts_per_page'] ) ) {
				$num = absint( $news_settings['posts_per_page'] );
				update_post_meta( $post_id, '_corporate_news_posts_per_page', $num > 0 ? $num : 8 );
			}
			if ( isset( $news_settings['orderby'] ) ) {
				$allowed_orderby = array( 'date', 'title', 'modified', 'rand' );
				$ob = sanitize_key( $news_settings['orderby'] );
				update_post_meta( $post_id, '_corporate_news_orderby', in_array( $ob, $allowed_orderby, true ) ? $ob : 'date' );
			}
			if ( isset( $news_settings['order'] ) ) {
				$ord = strtoupper( sanitize_key( $news_settings['order'] ) );
				update_post_meta( $post_id, '_corporate_news_order', in_array( $ord, array( 'DESC', 'ASC' ), true ) ? $ord : 'DESC' );
			}

			// Toggles
			update_post_meta( $post_id, '_corporate_news_show_filters', ! empty( $news_settings['show_filters'] ) ? '1' : '0' );
			update_post_meta( $post_id, '_corporate_news_show_canvas', ! empty( $news_settings['show_canvas'] ) ? '1' : '0' );
			update_post_meta( $post_id, '_corporate_news_show_button', ! empty( $news_settings['show_button'] ) ? '1' : '0' );

			if ( isset( $news_settings['filter_all_label'] ) ) {
				update_post_meta( $post_id, '_corporate_news_filter_all_label', sanitize_text_field( $news_settings['filter_all_label'] ) );
			}
			if ( isset( $news_settings['button_text'] ) ) {
				update_post_meta( $post_id, '_corporate_news_button_text', sanitize_text_field( $news_settings['button_text'] ) );
			}
			if ( isset( $news_settings['button_url'] ) ) {
				update_post_meta( $post_id, '_corporate_news_button_url', esc_url_raw( trim( $news_settings['button_url'] ) ) );
			}
			update_post_meta( $post_id, '_corporate_news_button_target', ! empty( $news_settings['button_target'] ) ? '1' : '0' );
		}
	}

	// ─── 12. FAQ Metabox Save ───
	if ( isset( $_POST['corporate_faq_nonce'] ) && wp_verify_nonce( $_POST['corporate_faq_nonce'], 'corporate_faq_metabox_save' ) ) {
		if ( isset( $_POST['corporate_faq'] ) && is_array( $_POST['corporate_faq'] ) ) {
			$faq_settings = $_POST['corporate_faq'];

			if ( isset( $faq_settings['preheading'] ) ) {
				update_post_meta( $post_id, '_corporate_faq_preheading', sanitize_text_field( $faq_settings['preheading'] ) );
			}
			if ( isset( $faq_settings['title'] ) ) {
				$allowed_title_tags = array(
					'br'     => array(),
					'span'   => array( 'class' => array() ),
					'em'     => array(),
					'strong' => array(),
				);
				update_post_meta( $post_id, '_corporate_faq_title', wp_kses( $faq_settings['title'], $allowed_title_tags ) );
			}
			if ( isset( $faq_settings['cta_title'] ) ) {
				update_post_meta( $post_id, '_corporate_faq_cta_title', sanitize_text_field( $faq_settings['cta_title'] ) );
			}
			if ( isset( $faq_settings['cta_desc'] ) ) {
				update_post_meta( $post_id, '_corporate_faq_cta_desc', sanitize_textarea_field( $faq_settings['cta_desc'] ) );
			}
			if ( isset( $faq_settings['cta_btn_text'] ) ) {
				update_post_meta( $post_id, '_corporate_faq_cta_btn_text', sanitize_text_field( $faq_settings['cta_btn_text'] ) );
			}
			if ( isset( $faq_settings['cta_btn_url'] ) ) {
				update_post_meta( $post_id, '_corporate_faq_cta_btn_url', sanitize_text_field( trim( $faq_settings['cta_btn_url'] ) ) );
			}
			update_post_meta( $post_id, '_corporate_faq_cta_btn_target', ! empty( $faq_settings['cta_btn_target'] ) ? '1' : '0' );

			if ( isset( $faq_settings['posts_per_page'] ) ) {
				$num = intval( $faq_settings['posts_per_page'] );
				update_post_meta( $post_id, '_corporate_faq_posts_per_page', 0 !== $num ? $num : -1 );
			}
			if ( isset( $faq_settings['orderby'] ) ) {
				$allowed_orderby = array( 'menu_order', 'date', 'title', 'rand' );
				$ob = sanitize_key( $faq_settings['orderby'] );
				update_post_meta( $post_id, '_corporate_faq_orderby', in_array( $ob, $allowed_orderby, true ) ? $ob : 'menu_order' );
			}
			if ( isset( $faq_settings['order'] ) ) {
				$ord = strtoupper( sanitize_key( $faq_settings['order'] ) );
				update_post_meta( $post_id, '_corporate_faq_order', in_array( $ord, array( 'ASC', 'DESC' ), true ) ? $ord : 'ASC' );
			}
			update_post_meta( $post_id, '_corporate_faq_first_open', ! empty( $faq_settings['first_open'] ) ? '1' : '0' );
		}
	}

	// ─── Synchronize shared sections across Corporate and Individuals ───
	if (
		isset( $_POST['corporate_cta_nonce'] ) ||
		isset( $_POST['corporate_upcoming_events_nonce'] ) ||
		isset( $_POST['corporate_news_nonce'] ) ||
		isset( $_POST['corporate_faq_nonce'] )
	) {
		thecrisisacademy_sync_shared_corporate_meta( $post_id );
	}
}
add_action( 'save_post', 'thecrisisacademy_save_corporate_metaboxes', 10, 2 );

