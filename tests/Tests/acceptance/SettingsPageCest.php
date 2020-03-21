<?php

namespace Tests\acceptance;

use Tests\Step\Acceptance\WpAdmin;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

class SettingsPageCest {
	public function exists( WpAdmin $I )
	{
		$I->wantToTest( 'I can see "WPO Checker" settings page' );
		$I->loginAsUser();
		$I->openMenu( 'Ajustes' );
		$I->openMenu( 'WPO Checker' );
	}

	public function containSites( WpAdmin $I )
	{
		$I->wantToTest( 'I can see "WPO Checker" settings page' );
		$I->loginAsUser();
		$I->openMenu( 'Ajustes' );
		$I->openMenu( 'WPO Checker' );
		$actual_sites   = $I->grabMultiple( '#the-list tr' );
		$expected_sites = ( new Sites() )->get_available_site_collection();

		$I->assertSame( count( $expected_sites ), count( $actual_sites ) );
	}

	public function selectRightSites( WpAdmin $I )
	{
		$I->wantToTest( 'Saved sites are selected' );
		$I->loginAsUser();
		$I->openMenu( 'Ajustes' );
		$I->openMenu( 'WPO Checker' );

		$saved_sites    = array_column( ( new Sites() )->get_saved_site_collection()->get_items(), 'id' );
		$selected_sites = $I->grabValueFrom( '#the-list tr.active input[type="checkbox"]' );

		$I->assertSame( $saved_sites, $selected_sites );
	}
}