<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function displayCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();
        return $this->render('MixSBundle:Default:displayproductcategories.html.twig',
            array('productCategories' => $productCategories));
    }
}
