<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route("/mdp_oublie", name="mdp_oublie")
     */
    public function index()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('accueil');
        }
        return $this->render('password/mdpOublie.html.twig');
    }

    /**
     * @Route("/sendPasswordEmail", name="sendPasswordEmail")
     */
    public function sendPasswordEmail()
    {
        echo 'email sent';
        die();
    }
}
