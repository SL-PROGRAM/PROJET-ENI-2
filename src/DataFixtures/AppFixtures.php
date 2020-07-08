<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\SortieParticipant;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        //appel à la bibliotheque faker
        $faker = Factory::create('fr_FR');
        $villes = $this->loadFixtureVilles($faker, $manager);
        $campuses = $this->loadFixtureCapmpus($faker, $manager);
        $etats = $this->loadFixtureEtat($manager);
        $lieux = $this->loadFixtureLieux($faker, $villes, $manager);
        $participants = $this->loadFixtureParticipant($manager, $faker, $campuses);
        $sorties = $this->loadFixtudeSortie($faker, $etats, $participants, $campuses, $lieux, $manager);
        $sortieParticipants = $this->loadFixtureSortieParticipant($participants, $sorties, $manager);
    }

    /**
     * @param ObjectManager $manager
     * @return array
     */
    public function loadFixtureEtat(ObjectManager $manager): array
    {
        $etats = [];
        //Création des états
        $etat = new Etat();
        $etat->setLibelle("Créée");
        $etats[0] = $etat;
        $manager->persist($etat);

        $etat1 = new Etat();
        $etat1->setLibelle("Ouverte");
        $etats[1] = $etat1;
        $manager->persist($etat1);

        $etat2 = new Etat();
        $etat2->setLibelle("Clôturée");
        $etats[2] = $etat2;
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("Activité en cours");
        $etats[3] = $etat3;
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("Passée");
        $etats[4] = $etat4;
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("Annulée");
        $etats[5] = $etat5;
        $manager->persist($etat5);

        $manager->flush();

        return $etats;
    }

    /**
     * @param ObjectManager $manager
     * @param \Faker\Generator $faker
     * @param array $campuses
     * @return array
     * @throws \Exception
     */
    public function loadFixtureParticipant(ObjectManager $manager, \Faker\Generator $faker, array $campuses): array
    {

        //Creation d'un admin pour dev
        $participant = new Participant();
        $participant->setNom('admin');
        $participant->setPrenom('admin');
        $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
        $participant->setEmail('admin@admin.admin');
        $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
        $participant->setRoles(['ROLE_ADMIN','ROLE_USER']);
        $ranomCampus = random_int(0, count($campuses)-1);
        $participant->setCampus($campuses[$ranomCampus]);

        $manager->persist($participant);
        $participants = [];
        //creation utilisateur pour connection et dev
        $participant = new Participant();
        $participant->setNom('user');
        $participant->setPrenom('user');
        $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
        $participant->setEmail('user@user.user');
        $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
        $participant->setRoles(['ROLE_USER']);
        $participants[0] = $participant;
        $ranomCampus = random_int(0, count($campuses)-1);
        $participant->setCampus($campuses[$ranomCampus]);

        $manager->persist($participant);

        //creation user
        for ($i = 1; $i < 20; $i++) {
            $participant = new Participant();
            $participant->setNom($faker->firstName);
            $participant->setPrenom($faker->lastName);
            $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
            $participant->setEmail($participant->getNom().".".$participant->getPrenom()."@eni-campus.fr");
            $participant->setRoles(['ROLE_USER']);
            $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
            $ranomCampus = random_int(0, count($campuses)-1);
            $participant->setCampus($campuses[$ranomCampus]);
            $participants[$i] = $participant;

            $manager->persist($participant);
        }

        $manager->flush();

        return $participants;
    }

    /**
     * @param \Faker\Generator $faker
     * @param ObjectManager $manager
     * @return array
     */
    public function loadFixtureCapmpus(\Faker\Generator $faker, ObjectManager $manager): array
    {
        $campuses = [];
        for ($i = 0; $i < 10; $i++) {
            $campus = new Campus();
            $campus->setNom("campus ".$faker->name);
            $manager->persist($campus);
            $campuses[$i] = $campus;

            $manager->flush();
        }
        $manager->flush();
        return $campuses;
    }

    /**
     * @param \Faker\Generator $faker
     * @param ObjectManager $manager
     * @return array
     */
    public function loadFixtureVilles(\Faker\Generator $faker, ObjectManager $manager): array
    {
        $villes = [];
        for ($i = 0; $i < 10; $i++) {
            $ville = new Ville();
            $ville->setNomVille($faker->city);
            $ville->setCodePostal($faker->postcode);
            $villes[$i] = $ville;
            $manager->persist($ville);
        }
        $manager->flush();
        return $villes;
    }

    /**
     * @param \Faker\Generator $faker
     * @param array $villes
     * @param ObjectManager $manager
     * @return array
     * @throws \Exception
     */
    public function loadFixtureLieux(\Faker\Generator $faker, array $villes, ObjectManager $manager): array
    {
        $lieux = [];
        for ($i = 0; $i < 10; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->name);
            $lieu->setRue($faker->numberBetween(1, 200) . " " . $faker->streetName);
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);

            $randomVille = random_int(0, count($villes)-1);
            $lieu->setVille($villes[$randomVille]);
            $lieux[$i] = $lieu;
            $manager->persist($lieu);
        }
        $manager->flush();

        return $lieux;
    }

    /**
     * @param \Faker\Generator $faker
     * @param array $etats
     * @param array $participants
     * @param array $campuses
     * @param array $lieux
     * @param ObjectManager $manager
     * @return array
     * @throws \Exception
     */
    public function loadFixtudeSortie(\Faker\Generator $faker, array $etats, array $participants, array $campuses, array $lieux, ObjectManager $manager): array
    {
        $sorties = [];
        for ($i = 0; $i < 20; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($faker->name);
            $sortie->setDateHeureDebut($faker->dateTimeBetween($startDate = '+5 days', $interval = '+ 10 days', $timezone = 'Europe/Paris'));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween($startDate = 'now', $interval = '+ 3 days', $timezone = 'Europe/Paris'));
            $sortie->setNbInscriptionMax($faker->numberBetween(4, 8));
            $sortie->setDuree(24 * 60 * 60);
            $sortie->setInfosSortie($faker->text($maxNbChars = 200));

            //choisir un etat au hasard
            $ramdomEtat = random_int(0, count($etats)-1);
            //lier l'etat a la sortie
            $sortie->setEtat($etats[$ramdomEtat]);


            if($i < 2){
                $sortie->setOrganisateur($participants[0]);
            }
            else{
                $ramdomParticipant = random_int(0, count($participants)-1);
                $sortie->setOrganisateur($participants[$ramdomParticipant]);
            }

            $randomCampus = random_int(0, count($campuses)-1);
            $sortie->setCampus($campuses[$randomCampus]);

            $randomLieu = random_int(0, count($lieux)-1);
            $sortie->setLieu($lieux[$randomLieu]);

            $sorties[$i] = $sortie;
            $manager->persist($sortie);
        }

        $manager->flush();

        return $sorties;
    }

    /**
     * @param array $participants
     * @param array $sorties
     * @param ObjectManager $manager
     * @return array
     * @throws \Exception
     */
    public function loadFixtureSortieParticipant(array $participants, array $sorties, ObjectManager $manager): array
    {
        $sortiParcicipants = [];
        for ($i = 0; $i < 10; $i++){
            $sortiParcicipant = new SortieParticipant();


            if ($i < 5){
                $sortiParcicipant->setParticipant($participants[0]);
            }else{
                $ranomParticipant = random_int(0, count($participants) - 1);
                $sortiParcicipant->setParticipant($participants[$ranomParticipant]);
            }

            $randomSortie = random_int(0, count($sorties) - 1);
            $sortiParcicipant->setSortie($sorties[$randomSortie]);
            $sortiParcicipants[$i] = $sortiParcicipant;
            $manager->persist($sortiParcicipant);
        }
            $manager->flush();
            return $sortiParcicipants;
    }
}
