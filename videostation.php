#!/usr/bin/env php
<?php
// Base application

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use VideoStation\Command\StatusCommand;

$application = new Application();
$application->add(new StatusCommand());
$application->run();