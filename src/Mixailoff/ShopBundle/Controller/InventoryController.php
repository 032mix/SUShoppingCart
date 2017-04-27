<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InventoryController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $inventory = $em->getRepository('MixSBundle:UserInventory')->findBy(['user' => $user]);
        $items = $em
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findBy(['inventory' => $inventory]);
        return $this->render('MixSBundle:Default:showinventory.html.twig', array(
            'items' => $items,
            'user' => $user
        ));
    }
}
