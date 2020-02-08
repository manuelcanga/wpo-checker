<?php
declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Places;

use Trasweb\Plugins\WpoChecker\Entities\Site;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;
use WP_Post;
use function get_object_vars;
use function get_permalink;

/**
 * Class Posts_List
 *
 * @package Pages
 */
class Posts_List {
	private const STATUS_WITH_WPO_LINKS = 'publish';

	/**
	 * Add wpo quick links to action list of post.
	 *
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @filter post_row_actions
	 * @filter page_row_actions
	 *
	 * @return array
	 */
	public function show_wpo_links_in_cpt_row_actions( array $actions, WP_Post $post ): array
	{
		$wpo_actions = Sites::get_sites()->get_saved_site_collection();

		$url = get_permalink( $post->ID );
		if ( ! $url || self::STATUS_WITH_WPO_LINKS !== $post->post_status ) {
			return $actions;
		}

		foreach ( $wpo_actions as $site_id => $site ) {
			$actions[ $site_id ] = $this->generate_action_for_post( $post, $site, $url );
		}

		return $actions;
	}

	/**
	 * Helper: Generate site quick links for post.
	 *
	 * @param WP_Post $post
	 * @param Site    $site
	 * @param string  $permalink
	 *
	 * @return string
	 */
	private function generate_action_for_post( WP_Post $post, Site $site, string $permalink ): string
	{
		$vars = get_object_vars( $site );

		$vars[ 'item_url' ] = $permalink;
		$vars[ 'form_id' ]  = $site->id . '_' . $post->ID;

		return View::get( 'action_template', $vars );
	}
}
