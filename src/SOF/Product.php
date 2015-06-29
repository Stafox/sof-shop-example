<?php
namespace SOF;

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