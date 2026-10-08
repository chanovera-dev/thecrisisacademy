<?php
/**
 * The Header for the Stories theme
 *
 * Displays all of the <head> section, header branding, and opens the main element.
 *
 * @package Stories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<header id="main-header" role="banner" aria-label="<?php echo esc_attr__( 'Main header', 'stories' ); ?>">
		<div class="main-header--backdrop"></div>
		<div class="block">
			<div class="content">
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
				<div class="stories-navigation">
					<?php
					$menu_html = wp_nav_menu(
						array(
							'theme_location'  => 'primary',
							'container'       => 'nav',
							'container_class' => 'main-navigation',
							'echo'            => false,
							'fallback_cb'     => false,
						)
					);

					if ( $menu_html ) {
						$backdrop  = '<div class="main-navigation--backdrop" aria-hidden="true"></div>';
						$menu_html = preg_replace(
							'/(<nav\b[^>]*class=["\'][^"\']*main-navigation[^"\']*["\'][^>]*>)/i',
							'$1' . $backdrop,
							$menu_html,
							1
						);
						echo $menu_html;
					}
					?>
					<form role="search" method="get" class="essentialis-custom-searchform" id="essentialis-custom-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<div class="section">
							<label class="screen-reader-text" for="s"><?php esc_html_e( 'Buscar', 'stories' ); ?></label>
							<input class="wp-block-search__input" type="text" value="" name="s" id="s" placeholder="<?php esc_html_e( 'Buscar', 'stories' ); ?>">
							<div class="buttons-container">
								<button type="submit" id="searchsubmit" value="Search" aria-label="Activate the search">
									<?php echo stories_get_icon( 'search' ); ?>
								</button>
								<button type="button" class="close-mobile-searchform" onclick="closeCustomSearchform()" aria-label="Close mobile search" style="background: transparent; border: none; padding: 0; cursor: pointer;"></button>
							</div>
						</div>
					</form>
				</div>
				<div class="header-actions">
					<?php
					if ( function_exists( 'stories_language_switcher' ) ) {
						stories_language_switcher();
					}
					?>
					<button type="button" id="search-mobile__button" class="search-mobile__button" onclick="toggleCustomSearchform()" aria-label="Open search">
						<div class="icon--wrapper">
							<div class="bar"></div>
						</div>
					</button>
					<?php if ( has_nav_menu( 'primary' ) ) : ?>
						<button type="button" id="menu-mobile__button" class="menu-mobile__button" onclick="toggleMenuMobile()" aria-label="Open mobile menu">
							<span class="bar"></span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'stories' ); ?></a>

		<main id="main" class="site-main" role="main">
		<?php
		if ( function_exists( 'stories_breadcrumbs' ) ) {
			stories_breadcrumbs();
		}
		?>
