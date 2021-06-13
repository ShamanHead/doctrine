<?php

namespace Model;

use Entities\User as User;

class UserModel
{

    function __construct($entityManager, $bot){
        $this->userId = $bot->getUserId();
        $this->chatId = $bot->getChatId();
        $this->token = $bot->getToken();
        $this->EM = $entityManager;
        $repo = $this->EM->getRepository('Entities\User');
        $entities = $repo->findBy([
            'userId' => $this->userId,
            'chatId' => $this->chatId,
            'botToken' => $this->token
        ]);
        $this->instanse = $entities;
        if(count($entities) == 0){
            $user = new User();
            $user->setChatId($this->chatId);
            $user->setUserId($this->userId);
            $user->setBotToken($this->token);
            $this->EM->persist($user);
            $this->EM->flush();
            $this->instanse = $user;
        }else{
            $this->instanse = $entities[0];
        }
    }

    public function setLanguage(string $lang){
        $this->instanse->setLanguage($lang);
        $this->EM->persist($this->instanse);
        $this->EM->flush();
    }

    public function getInstanse(){
        return $this->instanse;
    }

    public function getLanguage(){
        return $this->instanse->getLanguage();
    }

    public function flush(){
        $this->EM->flush();
    }
}
