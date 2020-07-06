<?php

namespace App\DataFixtures;

use App\Entity\Participant;
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
        for ($i = 0;$i<100; $i++){
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
