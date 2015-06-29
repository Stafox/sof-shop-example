<?php
namespace SOF;

class Shop
{
    protected $name = 'iMarket';
    protected $cart = null;
    protected $discounts = [];
    protected $stocks = [];

    public function getCart()
    {
        if (!$this->cart) {
            $this->cart = new Cart();
        }
        return $this->cart;
    }

    public function login(User $user)
    {
        echo "{$user->name} logged in to {$this->name}" . NL;
    }

    public function addDiscount(Discount\DiscountDecorator $d)
    {
        $this->discounts[] = $d;
        echo "Enable '{$d->title}'" . NL;
        return $this;
    }

    public function addStock(Stock\StockDecorator $s)
    {
        $this->stocks[] = $s;
        echo "Enable '{$s->title}'" . NL;
        return $this;
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function getStocks()
    {
        return $this->stocks;
    }
}