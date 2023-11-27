<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Kernel\KernelFactory;

$application = KernelFactory::buildApplication();
$application->run();;