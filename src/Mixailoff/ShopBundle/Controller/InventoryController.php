<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InventoryController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $inventory = $em->getRepository('MixSBundle:UserInventory')->findBy(['user' => $user]);
        $items = $em
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findBy(['inventory' => $inventory]);
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $items,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:showinventory.html.twig', array(
            'items' => $paginatedQuery,
            'user' => $user,
            'promoServ' => $promoServ,
        ));
    }

    public function sellBackAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $productId = $request->get('productId');
        $quantitySelected = $request->get('quantityToSell');

        $product = $this
            ->getDoctrine()
            ->getRepository('MixSBundle:Product')
            ->findOneBy(['id' => $productId]);
        $userInventory = $this
            ->getDoctrine()
            ->getRepository('MixSBundle:UserInventory')
            ->findOneBy(['user' => $user]);
        $inventoryProduct = $this
            ->getDoctrine()
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findOneBy(['inventory' => $userInventory,
                'product' => $product]);
        $inventoryQuantity = $inventoryProduct->getQuantity();

        if (($inventoryQuantity - $quantitySelected) < 0) {
            throw $this->createNotFoundException(
                'Not enough products of that type in inventory.
                You have:' . ' ' . $inventoryQuantity . ' ' . 'left!');
        } else {
            $productPrice = $this->get('app.promotion.service')->calculatePromotedPrice($product);
            $productQtyPrice = $productPrice * $quantitySelected;
            $user->setCurrentBalance($user->getCurrentBalance() + $productQtyPrice);
            $product->setQuantity($product->getQuantity() + $quantitySelected);
            if (($inventoryProduct->getQuantity() - $quantitySelected) == 0) {
                $em->remove($inventoryProduct);
            } else {
                $inventoryProduct->setQuantity($inventoryQuantity - $quantitySelected);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add('success',
                "Product/'s/ successfully sold back!");
        }

        return $this->redirect($this->generateUrl('mix_s_inventory_display'));
    }
}
