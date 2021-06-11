<?php

namespace Model\Api;

use Colyt\Core as Core;

class Connection{
  private $DBH;

  function __construct($connection){
    $this->DBH = new PDO("mysql:host=$connection[host];dbname=$connection[dbname]", $connection['username'], $connection['password']);
  }


}

?>
