<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UserAdhesion extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('csvFile', FileType::class, [
                'label' => 'Choisir le fichier',
                'mapped' => false, // 'mapped' => false means that this field is not associated with any entity property
                'required' => true,
            ])
            ->add('submit', SubmitType::class, array('attr' => array( 'class' => 'btn btn-primary' ), 'label' => 'Importer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {}
}
