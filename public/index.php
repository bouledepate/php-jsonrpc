<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use WoopLeague\Kernel\KernelFactory;

$application = KernelFactory::buildApplication();
$application->run();;