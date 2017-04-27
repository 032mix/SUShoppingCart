<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Mixailoff\ShopBundle\Entity\Promotion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PromotionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $promotions = $em->getRepository('MixSBundle:Promotion')->findAll();

        return $this->render('MixSBundle:Admin/promotion:index.html.twig', array(
            'promotions' => $promotions,
        ));
    }

    public function newAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\PromotionType', $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            return $this->redirectToRoute('admin_promotion_show', array('id' => $promotion->getId()));
        }

        return $this->render('MixSBundle:Admin/promotion:new.html.twig', array(
            'promotion' => $promotion,
            'form' => $form->createView(),
        ));
    }

    public function showAction(Promotion $promotion)
    {
        $deleteForm = $this->createDeleteForm($promotion);

        return $this->render('MixSBundle:Admin/promotion:show.html.twig', array(
            'promotion' => $promotion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editAction(Request $request, Promotion $promotion)
    {
        $deleteForm = $this->createDeleteForm($promotion);
        $editForm = $this->createForm('Mixailoff\ShopBundle\Form\PromotionType', $promotion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_promotion_edit', array('id' => $promotion->getId()));
        }

        return $this->render('MixSBundle:Admin/promotion:edit.html.twig', array(
            'promotion' => $promotion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function deleteAction(Request $request, Promotion $promotion)
    {
        $form = $this->createDeleteForm($promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($promotion);
            $em->flush();
        }

        return $this->redirectToRoute('admin_promotion_index');
    }

    /**
     * Creates a form to delete a promotion entity.
     *
     * @param Promotion $promotion The promotion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Promotion $promotion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_promotion_delete', array('id' => $promotion->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
