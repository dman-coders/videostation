#!/usr/bin/env php
<?php
// Base application

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

// ... register commands

$application->run();