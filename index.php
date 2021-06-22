<?php

require_once './QueryBuilder.php';

$builder = new QueryBuilder(new PDO('mysql:host=localhost;dbname=test', 'root', ''));
$builder->select('name')
    ->from('vendors')
    ->where('name', '=', 'Vanos')
    ->get();