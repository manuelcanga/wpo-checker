<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Repositories;

use const Trasweb\Plugins\WpoChecker\_PLUGIN_;
use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Config  retrieve data from config files.
 *
 * @package Repositories
 */
class Config {
    private const PATH = _PLUGIN_ . '/config';

    /**
     * Retrieve sites from config
     *
     * @return array<string, array>
     */
    public static function sites(): array {
        $sites_list = \parse_ini_file( self::PATH . '/sites.ini', $with_sections = true );

        return apply_filters( PLUGIN_NAME . '-sites', $sites_list ) ?: [];
    }

    /**
     * Retrieve plugin hooks from config
     *
     * @return array<int, Hook>
     */
    public static function hooks(): array {
        $hooks = include self::PATH . '/hooks.php';

        return apply_filters( PLUGIN_NAME . '-hooks-list', $hooks ) ?: [];
    }

    /**
     * Retrieve plugin services from config
     *
     * @return array<int, Service>
     */
    public static function services(): array {
        $services = include self::PATH . '/services.php';

        return apply_filters( PLUGIN_NAME . '-services-list', $services ) ?: [];
    }
}
