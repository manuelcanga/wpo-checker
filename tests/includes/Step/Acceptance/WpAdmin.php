<?php

namespace Tests\Step\Acceptance;

use Tests\AcceptanceTester;

class WpAdmin extends AcceptanceTester {

	public function loginAsUser(...$args){
		$I = $this;

		$I->amOnPage( '/wp-admin/' );

		if (method_exists($this, 'waitForElementVisible')) {
			$I->waitForElementVisible('#loginform');
		}

		$scenario = $I->getScenario();

		$username = $scenario->current('username');
		$passwd = $scenario->current('passwd');

		$params = ['log' => $username, 'pwd' => $passwd, 'testcookie' => '1', 'redirect_to' => ''];
		$I->submitForm('#loginform', $params, '#wp-submit');
		$I->amOnPage( '/wp-admin/' );

		if(function_exists( 'load_default_textdomain')) {
			$lang = $scenario->current('lang');
			load_default_textdomain($lang);
		}
	}

	public function openMenu( string $menu_name ) {
		$I = $this;

		$I->canSeeLink( $menu_name );
		$I->click( $menu_name );
	}
}