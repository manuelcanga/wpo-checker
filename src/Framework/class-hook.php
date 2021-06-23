<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Framework;

/**
 * Class Hook. Abstraction for WP Hooks.
 *
 * @package Framework
 */
class Hook {
    private $id;
    private $listener;
    private $method;
    private $type;
    private $priority;
    private $options;
    private $with_args;
    private $expand_args;

    const COMMON_UP_METHOD = 'commons';
    const COMMON_DOWN_METHOD = 'down';

    /**
     * Create a new hook.
     *
     * @param string $hook_id WP tag.
     * @param array  $hook_options
     *
     * @return Hook
     */
    final public static function new( string $hook_id, array $hook_options ): self {
        $hook = new Hook();

        $hook->id          = $hook_id;
        $hook->listener    = $hook_options['listener'];
        $hook->method      = $hook_options['filter'] ?? $hook_options['action'];
        $hook->type        = isset( $hook_options['filter'] ) ? 'filter' : 'action';
        $hook->priority    = $hook_options['priority'] ?? 10;
        $hook->options     = $hook_options['options'] ?? [];
        $hook->with_args   = $hook_options['with_args'] ?? true;
        $hook->expand_args = $hook_options['expand_args'] ?? $hook->with_args;

        return $hook;
    }

    /**
     * Generate a new Hook callback on the fly.
     *
     * @param mixed   $listener Object or name of class.
     * @param string  $method
     * @param array   $options
     * @param boolean $with_args
     *
     * @return callable
     */
    final public static function callback( $listener, $method, $options = [], $with_args = true ): callable {
        $hook = new Hook();

        $hook->listener    = $listener;
        $hook->method      = $method;
        $hook->options     = $options;
        $hook->with_args   = $with_args;
        $hook->expand_args = true;

        return [ $hook, 'invoke' ];
    }

    /**
     * Register hook in WP.
     *
     * @return null
     */
    final public function enqueue(): void {
        $to_register = 'add_' . $this->type;

        $to_register( $this->id, [ $this, 'invoke' ], $this->priority, 99 );
    }

    /**
     * Invoke hook.
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    final public function invoke( ...$args ) {
        $class  = $this->listener;
        $method = $this->method;

        if ( $this->with_args ) {
            return $this->invoke_hook( $class, $method, $args, $this->options );
        }

        return $this->invoke_hook( $class, $method, $this->options );
    }

    /**
     * Helper: Invoke hook in low level.
     *
     * @param mixed  $class      Object or name of class.
     * @param string $method
     * @param array  $method_args
     * @param array  $class_args Arguments for class.
     *
     * @return mixed
     */
    final private function invoke_hook( $class, string $method, array $method_args = [], ?array $class_args = null ) {
        $object = \is_object( $class ) ? $class : new $class( $class_args );

        $common_task_method = self::COMMON_UP_METHOD;
        if ( \method_exists( $object, $common_task_method ) ) {
            $object->$common_task_method( $method, $method_args );
        }

        if ( $this->expand_args ) {
            $result = $object->$method( ...$method_args );
        } else {
            $result = $object->$method( $method_args );
        }

        return $result;
    }
}
