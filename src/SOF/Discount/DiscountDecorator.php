<?php
namespace SOF\Discount;

use SOF\Product;

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

    public function getPrice()
    {
        return $this->product->getPrice();
    }

    public function getName()
    {
        return $this->product->getName();
    }

    public function getParent()
    {
        return $this->product;
    }
}