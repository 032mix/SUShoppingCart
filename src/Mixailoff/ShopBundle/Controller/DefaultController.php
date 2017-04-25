<?php

namespace Mixailoff\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function welcomeAction()
    {
        $user = $this->getUser();
        return $this->render('MixSBundle:Default:welcomepage.html.twig',
            array('user' => $user));
    }
}
