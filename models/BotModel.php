<?php

namespace Model;

use \Telbot\Bot as Bot;
use \Telbot\Inquiry as Inquiry;
use \Telbot\InputHandle as InputHandle;

class BotModel
{

    private $bot;

    private $messagePresets = [
      'hello' => 'Добро пожаловать!'
    ];

    private $keyboardPresets = [

    ];

    private $inlinePresets = [

    ];

    function __construct(string $token){
        $this->InputHandle = new InputHandle();
        $this->bot = new Bot($token);
        return true;
    }

    public function getBot(){
        return $this->bot;
    }

    public function sendMessage($preset){
        Inquiry::send($this->bot, 'sendMessage', [
            'chat_id' => $this->InputHandle->getChatId(),
            'text' => $this->messagePresets[$preset]

        ]);
    }

}
