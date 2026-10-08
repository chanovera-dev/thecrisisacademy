<?php
/**
 * Master Custom Post Types Loader
 *
 * Centralizes and registers native Custom Post Types:
 * - News / Noticias (inc/cpt/news.php)
 * - Events / Eventos (inc/cpt/events.php)
 * - FAQ / Preguntas Frecuentes (inc/cpt/faq.php)
 * - Future CPTs
 *
 * @package TheCrisisAcademy
 * @subpackage Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thecrisisacademy_cpt_dir = __DIR__ . '/cpt';

// 1. News (Noticias) CPT
if ( file_exists( $thecrisisacademy_cpt_dir . '/news.php' ) ) {
	require_once $thecrisisacademy_cpt_dir . '/news.php';
}

// 2. Events (Eventos) CPT
if ( file_exists( $thecrisisacademy_cpt_dir . '/events.php' ) ) {
	require_once $thecrisisacademy_cpt_dir . '/events.php';
}

// 3. FAQ (Preguntas Frecuentes) CPT
if ( file_exists( $thecrisisacademy_cpt_dir . '/faq.php' ) ) {
	require_once $thecrisisacademy_cpt_dir . '/faq.php';
}
