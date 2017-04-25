<?php

namespace Mixailoff\ShopBundle\Controller;

use Mixailoff\ShopBundle\Entity\Cart;
use Mixailoff\ShopBundle\Entity\CartProduct;
use Mixailoff\ShopBundle\Entity\UserInventory;
use Mixailoff\ShopBundle\Entity\UserInventoryProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        $items = $em
            ->getRepository('MixSBundle:CartProduct')
            ->findBy(['cart' => $cartId]);
        return $this->render('MixSBundle:Default:showcart.html.twig', array(
            'items' => $items,
            'user' => $user
        ));
    }

    public function addToCartAction($productId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepo = $em->getRepository('MixSBundle:Product');

        if (!$product = $productRepo->find($productId)) {
            throw $this->createNotFoundException('Product not found!');
        }

        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        $cart = $em->getRepository('MixSBundle:Cart')->find($session->get('id_cart', false));

        if (!$cartId or !$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setDateCreated(new \DateTime());
            $cart->setDateUpdated(new \DateTime());

            $em->persist($cart);
            $em->flush();

            $session->set('id_cart', $cart->getId());
        }

        $product = $productRepo->find($productId);
        $quantity = $request->get('quantity');
        if ($product) {

            $cartProduct = $em
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
            $em->persist($cartProduct);
        }

        $cart->setDateUpdated(new \DateTime());
        $em->persist($cart);
        $em->flush();

        return $this->redirect($this->generateUrl('mix_s_cart_display'));
    }

    public function checkoutAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cartProductRepo = $em->getRepository('MixSBundle:CartProduct');
        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $em->getRepository('MixSBundle:Cart')->findBy(['user' => $user]);

        if (!$cartId) {
            throw $this->createNotFoundException();
        }

        $userInventoryId = $em->getRepository('MixSBundle:UserInventory')->findBy(['user' => $user]);

        if (!$userInventoryId) {
            $inventory = new UserInventory();
            $inventory->setUser($user);

            $em->persist($inventory);
            $em->flush();

            $session->set('id_inventory', $inventory->getId());
        }
        $userInventory = $em
            ->getRepository('MixSBundle:UserInventory')
            ->find($session->get('id_inventory', false));
        $cartProduct = $cartProductRepo->findBy(['cart' => $cartId]);
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
            $productPrice = $product->getPrice();
            $productQtyPrice = $productPrice * $productQuantity;
            if ($userBalance < $productQtyPrice) {
                throw $this->createNotFoundException('Not enough money in your balance.');
            } else {
                $user->setCurrentBalance($userBalance - $productQtyPrice);
                if ($product) {
                    $userInventoryProduct = $em
                        ->getRepository('MixSBundle:UserInventoryProduct')
                        ->findOneBy([
                            'inventory' => $userInventory,
                            'product' => $product
                        ]);
                    $realProduct = $em
                        ->getRepository('MixSBundle:Product')
                        ->find($product);
                    $realProductNewQty = $realProduct->getQuantity() - $productQuantity;
                    if ($realProductNewQty < 0) {
                        throw $this->createNotFoundException(
                            'Not enough products left. Products remaining:' . $realProduct->getQuantity());
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
                        $em->persist($userInventoryProduct);
                    }
                }
            }
        }

        $this->clearCartAction();
        $em->flush();

        return $this->redirect($this->generateUrl('mix_s_cart_display'));
    }

    public function clearCartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cartProductRepo = $em->getRepository('MixSBundle:CartProduct');
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        if ($cartId) {
            $cartProduct = $cartProductRepo->findBy(['cart' => $cartId]);
            foreach ($cartProduct as $productToRemove) {
                $em->remove($productToRemove);
            }
            $em->flush();
        } else {
            throw $this->createNotFoundException();
        }
        return $this->redirect($this->generateUrl('mix_s_cart_display'));
    }

    public function removeProductFromCartAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $cartProductRepo = $em->getRepository('MixSBundle:CartProduct');
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        if ($cartId) {
            $cartProduct = $cartProductRepo->findOneBy(['cart' => $cartId, 'id' => $productId]);
            $em->remove($cartProduct);
            $em->flush();
        } else {
            throw $this->createNotFoundException();
        }
        return $this->redirect($this->generateUrl('mix_s_cart_display'));
    }
}
