<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getAllProducts($page);

        $allproducts = $products->count();
        $pages = range(1, ceil($allproducts / 9));
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();
        return $this->render('MixSBundle:Default:index.html.twig', array('products' => $products,
            'pages' => $pages, 'productCategories' => $productCategories));
    }

    public function displayProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em
            ->getRepository('MixSBundle:Product')
            ->find($id);
        return $this->render('MixSBundle:Default:displayproduct.html.twig', array('product' => $product));
    }

    public function displayCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();
        return $this->render('MixSBundle:Default:displayproductcategories.html.twig',
            array('productCategories' => $productCategories));
    }

    public function displayCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->findBy(
                array('productcategory' => $id));
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();
        return $this->render('MixSBundle:Default:displayproductcategory.html.twig',
            array('products' => $products, 'productCategories' => $productCategories));
    }

    public function welcomeAction()
    {
        return $this->render('MixSBundle:Default:welcomepage.html.twig');
    }
}
