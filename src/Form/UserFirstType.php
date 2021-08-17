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

class UserFirstType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, array('label' => 'Nom', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('prenom', null, array('label' => 'Prénom', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class,
                'options' => array('translation_domain' => 'FOSUserBundle', 'attr' => array( 'class' => 'form-control' )),
                'first_options' => array('label' => 'Email'),
                'second_options' => array('label' => 'Confirmer l\'email'),
                'invalid_message' => "les emails ne sont pas identiques",
            ))
            ->add('motDePasse', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle', 'attr' => array( 'class' => 'form-control' )),
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe :'),
                'invalid_message' => 'Les mots de passe ne sont pas identique',
            ))
            ->add('submit', SubmitType::class, array('attr' => array( 'class' => 'btn btn-primary' ), 'label' => 'Enregistrer'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
