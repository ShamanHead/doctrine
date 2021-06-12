<?php

namespace Controller;

use Colyt\Core as Core;

//$route = $_GET['route'];

use Model\BotModel as BotModel;

$botModel = new BotModel('1867256706:AAHca7ts3DcGDFxF8iz0bB1euoTAJY4d1hY');

$botModel->sqlCredentials(
    [
    'database_server' => 'us-cdbr-east-04.cleardb.com',
    'database_name' => 'heroku_3d1d87a8d7d11e3',
    'username' => 'bb0b4a3c418e0d',
    'password' => '29a5b1cb'
    ]
);

$botModel->sendMessage('hello');
$botModel->setContext('langchse');

echo "efe";
//bb0b4a3c418e0d:29a5b1cb@us-cdbr-east-04.cleardb.com/heroku_3d1d87a8d7d11e3?reconnect=true
