<?php

$metadata->setPrimaryTable(['name' => 'tasks']);

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
    'fieldName' => 'name',
    'type' => 'string',
    'length' => 50
));

$metadata->mapField(array(
    'fieldName' => 'done',
    'type' => 'boolean',
    'options' => [
        'default' => false
    ]
));

$metadata->mapField(array(
    'fieldName' => 'description',
    'type' => 'string',
    'length' => 255
));