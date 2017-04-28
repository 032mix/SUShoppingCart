<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        $items = $em
            ->getRepository('MixSBundle:CartProduct')
            ->findBy(['cart' => $cartId]);
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $items,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:showcart.html.twig', array(
            'items' => $paginatedQuery,
            'user' => $user,
            'promoServ' => $promoServ,
        ));
    }

    public function addToCartAction($productId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepo = $em->getRepository('MixSBundle:Product');
        if (!$productRepo->find($productId)) {
            throw $this->createNotFoundException('Product not found!');
        }
        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        $cart = $em->getRepository('MixSBundle:Cart')->find($session->get('id_cart', false));
        $cartService = $this->get('app.cart.service');
        if (!$cartId or !$cart) {
            $cart = $cartService->newCart($user);
            $session->set('id_cart', $cart->getId());
        }
        $quantity = $request->get('quantity');
        $cartService->addToCart($productId, $quantity, $cart);
        $cart->setDateUpdated(new \DateTime());

        $em->persist($cart);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Product successfully added to cart!');

        return $this->redirect($this->generateUrl('mix_s_cart_display'));
    }

    public function checkoutAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $session = $this->get('session');
        $cartId = $session->get('id_cart', false);
        if (!$cartId) {
            throw $this->createNotFoundException();
        }
        $cart = $em->getRepository('MixSBundle:Cart')->find($session->get('id_cart', false));
        $cartService = $this->get('app.cart.service');
        $cartService->checkoutCart($user, $cart);
        $this->clearCartAction();

        $em->flush();

        $this->get('session')->getFlashBag()->add('success',
            "Product/'s/ successfully bought!");

        return $this->redirect($this->generateUrl('mix_s_inventory_display'));
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
