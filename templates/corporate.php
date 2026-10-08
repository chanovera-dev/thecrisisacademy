<?php
/**
 * Template Name: Corporativo
 *
 * Corporate landing page template for The Crisis Academy.
 *
 * @package TheCrisisAcademy
 */

get_header(); ?>

    <?php
    $directory = get_stylesheet_directory() . '/template-parts/corporate';

    $sections = [
        'hero',
        'trouble',
        'hearings',
        'founder',
        'program',
        'simulation',
        'diff', 
        'testimonies',
        'thought',
        'cta',
        'upcoming-events',
        'news',
        'faq',
        'lightbox'
    ];

    foreach ($sections as $section => $condition) {
        if (is_int($section)) {
            $section = $condition;
            $condition = true;
        }

        if ($condition && file_exists("$directory/$section.php")) {
            include "$directory/$section.php";
        }
    }
    ?>

<?php get_footer();