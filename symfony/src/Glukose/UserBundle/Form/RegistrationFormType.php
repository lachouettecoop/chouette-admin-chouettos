<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glukose\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct()
    {
        //$this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', 'choice',
                array('choices' => array(
                    'mme' => 'Madame',
                    'mr' => 'Monsieur',
                    'mlle' => 'Mademoiselle'),
                    'label' => 'Civilité',
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add('nom', null, array('label' => 'Nom', 'attr' => array('class' => 'form-control')))
            ->add('prenom', null, array('label' => 'Prénom', 'attr' => array('class' => 'form-control')))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array('class' => 'form-control')))
            ->add('username', 'email', array('label' => 'Confirmer l\'email :', 'translation_domain' => 'FOSUserBundle', 'attr' => array('class' => 'form-control')))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle', 'attr' => array('class' => 'form-control')),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'Confirmer le mot de passe :'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))/*->add('telephone', null, array('label' => 'téléphone', 'attr' => array( 'class' => 'form-control' )))
            ->add('portable', null, array('label' => 'Tél portable', 'attr' => array( 'class' => 'form-control' )))*/
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Glukose\UserBundle\Entity\User',
            'intention' => 'registration',
        ));
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
