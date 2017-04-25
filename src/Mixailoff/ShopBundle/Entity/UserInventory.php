<?php

namespace Mixailoff\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInventory
 *
 * @ORM\Table(name="user_inventory")
 * @ORM\Entity(repositoryClass="Mixailoff\ShopBundle\Repository\UserInventoryRepository")
 */
class UserInventory
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
     * @var User
     * @ORM\ManyToOne(targetEntity="Mixailoff\ShopBundle\Entity\User", inversedBy="userinventory")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set user
     *
     * @param \stdClass $user
     *
     * @return UserInventory
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \stdClass
     */
    public function getUser()
    {
        return $this->user;
    }
}

