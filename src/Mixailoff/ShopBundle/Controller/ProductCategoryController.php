<?php

namespace Mixailoff\ShopBundle\Controller;

use Mixailoff\ShopBundle\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Productcategory controller.
 *
 */
class ProductCategoryController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productCategories = $em->getRepository('MixSBundle:ProductCategory')->findAll();

        return $this->render('productcategory/index.html.twig', array(
            'productCategories' => $productCategories,
        ));
    }

/*    public function navbarListCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();
        return $this->render('MixSBundle:Default:navbarListCategories.html.twig',
            array('productCategories' => $productCategories));
    }*/

    public function newAction(Request $request)
    {
        $productCategory = new Productcategory();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ProductCategoryType', $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCategory);
            $em->flush($productCategory);

            return $this->redirectToRoute('edit_productcategory_show', array('id' => $productCategory->getId()));
        }

        return $this->render('productcategory/new.html.twig', array(
            'productCategory' => $productCategory,
            'form' => $form->createView(),
        ));
    }

    public function showAction(ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);

        return $this->render('productcategory/show.html.twig', array(
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

        return $this->render('productcategory/edit.html.twig', array(
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
            $em->flush($productCategory);
        }

        return $this->redirectToRoute('edit_productcategory_index');
    }

    /**
     * Creates a form to delete a productCategory entity.
     *
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
