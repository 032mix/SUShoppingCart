<?php

namespace Mixailoff\ShopBundle\Controller;

use Mixailoff\ShopBundle\Entity\Product;
use Mixailoff\ShopBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class ReviewController extends Controller
{
    public function newReviewFormAction(Product $product)
    {
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ReviewType');

        return $this->render('MixSBundle:Default:newreview.html.twig', array(
            'form' => $form->createView(),
            'product' => $product
        ));
    }

    public function newReviewProcessAction(Product $product, Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new Exception('You are not logged in.');
        }
        $review = new Review();
        $form = $this->createForm('Mixailoff\ShopBundle\Form\ReviewType', $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $review->setUser($user);
            $review->setProduct($product);
            $review->setCreatedAt(new \DateTime());

            $em->persist($review);
            $em->flush();
        }

        $productId = $product->getId();
        return $this->redirectToRoute('mix_s_product_display', array('id' => $productId));
    }

    public function deleteReviewProcessAction(Product $product, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reviewId = $request->get('deleteReview');
        $review = $em->getRepository('MixSBundle:Review')->findOneBy(['id' => $reviewId]);

        $em->remove($review);
        $em->flush();

        $productId = $product->getId();
        return $this->redirectToRoute('mix_s_product_display', array('id' => $productId));
    }
}
