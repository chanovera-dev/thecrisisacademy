<?php
/**
 * Template part for displaying the Signals section in Individuals template.
 *
 * This section features statistical indicators showcasing pre-crisis warning signals.
 * All content is managed through Advanced Custom Fields (ACF) with default fallbacks
 * from the live homepage (https://thecrisisacademy.com/crisis-academy-homepage/).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package TheCrisisAcademy
 * @subpackage Template-parts/individuals
 * @since 1.0.0
 * @version 1.0.0
 */

// Retrieve ACF field values with safe fallback to homepage defaults
$title    = function_exists( 'get_field' ) ? get_field( 'signals_title' ) : null;
$subtitle = function_exists( 'get_field' ) ? get_field( 'signals_subtitle' ) : null;

// Default fallback content from live homepage
$default_title    = '<h2 class="title-section title-reveal">La mayoría de las crisis <strong>sí dieron señales</strong> antes de explotar</h2>';
$default_subtitle = '<h3 class="subtitle-section title-reveal">Improvisar frente a una crisis no es un error operativo, es <strong>negligencia reputacional.</strong></h3>';

if ( empty( $title ) ) {
	$title = $default_title;
}

if ( empty( $subtitle ) ) {
	$subtitle = $default_subtitle;
}

// Retrieve repeater rows or use live homepage defaults
$signal_items = array();

if ( function_exists( 'have_rows' ) && have_rows( 'signals_container' ) ) {
	while ( have_rows( 'signals_container' ) ) {
		the_row();
		$signal_items[] = array(
			'icon'         => get_sub_field( 'signal_item_icon' ),
			'number'       => get_sub_field( 'signal_item_number' ),
			'label'        => get_sub_field( 'signal_item_label' ),
			'info'         => get_sub_field( 'signal_item_info' ),
			'source_label' => get_sub_field( 'signal_item_source_label' ),
			'source_url'   => get_sub_field( 'signal_item_source_url' ),
		);
	}
} elseif ( function_exists( 'get_field' ) ) {
	$raw_signals = get_field( 'signals_container' );
	if ( ! empty( $raw_signals ) && is_array( $raw_signals ) ) {
		foreach ( $raw_signals as $row ) {
			$signal_items[] = array(
				'icon'         => $row['signal_item_icon'] ?? ( $row['field_signals_item_icon'] ?? null ),
				'number'       => $row['signal_item_number'] ?? ( $row['field_signals_item_number'] ?? '' ),
				'label'        => $row['signal_item_label'] ?? ( $row['field_signals_item_label'] ?? '' ),
				'info'         => $row['signal_item_info'] ?? ( $row['field_signals_item_info'] ?? '' ),
				'source_label' => $row['signal_item_source_label'] ?? ( $row['field_signals_item_source_label'] ?? '' ),
				'source_url'   => $row['signal_item_source_url'] ?? ( $row['field_signals_item_source_url'] ?? '' ),
			);
		}
	}
}

