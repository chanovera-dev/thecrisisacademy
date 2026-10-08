<?php
/**
 * Color Schemes for The Crisis Academy
 *
 * Custom color palettes registered for the Stories theme engine.
 *
 * @package TheCrisisAcademy
 * @subpackage Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register custom color schemes for The Crisis Academy.
 *
 * @param array $schemes Associative array of existing color schemes from Stories.
 * @return array Modified array of color schemes including child theme additions.
 */
function thecrisisacademy_register_color_schemes( $schemes ) {
	$schemes['carolina_eslava'] = array(
		'label'       => __( 'Carolina Eslava', 'thecrisisacademy' ),
		'description' => __( 'Paleta personalizada para Carolina Eslava.', 'thecrisisacademy' ),
		'preview'     => array(
			'primary' => '#466caa',
			'accent'  => '#0050b3',
			'header'  => '#e6edf8',
			'footer'  => '#040722',
		),
		'vars'        => array(
			'--color-primary'              => '#466caa',
			'--color-primary-hover'        => '#0062cc',
			'--color-primary-light'        => '#e6f1ff',
			'--color-primary-accent'       => '#0050b3',
			'--color-primary-accent-light' => '#3395ff',
			'--button-primary-background'  => '#c90a21',
			'--color-secondary'            => '#cfe3ff',
			'--color-tertiary'             => '#80baff',
			'--color-quaternary'           => '#b3d4ff',
			'--bg-body'                    => '#ebe8e6',
			'--border-light'               => '#f3f1f0',
			'--header-background'          => '#e6edf8',
			'--footer-background'          => '#040722',
			'--accent-background-1'        => '#0c3875',
			'--accent-background-2'        => '#1a87ff',
			'--bright-line'                => '#ffffff',
			'--shadow-color'               => '#06152d',
		),
	);

	return $schemes;
}
add_filter( 'stories_color_schemes', 'thecrisisacademy_register_color_schemes' );
