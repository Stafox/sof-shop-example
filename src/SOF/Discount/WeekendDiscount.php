<?php
namespace SOF\Discount;

class WeekendDiscount extends DiscountDecorator
{
    public $title = 'Weekend discount';

    public function getPrice()
    {
        return $this->product->getPrice() * 0.98;
    }
}