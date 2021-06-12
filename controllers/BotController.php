<?php

namespace Controller;

require_once "vendor/autoload.php";

use Colyt\Core as Core;

//$route = $_GET['route'];

use Model\BotModel as BotModel;

$botModel = new BotModel('1867256706:AAHca7ts3DcGDFxF8iz0bB1euoTAJY4d1hY');

$botModel->sendMessage('hello');

echo "efe";
