<?php
declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker;

use Trasweb\Plugins\WpoChecker\Framework\Hook;
use Trasweb\Plugins\WpoChecker\Framework\Service;
use Trasweb\Plugins\WpoChecker\Repositories\Config;
use Trasweb\Plugins\WpoChecker\Repositories\Settings;

use function admin_url;
use function apply_filters;
use function define;
use function defined;
use function file_get_contents;
use function get_self_link;

use const PHP_VERSION;

/**
 * Class Plugin. Initialize and configure plugin
 */
final class Plugin {
	public const _CLASSES_ = __DIR__;
	public const NAMESPACE = __NAMESPACE__;
	public const CURRENT_VERSION = '0.2';
	public const CONFIG_PAGE = 'sites_selection_page';

	private const SUPPORTED_PHP_VERSION = '7.2.0';
	private const UNSUPPORTED_PHP_VERSION_MSG = 'You need %s version of PHP for <strong>%s</strong> plugin';
	private const UNSUPPORTED_PHP_VERSION_VIEW = '/views/need_php_version.tpl';
	private const LANG_DIR = '/languages';

	/**
	 * Plugin Starts
	 *
	 * @return void
	 */
	public function __invoke(): void
	{
		$this->bootstrap();
		$this->register_services();
		$this->register_autoload();

		if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			$this->uninstall();

			return;
		}

		$this->initialize();

		if ( $this->is_activation() ) {
			$this->activation();
		}
	}

	/**
	 * Define basic of plugin in order to can be loaded.
	 *
	 * @return void
	 */
	final public function bootstrap(): void
	{
		define( __NAMESPACE__ . '\_PLUGIN_', dirname( __DIR__ ) );
		define( __NAMESPACE__ . '\PLUGIN_NAME', basename( _PLUGIN_ ) );
		define( __NAMESPACE__ . '\PLUGIN_TITLE', __( 'WPO Checker', PLUGIN_NAME ) );

		if ( version_compare( PHP_VERSION, self::SUPPORTED_PHP_VERSION ) < 0 ) {
			add_action( 'admin_notices', [ $this, 'you_need_recent_version_of_PHP' ] );

			return;
		}

		require_once( self::_CLASSES_ . '/Framework/class-service.php' );
		require_once( self::_CLASSES_ . '/Repositories/class-config.php' );
	}

	/**
	 * Config action
	 *
	 * @return void
	 */
	final private function initialize(): void
	{
		$this->add_i18n();
		$this->enqueue_hooks();

		do_action( PLUGIN_NAME . '-loaded' );
	}

	/**
	 * Tasks of uninstallation  :(
	 *
	 * @return void
	 */
	final private function uninstall(): void
	{
		( new Settings() )->unregister();
	}

	/**
	 * Tasks in plugin activation  :)
	 *
	 * @return void
	 */
	final private function activation(): void
	{
		define( __NAMESPACE__ . '\PLUGIN_ACTIVATION', true );
	}

	/**
	 * Helper: Retrieve if current request is a request of activation of this plugin.
	 *
	 * @return bool
	 */
	final private function is_activation(): bool {
		if ( !defined( 'WP_SANDBOX_SCRAPING' ) ) {
			return false;
		}

		$plugin_init_file = PLUGIN_NAME . '/' . PLUGIN_NAME.'.php';
		$plugins_page     = admin_url( 'plugins.php' );
		$plugin_name      = $_GET[ 'plugin' ] ?? '';
		$action           = $_GET[ 'action' ] ?? '';

		$is_plugins_page = strpos( get_self_link(), $plugins_page ) !== false;

		$is_plugin_activation = $is_plugins_page && $plugin_init_file === $plugin_name && 'activate' === $action;

		return $is_plugin_activation;
	}

	/**
	 * Show a warning when PHP version is not recent.
	 *
	 * @return void
	 */
	final public function you_need_recent_version_of_PHP(): void
	{
		$msg = sprintf( self::UNSUPPORTED_PHP_VERSION_MSG, self::SUPPORTED_PHP_VERSION, PLUGIN_TITLE );

		$alert = __( $msg, PLUGIN_NAME );

		$alert_content = file_get_contents( _PLUGIN_ . self::UNSUPPORTED_PHP_VERSION_VIEW );

		echo str_replace( '{{ alert }}', $alert, $alert_content );
	}

	/**
	 * Register plugin services. Services are loaded in this way in order to other developers can customize them.
	 *
	 * @return void
	 */
	final private function register_services(): void
	{
		$service_list = Config::services();

		/**
		 * @var Service[] $service_list
		 */
		$service_list = apply_filters( PLUGIN_NAME . '-services', $service_list ) ?: [];

		foreach ( $service_list as $service ) {
			$service->register();
		}
	}

	/**
	 * Register plugin autoload.
	 *
	 * @return void
	 */
	final private function register_autoload(): void
	{
		$autoload = Service::get( 'Autoload', __NAMESPACE__ );

		spl_autoload_register( [ $autoload, 'find_class' ], $throw_exception = false );
	}

	/**
	 * Add support to i18n for plugin.
	 *
	 * @return void
	 */
	final private function add_i18n(): void
	{
		load_plugin_textdomain( PLUGIN_NAME, false, PLUGIN_NAME . self::LANG_DIR );
	}

	/**
	 * Enqueue hooks used in plugin.
	 *
	 * @return void
	 */
	final private function enqueue_hooks(): void
	{
		/**
		 * @var Hook[] $hooks_list
		 */
		$hooks_list = Config::hooks();

		foreach ( $hooks_list as $hook ) {
			$hook->enqueue();
		}
	}
}
