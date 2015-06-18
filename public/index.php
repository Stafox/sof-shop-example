<?php

$user = new User('Bob', 'bob@gmail.com');
$cart = new Cart();
$cart->addDiscount(new TenPercentDiscount());
$product1 = new Product('iPhone', 600);
$product2 = new Product('iPad', 1000);
$user->addToCart($cart, $product1);
$user->addToCart($cart, $product2);
$order = $user->makeOrder($cart);
$user->getTotalPrice($order);

class Product
{
    protected $price;
    protected $name;

    public function __construct($name, $price)
    {
        $this->price = $price;
        $this->name = $name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getName()
    {
        return $this->name;
    }
}

class User
{
    public $name;
    public $email;

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
        echo "$name logged in to iMarket<br>";
    }

    public function addToCart(Cart $c, Product $p)
    {
        $c->addItem($p);
        echo "{$this->name} added to cart '{$p->getName()}' for \${$p->getPrice()}<br>";
    }

    public function makeOrder(Cart $c)
    {
        echo "{$this->name} made order<br>";

        return $c->makeOrder();
    }

    public function getTotalPrice(Order $o)
    {
        $totalPrice = $o->getTotalPrice();
        echo "{$this->name} chose products for \$$totalPrice<br>";
    }
}

class Cart
{
    public $items = [];
    public $discounts = [];

    public function addItem(Product $p)
    {
        $this->items[$p->getName()][] = $p;
    }

    public function makeOrder()
    {
        $this->applyDiscounts();
        return new Order($this->items);
    }

    public function addDiscount(DiscountDecorator $d)
    {
        $this->discounts[] = $d;
        echo "Enable '{$d->title}'<br>";
    }

    protected function applyDiscounts()
    {
        foreach($this->items as &$group) {
            foreach($group as &$item) {
                foreach ($this->discounts as $discount) {
                    $item = clone $discount->applyToProduct($item);
                }
            }
        }
    }
}

class Order
{
    public $items = [];

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function getTotalPrice()
    {
        $price = 0;
        foreach ($this->items as $group) {
            foreach($group as $item) {
                $price += $item->getPrice();
            }
        }
        return $price;
    }
}

abstract class DiscountDecorator extends Product
{
    protected $product;

    public function __construct()
    {
    }

    public function applyToProduct(Product $p)
    {
        $this->product = $p;
        return $this;
    }
}

class TenPercentDiscount extends DiscountDecorator
{
    public $title = 'Ten percent discount';

    public function getPrice()
    {
        return $this->product->getPrice() * 0.9;
    }
}