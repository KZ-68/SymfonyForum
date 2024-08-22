<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route(
    '/profile', 
    name: 'profile_'
)]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{

    #[Route('/my-profile', name: 'my_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
