<?php

require_once './QueryBuilder.php';

try {
    $connection = new PDO('mysql:host=localhost;port=3306;dbname=test', 'root', '');


    $builder = new QueryBuilder($connection);
    $query = $builder->select('items.name')
        ->from('items')
        ->join('category', 'items.category_id', '=', 'category.id')
        ->join('vendor', 'items.vendor_id', '=', 'vendor.id')
        ->when(!empty($_GET['category_name']), function (QueryBuilder $query) {
            $query->where('category.name', '=', $_GET['category_name']);
        })
        ->when(!empty($_GET['vendor_name']), function (QueryBuilder $query) {
            $query->where('vendor.name', '=', $_GET['vendor_name']);
        })
        ->when(!empty($_GET['price_from']), function (QueryBuilder $query) {
            $query->where('items.price', '>=', $_GET['price_from']);
        })
        ->when(!empty($_GET['price_to']), function (QueryBuilder $query) {
            $query->where('items.price', '<=', $_GET['price_to']);
        })
        ->get();

    if (!$query->execute()) {
        var_dump($query->errorInfo());exit;
    }

    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        var_dump($row);
    }
} catch (Exception $exception) {
    var_dump($exception->getMessage());exit;
}