// Fallback to default indicators if no repeater rows exist
if ( empty( $signal_items ) ) {
	$signal_items = array(
		array(
			'icon'         => null,
			'number'       => '75',
			'label'        => '',
			'info'         => '<p>de las crisis mostraron señales previas <strong>que nadie detectó</strong></p>',
			'source_label' => 'Institute for Crisis Management (ICM)',
			'source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
		array(
			'icon'         => null,
			'number'       => '11',
			'label'        => '',
			'info'         => '<p>de pérdida del valor del mercado en <strong>solo 5 días por una mala respuesta</strong></p>',
			'source_label' => 'PwC + Oxford Metrica',
			'source_url'   => 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/',
		),
		array(
			'icon'         => null,
			'number'       => '',
			'label'        => 'Hoy una crisis puede escalar en minutos',
			'info'         => '<p>por IA, redes sociales y desinformación</p>',
			'source_label' => 'Institute for Crisis Management (ICM)',
			'source_url'   => 'https://crisisconsultant.com/icm-annual-crisis-report/',
		),
	);
} else {
	// Ensure default individual sources are supplied if not yet set in existing ACF rows
	foreach ( $signal_items as &$item ) {
		if ( empty( $item['source_url'] ) ) {
			if ( '75' === (string) $item['number'] ) {
				$item['source_label'] = 'Institute for Crisis Management (ICM)';
				$item['source_url']   = 'https://crisisconsultant.com/icm-annual-crisis-report/';
			} elseif ( '11' === (string) $item['number'] ) {
				$item['source_label'] = 'PwC + Oxford Metrica';
				$item['source_url']   = 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/';
			} elseif ( empty( $item['number'] ) && false !== stripos( (string) $item['label'], 'escalar' ) ) {
				$item['source_label'] = 'Institute for Crisis Management (ICM)';
				$item['source_url']   = 'https://crisisconsultant.com/icm-annual-crisis-report/';
			}
		}
	}
	unset( $item );
}
?>
<section id="signals" class="block blue-background-01">
	<canvas id="signals-canvas" class="signals-canvas" aria-hidden="true"></canvas>
	<div class="content">
		<?php if ( $title ) : ?>
			<header class="section-header"><?php echo apply_filters( 'the_content', $title ); ?></header>
		<?php endif; ?>
		<div class="signals-container">
			<?php foreach ( $signal_items as $item ) : ?>
				<?php
					$icon         = $item['icon'];
					$number       = $item['number'];
					$label        = $item['label'];
					$info         = $item['info'];
					$source_label = ! empty( $item['source_label'] ) ? $item['source_label'] : '';
					$source_url   = ! empty( $item['source_url'] ) ? $item['source_url'] : '';
				?>
				<div class="signal-item card-reveal">
					<div class="signal-graph">
						<?php if ( $number ) : 
							$percent    = intval( $number );
							$dashoffset = 251.32 * ( 1 - $percent / 100 );
							$anim_id    = 'draw-ring-' . $percent . '-' . uniqid();
						?>
							<svg width="100%" height="100%" viewBox="0 0 100 100" class="tech-progress-svg" style="transform: rotate(-90deg);">
								<defs>
									<filter id="glow-<?= $percent ?>" x="-20%" y="-20%" width="140%" height="140%">
										<feGaussianBlur stdDeviation="3.5" result="blur" />
										<feMerge>
											<feMergeNode in="blur" />
											<feMergeNode in="SourceGraphic" />
										</feMerge>
									</filter>
									<linearGradient id="grad-<?= $percent ?>" x1="0%" y1="0%" x2="100%" y2="100%">
										<stop offset="0%" stop-color="#00d9ff" />
										<stop offset="100%" stop-color="#0077ff" />
									</linearGradient>
								</defs>
								
								<!-- Inner tech radar rings -->
								<circle cx="50" cy="50" r="46" stroke="rgba(0, 217, 255, 0.04)" stroke-width="1" fill="none" />
								<circle cx="50" cy="50" r="34" stroke="rgba(0, 217, 255, 0.04)" stroke-width="1" fill="none" />
								
								<!-- Background track -->
								<circle cx="50" cy="50" r="40" stroke="rgba(0, 217, 255, 0.08)" stroke-width="5" fill="none" stroke-linecap="round" />
								
								<!-- Animated Progress Ring -->
								<circle cx="50" cy="50" r="40" 
										stroke="url(#grad-<?= $percent ?>)" 
										stroke-width="5.5" 
										fill="none" 
										stroke-linecap="round"
										stroke-dasharray="251.32" 
										stroke-dashoffset="251.32"
										filter="url(#glow-<?= $percent ?>)"
										class="progress-ring-circle-<?= $percent ?>" />
										
								<!-- Outer cyber ticks -->
								<circle cx="50" cy="50" r="44" stroke="rgba(0, 217, 255, 0.25)" stroke-width="1.5" fill="none" stroke-dasharray="1.5 5.5" />
								
								<!-- Scanning radar needle -->
								<line x1="50" y1="50" x2="50" y2="10" stroke="rgba(0, 217, 255, 0.2)" stroke-width="1.5" stroke-linecap="round" style="transform-origin: 50px 50px; animation: radar-needle-sweep 3s linear infinite;" />
								
								<style>
									.signal-item.is-visible .progress-ring-circle-<?= $percent ?> {
										animation: <?= $anim_id ?> 2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
									}
									@keyframes <?= $anim_id ?> {
										from { stroke-dashoffset: 251.32; }
										to { stroke-dashoffset: <?= $dashoffset ?>; }
									}
									@keyframes radar-needle-sweep {
										from { transform: rotate(0deg); }
										to { transform: rotate(360deg); }
									}
								</style>
							</svg>
						<?php else : ?>
							<svg width="100%" height="100%" viewBox="0 0 100 100" class="tech-progress-svg" style="transform: rotate(-90deg);">
								<defs>
									<filter id="glow-spin" x="-20%" y="-20%" width="140%" height="140%">
										<feGaussianBlur stdDeviation="3.5" result="blur" />
										<feMerge>
											<feMergeNode in="blur" />
											<feMergeNode in="SourceGraphic" />
										</feMerge>
									</filter>
									<linearGradient id="grad-spin" x1="0%" y1="0%" x2="100%" y2="100%">
										<stop offset="0%" stop-color="#00d9ff" />
										<stop offset="100%" stop-color="#0077ff" />
									</linearGradient>
								</defs>
								
								<!-- Inner tech radar rings -->
								<circle cx="50" cy="50" r="46" stroke="rgba(0, 217, 255, 0.04)" stroke-width="1" fill="none" />
								<circle cx="50" cy="50" r="34" stroke="rgba(0, 217, 255, 0.04)" stroke-width="1" fill="none" />
								
								<!-- Background track -->
								<circle cx="50" cy="50" r="40" stroke="rgba(0, 217, 255, 0.08)" stroke-width="5" fill="none" stroke-linecap="round" />
								
								<!-- Infinite Spinning Short Glowing Arc -->
								<circle cx="50" cy="50" r="40" 
										stroke="url(#grad-spin)" 
										stroke-width="5.5" 
										fill="none" 
										stroke-linecap="round"
										stroke-dasharray="60 192.32" 
										stroke-dashoffset="0"
										filter="url(#glow-spin)"
										style="transform-origin: 50px 50px; animation: infinite-arc-spin 2s linear infinite;" />
										
								<!-- Outer cyber ticks -->
								<circle cx="50" cy="50" r="44" stroke="rgba(0, 217, 255, 0.25)" stroke-width="1.5" fill="none" stroke-dasharray="1.5 5.5" />
								
								<!-- Scanning radar needle -->
								<line x1="50" y1="50" x2="50" y2="10" stroke="rgba(0, 217, 255, 0.2)" stroke-width="1.5" stroke-linecap="round" style="transform-origin: 50px 50px; animation: radar-needle-sweep 3s linear infinite;" />
								
								<!-- Fallback static icon inside the center of the spinning ring -->
								<?php if ( $icon ) : ?>
									<g style="transform: rotate(90deg); transform-origin: 50px 50px;">
										<image href="<?= esc_url( is_array( $icon ) ? $icon['url'] : $icon ) ?>" x="32" y="32" width="36" height="36" style="opacity: 0.85;" />
									</g>
								<?php endif; ?>

								<style>
									@keyframes infinite-arc-spin {
										from { transform: rotate(0deg); }
										to { transform: rotate(360deg); }
									}
									@keyframes radar-needle-sweep {
										from { transform: rotate(0deg); }
										to { transform: rotate(360deg); }
									}
								</style>
							</svg>
						<?php endif; ?>
					</div>
					<?php if ( $number ) : ?>
						<div class="signal-number"><span class="number number-value-counter" data-target="<?= esc_attr( $number ) ?>" data-decimals="0" data-duration="7000"><?= esc_html( $number ) ?></span><span class="sign">%</span></div>
					<?php endif; ?>
					<?php if ( $label ) : ?>
						<span class="signal-label"><?= esc_html( $label ) ?></span>
					<?php endif; ?>
					<?php if ( $info ) : ?>
						<span class="signal-info"><?php echo apply_filters( 'the_content', $info ); ?></span>
					<?php endif; ?>
					<?php if ( $source_url && $source_label ) : ?>
						<span class="signal-source"><?php esc_html_e( 'Fuente:', 'thecrisisacademy' ); ?> <a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source_label ); ?></a></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $subtitle ) : ?>
			<?php echo apply_filters( 'the_content', $subtitle ); ?>
		<?php endif; ?>
	</div>
</section>