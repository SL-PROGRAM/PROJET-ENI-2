<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
     * @Route("/sendPasswordEmail", name="sendPasswordEmail", methods={"POST"})
     */
    public function sendPasswordEmail(MailerInterface $mailer, Request $request)
    {
        $adresse = $request->request->get('_email');
        $email = (new Email())
            ->from('contact.sortir.eni@gmail.com')
            ->to($adresse)
            ->subject('Votre réinitialisation de mot de passe')
            ->text('Texte de réinitialisation de mot de passe')
            ->html('<p>du HTML si on veut</p>');
        $mailer->send($email);

        echo 'email sent';
        die();
    }
}
