<?php
/**
 * Template part for Founder section in Corporate template
 * Academic Leadership, Founder bio, methodology accordion, and credentials.
 *
 * @package TheCrisisAcademy
 */

$founder_data = function_exists( 'thecrisisacademy_get_founder_data' )
	? thecrisisacademy_get_founder_data()
	: array();

$photo_url         = $founder_data['photo_url'] ?? '';
$photo_alt         = $founder_data['photo_alt'] ?? 'Carolina Eslava - Fundadora';
$preheading        = $founder_data['preheading'] ?? 'Liderazgo Académico';
$name              = $founder_data['name'] ?? 'Carolina Eslava';
$role              = $founder_data['role'] ?? 'Fundadora & Directora de The Crisis Academy';
$quote             = $founder_data['quote'] ?? '';
$methodology_title = $founder_data['methodology_title'] ?? 'Metodología Basada en Investigación Científica';
$methodology_items = $founder_data['methodology_items'] ?? array();
$stat_number       = $founder_data['stat_number'] ?? '+2,000';
$stat_label        = $founder_data['stat_label'] ?? 'Ejecutivos entrenados bajo simulación de crisis activa en toda la región.';
?>
<section id="founder" class="block whiteprint-background">
    <div class="content content-grid">
            
            <!-- Left Side: Elegant Portrait Photo -->
            <div class="founder-visual">
                <div class="visual-frame object-reveal">
                    <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $photo_alt ); ?>" width="400" height="400" class="founder-photo" loading="lazy">
                    <div class="post__overlay"></div>
                </div>
            </div>

            <!-- Right Side: Content Bio & Methodology -->
            <div class="founder-text section-header">
                <span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
                <h2 class="title-section founder-name title-reveal"><?php echo esc_html( $name ); ?></h2>
                <div class="subtitle-section founder-role object-reveal"><?php echo esc_html( $role ); ?></div>

                <?php if ( ! empty( $quote ) ) : ?>
                    <div class="card-quote card-reveal">
                        <div class="aside-decor" aria-hidden="true">
                            <div class="aside-holes">
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                                <span class="aside-hole"></span>
                            </div>
                        </div>
                        <svg class="quote-icon" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path>
                        </svg>
                        <p><?php echo wp_kses_post( $quote ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $methodology_items ) ) : ?>
                    <div class="founder-methodology object-reveal">
                        <?php if ( ! empty( $methodology_title ) ) : ?>
                            <h3><?php echo esc_html( $methodology_title ); ?></h3>
                        <?php endif; ?>
                        
                        <div class="accordion-interactive-list">
                            <?php foreach ( $methodology_items as $index => $item ) : 
                                $is_active = ( 0 === $index );
                                $icon_key  = $item['icon'] ?? 'book';
                            ?>
                                <div class="accordion-item <?php echo $is_active ? 'active' : ''; ?>">
                                    <div class="sub-heading accordion-icon">
                                        <?php echo function_exists( 'thecrisisacademy_get_founder_icon_svg' ) ? thecrisisacademy_get_founder_icon_svg( $icon_key ) : ''; ?>
                                    </div>
                                    <div class="accordion-main">
                                        <h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
                                        <?php if ( ! empty( $item['description'] ) ) : ?>
                                            <div class="accordion-expandable">
                                                <p><?php echo wp_kses( $item['description'], array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="accordion-arrow">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Big Callout Stat -->
                <?php if ( ! empty( $stat_number ) || ! empty( $stat_label ) ) : ?>
                    <div class="founder-counter card-reveal">
                        <?php if ( ! empty( $stat_number ) ) : ?>
                            <div class="counter-number"><?php echo esc_html( $stat_number ); ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $stat_label ) ) : ?>
                            <div class="counter-label"><?php echo esc_html( $stat_label ); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
    </div>
</section>