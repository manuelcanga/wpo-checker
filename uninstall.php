<?php

use Trasweb\Plugins\WpoChecker\Plugin;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( "Hello, World!" );
}

require( __DIR__ . '/src/class-plugin.php' );

$plugin = new Plugin();
$plugin();