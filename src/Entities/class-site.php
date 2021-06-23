<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Entities;

/***
 * Class Site.
 *
 * @package Entities
 */
final class Site {
    public $id;
    public $name;
    public $short_name;
    public $link;
    public $method;
    public $input_name;
    public $active;
    public $description;

    /**
     * Site constructor.
     *
     * @param string $current_site_id
     * @param array  $current_site_options
     */
    final public function __construct( string $current_site_id, array $current_site_options ) {
        $this->id          = $current_site_id;
        $this->name        = $current_site_options['name'];
        $this->short_name  = $current_site_options['short_name'] ?? $this->name;
        $this->link        = $current_site_options['link'];
        $this->method      = $current_site_options['method'] ?? 'post';
        $this->input_name  = $current_site_options['var'] ?? 'url';
        $this->active      = $current_site_options['active'] ?? true;
        $this->description = $current_site_options['description'] ?? '';
    }
}
