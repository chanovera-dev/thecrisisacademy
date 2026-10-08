<?php
/**
 * Template Name: Equipo
 *
 * Team landing page template for The Crisis Academy.
 *
 * @package TheCrisisAcademy
 */

get_header(); ?>

    <?php
    $template_sections = array(
        'team/hero',
        'corporate/founder',
        'team/team',
        'team/methodology',
        'team/cta',
    );

    foreach ( $template_sections as $section ) {
        $file = get_stylesheet_directory() . '/template-parts/' . $section . '.php';
        if ( file_exists( $file ) ) {
            include $file;
        }
    }
    ?>

<?php get_footer();