<?php

ini_set('display_errors','1');
require_once dirname(__FILE__, 2) . "/vendor/autoload.php";

use Symfony\Component\Console\Application;

$application = new Application("Whiteacorn Control", "v2.2.2");
$application->add(new \Commands\BuildCommand());
$application->add(new \Commands\BackupCommand());
// $application->add(new VersionCommand);
$application->run();
