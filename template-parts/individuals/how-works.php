<?php
/**
 * Template part for displaying the How Works section in Individuals template.
 *
 * This section features the interactive radar visualizer and an accordion list
 * powered by ACF repeater fields with safe fallbacks.
 *
 * @package TheCrisisAcademy
 * @subpackage Individuals
 */

// Register extended content panes inside the shared lightbox (template-parts/corporate/lightbox.php)
add_action( 'tca_lightbox_panes', function () {
	get_template_part( 'template-parts/individuals/how-works-panes' );
} );

// Retrieve ACF field values with safe fallback to defaults
$preheading = function_exists( 'get_field' ) ? get_field( 'how_works_preheading' ) : '';
$title      = function_exists( 'get_field' ) ? get_field( 'how_works_title' ) : '';

if ( empty( $preheading ) ) {
	$preheading = '¿Cómo funciona?';
}
if ( empty( $title ) ) {
	$title = 'Tres soluciones para fortalecer tu preparación ante una crisis';
}

$how_works_items = array();

if ( function_exists( 'have_rows' ) && have_rows( 'how_works_items' ) ) {
	while ( have_rows( 'how_works_items' ) ) {
		the_row();
		$how_works_items[] = array(
			'department'      => get_sub_field( 'department' ),
			'radar_code'      => get_sub_field( 'radar_code' ),
			'number'          => get_sub_field( 'number' ),
			'title'           => get_sub_field( 'title' ),
			'description'     => get_sub_field( 'description' ),
			'bullets'         => get_sub_field( 'bullets' ),
			'button_label'    => get_sub_field( 'button_label' ),
			'lightbox_target' => get_sub_field( 'lightbox_target' ),
		);
	}
} elseif ( function_exists( 'get_field' ) ) {
	$raw_items = get_field( 'how_works_items' );
	if ( ! empty( $raw_items ) && is_array( $raw_items ) ) {
		foreach ( $raw_items as $row ) {
			$how_works_items[] = array(
				'department'      => $row['department'] ?? ( $row['field_how_works_item_dept'] ?? '' ),
				'radar_code'      => $row['radar_code'] ?? ( $row['field_how_works_item_radar_code'] ?? '' ),
				'number'          => $row['number'] ?? ( $row['field_how_works_item_number'] ?? '' ),
				'title'           => $row['title'] ?? ( $row['field_how_works_item_title'] ?? '' ),
				'description'     => $row['description'] ?? ( $row['field_how_works_item_desc'] ?? '' ),
				'bullets'         => $row['bullets'] ?? ( $row['field_how_works_item_bullets'] ?? '' ),
				'button_label'    => $row['button_label'] ?? ( $row['field_how_works_item_button_label'] ?? 'Más info' ),
				'lightbox_target' => $row['lightbox_target'] ?? ( $row['field_how_works_item_lb_target'] ?? '' ),
			);
		}
	}
}

