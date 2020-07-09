<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    private $passwordEncoder;
    public  function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this -> passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="participant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $participant = new Participant();
        $participant -> setPassword('123456');
        $participant -> setToken('azerty');
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $passwordEncoded = $this->passwordEncoder->encodePassword($participant, '123456');
            $participant->setPassword($passwordEncoded);
            $participant->setToken(substr(str_replace('/', '',$passwordEncoded),50));
            $imageFile = $participant->getImageFile();

            if($imageFile){
                $safeFileName = uniqid();
                $newFileName = $safeFileName.".".$imageFile->guessExtension();
                $participant->setImageUrl($newFileName);

                try{
                    $imageFile->move($this->getParameter('upload_dir'),
                        $newFileName);
                } catch (FileException $e){

                }
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant): Response
    {

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupérer l'image depuis le formulaire
            $imageFile = $participant->getImageFile();
            if($imageFile){
                $safeFileName = uniqid();
                $newFileName = $safeFileName.".".$imageFile->guessExtension();
                $participant->setImageUrl($newFileName);

                try{
                    $imageFile->move($this->getParameter('upload_dir'),
                        $newFileName);
                } catch (FileException $e){

                }
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="participant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index');
    }
}
