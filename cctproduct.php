<?php
// create_product.php <name>
require_once "doctrine/config/boostrap.php";
require_once "doctrine/src/Product.php";

$newProductName = $argv[1];

$product = new Product();
$product->setName($newProductName);

$entityManager->persist($product);
$entityManager->flush();

echo "Created Product with ID " . $product->getId() . "\n";
