<?php
/**
 * Corporate Template Part: Program Section
 *
 * Implements dynamic fields and repeater for .process-steps-track carousel.
 *
 * @package TheCrisisAcademy
 */

$program_data = thecrisisacademy_get_program_data();
$steps        = ! empty( $program_data['steps'] ) ? $program_data['steps'] : array();
?>
<section id="program" class="block">
    <div class="content">
        <div class="section-header center">
            <span class="sub-heading pretext-reveal" aria-label="<?= esc_attr( wp_strip_all_tags( $program_data['preheading'] ) ); ?>"><?= esc_html( $program_data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?= wp_kses( $program_data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'strong' => array(), 'em' => array() ) ); ?></h2>
        </div>

        <div class="state-program tabs object-reveal">
            <input type="radio" id="btn1" name="tab-control">
            <input type="radio" id="btn2" name="tab-control" checked>

            <div class="state-buttons">
                <label for="btn1">
                    <div class="sub-heading timeline warning">
                        <span class="status-pulse-dot"></span>
                        <span class="label"><?= esc_html( $program_data['before_label'] ); ?></span>
                    </div>
                </label>
                <label for="btn2">
                    <div class="sub-heading timeline success">
                        <span class="status-pulse-dot"></span>
                        <span class="label"><?= esc_html( $program_data['after_label'] ); ?></span>
                    </div>
                </label>
            </div>

            <div class="state-program--container">
                <div class="state-program--before">
                    <header class="state-program-header">
                        <h3><?= esc_html( $program_data['before_title'] ); ?></h3>
                        <p><?= esc_html( $program_data['before_description'] ); ?></p>
                    </header>
                </div>
                <div class="state-program--after">
                    <header class="state-program-header">
                        <h3><?= esc_html( $program_data['after_title'] ); ?></h3>
                        <p><?= esc_html( $program_data['after_description'] ); ?></p>
                    </header>

                    <div class="corporate-slideshow process-carousel-wrapper" data-effect="fade" data-autoplay="6000">
                        <div class="process-steps-track" id="aboutMetricsTrack">
                            <?php foreach ( $steps as $index => $step ) :
                                $is_active = ( 0 === $index );
                                $phase_num = sprintf( '%02d', $index + 1 );
                                $alt_text  = ! empty( $step['photo_alt'] ) ? $step['photo_alt'] : ( ! empty( $step['short_title'] ) ? $step['short_title'] : $step['title'] );
                            ?>
                                <div class="process-step-item about-metric-step <?= $is_active ? 'active' : ''; ?>" data-index="<?= esc_attr( $index ); ?>">
                                    <div class="process-step-visual about-metric-visual">
                                        <img src="<?= esc_url( $step['photo_url'] ); ?>" alt="<?= esc_attr( $alt_text ); ?>" class="metric-bg-photo" loading="lazy" width="1280" height="720">
                                        <div class="metric-slide-blueprint">
                                            <div class="metric-slide-main-figure">
                                                <div class="big-badge sub-heading">
                                                    <?= thecrisisacademy_get_program_icon_svg( $step['icon'] ); ?>
                                                </div>
                                                <div class="metric-slide-text-group">
                                                    <strong class="metric-slide-big-value"><?= esc_html( $phase_num ); ?></strong>
                                                    <span class="metric-slide-sub-title"><?= esc_html( $step['short_title'] ); ?></span>
                                                </div>
                                            </div>
                                            <div class="metric-slide-footer-bars" aria-hidden="true">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="process-step-content about-metric-content">
                                        <div class="process-step-header">
                                            <h3><?= esc_html( $step['title'] ); ?></h3>
                                        </div>
                                        <p><?= esc_html( $step['description'] ); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="process-carousel-controls">
                            <button type="button" class="slideshow-prev sub-heading" id="aboutMetricsPrevBtn" aria-label="<?php esc_attr_e( 'Fase anterior', 'thecrisisacademy' ); ?>">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <div class="carousel-dots" id="aboutMetricsDots">
                                <?php foreach ( $steps as $i => $step ) : ?>
                                    <button type="button" class="carousel-dot <?= 0 === $i ? 'active' : ''; ?>" data-slide="<?= esc_attr( $i ); ?>" aria-label="<?= esc_attr( sprintf( __( 'Ir a fase %d', 'thecrisisacademy' ), $i + 1 ) ); ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="slideshow-next sub-heading" id="aboutMetricsNextBtn" aria-label="<?php esc_attr_e( 'Siguiente fase', 'thecrisisacademy' ); ?>">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>