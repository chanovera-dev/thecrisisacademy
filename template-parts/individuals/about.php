<?php
/**
 * Template part for About section in Individuals template
 * Features:
 * 1. WebGL App Slideshow + About Overview
 * 2. 3D Timeline Showcase for Módulos de Especialización (Stepper on left, 3D card deck on right)
 *
 * @package TheCrisisAcademy
 */

// Retrieve ACF field values with safe fallback to defaults
$raw_gallery = function_exists( 'get_field' ) ? get_field( 'about_gallery' ) : null;
$slides      = array();
$theme_uri   = get_stylesheet_directory_uri();
$default_slides = array(
	array(
		'url' => $theme_uri . '/assets/img/about/mapa-de-stakeholders.webp',
		'alt' => __( 'Mapa de stakeholders', 'thecrisisacademy' ),
	),
	array(
		'url' => $theme_uri . '/assets/img/about/radar-de-amenazas.webp',
		'alt' => __( 'Radar de amenazas', 'thecrisisacademy' ),
	),
	array(
		'url' => $theme_uri . '/assets/img/about/war-room.webp',
		'alt' => __( 'War room', 'thecrisisacademy' ),
	),
);

if ( ! empty( $raw_gallery ) && is_array( $raw_gallery ) ) {
	foreach ( $raw_gallery as $item ) {
		$img_id = 0;
		if ( is_numeric( $item ) ) {
			$img_id = (int) $item;
		} elseif ( is_array( $item ) && ! empty( $item['ID'] ) ) {
			$img_id = (int) $item['ID'];
		} elseif ( is_array( $item ) && ! empty( $item['id'] ) ) {
			$img_id = (int) $item['id'];
		}

		if ( $img_id > 0 ) {
			$img_html = wp_get_attachment_image( $img_id, 'full', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
			if ( ! empty( $img_html ) ) {
				$slides[] = $img_html;
			}
		}
	}
}

if ( empty( $slides ) ) {
	foreach ( $default_slides as $slide ) {
		$slides[] = sprintf(
			'<img src="%s" alt="%s" width="600" height="450" loading="lazy" decoding="async" />',
			esc_url( $slide['url'] ),
			esc_attr( $slide['alt'] )
		);
	}
}

$about_data = function_exists( 'thecrisisacademy_get_individuals_about_data' ) ? thecrisisacademy_get_individuals_about_data() : array();

$about_preheading  = ! empty( $about_data['preheading'] ) ? $about_data['preheading'] : ( function_exists( 'get_field' ) ? get_field( 'about_preheading' ) : '¿Qué hacemos?' );
$about_title       = ! empty( $about_data['title'] ) ? $about_data['title'] : ( function_exists( 'get_field' ) ? get_field( 'about_title' ) : 'Entrenamos para proteger un activo crucial: la reputación' );
$about_subtitle    = ! empty( $about_data['subtitle'] ) ? $about_data['subtitle'] : ( function_exists( 'get_field' ) ? get_field( 'about_subtitle' ) : 'The Crisis Academy es una academia especializada en entrenamiento estratégico para el manejo de crisis reputacionales, comunicación de riesgos y control de narrativa.' );
$about_description = ! empty( $about_data['description'] ) ? $about_data['description'] : ( function_exists( 'get_field' ) ? get_field( 'about_description' ) : 'Formamos a equipos de crisis, áreas de comunicación, directivos y voceros para actuar con método, rapidez y precisión cuando más se necesita.' );

$about_modules_title = ! empty( $about_data['modules_title'] ) ? $about_data['modules_title'] : ( function_exists( 'get_field' ) ? get_field( 'about_modules_title' ) : 'Módulos de especialización' );

$raw_modules = ! empty( $about_data['modules'] ) ? $about_data['modules'] : ( function_exists( 'get_field' ) ? get_field( 'about_modules' ) : null );
$modules     = array();

if ( ! empty( $raw_modules ) && is_array( $raw_modules ) ) {
	foreach ( $raw_modules as $mod ) {
		$modules[] = array(
			'icon'        => ! empty( $mod['icon'] ) ? $mod['icon'] : 'radar',
			'tag'         => $mod['tag'] ?? '',
			'tag_class'   => ! empty( $mod['tag_class'] ) ? $mod['tag_class'] : 'alert-tag',
			'period'      => $mod['period'] ?? '',
			'short_title' => $mod['short_title'] ?? '',
			'title'       => $mod['title'] ?? '',
			'description' => $mod['description'] ?? '',
		);
	}
}

if ( empty( $modules ) ) {
	$modules = array(
		array(
			'icon'        => 'radar',
			'tag'         => 'Tendencias 2026',
			'tag_class'   => 'alert-tag',
			'period'      => '01 • Radar de Riesgos',
			'short_title' => 'Investigación y estudios de crisis',
			'title'       => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
			'description' => 'Análisis profundo de incidentes recientes y anticipación de escenarios de riesgo reputacional adaptados al entorno actual y a las amenazas emergentes.',
		),
		array(
			'icon'        => 'chart',
			'tag'         => 'Métricas & Control',
			'tag_class'   => 'alert-tag',
			'period'      => '02 • Medición',
			'short_title' => 'Herramientas y parámetros de medición',
			'title'       => 'Herramientas y parámetros de medición de una crisis y su respuesta',
			'description' => 'Establecimiento de indicadores cuantitativos y cualitativos para evaluar el impacto del incidente, la velocidad de reacción y la efectividad de la respuesta.',
		),
		array(
			'icon'        => 'cpu',
			'tag'         => 'IA & Nuevos Medios',
			'tag_class'   => 'alert-tag',
			'period'      => '03 • IA y Digital',
			'short_title' => 'Entorno mediático y digital: rol de la IA',
			'title'       => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
			'description' => 'Evaluación de la desinformación masiva, deepfakes y uso de IA en la amplificación, análisis y monitoreo predictivo de crisis modernas.',
		),
		array(
			'icon'        => 'target',
			'tag'         => 'Estrategia 4.0',
			'tag_class'   => 'alert-tag',
			'period'      => '04 • Estrategia',
			'short_title' => 'Comunicación estratégica de crisis 4.0',
			'title'       => 'Comunicación estratégica para manejo de crisis 4.0',
			'description' => 'Diseño de mensajes clave hiperdirigidos, comunicados ágiles y posicionamiento corporativo multicanal bajo situaciones de extrema presión.',
		),
		array(
			'icon'        => 'share-2',
			'tag'         => 'Redes Sociales',
			'tag_class'   => 'alert-tag',
			'period'      => '05 • Respuesta Ágil',
			'short_title' => 'Manejo ágil en redes sociales',
			'title'       => 'Procesos para un manejo ágil de crisis en redes sociales',
			'description' => 'Protocolos de contención inmediata en plataformas digitales, gestión de comunidades y desaceleración de tendencias negativas virales.',
		),
		array(
			'icon'        => 'mic',
			'tag'         => 'Vocerías Oficiales',
			'tag_class'   => 'alert-tag',
			'period'      => '06 • Portavoces',
			'short_title' => 'Control de narrativa y vocerías',
			'title'       => 'Control de narrativa y arquitectura de vocerías',
			'description' => 'Definición de portavoces oficiales, lineamientos de conducta ante la prensa y técnicas avanzadas de control del relato público.',
		),
		array(
			'icon'        => 'shield-alert',
			'tag'         => 'Ejercicio Inmersivo',
			'tag_class'   => 'alert-tag',
			'period'      => '07 • Práctica Real',
			'short_title' => 'Simulacro de alta intensidad',
			'title'       => 'Simulacro de alta intensidad para probar la capacidad de respuesta a una crisis de reputación',
			'description' => 'Ejercicio inmersivo en tiempo real con periodistas simulados e interacciones hostiles para auditar la resistencia y eficacia de los comités.',
		),
	);
}
$total_modules = count( $modules );
?>
<section id="about" class="block">
    <div class="content content-grid">
        <div class="left-bar container app card-reveal">
            <div class="slideshow--wrapper">
                <div class="slideshow">
                    <?php foreach ( $slides as $index => $slide_html ) : 
                        $is_active = ( 0 === $index );
                    ?>
                        <article id="about-item-<?php echo esc_attr( $index + 1 ); ?>" class="about-item post animate-in<?php echo $is_active ? ' is-active active' : ''; ?>">
                            <div class="about-content">
                                <?php echo $slide_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="slideshow-bullets-wrapper">
                <button type="button" class="slideshow-prev btn-pagination small-pagination" aria-label="<?php esc_attr_e( 'Anterior diapositiva', 'thecrisisacademy' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"></path></svg>                
                </button>
                <div class="slideshow-bullets bullets">
                    <?php foreach ( $slides as $index => $slide_html ) : ?>
                        <div class="bullet<?php echo 0 === $index ? ' active' : ''; ?>" data-index="<?php echo esc_attr( $index ); ?>"></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="slideshow-next btn-pagination small-pagination" aria-label="<?php esc_attr_e( 'Siguiente diapositiva', 'thecrisisacademy' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path></svg>                
                </button>
            </div>
        </div>
        <div class="section-header">
            <span class="sub-heading pretext-reveal">
                <?php echo esc_html( $about_preheading ); ?>
            </span>
            <h2 class="title-section title-reveal">
                <?php echo wp_kses_post( $about_title ); ?>
            </h2>
            <h3 class="subtitle-section object-reveal">
                <?php echo wp_kses_post( $about_subtitle ); ?>
            </h3>
            <p class="object-reveal"><?php echo wp_kses_post( $about_description ); ?></p>
        </div>
    </div>
    <div class="content content-grid timeline-3d-showcase">
        <div class="left-bar">
            <div class="section-header">
                <h2 class="title-section title-reveal"><?php echo esc_html( $about_modules_title ); ?></h2>
            </div>
            <div class="timeline-nav-stepper object-reveal" role="tablist" aria-label="Módulos de especialización">
                <?php foreach ( $modules as $index => $mod ) : 
                    $is_active = ( 0 === $index );
                    $icon_key  = $mod['icon'] ?? 'alert-circle';
                ?>
                    <button type="button" class="timeline-step-btn <?php echo $is_active ? 'is-active' : ''; ?>" data-timeline-index="<?php echo esc_attr( $index ); ?>" role="tab" aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                        <span class="step-node-badge">
                            <span class="step-node-num">
                                <?php echo function_exists( 'thecrisisacademy_get_trouble_icon_svg' ) ? thecrisisacademy_get_trouble_icon_svg( $icon_key, 20, 20, 2 ) : ''; ?>
                            </span>
                            <span class="timeline-beacon-ring"></span>
                        </span>
                        <span class="step-meta">
                            <span class="step-period"><?php echo esc_html( $mod['period'] ); ?></span>
                            <span class="step-title"><?php echo esc_html( $mod['short_title'] ); ?></span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Controls toolbar inside stepper column -->
            <div class="timeline-controls-bar card-reveal">
                <button type="button" class="timeline-ctrl-btn timeline-prev-btn" aria-label="<?php esc_attr_e( 'Módulo anterior', 'thecrisisacademy' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>

                <div class="timeline-autoplay-indicator" title="<?php esc_attr_e( 'Reproducción automática', 'thecrisisacademy' ); ?>">
                    <button type="button" class="timeline-play-toggle" aria-label="<?php esc_attr_e( 'Pausar/Reanudar', 'thecrisisacademy' ); ?>">
                        <span class="play-icon">▶</span>
                        <span class="pause-icon">❚❚</span>
                    </button>
                    <div class="timeline-autoplay-bar">
                        <div class="timeline-autoplay-fill"></div>
                    </div>
                </div>

                <button type="button" class="timeline-ctrl-btn timeline-next-btn" aria-label="<?php esc_attr_e( 'Siguiente módulo', 'thecrisisacademy' ); ?>">
                    <span><?php esc_html_e( 'Siguiente', 'thecrisisacademy' ); ?></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <div class="right-bar timeline-stage-wrapper card-reveal">
            <div class="timeline-3d-stage">
                <?php foreach ( $modules as $index => $mod ) :
                    $is_active    = ( 0 === $index );
                    $num_str      = sprintf( '%02d', $index + 1 );
                    $tag_class    = ! empty( $mod['tag_class'] ) ? $mod['tag_class'] : 'alert-tag';
                    $icon_key     = $mod['icon'] ?? 'alert-circle';
                    $is_last      = ( $index === $total_modules - 1 );
                    $btn_text     = $is_last ? __( 'Reiniciar', 'thecrisisacademy' ) : __( 'Siguiente', 'thecrisisacademy' );
                ?>
                    <!-- Card <?php echo esc_attr( $index ); ?>: <?php echo esc_html( $mod['short_title'] ); ?> -->
                    <article class="timeline-3d-card <?php echo $is_active ? 'is-active' : 'is-stacked'; ?>" data-card-index="<?php echo esc_attr( $index ); ?>" data-stack-offset="<?php echo esc_attr( $index ); ?>" style="--card-index: <?php echo esc_attr( $index ); ?>;">
                        <div class="timeline-card-front hover-glow">
                            <span class="timeline-card-watermark" aria-hidden="true"><?php echo esc_html( $num_str ); ?></span>
                            <div class="timeline-card-header">
                                <div class="timeline-meta-row">
                                    <span class="sub-heading timeline">
                                        <?php echo function_exists( 'thecrisisacademy_get_trouble_icon_svg' ) ? thecrisisacademy_get_trouble_icon_svg( $icon_key, 15, 15, 2 ) : ''; ?>
                                        <span><?php echo esc_html( sprintf( 'Módulo %s', $num_str ) ); ?></span>
                                    </span>
                                    <?php if ( ! empty( $mod['tag'] ) ) : ?>
                                        <span class="sub-heading <?php echo esc_attr( $tag_class ); ?>"><?php echo esc_html( $mod['tag'] ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="timeline-card-body">
                                <h3><?php echo esc_html( $mod['title'] ); ?></h3>
                                <?php if ( ! empty( $mod['description'] ) ) : ?>
                                    <p><?php echo esc_html( $mod['description'] ); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-card-footer">
                                <div class="sub-heading">
                                    <span class="status-pulse-dot"></span>
                                    <span><?php echo esc_html( sprintf( __( 'Módulo %s de %02d', 'thecrisisacademy' ), $num_str, $total_modules ) ); ?></span>
                                </div>
                                <button type="button" class="btn sub-heading timeline timeline-card-drop-trigger" aria-label="<?php echo esc_attr( $is_last ? __( 'Volver al inicio', 'thecrisisacademy' ) : __( 'Siguiente módulo', 'thecrisisacademy' ) ); ?>">
                                    <span><?php echo esc_html( $btn_text ); ?></span>
                                    <?php if ( $is_last ) : ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                                    <?php else : ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>