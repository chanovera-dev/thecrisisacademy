<?php
/**
 * Master Page Fields Loader (Native ACF-Alternative)
 *
 * Centralizes and registers native custom field groups, meta boxes,
 * and data getters for landing page templates:
 * - Corporate (templates/corporate.php)
 * - Individuals / Particulares (templates/individuals.php)
 * - Team / Equipo (templates/team.php)
 * - Future landing page templates
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thecrisisacademy_fields_dir = __DIR__ . '/fields';

// 1. Corporate page fields
if ( file_exists( $thecrisisacademy_fields_dir . '/corporate.php' ) ) {
	require_once $thecrisisacademy_fields_dir . '/corporate.php';
}

// 2. Individuals (Particulares) page fields
if ( file_exists( $thecrisisacademy_fields_dir . '/individuals.php' ) ) {
	require_once $thecrisisacademy_fields_dir . '/individuals.php';
}

// 3. Team page fields
if ( file_exists( $thecrisisacademy_fields_dir . '/team.php' ) ) {
	require_once $thecrisisacademy_fields_dir . '/team.php';
}
