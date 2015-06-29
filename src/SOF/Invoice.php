<?php
namespace SOF;

class Invoice
{
    protected $collection;

    public function __construct(ProductCollection $collection)
    {
        $this->collection = $collection;
    }

    public function show()
    {
        $totalOriginalPrice = $totalDiscountPrice = $totalPrice = 0;
        foreach ($this->collection->getItems() as $bundle) {
            foreach ($bundle as $item) {
                $totalDiscount = $item->getOriginalPrice() - $item->getPrice();
                echo "{$item->getName()} \${$item->getOriginalPrice()} (-\$$totalDiscount)" . NL;
                foreach($this->getDiscounts($item) as $discount) {
                    echo "  -\${$discount['price']} ({$discount['title']})" . NL;
                }
                echo "Total: \${$item->getPrice()}" . NL;
                $totalOriginalPrice += $item->getOriginalPrice();
                $totalDiscountPrice += $totalDiscount;
                $totalPrice += $item->getPrice();
            }
        }
        echo "============================" . NL;
        echo "Total original price: \$$totalOriginalPrice" . NL;
        echo "Total discount: \$$totalDiscountPrice" . NL;
        echo "Total price: \$$totalPrice" . NL;

    }

    public function getDiscounts(Product $item)
    {
        $discounts = [];
        while($item instanceof Discount\DiscountDecorator) {
            $discounts[] = array(
                'price' => $item->getParent()->getPrice() - $item->getPrice(),
                'title' => $item->title
            );
            $item = clone $item->getParent();
        }

        return array_reverse($discounts);
    }

}