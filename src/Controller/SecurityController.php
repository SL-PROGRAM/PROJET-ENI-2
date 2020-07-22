<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController, Gestion des connexions
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * Method to login with user credentials (email+password)
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response Return an error or login the user
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //Si l'utilisateur est connecté et essaye d'accéder à la page login, le redirige vers l'accueil
         if ($this->getUser()) {
             return $this->redirectToRoute('accueil');
         }
        //Sinon afficher la page login
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(){}
}
