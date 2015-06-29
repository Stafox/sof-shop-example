<?php
namespace SOF;

class Cart extends ProductCollection
{

    public function makeOrder()
    {
        return new Order($this);
    }

    public function applyDiscounts($discounts)
    {
        foreach ($this->items as &$bundle) {
            foreach ($bundle as &$item) {
                foreach ($discounts as $discount) {
                    $item = clone $discount->applyToProduct($item);
                }
            }
        }
    }

    public function applyStocks($stocks)
    {
        foreach ($stocks as $stock) {
            $stock->apply($this);
        }
    }
}