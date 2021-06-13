<?php

namespace Model;

use \Telbot\Context as Context;
use \Telbot\Bot as Bot;
use \Telbot\Inquiry as Inquiry;
use \Telbot\InputHandle as InputHandle;

class BotModel
{

    private $bot;

    private $messagePresets = [
      'hello' => ['Добро пожаловать!', 'Hello there'],
      'choose language' => ['Пожалуйста, выберите язык', 'Please, choose your language']
    ];

    private $keyboardPresets = [
        'langchse' => [[[':ru:', 'lanch_ru']], [[':gb:', 'lanch_eng']]]
    ];🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧🇷🇺🇬🇧

    private $inlinePresets = [

    ];

    private $languageTable = [
      'ru' => 0,
      'en' => 1
    ];

    private $language = 'ru';

    function __construct(string $token)
    {
        $this->InputHandle = new InputHandle();
        $this->bot = new Bot($token);
        return true;
    }

    public function sqlCredentials(array $credits)
    {
        $this->bot->sqlCredentials(
            $credits
        );
        $this->bot->enableSql();

        return true;
    }

    public function sendInlineQuery($preset){

    }

    public function sendKeyboard($preset){

    }

    public function setContext(string $context)
    {
        Context::write($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId(), 'smth');
    }

    public function getContext() : string
    {
        return Context::read($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId());
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function getPreset(string $preset, string $type)
    {
        $presets = '';
        switch($type){
            case 'message':
                $presets = $this->messagePresets;
                break;
            case 'inline':
                $presets = $this->inlinePresets;
                break;
            case 'keyboard':
                $presets = $this->keyboardPresets;
                break;
            default:
                throw new \Exception('Undefined preset type in');
        }

        $langTable = $this->languageTable;
        $lang = $this->language;
        for($i = 0, $ltKeys = array_keys($langTable);$i < count($ltKeys);$i++){
            if($ltKeys[$i] == $lang){
                $offset = $langTable[$ltKeys[$i]];
                for($j = 0, $msKeys = array_keys($presets);$j < count($msKeys);$j++){
                    if($preset == $msKeys[$j]){
                        return $presets[$msKeys[$j]][$offset];
                    }
                }
            }
        }

        throw new \Exception('Language not found');
    }

    public function sendMessage(string $preset)
    {
        Inquiry::send($this->bot, 'sendMessage', [
            'chat_id' => $this->InputHandle->getChatId(),
            'text' => $this->getPreset($preset, 'message')

        ]);
    }

}
