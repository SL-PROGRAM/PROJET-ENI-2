<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'invalid_message' => 'Le nom ne peut pas être vide',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'date_widget' => 'single_text',
                'invalid_message' => 'La date doit etre au minimum à +24h',
            ])
            ->add('duree', IntegerType::class, [
                'invalid_message' => 'La valeur %num% doit etre positive',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
                'invalid_message' => 'La date doit etre au minimum à +2h avant la date de debut',

            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'invalid_message' => 'La valeur %num% doit etre positive'
            ])
            ->add('infosSortie', TextareaType::class, [
            ])
            ->add('ville')

            ->add('lieu', LieuType::class, [
                'by_reference' => true
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => "btn btn-primary"
                ]
            ])
            ->add('Publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => [
                    'class' => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
