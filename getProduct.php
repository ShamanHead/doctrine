<?php
// create_product.php <name>
require_once "config/boostrap.php";

class ProductManager{
  protected static $entityManager;
  protected static $singleton;

  function __construct($entityManager){
    if(self::$singleton != true){
      self::$entityManager = $entityManager;
      self::$singleton = true;
    }else{
      throw new Exception("Cannot create second ProductManager class!");
    }
  }

  function __destruct(){
    self::$singleton = false;
  }

  public static function find($id = -1){
    $return = [];
    if($id === -1){
      $return = self::$entityManager->getRepository('Product')->findAll();
    }else{
      $return = self::$entityManager->find('Product', $id);
    }
    return $return ?? false;
  }

  public static function update($id, $newProperties){

  }
}

$manager = new ProductManager($entityManager);

print_r($manager::find(1));
