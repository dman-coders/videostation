#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';

use VideoStation\Application;

$application = new Application();
$application->run();