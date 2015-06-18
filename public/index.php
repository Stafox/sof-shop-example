<?php

$nl = php_sapi_name() == 'cli' ? "\n" : "<br>";
define('NL', $nl);

$user = new User('Bob', 'bob@gmail.com');
$cart = new Cart();
$cart->addDiscount(new TenPercentDiscount());
$product1 = new Product('iPhone 6', 600);
$product2 = new Product('iPad', 1000);
$product3 = new Product('iPhone 6', 600);
$user->addToCart($cart, $product1);
$user->addToCart($cart, $product2);
$user->addToCart($cart, $product3);
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

    public function getOriginalPrice()
    {
        return $this->price;
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
        echo "$name logged in to iMarket" . NL;
    }

    public function addToCart(Cart $c, Product $p)
    {
        $c->addItem($p);
        echo "{$this->name} added to cart '{$p->getName()}' for \${$p->getPrice()}" . NL;
    }

    public function makeOrder(Cart $c)
    {
        echo "{$this->name} made order" . NL;

        return $c->makeOrder();
    }

    public function getTotalPrice(Order $o)
    {
        $totalPrice = $o->getTotalPrice();
        $originalTotalPrice = $o->getOriginalTotalPrice();
        $discount = $originalTotalPrice - $totalPrice;
        echo "{$this->name} chose products for \$$totalPrice" . NL;
        echo "{$this->name} saved \$$discount with discounts" . NL;
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
        echo "Enable '{$d->title}'" . NL;
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

    public function getOriginalTotalPrice()
    {
        $price = 0;
        foreach ($this->items as $group) {
            foreach($group as $item) {
                $price += $item->getOriginalPrice();
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

    public function getOriginalPrice()
    {
        return $this->product->getOriginalPrice();
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