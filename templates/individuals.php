<?php
/**
 * Template Name: Particulares
 *
 * Individuals landing page template for The Crisis Academy.
 *
 * @package TheCrisisAcademy
 */

get_header(); ?>

    <?php
    $template_sections = [
        'individuals' => [
            'hero',
            'about',
            'certification',
            'signals',
            'how-works',
            'simulation',
        ],
        'corporate'   => [
            'cta',
            'upcoming-events',
            'news',
            'faq',
            'lightbox',
        ],
    ];

    foreach ( $template_sections as $folder => $sections ) {
        $directory = get_stylesheet_directory() . '/template-parts/' . $folder;

        foreach ( $sections as $section => $condition ) {
            if ( is_int( $section ) ) {
                $section   = $condition;
                $condition = true;
            }

            $file = "{$directory}/{$section}.php";
            if ( $condition && file_exists( $file ) ) {
                include $file;
            }
        }
    }
    ?>

<?php get_footer();