<?php

namespace Colyt;

class Routes{

	public static function get(){

		$routesFile = trim(str_replace(' ', '',file_get_contents($_SERVER['DOCUMENT_ROOT']."/routes.cf", 'r')));
		$routesList = [];

		for($i = 0, $buffer="", $state = 0, $routeBuffer = []; $i < iconv_strlen($routesFile);$i++){
		    $buffer .= trim($routesFile[$i]);
			if(strcasecmp(trim($buffer),'name:') == 0 && $state != 5){
				$state = 1;
				$buffer = '';
			}else if(strcasecmp(trim($buffer),'models:') == 0 && $state != 5){
				$state = 2;
				$buffer = '';
			}else if(strcasecmp(trim($buffer),'view:') == 0 && $state != 5){
				$state = 4;
				$buffer = '';
			}else if(strcasecmp(trim($buffer),'controllers:') == 0 && $state != 5){
				$state = 3;
				$buffer = '';
			}else if($routesFile[$i] == '{' && $state != 5){
				$state = 5;
			}else if($routesFile[$i] == '}' && $state == 5){
				$state = 0;
				$buffer = '';
				$i++;
			}else if($routesFile[$i] == ';' && $state != 5){
				switch($state){
					case 1:
						$routeBuffer['name'] = str_replace(";","", $buffer);
					break;
					case 2:
						$routeBuffer['models'] = str_replace(";","", $buffer);
					break;
					case 3:
						$routeBuffer['controllers'] = str_replace(";","", $buffer);
					break;
					case 4:
						$routeBuffer['view'] = str_replace(";","", $buffer);
					break;
					default:
						throw new \Error('something went wrong in '.$state);
					break;
				}
				$buffer = '';
				$state = 0;
				$i++;
				if(count($routeBuffer) == 4){
					array_push($routesList, $routeBuffer);
					$routeBuffer = [];
				}
			}
			if($i == iconv_strlen($routesFile) - 1 && count($routeBuffer) == 0){
				throw new \Error('semicolon not found in '.$buffer);
			}
		}
		return $routesList;
	}

}

?>
