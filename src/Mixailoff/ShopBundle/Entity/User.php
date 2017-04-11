<?php
// src/Mixailoff/ShopBundle/Entity/User.php

namespace Mixailoff\ShopBundle\Entity;

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

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var string
     *
     * @ORM\Column(name="current_balance", type="string")
     */
    private $currentBalance;

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
}