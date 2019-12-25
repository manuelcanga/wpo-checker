<?php

namespace Trasweb\Plugins\WpoChecker\Framework;

use Trasweb\Plugins\WpoChecker\Plugin;
use function class_exists;
use function method_exists;
use const Trasweb\Plugins\WpoChecker\PLUGIN_NAME;

/**
 * Class Service.
 *
 * @package Framework
 */
class Service
{
    private const options_METHOD = 'get_instance';

    private static $services = [];
    private static $services_by_context = [];

    private $id;
    private $class;
    private $options;
    private $context;
    private $only_once;
    private $instance;
    private $bootstrap;

    /**
     * Create a new hook.
     *
     * @param string $hook_id WP tag.
     * @param array  $hook_options
     *
     * @return Hook
     */
    final public static function new(string $service_id, string $service_class, array $instance_options = []): self
    {

        $service_class    = apply_filters(PLUGIN_NAME . "-{$service_id}-service-class", $service_class, $instance_options);
        $instance_options = apply_filters(PLUGIN_NAME . "-{$service_id}-service-options", $instance_options, $service_class);

        $service            = new self();
        $service->id        = $service_id;
        $service->class     = $service_class;
        $service->context   = $instance_options['context'] ?? 'any';
        $service->only_one  = $instance_options['only_one'] ?? false;
        $service->options   = $instance_options['options'] ?? [];
        $service->bootstrap = $instance_options['bootstrap'] ?? '';

        return $service;
    }

    /**
     * Retrieve a service according to its $service_id and its $context.
     *
     * @param string $service_id
     * @param string $context
     * @param mixed  ...$args
     *
     * @return object|null
     */
    final public static function get(string $service_id, string $context = '', ...$args): ?object
    {
        $service = self::$services_by_context[ $context ][ $service_id ] ?? self::$services[ $service_id ] ?? null;

        if (! $service) {
            return null;
        }

        return $service->get_instance($args);
    }

    /**
     * Register current service.
     *
     * @return void
     */
    final public function register(): void
    {
        if (! $this->context || 'any' === $this->context) {
            static::$services[ $this->id ] = $this;
        } else {
            static::$services_by_context[ $this->context ][ $this->id ] = $this;
        }
    }

    /**
     * Create an instance of a service.
     *
     * @param array $args
     *
     * @return ?object
     */
    final private function get_instance(array $args): ?object
    {
        if ($this->only_once && $this->instance) {
            return $this->instance;
        }

        if ($this->bootstrap) {
            $bootstrap_file = Plugin::_CLASSES_. $this->bootstrap;

            require_once($bootstrap_file);
        }

        $class_name = $this->class;
        $service = null;

        if (! class_exists($class_name)) {
            return $service;
        }

        if (!$this->options) {
            $service = new $class_name(...$args);
        } else {
            $options_method = self::options_METHOD;
            if (method_exists($class_name, $options_method)) {
                $service = $class_name::$options_method($this->options, $args);
            }
        }

        return $this->instance = $service;
    }
}
