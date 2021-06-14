<?php

$str = 'This {class} is {super} dinamic test';

$array = [
    'first' => 'coler',
    'class' => 'fold',
    'super' => 'kek'
];

for($i = 0, $keys = array_keys($array);$i < count($keys);$i++){
    $str = preg_replace('/{'.$keys[$i].'}/', $array[$keys[$i]], $str);
}

echo $str;