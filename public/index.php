<?php

require_once __DIR__."/../vendor/autoload.php";

use App\Main;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

//set logger
$logger = new Logger("logger");
$logger->pushHandler(new StreamHandler(__DIR__."/../var/log/out.log"));

//run application
$main = new Main($logger);
$main->run();