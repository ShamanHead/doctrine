<?php

namespace Model;

use \Telbot\Context as Context;
use \Telbot\Bot as Bot;
use \Telbot\Inquiry as Inquiry;
use \Telbot\InputHandle as InputHandle;
use \Telbot\Utils as Utils;

class BotModel
{

    private $bot;

    private $InputHandle;

    private $messagePresets = [
      'hello' => ['Добро пожаловать!', 'Hello there'],
      'langchse' => ['Please, choose your language']
    ];

    private $flow;

    private $keyboardPresets = [

    ];

    private $inlinePresets = [
        'langchse' => [[[['Русский', 'lanch_ru']], [['English', 'lanch_eng']]]]
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
        $this->addToFlow(['chat_id' => $this->InputHandle->getChatId()]);
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

    private function addToFlow(array $data)
    {
        for($i = 0, $keys = array_keys($data);$i < count($keys);$i++){
            $this->flow[$keys[$i]] = $data[$keys[$i]];
        }
    }

    public function sendInlineQuery($preset){
        $this->addToFlow(['reply_markup' => Utils::buildInlineKeyboard($this->getPreset($preset, 'inline'))]);
    }

    public function sendKeyboard($preset){

    }

    public function setContext(string $context)
    {
        Context::write($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId(), $context);
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
                        if(!isset($presets[$msKeys[$j]][$offset])) return $presets[$msKeys[$j]][0];
                        return $presets[$msKeys[$j]][$offset];
                    }
                }
            }
        }

        throw new \Exception('Preset not found');
    }

    public function sendMessage(string $preset)
    {
        $this->addToFlow([
            'text' => $this->getPreset($preset, 'message')
        ]);
    }

    public function run($method){
        Inquiry::send($this->bot, $method, $this->flow);
        $this->flow = ['chat_id' => $this->InputHandle->getChatId()];
    }

}
