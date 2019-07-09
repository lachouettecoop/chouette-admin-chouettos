# Admin Chouettos

[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

> Cette documentation reste à écrire… on s’y met petit à petit !
> Si vous souhaitez aider, contactez-nous.

## Contributions

De manière à faciliter la communication et le partage des nouveautés, nous essayons (depuis la v1.1.0) d’appliquer les conventions
[conventional commits](https://www.conventionalcommits.org/en/v1.0.0-beta.3/)
sur ce projet.

Si vous souhaitez un petit coup de main contactez-nous !
Vous pouvez également utilisez la commande suivante afin de vous aider à commiter :

```bash
npx git-cz
```

## Release

Nous essayons (depuis la v1.1.0) d’appliquer les conventions
[conventional commits](https://www.conventionalcommits.org/en/v1.0.0-beta.3/)
sur ce projet.
Cela nous permet de recommander la commande suivante pour réaliser une nouvelle release du projet. 

```bash
npx standard-version && git push --follow-tags origin master
```

## Déploiement

Afin de déployer une nouvelle version en production, voici la marche à suivre :

* se connecter sur `Hedwige`
* se rendre dans le projet `cd docker/adminchouettos/`
* récupérer la version souhaitée : `sudo git checkout v1.2.0`
* vérifier les modifications à apporter sur la base de données : `sudo docker-compose run --rm php app/console doctrine:sch:update --complete --dump-sql`
* le cas échéant, les exécuter : `sudo docker-compose run --rm php app/console doctrine:sch:update --complete --force`
* vider les caches : `sudo docker-compose run --rm php app/console cache:clear --env=prod`
* **IMPORTANT :** remettre des droits d'écriture `sudo chmod -R 777 symfony/app/{cache,logs}` sur les dossiers applicatifs
