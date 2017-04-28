<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Mixailoff\ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productsQuery = $em->getRepository('MixSBundle:Product')->findAll();
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 8)
        );
        $promoServ = $this->get('app.promotion.service');
        return $this->render('MixSBundle:Admin/product:index.html.twig', array(
            'products' => $paginatedQuery,
            'promoServ' => $promoServ
        ));
    }

    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file $file */
            $file = $product->getImageForm();
            $filename = md5($product->getTitle() . '' . $product
                    ->getCreatedAt()->format('Y-m-d H:i:s') . $file->getFileInfo()->getExtension());
            $file->move($this->get('kernel')->getRootDir() . '/../web/images/productimages/', $filename);
            $product->setImage($filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('edit_product_show', array('id' => $product->getId()));
        }

        return $this->render('MixSBundle:Admin/product:new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('MixSBundle:Admin/product:show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('Mixailoff\ShopBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            /** @var UploadedFile $file $file */
            $file = $product->getImageForm();

            if ($file instanceof UploadedFile) {
                $filename = md5($product->getTitle() . '' . $product
                        ->getCreatedAt()->format('Y-m-d H:i:s') . $file->getFileInfo()->getExtension());
                $file->move($this->get('kernel')->getRootDir() . '/../web/images/productimages/', $filename);
                $product->setImage($filename);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('edit_product_edit', array('id' => $product->getId()));
        }

        return $this->render('MixSBundle:Admin/product:edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('edit_product_index');
    }

    /**
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edit_product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
