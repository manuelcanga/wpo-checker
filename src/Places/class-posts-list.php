<?php

namespace Trasweb\Plugins\WpoChecker\Places;

use Trasweb\Plugins\WpoChecker\Entities\Site;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;
use WP_Post;

/**
 * Class Posts_List
 *
 * @package Pages
 */
class Posts_List {
	/**
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function show_wpo_links_in_cpt_row_actions( array $actions, WP_Post $post ) {
		$wpo_actions = Sites::get_sites()->get_saved_site_collection();

		$url =  \get_permalink( $post->ID );
		if ( ! $url || "publish" !== $post->post_status ) {
			return $actions;
		}

		foreach ( $wpo_actions as $site_id => $site ) {
			$actions[ $site_id ] = $this->generate_action_for_post( $post, $site, $url );
		}

		return $actions;
	}

	/**
	 * @param WP_Post $post
	 * @param Site    $site
	 * @param string  $permalink
	 *
	 * @return string
	 */
	private function generate_action_for_post( WP_Post $post, Site $site, string $permalink ): string {
		$vars = \get_object_vars( $site );

		$vars['item_url'] = $permalink;
		$vars['form_id']  = $site->id . '_' . $post->ID;

		return View::get( 'action_template', $vars );
	}
}