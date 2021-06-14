<?php

namespace Model;

use \Telbot\Context as Context;
use \Telbot\Bot as Bot;
use \Telbot\Inquiry as Inquiry;
use \Telbot\InputHandle as InputHandle;
use \Telbot\Utils as Utils;
use \Telbot\User as User;

class BotModel
{

    private $bot;

    private $token;

    private $InputHandle;

    private $messagePresets = [
        'hello' => ['Добро пожаловать!', 'Hello there'],
        'error' => ['Произошла ошибка.', 'An error has occurred'],
        'langchse' => ['Please, choose your language'],
        'sorry' => ['Извините, но такой команды нет.', 'Sorry, there is no command like this']
    ];

    private $responsePresets = [
        'about' => ['О проекте', 'About'],
        'settings' => ['Настройки', 'Settings'],
        'notes' => ['Мои заметки', 'My notes']
    ];

    private $flow;

    private $keyboardPresets = [
        'main' => [
            [
                [
                    [
                        ['Мои заметки']
                    ],
                    [
                        ['О проекте'],
                        ['Настройки']
                    ]
                ],
                true,
                false,
                false
            ],
            [
                [
                    [
                        ['My notes']
                    ],
                    [
                        ['About'],
                        ['Settings']
                    ]
                ],
                true,
                false,
                false
            ],
        ]
    ];

    private $inlinePresets = [
        'langchse' => [[[[['Русский', 'lanch_ru']], [['English', 'lanch_eng']]], false, false, false]]
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
        $this->token = $token;
        $this->addToFlow(['chat_id' => $this->InputHandle->getChatId()]);
        User::add($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId());
        return true;
    }

    public function deleteContext()
    {
        Context::delete($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId());
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
        for ($i = 0, $keys = array_keys($data); $i < count($keys); $i++) {
            $this->flow[$keys[$i]] = $data[$keys[$i]];
        }
    }

    public function getMessageText()
    {
        return $this->getPresetName($this->InputHandle->getMessageText(), $this->responsePresets);
    }

    public function getCallBackData()
    {
        return $this->InputHandle->getCallbackData();
    }

    public function sendInlineKeyboard($preset)
    {
        $keyboard = $this->getPreset($preset, $this->inlinePresets);
        $this->addToFlow(['reply_markup' => Utils::buildInlineKeyboard($keyboard[0], $keyboard[1], $keyboard[2], $keyboard[3])]);
    }

    public function sendKeyboard($preset)
    {
        $keyboard = $this->getPreset($preset, $this->keyboardPresets);
        $this->addToFlow(['reply_markup' => Utils::buildKeyboard($keyboard[0], $keyboard[1], $keyboard[2], $keyboard[3])]);
    }

    public function setContext(string $context)
    {
        Context::write($this->bot, $this->InputHandle->getChatId(), $this->InputHandle->getUserId(), $context);
    }

    public function getContext(): string
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

    public function getPreset(string $preset, array $presetsTable)
    {
        $langTable = $this->languageTable;
        $lang = $this->language;
        for ($i = 0, $ltKeys = array_keys($langTable); $i < count($ltKeys); $i++) {
            if ($ltKeys[$i] == $lang) {
                $offset = $langTable[$ltKeys[$i]];
                for ($j = 0, $msKeys = array_keys($presetsTable); $j < count($msKeys); $j++) {
                    if ($preset == $msKeys[$j]) {
                        if (!isset($presetsTable[$msKeys[$j]][$offset])) return $presetsTable[$msKeys[$j]][0];
                        return $presetsTable[$msKeys[$j]][$offset];
                    }
                }
            }
        }

        throw new \Exception('Preset not found');
    }

    public function getPresetName($value, array $presetsTable)
    {
        $langTable = $this->languageTable;
        $lang = $this->language;
        for ($j = 0, $msKeys = array_keys($presetsTable); $j < count($msKeys); $j++) {
            for($x = 0, $contents = $presetsTable[$msKeys[$j]];$x < count($contents);$x++){
                if ($contents[$x] == $value) {
                    return $msKeys[$j];
                }
            }
        }

        return null;
    }

    public function sendMessage(string $preset)
    {
        $this->addToFlow([
            'text' => $this->getPreset($preset, 'message')
        ]);
    }

    public function sendMessageAnyway(string $message)
    {
        Inquiry::send($this->bot, 'sendMessage', [
            'chat_id' => $this->InputHandle->getChatId(),
            'text' => $message
        ]);
    }

    public function getUserId()
    {
        return $this->InputHandle->getUserId();
    }

    public function getChatId()
    {
        return $this->InputHandle->getChatId();
    }

    public function getToken()
    {
        return $this->token;
    }

    public function run($method)
    {
        Inquiry::send($this->bot, $method, $this->flow);
        $this->flow = ['chat_id' => $this->InputHandle->getChatId()];
    }

}
