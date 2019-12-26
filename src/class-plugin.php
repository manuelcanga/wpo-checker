<?php

namespace Trasweb\Plugins\WpoChecker;

use Trasweb\Plugins\WpoChecker\Framework\Service;
use Trasweb\Plugins\WpoChecker\Repositories\Config;
use Trasweb\Plugins\WpoChecker\Repositories\Settings;

use const PHP_VERSION;

/**
 * Class Plugin. Initialize and configure plugin
 */
class Plugin
{
    public const _CLASSES_ = __DIR__;
    public const CURRENT_VERSION = '0.2';

    private const SUPPORTED_PHP_VERSION = '7.2.0';
    private const LANG_DIR = '/languages';

    /**
     * Plugin Bootstrap
     *
     * @return void
     */
    public function __invoke()
    {
        if (! \is_admin()) {
            return;
        }

        \define(__NAMESPACE__ . '\_PLUGIN_', dirname(__DIR__));
        \define(__NAMESPACE__ . '\PLUGIN_NAME', basename(_PLUGIN_));
        \define(__NAMESPACE__ . '\PLUGIN_TITLE', __('WPO Checker', PLUGIN_NAME));

        if (version_compare(PHP_VERSION, self::SUPPORTED_PHP_VERSION) < 0) {
            return add_action('admin_notices', [ $this, 'you_need_recent_version_of_PHP' ]);
        }

        require_once(self::_CLASSES_ . '/Framework/class-service.php');
        require_once(self::_CLASSES_ . '/Repositories/class-config.php');

        $this->register_services();
        $this->register_autoload();

        if (\defined('WP_UNINSTALL_PLUGIN')) {
            $this->uninstall();
        } else {
            $this->initialize();
        }
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

        do_action(PLUGIN_NAME . '-loaded');
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
     * Show a warning when PHP version is not recent.
     *
     * @return void
     */
    final public function you_need_recent_version_of_PHP(): void
    {
        $msg   = sprintf('You need %s version of PHP for <strong>%s</strong> plugin', self::SUPPORTED_PHP_VERSION, PLUGIN_TITLE);

        $alert = __($msg, PLUGIN_NAME);

        $alert_content = \file_get_contents(_PLUGIN_ . '/views/need_php_version.tpl');

        echo str_replace('{{ alert }}', $alert, $alert_content);
    }

    /**
     * Register plugin services.
     *
     * @return void
     */
    final private function register_services(): void
    {
        $service_list = Config::services();

        $service_list = \apply_filters(PLUGIN_NAME . '-services', $service_list);

        foreach ($service_list as $service) {
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
        $autoload = Service::get('Autoload', __NAMESPACE__);

        spl_autoload_register([ $autoload, 'find_class' ], $throw_exception = false);
    }

    /**
     * Add support to i18n for plugin.
     *
     * @return void
     */
    final private function add_i18n(): void
    {
        load_plugin_textdomain(PLUGIN_NAME, false, PLUGIN_NAME . self::LANG_DIR);
    }

    /**
     * Enqueue hooks used in plugin.
     *
     * @return void
     */
    final private function enqueue_hooks(): void
    {
        $hooks_list = Config::hooks();

        foreach ($hooks_list as $hook) {
            $hook->enqueue();
        }
    }
}
