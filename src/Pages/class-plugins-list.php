<?php

namespace Trasweb\Plugins\WpoChecker\Pages;

use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Plugins_List. Show settings link
 *
 * @package Pages
 */
class Plugins_List {
	/**
	 * Links to settings data
	 *
	 * @var array{capability; string, slug: string, label: string, format: string}
	 */
	private $link_to_settings;

	/**
	 * Plugins_List constructor.
	 *
	 * @param array<string, array> $config
	 */
	public function __construct( array $config ) {
		$this->link_to_settings = $config['link_to_settings'];
	}

	/**
	 * Enqueue link to settings page in plugins list page.
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function show_settings_in_plugin_links( array $links, string $file ): array {
		$init_file = PLUGIN_NAME.'/'.PLUGIN_NAME.".php";

		if ( $file === $init_file && current_user_can( $this->link_to_settings['capability'] ) ) {
			array_unshift( $links, $this->get_settings_link() );
		}

		return $links;
	}

	/**
	 * Retrieve a link to settings page of plugin.
	 *
	 * @return string
	 */
	private function get_settings_link(): string {
		$settings_url = admin_url( 'options-general.php?page=' . $this->link_to_settings['slug'] );

		return sprintf( $this->link_to_settings['format'], $settings_url, $this->link_to_settings['label'] );
	}
}