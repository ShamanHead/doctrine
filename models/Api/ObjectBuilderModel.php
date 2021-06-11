<?php

namespace Model\Api;

use Colyt\Core as Core;

class ObjectBuilder{
  public $DBH;
  public $tableInfo;

  function __construct($connection, $table){
    $this->DBH = new PDO("mysql:host=$connection[host];dbname=$connection[dbname]", $connection['username'], $connection['password']);
    $query = $this->DBH->query('DESCRIBE ?');
    $query->setFetchMode(PDO::FETCH_NUM);
    $this->$tableInfo = $query->fetchAll();
    
  }

  public function set
}

?>
