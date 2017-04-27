<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getAllProducts($page);
        $allProducts = count($products);
        $pages = range(1, ceil($allProducts / 9));
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $products,
                'pages' => $pages,
                'promoServ' => $promoServ
            ));
    }

    public function displayProductsByCategoryAction($catId, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getProductsByCategory($catId, $page);
        $productsPagination = count($products[0]);
        $pages = range(1, ceil($productsPagination / 9));
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $products[0],
                'pages' => $pages,
                'productInfo' => $products[1],
                'promoServ' => $promoServ
            ));
    }

    public function displayProductsByFilterAction(Request $request, $page = 1)
    {
        $minPrice = $request->get('minPrice');
        $maxPrice = $request->get('maxPrice');
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('MixSBundle:Product')
            ->getProductsByFilter($page, $minPrice, $maxPrice);
        $allProducts = count($products);
        $pages = range(1, ceil($allProducts / 9));
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $products,
                'pages' => $pages,
                'promoServ' => $promoServ
            ));
    }

    public function displayProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em
            ->getRepository('MixSBundle:Product')
            ->find($id);
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:displayproduct.html.twig',
            array('product' => $product,
                'promoServ' => $promoServ
            ));
    }
}
