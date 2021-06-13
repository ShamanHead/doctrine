<?php

namespace Controller;

use Colyt\Core as Core;

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

$__CONTEXT = $botModel->getContext();
$__CALLBACK_DATA = $botModel->getCallBackData();

switch($__CALLBACK_DATA){
    case 'lanch_ru':
        if($__CONTEXT == 'langchse'){
            $botModel->setLanguage('ru');
            $botModel->sendMessage('hello');
            $botModel->run('sendMessage');
        }else{
            $botModel->sendMessage('error');
        }
        break;
    case 'lanch_eng':
        if($__CONTEXT == 'langchse'){
            $botModel->setLanguage('en');
            $botModel->sendMessage('hello');
            $botModel->run('sendMessage');
        }else{
            $botModel->sendMessage('error');
        }
        break;
}

switch($__CONTEXT){
    case '':
        $botModel->sendInlineQuery('langchse');
        $botModel->sendMessage('langchse');
        $botModel->run('sendMessage');
        $botModel->setContext('langchse');
        break;
}