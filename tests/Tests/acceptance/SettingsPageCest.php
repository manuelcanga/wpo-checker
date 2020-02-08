<?php

namespace Tests\acceptance;

use Tests\Step\Acceptance\WpAdmin;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

class SettingsPageCest {
    public function exists(WpAdmin $I) {
    	$I->wantToTest( 'I can see "WPO Checker" settings page');
		$I->loginAsUser( );
		$I->openMenu('Ajustes');
		$I->openMenu( 'WPO Checker');
    }

	public function containSites(WpAdmin $I) {
		$I->wantToTest( 'I can see "WPO Checker" settings page');
		$I->loginAsUser( );
		$I->openMenu('Ajustes');
		$I->openMenu( 'WPO Checker');
		$actual_sites = $I->grabMultiple( '#the-list tr');
		$expected_sites = (new Sites() )->get_available_site_collection();

		$I->assertSame( count($expected_sites), count($actual_sites));
	}
}