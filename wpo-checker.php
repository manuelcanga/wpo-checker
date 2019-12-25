<?php
/*
 * Plugin Name: WPO Checker
 * Plugin URI: https://github.com/trasweb/wpo-checker/
 * Description: Add new quick links to check WPO / WebPerf in your published posts, pages and terms. You'll test your website contents  with PageSpeed, GtMetrix and so on
 * Version: 0.2
 * Author: Manuel Canga
 * Author URI: https://manuelcanga.dev
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wpo-checker
 * Domain Path: /languages
*/

use Trasweb\Plugins\WpoChecker\Plugin;

if ( ! defined( "ABSPATH" ) ) {
	die( "Hello, World!" );
}

require( __DIR__ . '/src/class-plugin.php' );

add_action( 'init', new Plugin()  );
