<?php

namespace Controller;

use Colyt\Core as Core;

use Model\BotModel as BotModel;
use Model\UserModel as UserModel;

$botModel = new BotModel('1867256706:AAHca7ts3DcGDFxF8iz0bB1euoTAJY4d1hY');

$botModel->sqlCredentials(
    [
    'database_server' => 'us-cdbr-east-04.cleardb.com',
    'database_name' => 'heroku_3d1d87a8d7d11e3',
    'username' => 'bb0b4a3c418e0d',
    'password' => '29a5b1cb'
    ]
);

$__USER = new UserModel($entityManager,$botModel);
$botModel->setLanguage($__USER->getLanguage());
$__MESSAGE = $botModel->getMessageText();
$__CONTEXT = $botModel->getContext();
$__CALLBACK_DATA = $botModel->getCallBackData();

switch($__CALLBACK_DATA){
    case 'lanch_ru':
        if($__CONTEXT == 'langchse'){
            $__USER->setLanguage('ru');
            $botModel->setLanguage('ru');
            $botModel->sendMessage('hello');
            $botModel->sendKeyboard('main');
            $botModel->setContext('main_menu');
            $botModel->run('sendMessage');
        }else{
            $botModel->sendMessage('error');
            $botModel->run('sendMessage');
        }
        break;
    case 'lanch_eng':
        if($__CONTEXT == 'langchse'){
            $__USER->setLanguage('en');
            $botModel->setLanguage('en');
            $botModel->sendMessage('hello');
            $botModel->sendKeyboard('main');
            $botModel->setContext('main_menu');
            $botModel->run('sendMessage');
        }else{
            $botModel->sendMessage('error');
            $botModel->run('sendMessage');
        }
        break;
}

switch($__CONTEXT){
    case '':
        $botModel->sendInlineKeyboard('langchse');
        $botModel->sendMessage('langchse');
        $botModel->run('sendMessage');
        $botModel->setContext('langchse');
        break;
}

switch($__MESSAGE){
    default:
        $botModel->sendMessage('sorry');
        $botModel->sendKeyboard('menu');
        $botModel->run('sendMessage');
        break;
}