<?php

namespace Trasweb\Plugins\WpoChecker\Pages;

use Trasweb\Plugins\WpoChecker\Framework\Hook;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;
use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Sites_Selection
 *
 * @package Pages
 */
class Sites_Selection {
	private $page_title;
	private $menu_title;
	private $capability;
	private $menu_slug;

	/**
	 * Sites_Selection constructor.
	 *
	 * @param array $config
	 */
	public function __construct( array $config ) {
		$this->page_title = $config['page_title'];
		$this->menu_title = $config['menu_title'];
		$this->capability = $config['capability'];
		$this->menu_slug  = $config['menu_slug'];
	}

	/**
	 *
	 */
	public function show_menu(): void {
		$options_page = [
			'page_title' => $this->page_title,
			'menu_title' => $this->menu_title,
			'capability' => $this->capability,
			'menu_slug'  => $this->menu_slug,
			'renderer'   => Hook::callback( $this, 'show_page' ),
		];

		add_options_page( ...array_values( $options_page ) );
	}

	/**
	 *
	 */
	public function show_page(): void {
		$vars = [
			'title'           => $this->page_title,
			'PLUGIN'          => PLUGIN_NAME,
			'sites'           => Sites::get_sites()->get_available_site_collection(),
			'wordpress_tokens' => $this->get_wordpress_tokens(),
		];

		echo View::get( 'sites_selection', $vars );
	}

	/**
	 * @return string
	 */
	private function get_wordpress_tokens(): string {
		ob_start();
		settings_fields( PLUGIN_NAME );
		$wordpress_tokens = ob_get_contents();
		ob_end_clean();

		return strval( $wordpress_tokens );
	}
}