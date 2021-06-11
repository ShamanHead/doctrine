<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array("./src"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

$conn = array(
  'driver'   => 'pdo_mysql',
  'user'     => 'sha1',
  'password' => '2336077303Ars2200;',
  'dbname'   => 'plexfex',
);

$entityManager = EntityManager::create($conn, $config);
