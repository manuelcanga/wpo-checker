<?php

/**
 * Load autoload and base plugin in order to can check data from Web Driver.
 */
namespace Trasweb\Plugins\WpoChecker;

//mock gettext function
function __($string) {
	return $string;
}

require(__DIR__.'/../../src/class-plugin.php');
require(__DIR__.'/../../src/Framework/class-autoload.php');

//load plugin base
$plugin = new Plugin();
$plugin->bootstrap();

//load autoload
$autoload_class = Plugin::NAMESPACE.'\Framework\Autoload';
$autoload = $autoload_class::get_instance( [ 'base_namespace' =>  Plugin::NAMESPACE, 'base_dir' => Plugin::_CLASSES_ ] );

spl_autoload_register( [ $autoload, 'find_class' ], $throw_exception = false );