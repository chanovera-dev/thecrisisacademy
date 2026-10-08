<?php
/**
 * Corporate Template Part: Diff (Why Us) Section
 *
 * Implements dynamic fields and repeater for .diff-slide-item 3D flip slideshow.
 *
 * @package TheCrisisAcademy
 */

$diff_data = thecrisisacademy_get_diff_data();
$slides    = ! empty( $diff_data['slides'] ) ? $diff_data['slides'] : array();
$autoplay  = ! empty( $diff_data['autoplay'] ) ? $diff_data['autoplay'] : '14000';
?>
<section id="diff" class="block">
    <div class="content">
        <div class="section-header center">
            <span class="sub-heading pretext-reveal" aria-label="<?= esc_attr( wp_strip_all_tags( $diff_data['preheading'] ) ); ?>"><?= esc_html( $diff_data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?= wp_kses( $diff_data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'strong' => array(), 'em' => array() ) ); ?></h2>
        </div>

        <!-- The 3D flip slideshow container -->
        <div class="corporate-slideshow diff-slideshow-container card-reveal" data-effect="flip" data-autoplay="<?= esc_attr( $autoplay ); ?>">
            <div class="slideshow--wrapper">
                <div class="slideshow">
                    <?php foreach ( $slides as $i => $slide ) : 
                        $is_active = ( 0 === $i );
                    ?>
                        <div class="diff-slide-item <?= $is_active ? 'active' : ''; ?>">
                            <div class="big-badge sub-heading">
                                <?= thecrisisacademy_get_diff_icon_svg( $slide['icon'] ?? 'book-open' ); ?>
                            </div>
                            <h3><?= esc_html( $slide['title'] ); ?></h3>
                            <p><?= esc_html( $slide['description'] ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="slideshow-bullets-wrapper">
                <button type="button" class="slideshow-prev sub-heading" aria-label="<?php esc_attr_e( 'Anterior', 'thecrisisacademy' ); ?>">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="slideshow-bullets">
                    <?php foreach ( $slides as $i => $slide ) : ?>
                        <div class="bullet <?= 0 === $i ? 'active' : ''; ?>" data-index="<?= esc_attr( $i ); ?>"></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="slideshow-next sub-heading" aria-label="<?php esc_attr_e( 'Siguiente', 'thecrisisacademy' ); ?>">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>