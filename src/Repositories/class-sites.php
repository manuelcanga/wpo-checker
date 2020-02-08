<?php

namespace Trasweb\Plugins\WpoChecker\Repositories;

use Trasweb\Plugins\WpoChecker\Collections\Sites as SiteCollection;
use Trasweb\Plugins\WpoChecker\Framework\Service;

/**
 * Class Sites. Manage WPO sites
 *
 * @package Repositories
 */
class Sites {
	/**
	 * Retrieve a site collection.
	 *
	 * @return Sites
	 */
	public static function get_sites(): self
	{
		return Service::get( 'Sites', __NAMESPACE__ );
	}

	/**
	 * Sanitize sites before being saved.
	 *
	 * @param array|null $saved_sites
	 *
	 * @return array
	 */
	public function sanitize( ?array $saved_sites = [] ): array
	{
		$saved_sites     = array_filter( (array) $saved_sites, 'is_string' );
		$available_sites = array_keys( $this->get_availables() );

		$valid_sites = \array_intersect( $saved_sites, $available_sites );

		return $valid_sites;
	}

	/**
	 * Helper: Retrieve WPO sites from sites.ini and mark if they were actived or not from settings page.
	 *
	 * @return array<string, array>
	 */
	private function get_availables(): array
	{
		static $site_list;

		if ( ! empty( $site_list ) ) {
			return $site_list;
		}

		$site_list = Config::sites();

		$saved_list = ( new Settings() )->get_sites();

		foreach ( $site_list as &$site ) {
			$site['active'] = in_array( $site['id'], $saved_list, $strict = true );
		}

		return $site_list;
	}

	/**
	 * Retrieve if all sites are selected.
	 *
	 * @return bool
	 */
	public function are_all_sites_selected(): bool
	{
		return count( $this->get_saved() ) === count( $this->get_availables() );
	}

	/**
	 * Retrieve a site collection with available sites.
	 *
	 * @return SiteCollection
	 */
	public function get_available_site_collection(): SiteCollection
	{
		return new SiteCollection( $this->get_availables() );
	}

	/**
	 * Retrieve sites actives( by users ).
	 *
	 * @return SiteCollection
	 */
	public function get_saved_site_collection(): SiteCollection
	{
		return new SiteCollection( $this->get_saved() );
	}

	/**
	 * Helper: Retrieve saved sites.
	 *
	 * @return array<string, array>
	 */
	private function get_saved(): array
	{
		static $saved_list;

		if ( isset( $saved_list ) ) {
			return $saved_list;
		}

		$saved_list = array_flip( ( new Settings() )->get_sites() );

		$available_sites = $this->get_availables();

		$saved_list = \array_intersect_key( $available_sites, $saved_list );

		return $saved_list;
	}
}
