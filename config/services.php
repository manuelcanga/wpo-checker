<?php

namespace Trasweb\Plugins\WpoChecker;

use Trasweb\Plugins\WpoChecker\Framework\Autoload;
use Trasweb\Plugins\WpoChecker\Framework\Parser;
use Trasweb\Plugins\WpoChecker\Framework\Service;
use Trasweb\Plugins\WpoChecker\Framework\View;
use Trasweb\Plugins\WpoChecker\Repositories\Sites;

return [
	Service::new(
		'Autoload',
		Autoload::class,
		[
			'bootstrap' => '/Framework/class-autoload.php',
			'options' => [
				'base_namespace' => __NAMESPACE__,
				'base_dir' => Plugin::_CLASSES_,
			]
		]
	),
	Service::new(
		 'Parser',
		Parser::class
	),
	Service::new(
		'View',
		View::class
	),
	Service::new(
		'Sites',
		Sites::class
	),
];