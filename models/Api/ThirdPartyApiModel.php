<?php

namespace Model;

use \PDO as PDO;

class ThirdPartyApi{

  private static $requestFlow = "";
  public static $response = [];

  public static function addRequestFlow(string $requestFlow) : bool {
    self::$requestFlow = $requestFlow;
    return true;
  }

  public static function checkPattern(string $pattern) : array {
    $args = [true];

    $explodedRequestFlow = explode("/", self::$requestFlow);
    $pattern = explode("/", $pattern);

    if($pattern[array_key_last($pattern)] == "") unset($pattern[array_key_last($pattern)]);
    if($explodedRequestFlow[array_key_last($explodedRequestFlow)] == "") unset($explodedRequestFlow[array_key_last($explodedRequestFlow)]);

    if(count($pattern) != count($explodedRequestFlow)) return [false];
    for($i = 0, $matches;$i < count($pattern);$i++){
      if(preg_match('/{\$([a-zA-Z]+)}/', $pattern[$i], $matches)){
        $args[$matches[1]] = $explodedRequestFlow[$i];
      }else{
        if($pattern[$i] != $explodedRequestFlow[$i]) return [false];
      }
    }
    return $args;
  }

  public static function add(string $pattern, $callBack){
    self::$response[] = [$pattern,$callBack];
  }

  public static function run(){
    for($i = 0;$i < count(self::$response);$i++){
      $patternArgs = self::checkPattern(self::$response[$i][0]);
      if($patternArgs[0] != false){
        $DBH = new PDO("mysql:host=127.0.0.1;dbname=plexfex", "sha1", "2336077303Ars2200;");
          self::$response[$i][1]($patternArgs, $DBH);
      }
    }
  }

}

?>
