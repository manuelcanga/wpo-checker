<?php

namespace Tests\Module;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Codeception\TestInterface;

class WPLoader  extends Module{
	const DEFAULT_USER = 'user';
	const DEFAULT_PASSWD = 'passwd';

	protected $requiredFields = [];
	protected $roles;

	/**
	 * Module constructor.
	 *
	 * Requires module container (to provide access between modules of suite) and config.
	 *
	 * @param ModuleContainer $moduleContainer
	 * @param array|null $config
	 */
	public function __construct(ModuleContainer $moduleContainer, $config = null)
	{
		$wordpress_path = $config['wordpress_path'] ?? realpath(__DIR__.'/../../../../../../');

		$wp_admin = $config['wp_admin'] ?? true;

		if(!defined('WP_ADMIN')) {
			define('WP_ADMIN', $wp_admin);
		}

		if(!defined('SHORTINIT')) {
			define('SHORTINIT', true);
		}

		//Onlye basic config
		include_once($wordpress_path.'/wp-config.php');

		require_once( $wordpress_path. '/wp-includes/l10n.php' );
		require_once( $wordpress_path. '/wp-includes/class-wp-locale.php' );
		require_once( $wordpress_path. '/wp-includes/class-wp-locale-switcher.php' );

		$default_lang = $config['lang'] ?? true;

		load_default_textdomain($default_lang );

		parent::__construct( $moduleContainer, $config);
	}

	public function _before( TestInterface $test ) {
		parent::_before( $test );

		$test->getMetadata()->setCurrent(
			[
				'username'      => $this->config['user'] ?? self::DEFAULT_USER,
				'passwd' => $this->config['pass'] ?? self::DEFAULT_PASSWD,
				'lang' => $this->config['lang'] ?? 'en_EN',
			]
		);
	}
}