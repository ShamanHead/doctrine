<?php

namespace Model\Api;

use Colyt\Core as Core;

class DB{
  public static function initialize($connection){
    return new Connection($connection);
  }
}

?>
