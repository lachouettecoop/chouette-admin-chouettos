<?php

namespace Glukose\UserBundle\Admin;

use Exporter\Source\IteratorCallbackSourceIterator;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\EqualType;

class UserAdmin extends Admin
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";

    private $originalUserData;
    private $ldapService;

    protected $datagridValues = [
        '_sort_by' => 'nom',
    ];

    protected $maxPerPage = 100;

    protected $perPageOptions = array(10, 20, 50, 100, 500, 1000);

    public function setLdapService($ldapService)
    {
        $this->ldapService = $ldapService;
    }

    public function getExportFields()
    {
        return array('id', 'civilite', 'nom', 'prenom', 'codebarre', 'email', 'exportDateNaissance', 'telephone', 'enabled', 'domaineCompetence', 'exportAdresse1', 'exportAdresse2', 'exportAdresse4', 'exportAdresse5', 'exportAdresse6', 'adhesions', 'exportdAhesionAnnee', 'exportAdhesionDate', 'exportAdhesionMontant', 'exportSouscriptionDate');
    }

    public function getDataSourceIterator()
    {
        return new IteratorCallbackSourceIterator(parent::getDataSourceIterator(), function($data) {
            $data['nom'] = mb_strtoupper($data['nom']);
            return $data;
        });
    }

    public function getBatchActions()
    {
        $actions = [];
        if ($this->hasRoute('edit') && $this->isGranted('EDIT')) {
            $actions['imprimeCarte'] = array(
                'label' => 'Carte imprimée',
                'ask_confirmation' => true
            );

        }
        return $actions;
    }

    public function preUpdate($user)
    {
        $this->syncRelations($user);
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $this->originalUserData = $em->getUnitOfWork()->getOriginalEntityData($user);
    }

    public function syncRelations($user)
    {
        if ($user->getAdhesions() != null) {
            foreach ($user->getAdhesions() as $adhesion) {
                $adhesion->setUser($user);
            }
        }
        if ($user->getPaiements() != null) {
            foreach ($user->getPaiements() as $paiement) {
                $paiement->setUser($user);
            }
        }
    }

    public function postUpdate($user)
    {
        if ($this->originalUserData['enabled'] == $user->isEnabled() && $user->isEnabled()) {
            $this->ldapService->updateUserOnLDAP($user, $this->originalUserData);
        } elseif (!$user->isEnabled() && $this->originalUserData['enabled'] == true) {
            $this->ldapService->removeUserOnLDAP($user);
        } elseif (!$user->isEnabled()) {

        } else {
            $this->ldapService->addUserOnLDAP($user);
        }
    }

    public function prePersist($user)
    {
        $PasswordLDAP = $user->getMotDePasse();
        $PasswordFOS = $user->getPassword();
        if (empty($PasswordLDAP)) {
            $user->setMotDePasse('123456' . (string)time());
        }
        if (empty($PasswordFOS)) {
            $user->setPlainPassword('123456' . (string)time());
        }

        $username = $user->getUsername();
        if (empty($username)) {
            $user->setUsername($user->getEmail());
        }
        $timestamp = time();
        if (empty($user->getCodeBarre())) {
            $codeBarre = $this->generateEAN($timestamp);
            $user->setCodeBarre($codeBarre);
        }

        $this->syncRelations($user);
    }

    private function generateEAN($number)
    {
        $code = '24' . $number;
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
        // loop backwards to make the loop length-agnostic. The same basic functionality
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    public function postPersist($user)
    {
        if ($user->isEnabled()) {
            $this->ldapService->addUserOnLDAP($user);
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Civilité', array(
                'class' => 'col-md-6',
                'description' => '
                    Cette section contient les informations principales d’une personne.
                    <br>Dans un souci d’homogénéïté, merci de <strong>bien veiller à respecter le format proposé</strong> (majuscules, espaces…).
                    <br>L’édition du <strong>nom, prénom, email et téléphone</strong> ne doivent être effectuées qu’en connaissance de cause
                    (dans le cadre du suivi d’une procédure définie).
                    
                '
            ))
            ->add('civilite', 'choice', array(
                'label' => 'Civilité',
                'choices' => array(
                    'mme' => 'Madame',
                    'mr' => 'Monsieur'
                )
            ))
            ->add('nom', null, array(
                'attr' => array('placeholder' => 'Tibou')
            ))
            ->add('prenom', null, array(
                'label' => 'Prénom',
                'attr' => array('placeholder' => 'Jean')
            ))
            ->add('telephone', null, array(
                'label' => 'Téléphone',
                'attr' => array('placeholder' => '06 02 03 04 05')
            ))
            ->add('email', null, array(
                'attr' => array('placeholder' => 'j.tibou@example.com'),
                'help' => "Utilisée notamment pour se connecter à l'espace membres et pour retrouver la personne à travers les différents outils."
            ))
            ->add('dateNaissance', 'sonata_type_date_picker', array(
                'label' => 'Date de naissance',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                )
            ))
            ->add('codeBarre', null, array(
                'read_only' => true,
                'help' => 'Le code barre est généré automatiquement à la création du Chouettos. Il n\'est pas possible de le modifier ici afin d\'éviter les mauvaises manipulations.'
            ))
            ->add('enabled', null, array(
                'required' => false,
                'label' => 'Membre ?',
                'help' => '
                    Le fait de cocher cette case autorise le passage en caisse et l’accès aux différents outils réservés aux membres.
                    La décoche supprime ces autorisations.
                    <br><em>Il faut compter environ 1 journée afin que la modification se propage au sein des différents outils.</em>
                '
            ))
            ->add('actif', null, array('required' => false, 'label' => 'Actif·ve dans un groupe ?'))
            ->add('gh', null, array('required' => false, 'label' => 'Grand Hibou ? (donne authorisation d\'ouvrir la porte du supermarché) '))
            ->add('carteImprimee', null, array('required' => false, 'label' => 'Carte imprimée ?'))
            ->end()

            ->with('Association', array(
                'class' => 'col-md-6',
                'description' => 'Informations additionnelles pouvant être utiles pour le projet coopératif.'
            ))
            ->add('domaineCompetence', null, array(
                'label' => 'Domaines de compétences',
                'attr' => array('placeholder' => 'Électricité, Réalisation de site web, Communication')
            ))
            ->add('notes')
            ->end()

            ->with('Adresse', array(
                'class' => 'col-md-12',
                'description' => 'Localisation de la personne, pour lui transmettre des courriers et avoir une idée de répartition géographique des membres.'
            ))
            ->add(
                'adresses',
                'sonata_type_collection',
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end()

            ->with('Paiements des parts sociales', array(
                'class' => 'col-md-12',
                'description' => '
                    Pour les coopérateurs et coopératrices uniquement.
                    Permet de faire le suivi des souscriptions de parts suite à la réunion initiale.
                '
            ))
            ->add(
                'paiements',
                'sonata_type_collection',
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end()

            ->with('Historique des adhésions', array(
                'class' => 'col-md-12',
                'description' => 'Pour les membres de l’association « Les Amis de La Chouette Coop ».'
            ))
            ->add(
                'adhesions',
                'sonata_type_collection',
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('full_text', 'doctrine_orm_callback', [
                'label' => 'Recherche rapide',
                'show_filter' => true,
                'callback' => [$this, 'getFullTextFilter'],
                'field_type' => 'text'
            ])
            ->add('paiements.dateEcheance', 'doctrine_orm_date_range', [
                'label' => 'Date de souscription'
            ])
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom',
            ])
            ->add('email')
            ->add('carteImprimee', null, ['label' => 'Carte imprimée ?'])
            ->add('enabled', null, ['label' => 'Actif ?'])
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

    public function getFilterParameters()
    {
        $this->datagridValues = array_merge([
            'enabled' => [
                'type'  => EqualType::TYPE_IS_EQUAL,
                'value' => BooleanType::TYPE_YES
            ]
        ], $this->datagridValues);

        return parent::getFilterParameters();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('prenom', null, array('label' => 'Prénom'))
            ->addIdentifier('email')
            ->add('telephone')
            ->add('adhesions')
            ->add('enabled', null, array('label' => 'Activé', 'editable' => true))
            ->add('carteImprimee', null, array('label' => 'Carte imprimée', 'editable' => true));
    }

}
