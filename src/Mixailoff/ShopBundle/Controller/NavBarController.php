<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NavBarController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();

        return $this->render('navbar.html.twig', array('categories' => $productCategories));
    }
}
