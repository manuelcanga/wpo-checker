<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\WpoChecker\Framework;

/**
 * Class Autoload. Autoload filename in WP style( class-{Name_Class}.php ).
 *
 * @package Framework
 */
final class Autoload {
    /**
     * @var string
     */
    private $base_namespace;
    /**
     * @var string
     */
    private $base_dir;

    /**
     * Autoload constructor.
     *
     * @param $base_namespace
     * @param $base_dir
     */
    final private function __construct( string $base_namespace, string $base_dir ) {
        $this->base_namespace = $base_namespace;
        $this->base_dir       = $base_dir;
    }

    /**
     * Named constructor.
     *
     * @param array $options
     *
     * @return static
     */
    final public static function get_instance( array $options ): self {
        return new self( $options['base_namespace'], $options['base_dir'] );
    }

    /**
     * Find a class according to its name.
     *
     * @param string $class_name
     *
     * @return void
     */
    final public function find_class( string $class_name ): void {
        $class_relative_namespace = explode( $this->base_namespace, $class_name )[1] ?? '';

        if ( ! $class_relative_namespace ) {
            return;
        }

        $class_name = $this->class_namespace_to_class_name( $class_relative_namespace );
        $file_name  = $this->class_name_to_file_name( $class_name );
        $class_path = $this->class_path_from_file( $class_relative_namespace, $class_name );

        include $this->base_dir . "{$class_path}{$file_name}";
    }

    /**
     * Retrieve class name from class namespace.
     *
     * @example With `\Trasweb\Plugins\Wpo_Checker` class namespace ===> We'll get `Wpo_Checker` as class name.
     *
     * @param string $class_file
     *
     * @return string
     */
    final private function class_namespace_to_class_name( string $class_file ): string {
        //cut for the last '\'
        return trim( strrchr( $class_file, '\\' ), '\\' );
    }

    /**
     * Retrieve name of file from clas name.
     *
     * @example With `Wpo_Checker` class name ===> We'll get `class-wpo-checker.php`
     *
     * @param string $class_name
     *
     * @return string
     */
    final private function class_name_to_file_name( string $class_name ): string {
        //Wpo_Checker => wpo-checker
        $snake_case_name = strtolower( str_replace( '_', '-', $class_name ) );

        //wpo_checker => class-wpo-checker.php
        $file_name = "class-{$snake_case_name}.php";

        return $file_name;
    }

    /**
     * Extract path part from namespace and convert this a path.
     *
     * @param $class_file
     * @param $file_name
     *
     * @return string
     */
    final private function class_path_from_file( $class_relative_namespace, $file_name ): string {
        //\Trasweb\Plugins\Entitites/Site  ==>  \Trasweb\Plugins\Entities
        $namespace_path = substr( $class_relative_namespace, 0, -strlen( $file_name ) );

        //\Trasweb\Plugins\\Entities => /Trasweb/Plugins/Entities
        return str_replace( '\\', '/', $namespace_path );
    }
}
