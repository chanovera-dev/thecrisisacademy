<?php
/**
 * Backward compatibility wrapper for Corporate Fields.
 * All field groups are now managed centrally in inc/page-fields.php and inc/fields/corporate.php.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/page-fields.php';
