<?php

namespace App\Controller;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
     * @throws TransportExceptionInterface
     */
    public function sendPasswordEmail(MailerInterface $mailer, Request $request)
    {
        $adresse = $request->request->get('_email');

        $userRepo = $this->getDoctrine()->getRepository(Participant::class);
        $user = $userRepo->findOneBy(['email' => $adresse]);

        if(!$user){
            throw $this->createNotFoundException('Utilisateur introuvable, avez-vous correctement saisi votre email?');
        }
        $hashPwd = substr($user->getPassword(), 30);

        $email = (new TemplatedEmail())
            ->from('contact@sortir.com')
            ->to($adresse)
            ->subject('Votre nouveau mot de passe')
            ->htmlTemplate('password/mail.html.twig')
            ->context([
                'token'=>$user->getToken()
            ]);
        $mailer->send($email);
        $this->addFlash('success', 'Un mail contenant un lien de réinitialisation de mot de passe a été envoyé sur cette adresse mail : '.$adresse.'.');
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/resetPwd/{token}", name="resetPwd")
     * @param Request $request
     * @return mixed
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $userRepo = $this->getDoctrine()->getRepository(Participant::class);
        $user = $userRepo->findOneBy(['token' => $token]);

        $password = $request->request->get('_pwd');
        if($password != null)
        {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);

            $user->setToken(substr(str_replace('/', '',$encodedPassword),50));
            $user->setPassword($encodedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été changé.');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('password/resetPwd.html.twig',[
            'token' =>$token
        ]);
    }
}
