<?php
namespace SOF\Discount;

class TenPercentDiscount extends DiscountDecorator
{
    public $title = '10% discount';

    public function getPrice()
    {
        return $this->product->getPrice() * 0.9;
    }
}