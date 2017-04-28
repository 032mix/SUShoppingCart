<?php

namespace Mixailoff\ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use Mixailoff\ShopBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;

class InventoryService
{
    protected $em;

    protected $promotionService;

    public function __construct(EntityManager $em, PromotionService $promotionService)
    {
        $this->em = $em;
        $this->promotionService = $promotionService;
    }

    public function sellBackProduct(User $user, $productId, $quantitySelected)
    {
        $product = $this
            ->em
            ->getRepository('MixSBundle:Product')
            ->findOneBy(['id' => $productId]);
        $userInventory = $this
            ->em
            ->getRepository('MixSBundle:UserInventory')
            ->findOneBy(['user' => $user]);
        $inventoryProduct = $this
            ->em
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findOneBy(['inventory' => $userInventory,
                'product' => $product]);
        $inventoryQuantity = $inventoryProduct->getQuantity();

        if (($inventoryQuantity - $quantitySelected) < 0) {
            throw new Exception(
                'Not enough products of that type in inventory.
                You have:' . ' ' . $inventoryQuantity . ' ' . 'left!');
        } else {
            $productPrice = $this->promotionService->calculatePromotedPrice($product);
            $productQtyPrice = $productPrice * $quantitySelected;
            $user->setCurrentBalance($user->getCurrentBalance() + $productQtyPrice);
            $product->setQuantity($product->getQuantity() + $quantitySelected);
            if (($inventoryProduct->getQuantity() - $quantitySelected) == 0) {
                $this->em->remove($inventoryProduct);
            } else {
                $inventoryProduct->setQuantity($inventoryQuantity - $quantitySelected);
            }

            $this->em->flush();


        }
    }
}