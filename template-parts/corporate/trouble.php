<?php
/**
 * Template part for Trouble section in Corporate template
 * Interactive 3D timeline showcase: Stepper navigation on left, 3D card deck on right.
 *
 * @package TheCrisisAcademy
 */

$trouble_data = function_exists( 'thecrisisacademy_get_trouble_data' )
	? thecrisisacademy_get_trouble_data()
	: array();

$preheading  = $trouble_data['preheading'] ?? 'El problema';
$title       = $trouble_data['title'] ?? 'Muchas empresas descubren que sus protocolos son del siglo pasado... <br><em>cuando ya es demasiado tarde.</em>';
$steps       = $trouble_data['steps'] ?? array();
$total_steps = count( $steps );

// Allowed tags for section title
$allowed_title_tags = array(
	'br'     => array(),
	'span'   => array( 'class' => array() ),
	'em'     => array(),
	'strong' => array(),
);
?>
<section id="trouble" class="block">
    <div class="content content-grid timeline-3d-showcase">
        <div class="left-bar">
            <div class="section-header">
                <span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses( $title, $allowed_title_tags ); ?></h2>
            </div>
            
            <?php if ( ! empty( $steps ) ) : ?>
                <div class="timeline-nav-stepper object-reveal" role="tablist" aria-label="<?php esc_attr_e( 'Etapas de la crisis', 'thecrisisacademy' ); ?>">
                    <?php foreach ( $steps as $index => $step ) : 
                        $is_active = ( 0 === $index );
                        $num_str   = sprintf( '%02d', $index + 1 );
                        $icon_key  = $step['icon'] ?? 'alert-circle';
                    ?>
                        <button type="button" class="timeline-step-btn <?php echo $is_active ? 'is-active' : ''; ?>" data-timeline-index="<?php echo esc_attr( $index ); ?>" role="tab" aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                            <span class="step-node-badge">
                                <span class="step-node-num">
                                    <?php echo function_exists( 'thecrisisacademy_get_trouble_icon_svg' ) ? thecrisisacademy_get_trouble_icon_svg( $icon_key, 20, 20, 2 ) : ''; ?>
                                </span>
                                <span class="timeline-beacon-ring"></span>
                            </span>
                            <span class="step-meta">
                                <span class="step-period"><?php echo esc_html( $num_str . ' • ' . ( $step['department'] ?? '' ) ); ?></span>
                                <span class="step-title"><?php echo esc_html( $step['title'] ?? '' ); ?></span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Controls toolbar inside stepper column -->
            <div class="timeline-controls-bar card-reveal">
                <button type="button" class="timeline-ctrl-btn timeline-prev-btn" aria-label="<?php esc_attr_e( 'Hito anterior', 'thecrisisacademy' ); ?>">
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

                <button type="button" class="timeline-ctrl-btn timeline-next-btn" aria-label="<?php esc_attr_e( 'Siguiente hito', 'thecrisisacademy' ); ?>">
                    <span><?php esc_html_e( 'Siguiente', 'thecrisisacademy' ); ?></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <div class="right-bar timeline-stage-wrapper card-reveal">
            <div class="timeline-3d-stage">
                <?php if ( ! empty( $steps ) ) : ?>
                    <?php foreach ( $steps as $index => $step ) :
                        $is_active    = ( 0 === $index );
                        $num_str      = sprintf( '%02d', $index + 1 );
                        $tag_class    = ! empty( $step['tag_class'] ) ? $step['tag_class'] : 'alert-tag';
                        $icon_key     = $step['icon'] ?? 'alert-circle';
                        $is_last      = ( $index === $total_steps - 1 );
                        $btn_text     = $is_last ? __( 'Reiniciar', 'thecrisisacademy' ) : __( 'Siguiente', 'thecrisisacademy' );
                        $points       = is_array( $step['points'] ) ? $step['points'] : array();
                        $points_count = count( $points );
                    ?>
                        <!-- Card <?php echo esc_attr( $index ); ?>: <?php echo esc_html( $step['department'] ?? '' ); ?> -->
                        <article class="timeline-3d-card <?php echo $is_active ? 'is-active' : 'is-stacked'; ?>" data-card-index="<?php echo esc_attr( $index ); ?>" data-stack-offset="<?php echo esc_attr( $index ); ?>" style="--card-index: <?php echo esc_attr( $index ); ?>;">
                            <div class="timeline-card-front hover-glow">
                                <span class="timeline-card-watermark" aria-hidden="true"><?php echo esc_html( $num_str ); ?></span>
                                <div class="timeline-card-header">
                                    <div class="timeline-meta-row">
                                        <span class="sub-heading timeline">
                                            <?php echo function_exists( 'thecrisisacademy_get_trouble_icon_svg' ) ? thecrisisacademy_get_trouble_icon_svg( $icon_key, 13, 13, 2.5 ) : ''; ?>
                                            <?php echo esc_html( $step['department'] ?? '' ); ?>
                                        </span>
                                        <?php if ( ! empty( $step['tag'] ) ) : ?>
                                            <span class="sub-heading <?php echo esc_attr( $tag_class ); ?>"><?php echo esc_html( $step['tag'] ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-card-body">
                                    <h3><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
                                    <?php if ( ! empty( $step['description'] ) ) : ?>
                                        <p><?php echo wp_kses( $step['description'], array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?></p>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $points ) ) : ?>
                                        <div class="points-slideshow">
                                            <ul class="card-points">
                                                <?php foreach ( $points as $p_idx => $point_text ) : 
                                                    if ( '' === trim( $point_text ) ) continue;
                                                ?>
                                                    <li class="<?php echo 0 === $p_idx ? 'is-active' : ''; ?>">
                                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                        <span><?php echo esc_html( $point_text ); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php if ( $points_count > 1 ) : ?>
                                                <div class="points-nav" role="tablist" aria-label="<?php esc_attr_e( 'Puntos clave', 'thecrisisacademy' ); ?>">
                                                    <?php for ( $pi = 0; $pi < $points_count; $pi++ ) : ?>
                                                        <button type="button" class="point-dot <?php echo 0 === $pi ? 'is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $pi ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Punto %d de %d', 'thecrisisacademy' ), $pi + 1, $points_count ) ); ?>"></button>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-card-footer">
                                    <div class="sub-heading">
                                        <span class="status-pulse-dot"></span>
                                        <span><?php echo esc_html( sprintf( __( 'Fase %s de %02d', 'thecrisisacademy' ), $num_str, $total_steps ) ); ?></span>
                                    </div>
                                    <button type="button" class="btn sub-heading timeline timeline-card-drop-trigger" aria-label="<?php echo esc_attr( $is_last ? __( 'Volver al inicio', 'thecrisisacademy' ) : __( 'Siguiente fase', 'thecrisisacademy' ) ); ?>">
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>