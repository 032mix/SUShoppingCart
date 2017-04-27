<?php

namespace Mixailoff\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInventoryProducts
 *
 * @ORM\Table(name="user_inventory_products")
 * @ORM\Entity(repositoryClass="Mixailoff\ShopBundle\Repository\UserInventoryProductsRepository")
 */
class UserInventoryProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UserInventory
     *
     * @ORM\ManyToOne(targetEntity="Mixailoff\ShopBundle\Entity\UserInventory")
     * @ORM\JoinColumn(name="inventory_id", referencedColumnName="id")
     */
    private $inventory;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Mixailoff\ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="bought_price", type="integer")
     */
    private $boughtPrice;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set inventory
     *
     * @param object $inventory
     *
     * @return UserInventoryProduct
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return object
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return UserInventoryProduct
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return UserInventoryProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getBoughtPrice()
    {
        return $this->boughtPrice;
    }

    /**
     * @param int $boughtPrice
     */
    public function setBoughtPrice($boughtPrice)
    {
        $this->boughtPrice = $boughtPrice;
    }
}

