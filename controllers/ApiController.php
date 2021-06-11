<?php

namespace Controller;

use Model\ThirdPartyApi as Api;
use Model\Json as Json;
use Colyt\Core as Core;
use \PDO as PDO;

$route = $_GET['route'];

Api::addRequestFlow($route);

$DBH = new PDO("mysql:host=127.0.0.1;dbname=plexfex", "sha1", "2336077303Ars2200;");
$query = $DBH->query('DESCRIBE users');
$query->setFetchMode(PDO::FETCH_NUM);
print_r(JSON::composeResponse(200 ,$query->fetchAll()));

Api::add('api/register/{$name}/{$pass}/{$mail}', function(array $args, PDO $DBH){
  $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $STH = $DBH->prepare("INSERT INTO users ( login, password, mail ) VALUES ( ?, ?, ? )");
  $STH->execute([$args['name'], $args['pass'], $args['mail']]);
  $response = Json::composeResponse(200);
  print_r($response);
});

Api::add('api/users/getall', function(array $args, PDO $DBH){
  $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $STH = $DBH->query("SELECT * FROM users");
  $STH->setFetchMode(PDO::FETCH_OBJ);
  $response = Json::composeResponse(200, $STH->fetchAll());
  print_r($response);
});

Api::add('api/users/get/{$login}', function(array $args, PDO $DBH){
  $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  $STH = $DBH->prepare("SELECT * FROM users WHERE login = ?");
  $STH->bindParam(1, $args['login']);
  $STH->execute();
  $STH->setFetchMode(PDO::FETCH_OBJ);
  $response = Json::composeResponse(200, $STH->fetchAll());
  print_r($response);
});

Api::run();
?>
