<?php

$nl = php_sapi_name() == 'cli' ? "\n" : "<br>";
define('NL', $nl);

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

class Cart extends ProductCollection
{

    public function makeOrder()
    {
        return new Order($this->items);
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

    public function addDiscount(DiscountDecorator $d)
    {
        $this->discounts[] = $d;
        echo "Enable '{$d->title}'" . NL;
        return $this;
    }

    public function addStock(StockDecorator $s)
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

class Product
{
    protected $price;
    protected $name;

    public function __construct($name, $price)
    {
        $this->price = $price;
        $this->name = $name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOriginalPrice()
    {
        return $this->price;
    }
}

class User
{
    public $name;
    public $email;

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function addToCart(Cart $c, Product $p)
    {
        $c->addItem($p);
        echo "{$this->name} added to cart '{$p->getName()}' for \${$p->getPrice()}" . NL;
    }

    public function removeFromCart(Cart $c, Product $p)
    {
        $c->removeItem($p);
        echo "{$this->name} remove from cart '{$p->getName()}' for \${$p->getPrice()}" . NL;
    }

    public function makeOrder(Cart $c)
    {
        echo "{$this->name} made order" . NL;

        return $c->makeOrder();
    }

    public function getTotalPrice(Order $o)
    {
        $totalPrice = $o->getTotalPrice();
        $originalTotalPrice = $o->getOriginalTotalPrice();
        $discount = $originalTotalPrice - $totalPrice;
        echo "{$this->name} chose products for \$$totalPrice" . NL;
        echo "{$this->name} saved \$$discount with discounts" . NL;
    }
}


class Order
{
    public $items = [];

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function getTotalPrice()
    {
        $price = 0;
        foreach ($this->items as $bundle) {
            foreach ($bundle as $item) {
                $price += $item->getPrice();
            }
        }
        return $price;
    }

    public function getOriginalTotalPrice()
    {
        $price = 0;
        foreach ($this->items as $bundle) {
            foreach ($bundle as $item) {
                $price += $item->getOriginalPrice();
            }
        }
        return $price;
    }
}

abstract class DiscountDecorator extends Product
{
    protected $product;

    public function __construct()
    {
    }

    public function applyToProduct(Product $p)
    {
        $this->product = $p;
        return $this;
    }

    public function getOriginalPrice()
    {
        return $this->product->getOriginalPrice();
    }
}

class TenPercentDiscount extends DiscountDecorator
{
    public $title = '10% discount';

    public function getPrice()
    {
        return $this->product->getPrice() * 0.9;
    }
}


class FreeDiscount extends DiscountDecorator
{
    public $title = 'Get it for Free';

    public function getPrice()
    {
        return $this->product->getPrice() * 0;
    }

}

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

$shop = new Shop();
$user = new User('Bob', 'bob@gmail.com');
$shop->login($user);
$shop->addDiscount(new TenPercentDiscount())
    ->addStock(new TwoForOneStock(new FreeDiscount()));
$cart = $shop->getCart();

$user->addToCart($cart, new Product('iPhone 6', 600));
$user->addToCart($cart, new Product('iPad', 1000));
$user->addToCart($cart, new Product('iPad', 1000));
$user->addToCart($cart, new Product('iPhone 6', 600));
$user->addToCart($cart, new Product('iPhone 6', 600));
$product = new Product('iPhone 6S', 1600);
$user->addToCart($cart, $product);
$user->removeFromCart($cart, $product);

$discounts = $shop->getDiscounts();
$stocks = $shop->getStocks();
$cart->applyDiscounts($discounts);
$cart->applyStocks($stocks);
$order = $user->makeOrder($cart);
$user->getTotalPrice($order);