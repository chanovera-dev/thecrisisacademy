<?php
/**
 * The Footer for the Stories theme (essentialis footer structure)
 *
 * Closes the main tag, displays the footer section, and calls wp_footer().
 *
 * @package Stories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	</main><!-- #main -->

	<footer id="main-footer" class="site-footer">
		<section class="block middle-footer">
			<div class="content">
				<div class="about">
					<?php
					$stories_options = get_option( 'stories_theme_options', array() );
					$current_lang    = function_exists( 'stories_get_current_language' ) ? stories_get_current_language() : 'es';
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'Home', 'stories' ); ?>"> 
                        <div class="logo">
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
                            <div class="text-group">
                                <span class="brand-name">The Crisis Academy</span>
                                <div class="divider"></div>
                                <p class="tagline">Liderazgo en tiempos de crisis</p>
                            </div>
                        </div>
                    </a>
					<p class="site-bio">
						<?php
						$bio_default = __( 'Relatos y Cartas es un espacio dedicado a la creatividad y la expresión a través de las palabras. Aquí encontrarás cuentos, microcuentos, poemas e historias que buscan inspirar, emocionar y conectar con los lectores.', 'stories' );
						$bio = !empty($stories_options['footer_bio'])
							? $stories_options['footer_bio']
							: get_option('essentialis_bio', get_option('stories_bio'));

						if (false === $bio || empty($bio)) {
							$bio = get_theme_mod('essentialis_bio', get_theme_mod('stories_bio', $bio_default));
						}

						if ( 'en' === $current_lang && ! empty( $stories_options['footer_bio_en'] ) ) {
							$final_bio = $stories_options['footer_bio_en'];
						} else {
							$final_bio = function_exists( 'stories_translate_footer_bio' ) ? stories_translate_footer_bio( $bio ) : $bio;
						}
						echo wp_kses_post( $final_bio );
						?>
					</p>
					<?php
					wp_nav_menu(
						array(
							'container_id'    => 'social',
							'container_class' => 'social',
							'theme_location'  => 'social',
							'fallback_cb'     => false,
						)
					);
					?>
				</div>
				<div class="other-links">
					<?php
					$footer_menus   = array('footer-1', 'footer-2', 'footer-3');
					$menu_locations = get_nav_menu_locations();

					foreach ($footer_menus as $location):
						if (isset($menu_locations[$location])):
							$menu_id    = $menu_locations[$location];
							$menu_obj   = wp_get_nav_menu_object($menu_id);
							$menu_items = wp_get_nav_menu_items($menu_id);

							if (!empty($menu_items)): ?>
								<div class="group-links">
									<h3 class="title-section"><?php echo esc_html($menu_obj->name); ?></h3>
									<?php
									wp_nav_menu(array(
										'container'       => 'nav',
										'container_class' => 'footer',
										'theme_location'  => $location,
									));
									?>
								</div>
							<?php endif;
						endif;
					endforeach;
					?>
				</div>
			</div>
		</section>
		<section class="block end-footer">
			<div class="content">
				<p>© <?php bloginfo('name'); echo ' ' . date("Y"); ?> • <?php esc_html_e('Todos los Derechos Reservados', 'stories'); ?></p>
				<div class="credit">
					<p>
						<?php
						printf(
							/* translators: %s: Author website link. */
							__( 'Diseñado y desarrollado por %s', 'stories' ),
							'<a href="https://chano.dev/" target="_blank" rel="noopener noreferrer">@ChanoDEV</a>'
						);
						?>
					</p>
				</div>
			</div>
		</section>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
