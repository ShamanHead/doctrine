<?php

namespace Model;

use Colyt\Core as Core;

Class Json{
  private static $responseOk = [
    'ResponseCode' => '200 OK',
    'Data' => ''
  ];
  private static $responseError = [
    'ResponseCode' => '500',
    'Reason' => ''
  ];

  public static function composeResponse(string $responseCode, $data){
    $generatedResponse = [];

    if($responseCode == 200){
      $generatedResponse = self::$responseOk;
      $generatedResponse['Data'] = $data;
    }else if($responseCode == 500){
      $generatedResponse = self::$responseError;
      $generatedResponse['Reason'] = $data;
    }

    return json_encode($generatedResponse);
  }
}

?>
