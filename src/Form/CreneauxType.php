<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CreneauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DatePickerType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'data' => new \DateTime(),
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                ),
            ])
            ->add('endDate', DatePickerType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'data' => (new \DateTime())->modify("+4 week"),
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                ),
                'help' => " "
            ])
            ->add('pattern', ChoiceType::class, [
                'label' => 'Type de modèle',
                'choices'  => [
                    'Modèle principal (A)' => '1',
                    'Modèle vacances (B)' => '2',
                    '1 semaine sur 4 (C)' => '3',
                ],
                'data' => '1',
                'help' => " "
            ])
            ->add('submit', SubmitType::class, array('attr' => array( 'class' => 'btn btn-primary' ), 'label' => 'Générer'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}