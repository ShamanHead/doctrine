<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

class User
{
    protected $id;

    protected $userId;

    protected $chatId;

    protected $botToken;

    protected $context;

    protected $privilege;

    protected $language;

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @param mixed $chatId
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * @return mixed
     */
    public function getBotToken()
    {
        return $this->botToken;
    }

    /**
     * @param mixed $botToken
     */
    public function setBotToken($botToken)
    {
        $this->botToken = $botToken;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param mixed $privilege
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
    }


}