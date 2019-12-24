<?php

namespace Tests\acceptance;

use Tests\Step\Acceptance\WpAdmin;

class SettingsPageCest {
    public function exists(WpAdmin $I) {
    	$I->wantToTest( 'I can see "WPO Checker" settings page');
		$I->loginAsUser( );
		$I->openMenu('Ajustes');
		$I->openMenu( 'WPO Checker');
    }
}
