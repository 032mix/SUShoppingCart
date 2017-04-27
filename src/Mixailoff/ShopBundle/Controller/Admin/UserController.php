<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('MixSBundle:User')->findAll();

        return $this->render('MixSBundle:Admin/user:index.html.twig', array(
            'users' => $users
        ));
    }
}
