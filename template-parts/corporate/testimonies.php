<?php
/**
 * Corporate Page: Testimonies Section
 *
 * Coverflow 3D interactive testimonial carousel and witness avatar selector.
 *
 * @package TheCrisisAcademy
 */

$data  = thecrisisacademy_get_testimonies_data();
$items = ! empty( $data['items'] ) ? $data['items'] : array();

if ( empty( $items ) ) {
	return;
}

$middle_idx = (int) floor( count( $items ) / 2 );
?>
<section id="testimonies" class="block white-background-00">
    <div class="content">
        <div class="section-header center">
            <span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( wp_strip_all_tags( $data['preheading'] ) ); ?>"><?php echo esc_html( $data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?php echo wp_kses( $data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'em' => array(), 'strong' => array() ) ); ?></h2>
        </div>

        <div class="testimonies-interactive-container object-reveal">
            <!-- Avatars at the top -->
            <div class="testimonies-avatars-row">
                <?php foreach ( $items as $i => $item ) : ?>
                    <div class="avatar-item<?php echo $i === $middle_idx ? ' active' : ''; ?>" data-index="<?php echo (int) $i; ?>" title="<?php echo esc_attr( $item['name'] ); ?>">
                        <div class="avatar-ring"></div>
                        <div class="avatar-img-wrapper">
                            <?php if ( ! empty( $item['avatar_url'] ) ) : ?>
                                <img src="<?php echo esc_url( $item['avatar_url'] ); ?>" alt="<?php echo esc_attr( ! empty( $item['avatar_alt'] ) ? $item['avatar_alt'] : $item['name'] ); ?>" width="60" height="60" loading="lazy">
                            <?php else : ?>
                                <div style="width:60px; height:60px; border-radius:50%; background:#1e293b; display:flex; align-items:center; justify-content:center; color:#ffffff; font-weight:700; font-size:1.2rem;">
                                    <?php echo esc_html( mb_substr( $item['name'], 0, 1 ) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Testimonial Cards stack -->
            <div class="testimonies-cards-stack">
                <?php foreach ( $items as $i => $item ) :
                    $card_class = 'testimony-card';
                    if ( $i === $middle_idx ) {
                        $card_class .= ' active';
                    } elseif ( $i > $middle_idx ) {
                        $card_class .= ' next';
                    } else {
                        $card_class .= ' prev';
                    }
                ?>
                    <div class="<?php echo esc_attr( $card_class ); ?>" data-index="<?php echo (int) $i; ?>">
                        <div class="quote-symbol">“</div>
                        <p class="testimony-text"><?php echo esc_html( $item['text'] ); ?></p>
                        <div class="testimony-author">
                            <h3><?php echo esc_html( $item['name'] ); ?></h3>
                            <span><?php echo esc_html( $item['role'] ); ?></span>
                        </div>
                        <div class="post__overlay"></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Controls -->
            <div class="testimonies-controls">
                <button type="button" class="testi-prev sub-heading" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="testi-bullets">
                    <?php foreach ( $items as $i => $item ) : ?>
                        <div class="bullet<?php echo $i === $middle_idx ? ' active' : ''; ?>" data-index="<?php echo (int) $i; ?>"></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="testi-next sub-heading" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>