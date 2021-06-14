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

    public function addTask($userId, $name, $description)
    {
        $task = new Task();
        $task->setName($name);
        $task->setUserId($userId);
        $task->setDescription($description);
        $task->setDone(false);
        $this->EM->persist($task);
        $this->EM->flush();
    }

    public function deleteTask($id)
    {

    }
}
