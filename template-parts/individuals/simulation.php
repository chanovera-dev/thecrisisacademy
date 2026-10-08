<?php
/**
 * Individuals Template Part: Crisis Simulator Showcase
 *
 * Interactive showcase of the simulator stages. Tabs on the left drive a
 * dark "console" preview on the right (auto-advance with progress bars).
 * The CTA opens the simulator inside the shared lightbox (pane: crisis-simulator)
 * and falls back to the simulator page when JS is unavailable.
 *
 * @package TheCrisisAcademy
 * @subpackage Individuals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$simulation_data = function_exists( 'thecrisisacademy_get_individuals_simulation_data' )
	? thecrisisacademy_get_individuals_simulation_data()
	: array(
		'preheading'     => 'Simulador de crisis',
		'title'          => 'Experimenta la presión en tiempo real y descubre si tu equipo está preparado',
		'intro'          => 'Tres etapas, un mismo reloj. Recorre el ciclo completo de una crisis y mide cómo responde tu equipo cuando cada minuto cuenta.',
		'console_status' => 'Simulación en vivo',
		'cta_url'        => home_url( '/simulador-de-crisis/' ),
		'cta_label'      => 'Simular crisis',
		'cta_lightbox'   => 'crisis-simulator',
		'stages'         => function_exists( 'thecrisisacademy_get_default_simulation_stages_rows' ) ? thecrisisacademy_get_default_simulation_stages_rows() : array(),
	);

$preheading     = $simulation_data['preheading'];
$title          = $simulation_data['title'];
$intro          = $simulation_data['intro'];
$console_status = $simulation_data['console_status'];
$sim_cta_url    = $simulation_data['cta_url'];
$sim_cta_label  = $simulation_data['cta_label'];
$sim_cta_lb     = $simulation_data['cta_lightbox'];
$sim_stages     = $simulation_data['stages'];

// Verify if the Crisis Simulator plugin exists and is activated
$sim_plugin_rel_path = 'crisis-simulator/simulador-de-crisis.php';
$sim_plugin_abs_path = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR . '/' . $sim_plugin_rel_path : '';

if ( ! function_exists( 'is_plugin_active' ) && defined( 'ABSPATH' ) && file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$is_simulator_active = ( ! empty( $sim_plugin_abs_path ) && file_exists( $sim_plugin_abs_path ) ) && (
	( function_exists( 'is_plugin_active' ) && is_plugin_active( $sim_plugin_rel_path ) )
	|| shortcode_exists( 'simulador_de_crisis' )
	|| function_exists( 'sdc_simulator_shortcode' )
	|| in_array( $sim_plugin_rel_path, (array) apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true )
);
?>
<section id="crisis-simulator" class="block whiteprint-background">
	<div class="content">
		<header class="section-header center">
			<span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
			<h2 class="title-section title-reveal" aria-label="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"><?php echo wp_kses_post( $title ); ?></h2>
			<p class="simulator-intro object-reveal"><?php echo esc_html( $intro ); ?></p>
		</header>

		<div class="simulator-showcase content-grid" data-simulator-showcase>
			<!-- Stages (tabs) -->
			<div class="simulator-stages card-reveal">
				<div class="simulator-stages-list" role="tablist" aria-label="<?php esc_attr_e( 'Etapas del simulador de crisis', 'thecrisisacademy' ); ?>">
					<?php foreach ( $sim_stages as $i => $stage ) :
						$is_active = ( 0 === $i );
						$stage_id  = ! empty( $stage['id'] ) ? sanitize_html_class( $stage['id'] ) : 'stage-' . ( $i + 1 );
					?>
						<button
							type="button"
							role="tab"
							id="sim-tab-<?php echo esc_attr( $stage_id ); ?>"
							class="simulator-stage<?php echo $is_active ? ' is-active' : ''; ?>"
							aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
							aria-controls="sim-panel-<?php echo esc_attr( $stage_id ); ?>"
							tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
							data-stage-index="<?php echo (int) $i; ?>"
							data-kpi="<?php echo esc_attr( $stage['kpi'] ?? '' ); ?>"
							data-kpi-label="<?php echo esc_attr( $stage['kpi_label'] ?? '' ); ?>"
						>
							<span class="simulator-stage-text">
								<span class="sub-heading simulator-stage-code"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="simulator-stage-label"><?php echo esc_html( $stage['label'] ?? '' ); ?></span>
							</span>
							<span class="simulator-stage-desc">
								<span class="simulator-stage-title"><?php echo esc_html( $stage['title'] ?? '' ); ?></span>
								<p><?php echo esc_html( $stage['desc'] ?? '' ); ?></p>
							</span>
							<span class="simulator-stage-progress" aria-hidden="true"><span></span></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Console preview -->
			<div class="simulator-console card-reveal">
				<div class="simulator-console-bar">
					<span class="simulator-console-dots" aria-hidden="true"><i></i><i></i><i></i></span>
					<span class="simulator-console-status">
						<span class="simulator-console-pulse" aria-hidden="true"></span>
						<?php echo esc_html( $console_status ); ?>
					</span>
					<span class="simulator-console-clock" data-sim-clock aria-hidden="true">T+00:00:00</span>
				</div>

				<div class="simulator-console-screen">
					<?php if ( $is_simulator_active ) : ?>
						<a href="<?php echo esc_url( $sim_cta_url ); ?>" class="btn primary simulator-cta"<?php if ( ! empty( $sim_cta_lb ) ) : ?> data-open-lightbox="<?php echo esc_attr( $sim_cta_lb ); ?>"<?php endif; ?>>
							<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="4 17 10 11 4 5"></polyline>
								<line x1="12" y1="19" x2="20" y2="19"></line>
							</svg>
							<span><?php echo esc_html( $sim_cta_label ); ?></span>
						</a>
					<?php endif; ?>
					<?php foreach ( $sim_stages as $i => $stage ) :
						$is_active = ( 0 === $i );
						$stage_id  = ! empty( $stage['id'] ) ? sanitize_html_class( $stage['id'] ) : 'stage-' . ( $i + 1 );

						$img_src = '';
						$img_alt = ! empty( $stage['alt'] ) ? $stage['alt'] : ( ! empty( $stage['title'] ) ? $stage['title'] : '' );

						if ( ! empty( $stage['image'] ) ) {
							if ( is_array( $stage['image'] ) ) {
								$img_src = $stage['image']['url'] ?? '';
								if ( empty( $img_alt ) && ! empty( $stage['image']['alt'] ) ) {
									$img_alt = $stage['image']['alt'];
								}
							} elseif ( is_numeric( $stage['image'] ) ) {
								$img_src = wp_get_attachment_image_url( $stage['image'], 'full' );
								if ( empty( $img_alt ) ) {
									$img_alt = get_post_meta( $stage['image'], '_wp_attachment_image_alt', true );
								}
							} elseif ( is_string( $stage['image'] ) ) {
								$img_src = $stage['image'];
							}
						}

						// Fallback to local simulator assets if empty or pointing to legacy uploads URL
						if ( empty( $img_src ) || ( is_string( $img_src ) && strpos( $img_src, '/uploads/2026/05/' ) !== false ) ) {
							$theme_uri          = get_stylesheet_directory_uri();
							$local_fallback_map = array(
								'radar'        => $theme_uri . '/assets/img/simulator/radar.webp',
								'stakeholders' => $theme_uri . '/assets/img/simulator/stakeholders-map.webp',
								'war-room'     => $theme_uri . '/assets/img/simulator/war-room-1.webp',
							);
							$stg_key = $stage['id'] ?? '';
							if ( isset( $local_fallback_map[ $stg_key ] ) ) {
								$img_src = $local_fallback_map[ $stg_key ];
							} elseif ( is_string( $img_src ) && strpos( $img_src, 'radar' ) !== false ) {
								$img_src = $local_fallback_map['radar'];
							} elseif ( is_string( $img_src ) && strpos( $img_src, 'stakeholders' ) !== false ) {
								$img_src = $local_fallback_map['stakeholders'];
							} elseif ( is_string( $img_src ) && strpos( $img_src, 'war-room' ) !== false ) {
								$img_src = $local_fallback_map['war-room'];
							}
						}
					?>
						<figure
							role="tabpanel"
							id="sim-panel-<?php echo esc_attr( $stage_id ); ?>"
							class="simulator-screen<?php echo $is_active ? ' is-active' : ''; ?>"
							aria-labelledby="sim-tab-<?php echo esc_attr( $stage_id ); ?>"
							aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
						>
							<?php if ( ! empty( $img_src ) ) : ?>
								<img src="<?php echo esc_url( $img_src ); ?>" width="1000" height="429" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async">
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>

					<span class="simulator-scanline" aria-hidden="true"></span>
					<span class="simulator-corners" aria-hidden="true"><i></i><i></i><i></i><i></i></span>

					<?php
					$first_kpi       = ! empty( $sim_stages[0]['kpi'] ) ? $sim_stages[0]['kpi'] : '';
					$first_kpi_label = ! empty( $sim_stages[0]['kpi_label'] ) ? $sim_stages[0]['kpi_label'] : '';
					?>
					<div class="simulator-hud" aria-live="polite">
						<strong class="simulator-hud-kpi" data-sim-kpi><?php echo esc_html( $first_kpi ); ?></strong>
						<span class="simulator-hud-label" data-sim-kpi-label><?php echo esc_html( $first_kpi_label ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>