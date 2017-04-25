<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller
{
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getAllProducts($page);
        $allproducts = count($products);
        $pages = range(1, ceil($allproducts / 9));
        return $this->render('MixSBundle:Default:index.html.twig', array('products' => $products,
            'pages' => $pages));
    }

    public function displayProductsByCategoryAction($categoryId, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getProductsByCategory($categoryId, $page);
        $productsPagination = count($products[0]);
        $pages = range(1, ceil($productsPagination / 9));
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $products[0],
                'pages' => $pages,
                'productInfo' => $products[1]));
    }

    public function displayProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em
            ->getRepository('MixSBundle:Product')
            ->find($id);
        return $this->render('MixSBundle:Default:displayproduct.html.twig', array('product' => $product));
    }
}
