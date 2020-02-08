<?php
declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Places;

use Trasweb\Plugins\WpoChecker\Framework\Hook;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

use function ob_get_contents;

use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Sites_Selection. It is used in order to manage page selection.
 *
 * @package Pages
 */
class Sites_Selection {
	private $page_title;
	private $menu_title;
	private $capability;
	private $menu_slug;

	private const VIEW_NAME = 'sites_selection';

	/**
	 * Sites_Selection constructor.
	 *
	 * @param array $config
	 */
	public function __construct( array $config )
	{
		$this->page_title = $config[ 'page_title' ];
		$this->menu_title = $config[ 'menu_title' ];
		$this->capability = $config[ 'capability' ];
		$this->menu_slug  = $config[ 'menu_slug' ];
	}

	/**
	 * Show link to page in menu.
	 *
	 * @action admin_menu
	 *
	 * @return void
	 */
	public function show_menu(): void
	{
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
	 * Show page of sites selection.
	 *
	 * @return void
	 */
	public function show_page(): void
	{
		$vars = [
			'title'             => $this->page_title,
			'PLUGIN'            => PLUGIN_NAME,
			'sites'             => Sites::get_sites()->get_available_site_collection(),
			'all_sites_checked' => Sites::get_sites()->are_all_sites_selected(),
			'wordpress_tokens'  => $this->get_wordpress_tokens(),
		];

		echo View::get( self::VIEW_NAME, $vars );
	}

	/**
	 * Retrieve nonces( and metadada ) in order to use in sites selection page.
	 *
	 * @return string
	 */
	private function get_wordpress_tokens(): string
	{
		ob_start();
		settings_fields( PLUGIN_NAME );
		$wordpress_tokens = ob_get_contents();
		ob_end_clean();

		return strval( $wordpress_tokens );
	}
}
