# AdminChouettos

AdminChouettos c'est l'outil de gestion des membres et backend du planning de La Chouette Coop construit avec **Symfony**

## Fonction

Adminchouettos est une application symfony qui permet la gestion du LDAP. Les données rentrées dans adminchouettos sont toutes enregistrées dans une base de données.
Une partie de ces données sont ensuite transférées dans le LDAP (sous openLDAP), c'est à dire le nom, le mot de passe et le codebarre de l'utilisateur.
Le reste des données sont uniquement dans la base de données locale.
Adminchouettos offre une methode api pour se connecter au ldap. C'est à dire qu'on envoi les données

## Partie gestion des membres

---

## Partie planning

---

## Installation

- cp .env .env.local
- Modifier le fichier .env.local avec les paramètres du LDAP, du mailer et de la BDD
- [Install Symfony][2] with Composer (see [requirements details][1]).
- Rajouter un utilisateur en tant qu'admin : mettre la valeur `[ROLE_ADMIN]` dans le champs role d'un utilisateur dans la base.

## Documentation

### Description des dossiers et fichiers

- **src/Admin** : Contient tous les fichiers de configuration pour avoir l'interface d'administration via le module [Sonata Admin][3]
- **src/Controller** : LdapController contient tous les services pour administrer le ldap, PlanningController toutes les fonctions pour la partie planning du projet, SecurityController permet l'enregistrement et le changement d'user et des mots de passe.
- **src/Entity** : la définition des entity, gérées ensuite par doctrine
- **src/Form** : le formulaire d'ajout de nouvel utilisateur
- **src/Repository** : pour les requetes en BDD
- **src/Security** : Les méthodes d'authentification, par login pour se connecter à la partie administration, par jeton d'api pour le planning et autre.

- **templates** : les vues de l'application

### Cron a mettre en place

**toute les nuits** : `exportGHCodeBarreAction()` `notificationReserve()` `notificationParticipation()` `compterPiafAttendues()` `compterPiafEffectuees()` `generateCreneaux()`

### Docker

Pour la 1ere exécution, il faut configurer la database :

```shell
cp docker-compose.yaml.dev docker-compose.yaml  # Ou docker-compose.yaml.prod
# Pour la production, mettre à jour les variables MARIADB_ROOT_PASSWORD, MARIADB_PASSWORD et SYMFONY_DATABASE_PASSWORD
docker-compose up -d database
docker-compose exec database bash
# Puis dans le docker `database` :
mysql -p  # Enter the $MARIADB_ROOT_PASSWORD
CREATE USER 'adminchouettos'@'symfony' IDENTIFIED BY '...';
GRANT ALL ON adminchouettos.* TO 'adminchouettos'@'symfony';
# Vous pouvez quitter le docker
```

Pour restaurer un dump :

```shell
# put the backup.sql file in ./data
mysql -p adminchouettos < /var/lib/mysql/backup.sql
```

> Si vous voulez importer la db de prod vous devez copier tout le dossier data et non juste le fichier .sql.
> Maria db n'importe pas les séquences des PK et donc les `CREATE` ne fonctionne pas.

Une fois que la database est confgurée :

```shell
cp .env .env.local
* Modifier le fichier .env.local avec les paramètres du LDAP, du mailer et de la BDD
docker-compose build
docker-compose up -d
```

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/setup.html#setting-up-an-existing-symfony-project
[3]: https://docs.sonata-project.org/projects/SonataAdminBundle/en/4.x/index.html
