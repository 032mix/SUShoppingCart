<?php
// src/Mixailoff/ShopBundle/Entity/User.php

namespace Mixailoff\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Mixailoff\ShopBundle\Entity\Cart", mappedBy="user")
     */
    private $cart;

    /**
     * @ORM\OneToMany(targetEntity="Mixailoff\ShopBundle\Entity\UserInventory", mappedBy="user")
     */
    private $userinventory;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="current_balance", type="integer")
     */
    private $currentBalance = 1000;

    /**
     * @return string
     */
    public function getCurrentBalance()
    {
        return $this->currentBalance;
    }

    /**
     * @param string $currentBalance
     */
    public function setCurrentBalance($currentBalance)
    {
        $this->currentBalance = $currentBalance;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return mixed
     */
    public function getUserinventory()
    {
        return $this->userinventory;
    }

    /**
     * @param mixed $userinventory
     */
    public function setUserinventory($userinventory)
    {
        $this->userinventory = $userinventory;
    }
}