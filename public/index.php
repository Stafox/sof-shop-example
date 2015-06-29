<?php
namespace SOF;
require realpath(dirname(__FILE__) . '/../src/SOF/Autoloader.php');

$autoloader = new Autoloader();
$autoloader->register();

$nl = php_sapi_name() == 'cli' ? "\n" : "<br>";
define('NL', $nl);

$shop = new Shop();
$shop->addDiscount(new Discount\TenPercentDiscount())
    ->addDiscount(new Discount\WeekendDiscount())
    ->addStock(new Stock\TwoForOneStock(new Discount\FreeDiscount()));
$user = new User('Bob', 'bob@gmail.com');
$shop->login($user);
$cart = $shop->getCart();

$user->addToCart($cart, new Product('iPhone 6', 600));
$user->addToCart($cart, new Product('iPad', 1000));
$user->addToCart($cart, new Product('iPad', 1000));
$user->addToCart($cart, new Product('iPhone 6', 600));
$user->addToCart($cart, new Product('iPhone 6', 600));
$product = new Product('iPhone 6S', 1600);
$user->addToCart($cart, $product);
$user->removeFromCart($cart, $product);

$discounts = $shop->getDiscounts();
$stocks = $shop->getStocks();
$cart->applyDiscounts($discounts);
$cart->applyStocks($stocks);
$invoice = new Invoice($cart);
$invoice->show();
