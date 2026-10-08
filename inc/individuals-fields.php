<?php
/**
 * Backward compatibility wrapper for Individuals Fields.
 * All field groups are now managed centrally in inc/page-fields.php and inc/fields/individuals.php.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/page-fields.php';
