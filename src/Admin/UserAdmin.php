<?php

namespace App\Admin;

use App\Controller\LdapController;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\Exporter\Source\IteratorCallbackSourceIterator;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdmin extends AbstractAdmin
{
    const DN_MEMBRES = "ou=membres,o=lachouettecoop,dc=lachouettecoop,dc=fr";

    private $originalUserData;
    private $ldapService;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return self
     */
    public function setUserPasswordEncoder(UserPasswordEncoderInterface $passwordEncoder): self
    {
        $this->passwordEncoder = $passwordEncoder;
        return $this;
    }

    /**
     * @param LdapController $ldapService
     * @return $this
     */
    public function setLdapService(LdapController $ldapService)
    {
        $this->ldapService = $ldapService;
        return $this;
    }

    protected $datagridValues = [
        '_sort_by' => 'nom',
    ];

    protected $maxPerPage = 100;

    protected $perPageOptions = array(10, 20, 50, 100, 500, 1000);



    public function getExportFields()
    {
        return array('id', 'civilite', 'nom', 'prenom', 'codebarre', 'email', 'exportDateNaissance', 'telephone', 'enabled', 'domaineCompetence', 'exportAdresse1', 'exportAdresse2', 'exportAdresse4', 'exportAdresse5', 'exportAdresse6', 'adhesions', 'exportdAhesionAnnee', 'exportAdhesionDate', 'exportAdhesionMontant', 'exportSouscriptionDate', 'exportNomPersonneRattachee', 'exportPrenomPersonneRattachee','exportMailPersonneRattachee');
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
        $actions = array_merge($actions, parent::getBatchActions());
        return $actions;
    }

    public function preUpdate($user)
    {
        $this->syncRelations($user);
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $this->originalUserData = $em->getUnitOfWork()->getOriginalEntityData($user);
        $this->manageFileUpload($user);
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

    /** @var User $user */
    public function preRemove($user)
    {
        $this->ldapService->removeUserOnLDAP($user);
    }

    /** @var User $user */
    public function postUpdate($user)
    {
        if ($this->originalUserData['enabled'] == $user->getEnabled() && $user->getEnabled()) {
            $this->ldapService->updateUserOnLDAP($user, $this->originalUserData);
        } elseif (!$user->getEnabled() && $this->originalUserData['enabled'] == true) {
            $this->ldapService->removeUserOnLDAP($user);
        } elseif (!$user->getEnabled()) {

        } else {
            $this->ldapService->addUserOnLDAP($user);
        }
    }


    private function manageFileUpload($user) {
        if ($user->getFile()) {
            $user->refreshUpdated();
        }
    }

    /** @var User $user */
    public function prePersist($user)
    {
        $PasswordLDAP = $user->getMotDePasse();
        $PasswordFOS = $user->getPassword();
        if (empty($PasswordLDAP)) {
            $user->setMotDePasse('123456' . (string)time());
        }
        /*if (empty($PasswordFOS)) {
            $user->setPlainPassword('123456' . (string)time());
        }*/
        $pass = $this->passwordEncoder->encodePassword($user, random_bytes(10));
        $user->setPassword($pass);

        $timestamp = time();
        if (empty($user->getCodeBarre())) {
            $codeBarre = $this->generateEAN($timestamp);
            $user->setCodeBarre($codeBarre);
        }

        $this->syncRelations($user);

        //$this->manageFileUpload($user);
    }
    /** @var User $user */
    public function postPersist($user)
    {
        if ($user->getEnabled()) {
            $this->ldapService->addUserOnLDAP($user);
        }
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();

       $fileFieldOptions = array('required' => false, 'mapped'=>false,);
        if ($user && ($webPath = $user->getPhoto())) {
            $fileFieldOptions['help'] = '<img src="/uploads/documents/'.$webPath.'" class="admin-preview" style="width: 300px;" />';
        }

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
            ->add('civilite', ChoiceType::class, array(
                'label' => 'Civilité',
                'choices' => array(
                    'Madame' => 'mme',
                    'Monsieur' => 'mr'
                )
            ))
            ->add('nom', null, array(
                'attr' => array('placeholder' => 'Tibou')
            ))
            ->add('prenom', null, array(
                'label' => 'Prénom',
                'attr' => array('placeholder' => 'Jean')
            ))
            ->add('rolesChouette', ModelAutocompleteType::class, array(
                    'class' => 'App\Entity\Role',
                    'property' => 'libelle',
                    'help' => 'Merci de choisir le rôle "Chouettos" pour toute nouvelle création de fiche !',
                    'placeholder' => 'Taper les première lettres',
                    'multiple' => true,
                    'required' => false)
            )
            ->add('telephone', null, array(
                'label' => 'Téléphone',
                'attr' => array('placeholder' => '06 02 03 04 05')
            ))
            ->add('email', null, array(
                'attr' => array('placeholder' => 'j.tibou@example.com'),
                'help' => "Utilisée notamment pour se connecter à l'espace membres et pour retrouver la personne à travers les différents outils."
            ))
            ->add('dateNaissance', DatePickerType::class, array(
                'label' => 'Date de naissance',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                )
            ))
            ->add('codeBarre', null, array(
                'disabled' => false,
                'help' => "Le code barre est généré automatiquement à la création du Chouettos. '24'+ 10 digit du timestamp + 'x' ou x est le checksum. Attention aux mauvaises manipulations."
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
            ->add('periodeEssai', DatePickerType::class, array(
                'label' => "Période d'essai ?",
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                ),
                'help' => "Renseignez la date de la fin de la période d'essai à laquelle le chouettos devra sourscrire ou non."
            ))
            ->add('actif', null, array('required' => false, 'label' => 'Actif·ve dans un groupe ?'))
            ->add('gh', null, array('required' => false, 'label' => 'Authorisation d\'ouvrir la porte du supermarché ? (Anciennement GH ?) '))
            ->add('carteImprimee', null, array('required' => false, 'label' => 'Carte imprimée ?'))
            ->end()
            ->with('Photo', array(
                'class' => 'col-md-6'
            ))
            ->add('file', FileType::class, $fileFieldOptions)
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

            ->with('Planning', array(
                'class' => 'col-md-6'
            ))
            ->add('dateDebutPiaf', DatePickerType::class, array(
                'label' => 'Date de début du compteur PIAF',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'placeholder' => '31/01/1970'
                )
            ))
            ->add('statut', ChoiceType::class, ['choices' =>
                [
                'très chouette' => 'très chouette',
                'chouette' => 'chouette',
                'chouette en alerte' => 'chouette en alerte'
                ]
            ])
            ->add('nbPiafAttendues')
            ->add('nbPiafEffectuees')
            ->add('absenceLongueDureeSansCourses')
            ->add('absenceLongueDureeCourses')
            ->add('attenteCommissionParticipation')
            ->add('dispenseDefinitive')
            ->end()

            ->with('Adresse', array(
                'class' => 'col-md-12',
                'description' => 'Localisation de la personne, pour lui transmettre des courriers et avoir une idée de répartition géographique des membres.'
            ))
            ->add(
                'adresses',
                CollectionType::class,
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end()
            ->with('Personne Rattachée', array(
                'class' => 'col-md-12',
                'description' => ''
            ))
            ->add(
                'personneRattachee',
                CollectionType::class,
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
                CollectionType::class,
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
                CollectionType::class,
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
                'field_type' => TextType::class
            ])
            ->add('paiements.dateEcheance', 'doctrine_orm_date_range', [
                'label' => 'Date de souscription'
            ])
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom',
            ])
            ->add('email')
            ->add('gh')
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
                'type'  => EqualOperatorType::TYPE_EQUAL,
                'value' => BooleanType::TYPE_YES
            ]
        ], $this->datagridValues);

        return parent::getFilterParameters();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('nom')
            ->add('prenom', null, array('label' => 'Prénom'))
            ->addIdentifier('email')
            ->add('telephone')
            ->add('adhesions')
            ->add('enabled', null, array('label' => 'Activé', 'editable' => true))
            ->add('carteImprimee', null, array('label' => 'Carte imprimée', 'editable' => true));
    }

}
