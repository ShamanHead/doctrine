<?php

$metadata->setPrimaryTable(['name' => 'telbot_users']);
$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);

$metadata->mapField(array(
    'id' => true,
    'fieldName' => 'id',
    'type' => 'integer'
));

$metadata->mapField(array(
    'fieldName' => 'userId',
    'type' => 'string',
    'length' => 50
));

$metadata->mapField(array(
    'fieldName' => 'chatId',
    'type' => 'string',
    'length' => 50
));

$metadata->mapField(array(
    'fieldName' => 'language',
    'type' => 'string',
    'length' => 10,
    'options' => [
        'default' => ''
    ]
));

$metadata->mapField(array(
    'fieldName' => 'botToken',
    'type' => 'string',
    'length' => 50
));

$metadata->mapField(array(
    'fieldName' => 'context',
    'type' => 'string',
    'length' => 100,
    'options' => [
        'default' => ''
    ]
));

$metadata->mapField(array(
    'fieldName' => 'privilege',
    'type' => 'integer',
    'length' => 3,
    'options' => [
        'default' => 0
    ]
));