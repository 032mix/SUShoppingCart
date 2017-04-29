<?php

namespace Mixailoff\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('MixSBundle:User')->findAll();
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 8)
        );
        return $this->render('MixSBundle:Admin/user:index.html.twig', array(
            'users' => $paginatedQuery
        ));
    }

    public function banUserAction(Request $request)
    {
        $userId = $request->get('user');
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getDoctrine()->getRepository('MixSBundle:User')
            ->findOneBy(['id' => $userId]);
        $user->setEnabled(0);

        $userManager->updateUser($user);

        return $this->redirectToRoute('mix_s_admin_users_showall');
    }

    public function roleChangeAction(Request $request)
    {
        $userId = $request->get('user');
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getDoctrine()->getRepository('MixSBundle:User')
            ->findOneBy(['id' => $userId]);
        $userRole = $user->getRoles();

        if ($userRole[0] == 'ROLE_USER') {
            $user->addRole('ROLE_EDITOR');
        } elseif ($userRole[0] == 'ROLE_EDITOR') {
            $user->removeRole('ROLE_EDITOR');
        } else {
            throw new Exception('User role cannot be changed');
        }

        $userManager->updateUser($user);

        return $this->redirectToRoute('mix_s_admin_users_showall');
    }

    public function setBalanceAction(Request $request)
    {
        $userId = $request->get('user');
        $newBalance = $request->get('newBalance');
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getDoctrine()->getRepository('MixSBundle:User')
            ->findOneBy(['id' => $userId]);
        $user->setCurrentBalance($newBalance);

        $userManager->updateUser($user);

        return $this->redirectToRoute('mix_s_admin_users_showall');
    }

    public function showUserInventoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = $request->get('user');
        $user = $em->getRepository('MixSBundle:User')->findOneBy(['id' => $userId]);
        $inventory = $em->getRepository('MixSBundle:UserInventory')->findOneBy(['user' => $user]);
        $items = $em
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findBy(['inventory' => $inventory]);
        $paginator = $this->get('knp_paginator');
        $paginatedQuery = $paginator->paginate(
            $items,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('MixSBundle:Admin/user:userinventory.html.twig', array(
            'items' => $paginatedQuery,
            'user' => $user
        ));
    }

    public function removeProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productId = $request->get('productId');
        $userId = $request->get('user');
        $user = $em->getRepository('MixSBundle:User')->findOneBy(['id' => $userId]);
        $inventory = $em->getRepository('MixSBundle:UserInventory')->findOneBy(['user' => $user]);
        $product = $em->getRepository('MixSBundle:Product')->findOneBy(['id' => $productId]);
        $inventoryProduct = $em
            ->getRepository('MixSBundle:UserInventoryProduct')
            ->findOneBy(['inventory' => $inventory, 'product' => $product]);

        $em->remove($inventoryProduct);
        $em->flush();

        return $this->redirect($this->generateUrl('mix_s_admin_users_showall'));
    }
}
