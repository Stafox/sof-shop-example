<?php
namespace SOF;

class ProductCollection
{
    protected $items = [];

    public function addItem(Product $p)
    {
        $this->items[$p->getName()][] = $p;
    }

    public function removeItem(Product $p)
    {
        foreach ($this->items as $bundleIndex => $bundle) {
            foreach ($bundle as $itemIndex => $item) {
                if ($item === $p) {
                    if (count($bundle) == 1) {
                        unset($this->items[$bundleIndex]);
                    } else {
                        unset($this->items[$bundleIndex][$itemIndex]);
                    }
                    break 2;
                }
            }
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }
}