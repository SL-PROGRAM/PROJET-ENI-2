<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ParticipantController, Gestion des utilisateurs
 * @package App\Controller
 */
class ParticipantController extends AbstractController
{
    private $passwordEncoder;
    public  function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this -> passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ParticipantRepository $participantRepository
     * @return Response
     * @Route("/admin/participant/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @Route("/admin/participant/new", name="participant_new", methods={"GET","POST"})
     */
    public function new(Request $request, MailerInterface $mailer): Response
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
                $imageFile->move($this->getParameter('upload_dir'),$newFileName);
                $participant->setImageUrl($newFileName);
            }
            $entityManager->persist($participant);
            $entityManager->flush();
            $this->sendNewUserEmail($mailer,$participant);

            return $this->redirectToRoute('participant_index');
        }
        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Intégration de fichier csv pour l'ajout des users
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/importCsvFile", name="app_importCsvFile", methods={"GET","POST","DELETE"})
     */
    public function csv()
    {
        if($_FILES){
            if($_FILES['csv']['error'] == 0) {
                $csv = array();

                $csvFilename = $_FILES['csv']['name'];
                //Récupère les 3 derniers caractères du nom du fichier et vérifie que ça soit bien un fichier CSV
                $csvExtension = substr($csvFilename, -3);
                if($csvExtension !== 'csv'){
                    $this->addFlash('danger','Extension de fichier non prise en charge');
                    return $this->redirectToRoute('participant_index');
                }
                $csvFilename = $_FILES['csv']['tmp_name'];
                if(($handle = fopen($csvFilename, 'r')) !== FALSE) {
                    //Définit une limite de temps pour lire le csv et effectuer les actions
                    set_time_limit(0);

                    //Initialise les lignes à 0;
                    $row = 0;

                    //Lit toutes les lignes du csv et rempli le tableau 2D $csv
                    while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        $csv[$row]['users'] = $data[0];
                        $row++;
                    }

                    //Vérification et insertion des données en BDD
                    for($i = 1; $i < $row; $i++){
                        $entry = $csv[$i]['users'];
                        $this->insertNewUser($entry);
                    }
                    fclose($handle);
                }
                //Affiche un message flash pour informer de l'état de la requête
                $this->addFlash('success', 'Intégration du fichier CSV réussie');
                return $this->redirectToRoute('participant_index');
            }
        }
        return $this->redirectToRoute('participant_index');
    }

    /**
     * @param $raw
     */
    private function insertNewUser($raw){
        $rawParticipant = explode(",", $raw);

        $this->createNewParticipant($rawParticipant);
    }

    /**
     * @param array $rawParticipant
     */
    private function createNewParticipant(array $rawParticipant){
        $em = $this->getDoctrine()->getManager();
        $campus = $this->getDoctrine()
            ->getRepository(Campus::class)
            ->find($rawParticipant[1]);
        if (!$campus){
            throw $this->createNotFoundException(
                'Pas de campus avec cet ID : '.$rawParticipant[1]
            );
        }

        $participant = $this->getDoctrine()
            ->getRepository(Participant::class)
            ->findOneBy(['email' => $rawParticipant[2]]);
        //Si il existe déjà un participant dans la base, continuer à lire le fichier
        if($participant){
            return;
        }

        $participant = new Participant();
        $participant->setCampus($campus);
        $participant->setEmail($rawParticipant[2]);
        if($rawParticipant[3] === ['"ROLE_ADMIN"'])
            $participant->setRoles(['ROLE_ADMIN']);
        else
            $participant->setRoles(['ROLE_USER']);
        $passwordEncoded = $this->passwordEncoder->encodePassword($participant, $rawParticipant[4]);
        $participant->setPassword($passwordEncoded);
        $participant->setNom($rawParticipant[5]);
        $participant->setPrenom($rawParticipant[6]);
        $participant->setTelephone($rawParticipant[7]);
        $participant->setToken(substr(str_replace('/', '',$passwordEncoded),50));
        $participant->setActif(true);
        $participant->setPseudo($rawParticipant[8]);
        $participant->setImageUrl($rawParticipant[11]);
        $em->persist($participant);
        $em->flush();
    }

    /**
     * Simple new User Email sending function
     * @param Participant $participant
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function sendNewUserEmail(Mailer $mailer, Participant $participant)
    {
        $email = (new TemplatedEmail())
            ->from('contact@sortir.com')
            ->to($participant->getEmail())
            ->subject('Votre compte sur Sortir.com')
            ->htmlTemplate('password/mailNouvelUtilisateur.html.twig')
            ->context([
                'token'=>$participant->getToken()
            ]);
        $mailer->send($email);
    }

    /**
     * @param Participant $participant
     * @return Response
     * @Route("participant/{id}/show", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @param Request $request
     * @param Participant $participant
     * @return Response
     * @Route("participant/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant): Response
    {
        $ok = false;
        foreach ( $this->getUser()->getRoles() as $role){
            if ($role == "ROLE_ADMIN"){
                $ok = true;
            }
        }
        if($participant->getEmail() == $this->getUser()->getUsername() || $ok) {
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //Récupérer l'image depuis le formulaire
                $imageFile = $participant->getImageFile();
                if ($imageFile) {
                    $safeFileName = uniqid();
                    $newFileName = $safeFileName . "." . $imageFile->guessExtension();
                    $imageFile->move($this->getParameter('upload_dir'), $newFileName);
                    $participant->setImageUrl($newFileName);
                }
                $this->getDoctrine()->getManager()->flush();
                if($ok) {
                    return $this->redirectToRoute('participant_index');
                }else{
                    return $this->redirectToRoute('accueil');
                }
            }
        }else{
            return $this->redirectToRoute("accueil");
        }
        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Participant $participant
     * @return Response
     * @Route("/admin/participant/{id}", name="participant_delete", methods={"DELETE"})
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
