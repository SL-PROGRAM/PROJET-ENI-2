<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SortieType
 * @package App\Form
 */
class SortieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'date_widget' => 'single_text',
            ])
            ->add('duree', IntegerType::class            )
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionMax', IntegerType::class)
            ->add('infosSortie', TextareaType::class, [
            ])
            ->add('ville')

            ->add('lieu', LieuType::class, [
                'by_reference' => true
            ])
            ->add('lieu_', EntityType::class, [
                'class' => Lieu::class,
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
