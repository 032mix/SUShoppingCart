<?php

namespace Mixailoff\ShopBundle\Controller;

use Mixailoff\ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('MixSBundle:Product')->findAll();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();

        return $this->render('product/index.html.twig', array(
            'products' => $products,
            'productCategories' => $productCategories
        ));
    }

    /**
     * Creates a new product entity.
     *
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ProductType', $product);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush($product);

            return $this->redirectToRoute('edit_product_show', array('id' => $product->getId()));
        }

        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
            'productCategories' => $productCategories
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
            'productCategories' => $productCategories
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('Mixailoff\ShopBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $productCategories = $em
            ->getRepository('MixSBundle:ProductCategory')
            ->getAllProductCategories();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('edit_product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'productCategories' => $productCategories
        ));
    }

    /**
     * Deletes a product entity.
     *
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush($product);
        }

        return $this->redirectToRoute('edit_product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
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
