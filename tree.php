<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class GroceryItem {
    function __construct($text, $leaf, $items = array()) {
        $this->text = $text;
        $this->leaf = $leaf;
        $this->items = $items;
    }
}

class Groceries {
    function __construct($items) {
        $this->items = $items;
    }
}

$redBull = new GroceryItem("Red Bull", true);
$coke = new GroceryItem("Coke", true);
$dietCoke = new GroceryItem("Diet Coke", true);

$espresso = new GroceryItem("Espresso", true);
$coffee = new GroceryItem("Coffee", true);

$stillWater = new GroceryItem("Still", true);
$sparklingWater = new GroceryItem("Sparkling", true);
$water = new GroceryItem("Water", false, array($sparklingWater,$stillWater));
$drinks = new GroceryItem("Drinks", false, array($water,$coffee,$espresso,$redBull,$coke,$dietCoke));

$groceries = new Groceries($drinks);

//header('Cache-Control: no-cache, must-revalidate');
//header("content-type:application/json");

echo '<pre>';
print_r($groceries);
die();

echo(json_encode($groceries));