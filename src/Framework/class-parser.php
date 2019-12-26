<?php

namespace Trasweb\Plugins\WpoChecker\Framework;

use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Parser
 *
 * @package Framework
 */
final class Parser
{
    private const FIND_VAR = '/{{(?P<var>.*?)}}/';
    private const FIND_BLOCKS = '/' .                                   //start regex
                                '\{\%\s?(?<block_name>[\w\!\_\.]+)' .   //start block:  {% block_name
                                '(\s(?<var>[^{}]*))?' .                 // var ?
                                '\ \%\}' .                              // %}
                                '(?<content>(([^%]+)|(?R))*)' .         //content with not %
                                '\{\%\s?end\1\s?\%\}' .                 //end macro:  {% end{macro_name} %}
                                '/mU';                                  //end regex
    private const FIND_GETTEXT_STRINGS = '/\{\'(?P<string>.*?)\'\}/';
    private const DEFAULT_VALUE = '';

    private $vars;

    /**
     * Parse a text using as vars $vars
     *
     * @param string $content
     * @param array $vars
     *
     * @return string
     */
    final public function parse(string $content = '', array $vars = []): string
    {
        $this->vars = $vars;

        $patterns_and_callbacks = [
            self::FIND_BLOCKS          => [ $this, 'do_block' ],
            self::FIND_VAR             => [ $this, 'return_var_value_from_tokens' ],
            self::FIND_GETTEXT_STRINGS => [ $this, 'replace_gettext_strings' ],
        ];

        return \preg_replace_callback_array($patterns_and_callbacks, $content);
    }


    /**
     * Check if a content has a var inside or not.
     *
     * @param string $maybe_inside_var
     *
     * @return bool
     */
    final public function has_var(string $maybe_inside_var): bool
    {
        $matches = [];

        \preg_match('/' . static::FIND_VAR . '/us', $maybe_inside_var, $matches);

        return ! empty($matches['var']);
    }


    /**
     * Retrieve value of a var
     *
     * @param string $var_name
     *
     * @return mixed|string
     */
    final private function get_value_of_var_name(string $var_name)
    {
        $var_name = \trim($var_name);

        $vars_name = \explode('.', $var_name);
        $value     = static::DEFAULT_VALUE;

        $vars =& $this->vars;
        foreach ($vars_name as $var_name) {
            if (is_array($vars)) {
                $value = $vars[ $var_name ] ?? static::DEFAULT_VALUE;
            } elseif (\is_object($vars)) {
                $value = $vars->$var_name ?? static::DEFAULT_VALUE;
            } else {
                return static::DEFAULT_VALUE;
            }

            $vars =& $value;
        }

        return $value;
    }


    /**
     * Parse a block statement
     *
     * @param array{block_name: string, var: string, content: string}  $tokens
     *
     * @return string
     */
    final private function do_block($tokens): string
    {
        $block_name = $tokens['block_name'];
        $var        = $tokens['var'] ?? '';
        $content    = $tokens['content'] ?? '';

        if ('foreach' === $block_name) {
            return $this->do_foreach($var, $content);
        }

        if ('if' === $block_name) {
            return $this->do_if($var, $content);
        }

        return (string) \apply_filters('\trasweb\blocks\\' . $block_name, $content, $var, $this);
    }

    /**
     * Parse a foreach statement
     *
     * @param string $var
     * @param string $content_of_foreach
     *
     * @return string
     */
    final private function do_foreach(string $var, string $content_of_foreach): string
    {
        $vars      = explode(' as ', $var ?? '');
        $var_name  = trim($vars[0]);
        $var_alias = trim($vars[1] ?? 'item');

        $items_to_iterate = $this->get_value_of_var_name($var_name);

        if (empty($items_to_iterate) || ! \is_iterable($items_to_iterate) || '' === $content_of_foreach) {
            return $this::DEFAULT_VALUE;
        }

        return $this->parse_content_for_all_items($items_to_iterate, $content_of_foreach, $var_alias);
    }

    /**
     * Parse foreach content iteratively.
     *
     * @param iterable $items_to_iterate Items to iterate.
     * @param string   $content_of_foreach
     * @param string   $var_alias        Alias of each item
     *
     * @return string
     */
    final private function parse_content_for_all_items(iterable $items_to_iterate, string $content_of_foreach, string $var_alias): string
    {
        $index = 1;
        $max   = count($items_to_iterate);

        $foreach_result = '';
        foreach ($items_to_iterate as $item) {
            $vars = $this->vars;

            $vars[ $var_alias ] = $item;
            $vars['index']      = $index;
            $vars['count']      = $max;
            $vars['is_first']   = 1 === $index;
            $vars['is_last']    = $max === $index;

            $foreach_result .= (new $this)->parse($content_of_foreach, $vars);
            $index ++;
        }

        return $foreach_result;
    }

    /**
     * Parse an if statement
     *
     * @param string $var_of_conditional
     * @param string $content_of_conditional
     *
     * @return string
     */
    final private function do_if(string $var_of_conditional, string $content_of_conditional): string
    {
        $true_with_empty    = '!' === $var_of_conditional[0];
        $var_of_conditional = ltrim($var_of_conditional, '! ');

        if ('' === $var_of_conditional || '' === $content_of_conditional) {
            return $this::DEFAULT_VALUE;
        }

        $condictional_is_false = empty($this->get_value_of_var_name($var_of_conditional));

        if ($condictional_is_false !== $true_with_empty) {
            return $this::DEFAULT_VALUE;
        }

        return (new $this)->parse($content_of_conditional, $this->vars);
    }

    /**
     * Replace a {{var}} for its value
     *
     * @param array $tokens Tokens from parsing.
     *
     * @return string
     */
    final protected function return_var_value_from_tokens(array $tokens): string
    {
        return $this->get_value_of_var_name($tokens['var']);
    }

    /**
     * Replace a {'string'] for its gettext version.
     *
     * @param array{string: string} $tokens Tokens from parsing.
     *
     * @return string
     */
    final private function replace_gettext_strings(array $tokens): string
    {
        $string = $tokens['string'] ?? '';

        return __($string, PLUGIN_NAME);
    }
}
