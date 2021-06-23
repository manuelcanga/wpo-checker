<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Places;

use Trasweb\Plugins\WpoChecker\Entities\Site;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;
use WP_Term;

use function get_object_vars;
use function get_term_link;

/**
 * Class Terms_List
 *
 * @package Pages
 */
class Terms_List {
    /**
     * Add wpo quick links to action list of term.
     *
     * @param array   $actions
     * @param WP_Term $term
     *
     * @filter tag_row_actions
     *
     * @return array
     */
    public function show_wpo_links_in_tags_row_actions( array $actions, WP_Term $term ): array {
        $wpo_actions = Sites::get_sites()->get_saved_site_collection();

        $url = get_term_link( $term->term_id );
        if ( ! $url ) {
            return $actions;
        }

        foreach ( $wpo_actions as $site_id => $site ) {
            $actions[ $site_id ] = $this->generate_action_for_term( $term, $site, $url );
        }

        return $actions;
    }

    /**
     * Helper: Generate site quick links for term.
     *
     * @param WP_Term $term
     * @param Site    $site
     * @param string  $term_link
     *
     * @return string
     */
    private function generate_action_for_term( WP_Term $term, Site $site, string $term_link ): string {
        $vars = get_object_vars( $site );

        $vars['item_url'] = $term_link;
        $vars['form_id']  = $site->id . '_' . $term->term_id;

        return View::get( 'action_template', $vars );
    }
}
