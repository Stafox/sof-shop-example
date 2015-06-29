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
}

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
        echo "Initial               \$$originalTotalPrice" . NL;
        echo "Discount             -\$$discount" . NL;
        echo "Total                 \$$totalPrice" . NL;
    }
}


class Order
{
    protected $collection;

    public function __construct(ProductCollection $collection)
    {
        $this->collection = $collection;
    }

    public function getTotalPrice()
    {
        $price = 0;
        foreach ($this->collection->getItems() as $bundle) {
            foreach ($bundle as $item) {
                $price += $item->getPrice();
            }
        }
        return $price;
    }

    public function getOriginalTotalPrice()
    {
        $price = 0;
        foreach ($this->collection->getItems() as $bundle) {
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

    public function getPrice()
    {
        return $this->product->getPrice();
    }

    public function getName()
    {
        return $this->product->getName();
    }

    public function getParent()
    {
        return $this->product;
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

class WeekendDiscount extends DiscountDecorator
{
    public $title = 'Weekend discount';

    public function getPrice()
    {
        return $this->product->getPrice() * 0.98;
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
        foreach ($collection->getItems() as &$bundle) {
            foreach ($bundle as $index => &$item) {
                if(($index + 1) % 2 == 0) {
                    $item = clone $this->discount->applyToProduct($item);
                }
            }
        }
    }
}

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
        while($item instanceof DiscountDecorator) {
            $discounts[] = array(
                'price' => $item->getParent()->getPrice() - $item->getPrice(),
                'title' => $item->title
            );
            $item = clone $item->getParent();
        }

        return array_reverse($discounts);
    }

}

$shop = new Shop();
$shop->addDiscount(new TenPercentDiscount())
    ->addDiscount(new WeekendDiscount())
    ->addStock(new TwoForOneStock(new FreeDiscount()));
$user = new User('Bob', 'bob@gmail.com');
$shop->login($user);
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
$invoice = new Invoice($cart);
$invoice->show();