if ( empty( $how_works_items ) && function_exists( 'thecrisisacademy_get_default_how_works_items_rows' ) ) {
	$raw_defaults = thecrisisacademy_get_default_how_works_items_rows();
	foreach ( $raw_defaults as $row ) {
		$how_works_items[] = array(
			'department'      => $row['department'] ?? '',
			'radar_code'      => $row['radar_code'] ?? '',
			'number'          => $row['number'] ?? '',
			'title'           => $row['title'] ?? '',
			'description'     => $row['description'] ?? '',
			'bullets'         => $row['bullets'] ?? '',
			'button_label'    => $row['button_label'] ?? 'Más info',
			'lightbox_target' => $row['lightbox_target'] ?? '',
		);
	}
}
?>
<section id="how-works" class="block blue-background-00">
	<div class="content content-grid">
		<!-- Left Side: Interactive Radar Visualizer -->
		<div class="how-works--visual">
			<div class="section-header center">
				<div class="logo card-reveal">
					<div class="ring-wrap">
						<div class="glow-ring"></div>
						<div class="scene">
							<div class="cube">
								<div class="face front">
									<div class="grid"></div>
									<div class="ai-eye">
										<div class="pupil"></div>
										<span class="node node-1"></span>
										<span class="node node-2"></span>
										<span class="node node-3"></span>
										<span class="node node-4"></span>
									</div>
								</div>
								<div class="face back">
									<div class="grid"></div>
								</div>
								<div class="face left">
									<div class="grid"></div>
								</div>
								<div class="face right">
									<div class="grid"></div>
								</div>
								<div class="face top">
									<div class="grid"></div>
								</div>
								<div class="face bottom">
									<div class="grid"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
				<h2 class="title-section title-reveal" aria-label="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"><?php echo wp_kses_post( $title ); ?></h2>
			</div>
			
			<div class="radar-container object-reveal">
				<div class="radar-circle circle-1"></div>
				<div class="radar-circle circle-2"></div>
				<div class="radar-circle circle-3"></div>
				<div class="radar-crosshair-h"></div>
				<div class="radar-crosshair-v"></div>
				
				<!-- Radar Quadrants (highlighted by active card) -->
				<?php if ( ! empty( $how_works_items ) ) : ?>
					<?php foreach ( $how_works_items as $index => $item ) : 
						$dept       = ! empty( $item['department'] ) ? sanitize_html_class( $item['department'] ) : 'dept-' . ( $index + 1 );
						$radar_code = ! empty( $item['radar_code'] ) ? $item['radar_code'] : ( ! empty( $item['department'] ) ? strtoupper( $item['department'] ) : '' );
						$is_active  = ( 0 === $index ) ? ' active' : '';
					?>
						<div class="radar-quadrant q-<?php echo esc_attr( $dept ); ?><?php echo esc_attr( $is_active ); ?>" data-target="<?php echo esc_attr( $dept ); ?>">
							<span class="quadrant-label"><?php echo esc_html( $radar_code ); ?></span>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
								
				<div class="radar-pulse"></div>
			</div>
		</div>

		<!-- Right Side: Interactive Stacked Panels -->
		<div class="accordion-interactive-list card-reveal">
			<?php if ( ! empty( $how_works_items ) ) : ?>
				<?php foreach ( $how_works_items as $index => $item ) : 
					$dept         = ! empty( $item['department'] ) ? sanitize_html_class( $item['department'] ) : 'dept-' . ( $index + 1 );
					$number       = ! empty( $item['number'] ) ? $item['number'] : sprintf( '%02d', $index + 1 );
					$item_title   = $item['title'] ?? '';
					$item_desc    = $item['description'] ?? '';
					$btn_label    = ! empty( $item['button_label'] ) ? $item['button_label'] : __( 'Más info', 'thecrisisacademy' );
					$lb_target    = ! empty( $item['lightbox_target'] ) ? $item['lightbox_target'] : 'how-works-' . $dept;
					$is_active    = ( 0 === $index ) ? ' active' : '';

					// Parse bullets (one per line)
					$raw_bullets  = $item['bullets'] ?? '';
					$bullet_lines = array();
					if ( is_array( $raw_bullets ) ) {
						$bullet_lines = $raw_bullets;
					} elseif ( is_string( $raw_bullets ) && '' !== trim( $raw_bullets ) ) {
						$bullet_lines = array_filter( array_map( 'trim', explode( "\n", $raw_bullets ) ) );
					}
				?>
				<div class="accordion-item<?php echo esc_attr( $is_active ); ?>" data-department="<?php echo esc_attr( $dept ); ?>">
					<div class="sub-heading accordion-number"><?php echo esc_html( $number ); ?></div>
					<div class="accordion-main">
						<h3><?php echo esc_html( $item_title ); ?></h3>
						<div class="accordion-expandable">
							<div class="accordion-expandable-inner">
								<?php if ( ! empty( $item_desc ) ) : ?>
									<p><?php echo esc_html( $item_desc ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $bullet_lines ) ) : ?>
									<ul>
										<?php foreach ( $bullet_lines as $bullet ) : ?>
											<li><?php echo esc_html( $bullet ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<button type="button" class="btn-more-info sub-heading timeline" data-title="<?php echo esc_attr( $item_title ); ?>" data-open-lightbox="<?php echo esc_attr( $lb_target ); ?>" aria-haspopup="dialog" aria-controls="corporate-lightbox">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path></svg> 
									<?php echo esc_html( $btn_label ); ?>
								</button>
							</div>
						</div>
					</div>
					<div class="accordion-arrow">
						<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
					</div>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>