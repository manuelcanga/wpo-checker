<?php

namespace Tests\acceptance;

use Tests\Step\Acceptance\WpAdmin;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

class PostPageCest
{
	private const SYSTEM_QUICK_LICKS =  ['edit', 'inline hide-if-no-js', 'trash', 'view'];

	/**
	 * Check if quick links in posts are the same to save links.
	 *
	 * @param WpAdmin $I
	 */
	public function quickLinksWithSites( WpAdmin $I )
	{
		$I->wantToTest( 'I can see "WPO Checker" quick links in post' );
		$I->loginAsUser();
		$I->openMenu( 'Páginas' );
		$actual_sites   = $I->grabMultiple( '#the-list tr:first-child .row-actions span', 'class' );

		$selected_sites = array_values( array_diff($actual_sites, self::SYSTEM_QUICK_LICKS ) );

		$saved_sites    = array_column( ( new Sites() )->get_saved_site_collection()->get_items(), 'id' );

		$I->assertSame( $saved_sites, $selected_sites );
	}
}