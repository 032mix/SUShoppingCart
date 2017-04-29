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
        $inventory = $em->getRepository('MixSBundle:UserInventory')->findOneBy(['user' => $user]);
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
        $user = $this->getUser();
        $productId = $request->get('productId');
        $quantitySelected = $request->get('quantityToSell');

        $inventoryService = $this->get('app.inventory.service');
        $inventoryService->sellBackProduct($user, $productId, $quantitySelected);
        $this->get('session')->getFlashBag()->add('success',
            "Product/'s/ successfully sold back!");

        return $this->redirect($this->generateUrl('mix_s_inventory_display'));
    }
}
