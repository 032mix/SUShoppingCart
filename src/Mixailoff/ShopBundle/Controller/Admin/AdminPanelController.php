<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminPanelController extends Controller
{
    public function indexAction()
    {
        return $this->render('MixSBundle:Admin:index.html.twig');
    }
}
