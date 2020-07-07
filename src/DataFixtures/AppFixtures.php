<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\EtatRepository;
use App\Repository\VilleRepository;
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


        $participants = $this->loadFixtureParticipant($manager, $faker);
        $etats = $this->loadFixtureEtat($manager);
        $campuses = $this->loadFixtureCapmpus($faker, $manager, $participants);
        $villes = $this->loadFixtureVilles($faker, $manager);
        $lieux = $this->loadFixtureLieux($faker, $villes, $manager);

        $sortie = new Sortie();
        $sortie->setNom($faker->name);
//        $sortie->setDateHeureDebut($faker->)

    }

    /**
     * @param ObjectManager $manager
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
     */
    public function loadFixtureParticipant(ObjectManager $manager, \Faker\Generator $faker): array
    {

        //Creation d'un admin pour dev
        $participant = new Participant();
        $participant->setNom('admin');
        $participant->setPrenom('admin');
        $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
        $participant->setEmail('admin@admin.admin');
        $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
        $participant->setRoles(['ROLE_ADMIN']);


        $manager->persist($participant);

        //creation utilisateur pour connection et dev
        $participant = new Participant();
        $participant->setNom('user');
        $participant->setPrenom('user');
        $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
        $participant->setEmail('user@user.user');
        $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
        $participant->setRoles(['ROLE_USER']);

        $manager->persist($participant);
        $participants = [];
        //creation user
        for ($i = 0; $i < 20; $i++) {
            $participant = new Participant();
            $participant->setNom($faker->name);
            $participant->setPrenom($faker->name);
            $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
            $participant->setEmail($faker->email);
            $participant->setRoles(['ROLE_USER']);
            $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));

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
    public function loadFixtureCapmpus(\Faker\Generator $faker, ObjectManager $manager, array $participants): array
    {
        $campuses = [];
        for ($i = 0; $i < 10; $i++) {
            $campus = new Campus();
            $campus->setNom($faker->name);
            $manager->persist($campus);
            $campuses[$i] = $campus;
            $randParticipants = (array)array_rand($participants, rand(1, count($participants)));
            foreach ($randParticipants as $key => $value) {
                $campus[$i]->addShop($participants[$key]);
            }


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
            $ville->setCodePostal($faker->countryCode);
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

            // on récupère un nombre aléatoire de ville dans un tableau
            $randomVille = (array)array_rand($villes, rand(1, count($villes)));
            // puis on les ajoute au Customer
            foreach ($randomVille as $key => $value) {
                $lieu[$i]->addShop($villes[$key]);
            }
            $lieux[$i] = $lieu;
            $manager->persist($lieu);
        }
        $manager->flush();

        return $lieux;
    }
}
