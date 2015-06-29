<?php
namespace SOF\Stock;

use SOF\ProductCollection;

class TwoForOneStock extends StockDecorator
{
    public $title = 'Two for the price of one';

    public function apply(ProductCollection $collection)
    {
        $this->collection = $collection;
        $items = $collection->getItems();
        foreach ($items as &$bundle) {
            foreach ($bundle as $index => &$item) {
                if(($index + 1) % 2 == 0) {
                    $item = clone $this->discount->applyToProduct($item);
                }
            }
        }
        $collection->setItems($items);
    }
}