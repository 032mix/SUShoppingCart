<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Mixailoff\ShopBundle\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductCategoryController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productCategories = $em->getRepository('MixSBundle:ProductCategory')->findAll();
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $productCategories,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 8)
        );
        return $this->render('MixSBundle:Admin/productcategory:index.html.twig', array(
            'productCategories' => $paginatedQuery,
        ));
    }

    public function newAction(Request $request)
    {
        $productCategory = new Productcategory();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ProductCategoryType', $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCategory);
            $em->flush();

            return $this->redirectToRoute('edit_productcategory_show', array('id' => $productCategory->getId()));
        }

        return $this->render('MixSBundle:Admin/productcategory:new.html.twig', array(
            'productCategory' => $productCategory,
            'form' => $form->createView(),
        ));
    }

    public function showAction(ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);

        return $this->render('MixSBundle:Admin/productcategory:show.html.twig', array(
            'productCategory' => $productCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editAction(Request $request, ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);
        $editForm = $this->createForm('Mixailoff\ShopBundle\Form\ProductCategoryType', $productCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('edit_productcategory_edit', array('id' => $productCategory->getId()));
        }

        return $this->render('MixSBundle:Admin/productcategory:edit.html.twig', array(
            'productCategory' => $productCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function deleteAction(Request $request, ProductCategory $productCategory)
    {
        $form = $this->createDeleteForm($productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productCategory);
            $em->flush();
        }

        return $this->redirectToRoute('edit_productcategory_index');
    }

    /**
     * @param ProductCategory $productCategory The productCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductCategory $productCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edit_productcategory_delete', array('id' => $productCategory->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
