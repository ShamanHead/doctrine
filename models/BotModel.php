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
        'langchse' => ['Please, choose your language']
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
        'langchse' => [
            [
                [
                    [
                    ['Русский', 'lanch_ru']
                    ],
                    false,
                    false,
                    false],
                [
                    [
                    ['English', 'lanch_eng']
                    ],
                    false,
                    false,
                    false
                ]
            ]
        ]
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

    public function deleteContext(){
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

    public function getCallBackData()
    {
        return $this->InputHandle->getCallbackData();
    }

    public function sendInlineKeyboard($preset)
    {
        $keyboard = $this->getPreset($preset, 'inline');
        $this->addToFlow(['reply_markup' => Utils::buildInlineKeyboard($keyboard[0], $keyboard[1], $keyboard[2], $keyboard[3])]);
    }

    public function sendKeyboard($preset)
    {
        $keyboard = $this->getPreset($preset, 'keyboard');
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

    public function getPreset(string $preset, string $type)
    {
        $presets = '';
        switch ($type) {
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
        for ($i = 0, $ltKeys = array_keys($langTable); $i < count($ltKeys); $i++) {
            if ($ltKeys[$i] == $lang) {
                $offset = $langTable[$ltKeys[$i]];
                for ($j = 0, $msKeys = array_keys($presets); $j < count($msKeys); $j++) {
                    if ($preset == $msKeys[$j]) {
                        if (!isset($presets[$msKeys[$j]][$offset])) return $presets[$msKeys[$j]][0];
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

    public function sendMessageAnyway(string $message)
    {
        Inquiry::send($this->bot, 'sendMessage', [
            'chat_id' => $this->InputHandle->getChatId(),
            'text' => $message
        ]);
    }

    public function getUserId(){
        return $this->InputHandle->getUserId();
    }

    public function getChatId(){
        return $this->InputHandle->getChatId();
    }

    public function getToken(){
        return $this->token;
    }

    public function run($method)
    {
        Inquiry::send($this->bot, $method, $this->flow);
        $this->flow = ['chat_id' => $this->InputHandle->getChatId()];
    }

}
