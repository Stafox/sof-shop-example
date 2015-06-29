<?php
namespace SOF;

class Order
{
    protected $collection;

    public function __construct(ProductCollection $collection)
    {
        $this->collection = $collection;
    }

    public function getTotalPrice()
    {
        $price = 0;
        foreach ($this->collection->getItems() as $bundle) {
            foreach ($bundle as $item) {
                $price += $item->getPrice();
            }
        }
        return $price;
    }

    public function getOriginalTotalPrice()
    {
        $price = 0;
        foreach ($this->collection->getItems() as $bundle) {
            foreach ($bundle as $item) {
                $price += $item->getOriginalPrice();
            }
        }
        return $price;
    }
}