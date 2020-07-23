<?php

namespace App\Form;

use App\Entity\Lieu;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LieuType
 * @package App\Form
 */
class LieuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,['label' => 'Enregistrer',
                'attr' => [
                    'class' => "form-control"
                ]
            ])
            ->add('rue',TextType::class,['label' => 'Rue',
                'attr' => [
                    'class' => "form-control"
                ]
            ])
            ->add('latitude',TextType::class,['label' => 'Latitude',
                'attr' => [
                    'class' => "form-control"
                ]
            ])
            ->add('longitude',TextType::class,['label' => 'Longitude',
                'attr' => [
                    'class' => "form-control"
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
            'data_class' => Lieu::class,
        ]);
    }
}
