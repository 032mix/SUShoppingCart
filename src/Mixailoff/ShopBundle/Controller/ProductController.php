<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productsQuery = $em
            ->getRepository('MixSBundle:Product')
            ->getAllProducts();
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $paginatedQuery,
                'promoServ' => $promoServ
            ));
    }

    public function displayProductsByCategoryAction($catId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productsQuery = $em
            ->getRepository('MixSBundle:Product')
            ->getProductsByCategory($catId);
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $paginatedQuery,
                'promoServ' => $promoServ
            ));
    }

    public function displayProductsByFilterAction(Request $request)
    {
        $search = $request->query->getAlnum('search');
        $em = $this->getDoctrine()->getManager();
        $productsQuery = $em
            ->getRepository('MixSBundle:Product')
            ->getProductsByFilter($search);
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Default:index.html.twig',
            array('products' => $paginatedQuery,
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
