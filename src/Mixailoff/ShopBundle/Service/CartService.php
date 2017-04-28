<?php

namespace Mixailoff\ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use Mixailoff\ShopBundle\Entity\Cart;
use Mixailoff\ShopBundle\Entity\CartProduct;
use Mixailoff\ShopBundle\Entity\User;
use Mixailoff\ShopBundle\Entity\UserInventory;
use Mixailoff\ShopBundle\Entity\UserInventoryProduct;
use Symfony\Component\Config\Definition\Exception\Exception;

class CartService
{
    protected $em;

    protected $promotionService;

    public function __construct(EntityManager $em, PromotionService $promotionService)
    {
        $this->em = $em;
        $this->promotionService = $promotionService;
    }

    public function newCart($user)
    {
        $cart = new Cart();
        $cart->setUser($user);
        $cart->setDateCreated(new \DateTime());
        $cart->setDateUpdated(new \DateTime());

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    public function addToCart($productId, $quantity, $cart)
    {
        $productRepo = $this->em->getRepository('MixSBundle:Product');
        $product = $productRepo->find($productId);
        $cartProduct = $this->em
            ->getRepository('MixSBundle:CartProduct')
            ->findOneBy([
                'cart' => $cart,
                'product' => $product
            ]);

        if (!$cartProduct) {
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cart);
            $cartProduct->addProduct($product);
            $cartProduct->setQuantity($cartProduct->getQuantity() + $quantity);
        } else {
            $cartProduct->setQuantity($cartProduct->getQuantity() + $quantity);
        }
        $this->em->persist($cartProduct);

        return true;
    }

    public function newUserInventory(User $user)
    {
        $inventory = new UserInventory();
        $inventory->setUser($user);

        $this->em->persist($inventory);
        $this->em->flush();
    }

    public function checkoutCart(User $user, Cart $cart)
    {
        $userInventory = $this->em->getRepository('MixSBundle:UserInventory')->findOneBy(['user' => $user]);
        if (!$userInventory) {
            $this->newUserInventory($user);
        }
        $userInventory = $this->em->getRepository('MixSBundle:UserInventory')->findOneBy(['user' => $user]);
        $cartProductRepo = $this->em->getRepository('MixSBundle:CartProduct');
        $cartProduct = $cartProductRepo->findBy(['cart' => $cart]);
        $products = [];
        $quantity = [];
        foreach ($cartProduct as $productFromCart) {
            $quantityFromCart = $productFromCart->getQuantity();
            array_push($quantity, $quantityFromCart);
            $product = $productFromCart->getProduct();
            array_push($products, $product);
        }
        foreach ($products as $product) {
            $productQuantity = array_shift($quantity);
            $userBalance = $user->getCurrentBalance();
            $productPrice = $this->promotionService->calculatePromotedPrice($product);
            $productQtyPrice = $productPrice * $productQuantity;
            if ($userBalance < $productQtyPrice) {
                throw new Exception('Not enough money in your balance.');
            } else {
                $user->setCurrentBalance($userBalance - $productQtyPrice);
                if ($product) {
                    $userInventoryProduct = $this->em
                        ->getRepository('MixSBundle:UserInventoryProduct')
                        ->findOneBy([
                            'inventory' => $userInventory,
                            'product' => $product
                        ]);
                    $realProduct = $this->em
                        ->getRepository('MixSBundle:Product')
                        ->find($product);
                    $realProductNewQty = $realProduct->getQuantity() - $productQuantity;
                    if ($realProductNewQty < 0) {
                        throw new Exception(
                            'Not enough products left. Products remaining:' . ' ' . $realProduct->getQuantity());
                    } else {
                        $realProduct->setQuantity($realProductNewQty);

                        if (!$userInventoryProduct) {
                            $userInventoryProduct = new UserInventoryProduct();
                            $userInventoryProduct->setInventory($userInventory);
                            $userInventoryProduct->setProduct($product);
                            $userInventoryProduct->setQuantity($userInventoryProduct
                                    ->getQuantity() + $productQuantity);

                        } else {
                            $userInventoryProduct->setQuantity($userInventoryProduct
                                    ->getQuantity() + $productQuantity);
                        }
                        $this->em->persist($userInventoryProduct);
                    }
                }
            }
        }

    }

}