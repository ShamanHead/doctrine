<?php

namespace Controller;

class UserModel
{

    function __construct(string $userId){
        $this->userId = $userId;
        $repo = $entityManager->getRepository('User');
        $this->instanse = $repo->findBy([
            'userId' => $userId
        ]);
    }

    public function setLanguage(string $lang){
        $this->instanse->setLanguage($lang);
    }

    public function flush(){
        $entityManager->flush();
    }
}
