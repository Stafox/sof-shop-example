<?php
namespace SOF\Stock;

use SOF\Discount\DiscountDecorator;
use SOF\ProductCollection;

abstract class StockDecorator
{
    protected $collection;
    protected $discount;

    public function __construct(DiscountDecorator $d)
    {
        $this->discount = $d;
    }

    abstract public function apply(ProductCollection $collection);
}