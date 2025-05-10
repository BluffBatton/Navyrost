<?php
function getUnique(array $products, $field) {
    $values = array_map(function ($p) use ($field) {
        return $p->$field;
    }, $products);
    return array_unique($values);
}