<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Places;

use Trasweb\Plugins\WpoChecker\Plugin;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

use function admin_url;

use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Plugins_List. Show settings link
 *
 * @package Pages
 */
class Plugins_List {
    private const PLUGIN_INIT_FILE = PLUGIN_NAME . '/' . PLUGIN_NAME . '.php';
    /**
     * Links to settings data
     *
     * @var array{capability; string, slug: string, label: string, format: string}
     */
    private $link_to_settings;

    /**
     * Plugins_List constructor.
     *
     * @param array<string, array> $config
     */
    public function __construct( array $config = [] ) {
        $this->link_to_settings = $config['link_to_settings'] ?? [];
    }

    /***
     * Redirect to config page when plugin is activated.
     *
     * @param string $url Default url of filter.
     *
     * @filter wp_redirect
     *
     * @return string Default url or url to plugin settings page
     */
    public function redirect_to_sites_selection( string $url ): string {
        $redirect_normally = $url;

        if ( ! defined( Plugin::NAMESPACE . '\PLUGIN_ACTIVATION' ) ) {
            return $redirect_normally;
        }

        $is_final_redirection = strpos( $url, admin_url( 'plugins.php?activate=true' ) ) !== false;
        if ( ! $is_final_redirection ) {
            return $redirect_normally;
        }

        $is_activation_bulk = $_REQUEST['activate-multi'] ?? false;
        if ( $is_activation_bulk ) {
            return $redirect_normally;
        }

        $already_plugin_configured = count( Sites::get_sites()->get_saved_site_collection() );
        if ( $already_plugin_configured ) {
            return $redirect_normally;
        }

        $sites_selection_url = add_query_arg( [ 'page' => Plugin::CONFIG_PAGE ], admin_url( 'admin.php' ) );

        return $redirect_to = $sites_selection_url;
    }

    /**
     * Enqueue link to settings page in plugins list page.
     *
     * @param $links
     * @param $file
     *
     * @return array
     */
    public function show_settings_in_plugin_links( array $links, string $file ): array {
        if ( $file === self::PLUGIN_INIT_FILE && current_user_can( $this->link_to_settings['capability'] ) ) {
            array_unshift( $links, $this->get_settings_link() );
        }

        return $links;
    }

    /**
     * Retrieve a link to settings page of plugin.
     *
     * @return string
     */
    private function get_settings_link(): string {
        $settings_url = admin_url( 'options-general.php?page=' . $this->link_to_settings['slug'] );

        return sprintf( $this->link_to_settings['format'], $settings_url, $this->link_to_settings['label'] );
    }
}
