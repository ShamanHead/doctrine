<?php

require_once('cgi-bin/routes.php');
require_once('cgi-bin/core.php');

use Colyt\Core as Core;
use Colyt\Routes as Routes;

$route = explode('/',$_GET['route']);
$routes = Routes::get();

$finded = false;

for($i = 0; $i < count($routes);$i++){
	if($route[0] == $routes[$i]['name']){
		$finded = true;
		$controllers = explode(',', $routes[$i]['controllers']);
		$models = explode(',', $routes[$i]['models']);
		require_once($_SERVER['DOCUMENT_ROOT'].'/cgi-bin/core.php');
		for($j = 0;$j < count($models);$j++){
			if($models[0] == false) break;
			require_once($_SERVER['DOCUMENT_ROOT'].'/models/'.$models[$j].'Model.php');
		}
		for($j = 0;$j < count($controllers);$j++){
			if($controllers[0] == false) break;
			require_once($_SERVER['DOCUMENT_ROOT'].'/controllers/'.$controllers[$j].'Controller.php');
		}
		$routes[$i]['view'] ? require_once($_SERVER['DOCUMENT_ROOT'].'/public/'.$routes[$i]['view']) : false;
	}
}

if(!$finded){
	Header('Location:http://'.$_SERVER['SERVER_NAME'].'/'.Core::getConfig()['when_root_not_found']);
}

?>
