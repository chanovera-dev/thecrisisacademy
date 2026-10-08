<?php
/**
 * Template part for Hearings section in Corporate template
 * Interactive Radar visualizer on left, interactive accordion on right.
 *
 * @package TheCrisisAcademy
 */

$hearings_data = function_exists( 'thecrisisacademy_get_hearings_data' )
	? thecrisisacademy_get_hearings_data()
	: array();

$preheading = $hearings_data['preheading'] ?? 'Impacto 360°';
$title      = $hearings_data['title'] ?? 'Una crisis de reputación no es solo un asunto de comunicación.';
$items      = $hearings_data['items'] ?? array();

// Allowed tags for section title
$allowed_title_tags = array(
	'br'     => array(),
	'span'   => array( 'class' => array() ),
	'em'     => array(),
	'strong' => array(),
);

$quadrant_classes = array( 'q-comms', 'q-hr', 'q-csuite', 'q-ops' );
?>
<section id="hearings" class="block blue-background-00">
    <div class="content content-grid">
        <!-- Left Side: Interactive Radar Visualizer -->
        <div class="hearings-visual">
            <div class="section-header center">
                <span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses( $title, $allowed_title_tags ); ?></h2>
            </div>
            
            <div class="radar-container object-reveal">
                <div class="radar-circle circle-1"></div>
                <div class="radar-circle circle-2"></div>
                <div class="radar-circle circle-3"></div>
                <div class="radar-crosshair-h"></div>
                <div class="radar-crosshair-v"></div>
                
                <!-- Radar Quadrants (highlighted by active card) -->
                <?php foreach ( $items as $q_idx => $item ) : 
                    if ( $q_idx >= 4 ) break; // Radar has 4 physical quadrants
                    $q_class   = $quadrant_classes[ $q_idx ] ?? 'q-comms';
                    $q_slug    = ! empty( $item['slug'] ) ? $item['slug'] : sanitize_title( $item['radar_label'] ?: "dept-{$q_idx}" );
                    $is_active = ( 0 === $q_idx );
                ?>
                    <div class="radar-quadrant <?php echo esc_attr( $q_class ); ?> <?php echo $is_active ? 'active' : ''; ?>" data-target="<?php echo esc_attr( $q_slug ); ?>">
                        <span class="quadrant-label"><?php echo esc_html( $item['radar_label'] ?? '' ); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="radar-pulse"></div>
            </div>
        </div>

        <!-- Right Side: Interactive Stacked Panels -->
        <?php if ( ! empty( $items ) ) : ?>
            <div class="accordion-interactive-list card-reveal">
                <?php foreach ( $items as $index => $item ) : 
                    $is_active = ( 0 === $index );
                    $num_str   = sprintf( '%02d', $index + 1 );
                    $q_slug    = ! empty( $item['slug'] ) ? $item['slug'] : sanitize_title( $item['radar_label'] ?: "dept-{$index}" );
                ?>
                    <div class="accordion-item <?php echo $is_active ? 'active' : ''; ?>" data-department="<?php echo esc_attr( $q_slug ); ?>">
                        <div class="sub-heading accordion-number"><?php echo esc_html( $num_str ); ?></div>
                        <div class="accordion-main">
                            <h3>
                                <?php echo esc_html( $item['title'] ?? '' ); ?>
                                <?php if ( ! empty( $item['badge'] ) ) : ?>
                                    <span class="sub-heading"><?php echo esc_html( $item['badge'] ); ?></span>
                                <?php endif; ?>
                            </h3>
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
        <?php endif; ?>
    </div>
</section>