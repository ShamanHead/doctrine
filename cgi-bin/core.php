<?php

namespace Colyt;

use \PDO as PDO;

class Core{

	private static $config = [
		'shop_name' => 'Colyt',
		'update_plugins_list_on_render' => true,
		'when_root_not_found' => '404',
		'database' => [
			'host' => '127.0.0.1',
			'dbname' => 'plexfex',
			'username' => 'sha1',
			'password' => '2336077303Ars2200;'
		],
		'errors' => [
			'404' => 'site/errors/404.html'
		]
	];

	private $core_plugins = [
		'120983190283' => [
			"Name" => "Some",
			"Author" => "ShamanHead",
			"Rights" => [0, 1, 3, 2, 5],
			"Description" => "some value"
		]
	];

	public static function updatePluginsList(){
		//Some
	}

	public static function getPDOConnection(){
		// return $this->DBH;
	}

	public static function getAllPluginsList(){
		// return $plugins;
	}

	public static function getConfig(){
		return self::$config;
	}


}

?>
