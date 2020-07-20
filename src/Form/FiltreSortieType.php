<?php

namespace App\Form;

use App\Data\Search;
use App\Entity\Campus;
use App\Entity\Sortie;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FiltreSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'required' => false,
            ])
            ->add('dateHeureDebutMin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateHeureDebutMax', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label'    => 'Sorties dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je suis inscrit-e',
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit-e',
                'required' => false,
            ])
            ->add('passees', CheckboxType::class, [
                'label'    => 'Sorties passées',
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class'  => Campus::class,
                'placeholder' => 'Choisissez un campus',
                'choice_label'=>'nom',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}