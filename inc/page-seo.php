<?php
/**
 * Master Page SEO & Structured Data Loader
 *
 * Centralizes and registers native SEO metadata, Open Graph cards,
 * Twitter cards, and Schema.org JSON-LD graphs for landing page templates:
 * - Corporate (templates/corporate.php)
 * - Individuals / Particulares (templates/individuals.php)
 * - Future landing page templates
 *
 * @package TheCrisisAcademy
 * @subpackage SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thecrisisacademy_seo_dir = __DIR__ . '/seo';

// 1. Corporate page SEO & Structured Data
if ( file_exists( $thecrisisacademy_seo_dir . '/corporate.php' ) ) {
	require_once $thecrisisacademy_seo_dir . '/corporate.php';
}

// 2. Individuals (Particulares) page SEO & Structured Data
if ( file_exists( $thecrisisacademy_seo_dir . '/individuals.php' ) ) {
	require_once $thecrisisacademy_seo_dir . '/individuals.php';
}
