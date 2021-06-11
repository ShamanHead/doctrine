<?php

namespace Model\Orm;

use Colyt\Core as Core;
use \PDO as PDO;

Class Connection{
	public $DBH;

	function __construct(array $args){
			$this->DBH = new PDO("mysql:host=$args[host];dbname=$args[dbname]", $args['username'], $args['password']);
			$this->DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function exec($query){
		return $this->DBH->query($query);
	}


}

?>
