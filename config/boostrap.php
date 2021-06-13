<?php

require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\Mapping\Driver\PHPDriver as PHPDriver;

$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array("./Entities"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

$conn = array(
  'driver'   => 'pdo_mysql',
  'host'     => 'us-cdbr-east-04.cleardb.com',
  'user'     => 'bb0b4a3c418e0d',
  'password' => '29a5b1cb',
  'dbname'   => 'heroku_3d1d87a8d7d11e3',
);

$entityManager = EntityManager::create($conn, $config);
$driver = new PHPDriver('Mapping/');
$entityManager->getConfiguration()->setMetadataDriverImpl($driver);