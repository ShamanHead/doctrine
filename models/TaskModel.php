<?php

namespace Model;

use Entities\Task as Task;

class TaskModel
{

    private $EM;

    function __construct($entityManager)
    {
        $this->EM = $entityManager;
        $this->repository = $this->EM->getRepository('Entities\Task');
    }

    public function getTasks($userId)
    {
        $tasks = $this->repository->findBy([
            'userId' => $userId
        ]);
        return $tasks;
    }

    public function addTask($userId, $name)
    {
        $task = new Task();
        $task->setName($name);
        $task->setUserId($userId);
        $this->EM->persist($task);
        $this->EM->flush();
    }

    public function setDescription($userId, $name){
        $task = $this->repository->findBy([
                'userId' => $userId,
                'name' => $id,
        ]);
    }

    public function deleteTask($id)
    {

    }
}
