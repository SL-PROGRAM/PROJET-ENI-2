<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie :',

            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'date_widget' => 'single_text',
                'label' => 'Date et heure de la sortie :',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :',
                'invalid_message' => 'You entered an invalid value %num% must be positive',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place :',
                'invalid_message' => 'You entered an invalid value %num% must be positive'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et information'
            ])
            ->add('lieu')
            ->add('villes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
