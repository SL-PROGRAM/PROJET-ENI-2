<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Etat;
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
        $faker = Factory::create();
        $this->loadFixtureParticipant($manager, $faker);
        $this->loadFixtureEtat($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadFixtureEtat(ObjectManager $manager): void
    {
        //Création des états
        $etat = new Etat();
        $etat->setLibelle("Créée");
        $etat1 = new Etat();
        $etat1->setLibelle("Ouverte");
        $etat2 = new Etat();
        $etat2->setLibelle("Clôturée");
        $etat3 = new Etat();
        $etat3->setLibelle("Activité en cours");
        $etat4 = new Etat();
        $etat4->setLibelle("Passée");
        $etat5 = new Etat();
        $etat5->setLibelle("Annulée");
        $manager->persist($etat);
        $manager->persist($etat1);
        $manager->persist($etat2);
        $manager->persist($etat3);
        $manager->persist($etat4);
        $manager->persist($etat5);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param \Faker\Generator $faker
     */
    public function loadFixtureParticipant(ObjectManager $manager, \Faker\Generator $faker): void
    {
        //Creation d'un admin
        $participant = new Participant();
        $participant->setNom('admin');
        $participant->setPrenom('admin');
        $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
        $participant->setEmail('admin@admin.admin');
        $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));
        $participant->setRoles(['ROLE_ADMIN']);
        $manager->persist($participant);


        //creation user
        for ($i = 0; $i < 100; $i++) {
            $participant = new Participant();
            $participant->setNom($faker->name);
            $participant->setPrenom($faker->name);
            $participant->setTelephone($faker->optional($weight = 0.7)->e164PhoneNumber);
            $participant->setEmail($faker->email);
            $participant->setRoles(['ROLE_USER']);
            $participant->setPassword($this->passwordEncoder->encodePassword($participant, '123456'));

            $manager->persist($participant);
        }

        $manager->flush();
    }
}
