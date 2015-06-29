<?php
namespace SOF;

class User
{
    public $name;
    public $email;

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function addToCart(Cart $c, Product $p)
    {
        $c->addItem($p);
        echo "{$this->name} added to cart '{$p->getName()}' for \${$p->getPrice()}" . NL;
    }

    public function removeFromCart(Cart $c, Product $p)
    {
        $c->removeItem($p);
        echo "{$this->name} remove from cart '{$p->getName()}' for \${$p->getPrice()}" . NL;
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
        echo "Initial               \$$originalTotalPrice" . NL;
        echo "Discount             -\$$discount" . NL;
        echo "Total                 \$$totalPrice" . NL;
    }
}