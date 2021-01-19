<?php

namespace App\Admin;

use ChouetteCoop\PersonnesInteresseesBundle\Entity\Personne;
use Exporter\Source\IteratorCallbackSourceIterator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonneAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_sort_by' => 'nom',
    ];

    protected $maxPerPage = 100;

    protected $perPageOptions = array(10, 20, 50, 100, 500, 1000);

    public function toString($object)
    {
        return $object instanceof Personne
            ? $object->getNomAffichage()
            : 'Personne intéressée';
    }

    public function getExportFields()
    {
        return array('nom', 'prenom', 'email', 'exportDatePremiereReunion');
    }

    public function getDataSourceIterator()
    {
        return new \Sonata\Exporter\Source\IteratorCallbackSourceIterator(parent::getDataSourceIterator(), function($data) {
            $data['nom'] = mb_strtoupper($data['nom']);
            return $data;
        });
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Civilité', array(
                'class' => 'col-md-6',
                'description' => '
                    Cette section contient les informations principales d’une personne.
                    <br>Dans un souci d’homogénéïté, merci de <strong>bien veiller à respecter le format proposé</strong> (majuscules, espaces…).                
                '
            ))
            ->add('nom', null, array(
                'attr' => array('placeholder' => 'Tibou')
            ))
            ->add('prenom', null, array(
                'label' => 'Prénom',
                'attr' => array('placeholder' => 'Jean')
            ))
            ->add('email', null, array(
                'attr' => array('placeholder' => 'j.tibou@example.com'),
                'help' => "Utilisée pour recontacter cette personne et savoir qu'elle est déjà venue à une réunion si elle nous contacte."
            ))
            ->add('datePremiereReunion', DatePickerType::class, array(
                'label' => "Date de participation à sa dernière réunion d'accueil",
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '15/01/' . date('Y')
                )
            ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('full_text', 'doctrine_orm_callback', [
                'label' => 'Recherche rapide',
                'show_filter' => true,
                'callback' => [$this, 'getFullTextFilter'],
                'field_type' => TextType::class
            ])
            ->add('datePremiereReunion', 'doctrine_orm_date_range', [
                'label' => 'Date de première réunion'
            ])
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom',
            ])
            ->add('email')
        ;
    }

    public function getFullTextFilter($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return;
        }

        $words = array_filter(
            array_map('trim', explode(' ', $value['value']))
        );

        foreach ($words as $word) {
            // Use `andWhere` instead of `where` to prevent overriding existing `where` conditions
            $literal = $queryBuilder->expr()->literal('%' . $word . '%');
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like($alias.'.nom', $literal),
                $queryBuilder->expr()->like($alias.'.prenom', $literal),
                $queryBuilder->expr()->like($alias.'.email', $literal)
            ));
        }

        return true;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('prenom', null, array('label' => 'Prénom'))
            ->addIdentifier('email')
            ->add('datePremiereReunion', null, array('label' => 'Date de première réunion'))
        ;
    }

}
