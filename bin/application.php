<?php

require __DIR__.'/../vendor/autoload.php';

use App\Classes\Command;
use Symfony\Component\Console\Application;

define('COMMAND_DIR', getcwd());

$application = new Application('csv-to-audio', '1.0.0');
$command = new Command();

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
$application->run();
