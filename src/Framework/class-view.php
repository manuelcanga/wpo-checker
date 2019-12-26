<?php

namespace Trasweb\Plugins\WpoChecker\Framework;

use const Trasweb\Plugins\WpoChecker\_PLUGIN_;
use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class View.
 *
 * @package Framework
 */
class View
{
    private const _VIEWS_ = _PLUGIN_ . '/views';

    private $view_content;

    /**
     * Parse a view using some variables.
     *
     * @param string $view_name View to parse.
     * @param array  $vars      Variables to use in parsing.
     *
     * @return string
     */
    final public static function get(string $view_name, array $vars = []): string
    {
        $view_engine_class = Service::get('View', __NAMESPACE__, $view_name, $vars);

        return $view_engine_class->parse();
    }

    /**
     * View constructor.
     *
     * @param string $view_name View to parse.
     * @param array  $vars      Variables to use in parsing.
     */
    final public function __construct(string $view_name, array $vars = [])
    {
        $this->view_content = $this->get_view_content($view_name);
        $this->vars         = $vars;
    }

    /**
     * Retrieve content of a view.
     *
     * @param string $view_name View where content will be extract.
     *
     * @return string
     */
    final private function get_view_content(string $view_name): string
    {
        static $views_content_cache = [];

        if (! empty($views_content_cache[ $view_name ])) {
            return $views_content_cache[ $view_name ];
        }

        $view_content = \file_get_contents(self::_VIEWS_ . '/' . $view_name . '.tpl');

        $views_content_cache[ $view_name ] = \apply_filters(PLUGIN_NAME . '-view-' . $view_name, $view_content);

        return $views_content_cache[ $view_name ];
    }

    /**
     * Parse current view using a Parser service.
     *
     * @return mixed
     */
    final public function parse()
    {
        $parser_engine_class = Service::get('Parser', __NAMESPACE__);

        return $parser_engine_class->parse($this->view_content, $this->vars);
    }
}
