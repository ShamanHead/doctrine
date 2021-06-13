<?php

namespace Model;

class UserModel
{

    function __construct($entityManager,string $userId){
        $this->userId = $userId;
        $this->EM = $entityManager;
        $repo = $this->EM->getRepository('Entities\User');
        $this->instanse = $repo->findBy([
            'userId' => $userId
        ]);
    }

    public function setLanguage(string $lang){
        $this->instanse->setLanguage($lang);
    }

    public function flush(){
        $this->EM->flush();
    }
}
