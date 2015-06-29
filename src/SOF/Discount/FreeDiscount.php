<?php
namespace SOF\Discount;

class FreeDiscount extends DiscountDecorator
{
    public $title = 'Get it for Free';

    public function getPrice()
    {
        return $this->product->getPrice() * 0;
    }

}