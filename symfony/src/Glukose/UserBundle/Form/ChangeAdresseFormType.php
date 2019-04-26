<?php

namespace Glukose\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeAdresseFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, array('label' => 'Intitulé de l\'adresse', 'attr' => array('class' => 'form-control')))
            ->add('destinataire', null, array('label' => 'Destinataire (Nom Prénom)', 'required' => true, 'attr' => array('class' => 'form-control', 'maxlength' => '38')))
            ->add('ligne1', null, array('label' => 'Adresse', 'required' => true, 'attr' => array('class' => 'form-control', 'maxlength' => '38')))
            ->add('ligne2', null, array('label' => ' ', 'attr' => array('class' => 'form-control', 'maxlength' => '38')))
            ->add('ligne3', null, array('label' => ' ', 'attr' => array('class' => 'form-control', 'maxlength' => '38')))
            ->add('ville', null, array('label' => 'Ville', 'required' => true, 'attr' => array('class' => 'form-control')))
            ->add('codePostal', null, array('label' => 'Code Postal', 'required' => true, 'attr' => array('class' => 'form-control')))
            ->add('pays', 'country', array('label' => 'Pays', 'attr' => array('class' => 'form-control'), 'preferred_choices' => array('FR')))
            ->add('save', 'submit', array('label' => 'Enregistrer les modifications', 'attr' => array('class' => 'btn btn-primary')));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'empty_data' => null,
            'required' => false,
        );
    }

    public function getName()
    {
        return 'fos_user_adresse';
    }
}
