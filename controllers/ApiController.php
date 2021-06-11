<?php

namespace Controller;

require_once "vendor/autoload.php";

use Colyt\Core as Core;

//$route = $_GET['route'];

use \Telbot\Bot as Bot;
use \Telbot\Inquiry as Inquiry;
use \Telbot\InputHandle as InputHandle;

$bot = new Bot('1867256706:AAHca7ts3DcGDFxF8iz0bB1euoTAJY4d1hY');
$InputHandle = new InputHandle();

Inquiry::send($bot
    ,'sendMessage',
    [
        'chat_id' => $InputHandle->getChatId(),
        'text' => 'Testing your bot.'
    ]
);

echo "Work";

