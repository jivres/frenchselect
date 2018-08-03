# Mon B2B

Plateforme Web pour réaliser des commandes sur du prêt-à-porter.
Réalisée en PHP avec Symfony, HTML5/CSS3 et jQuery.


## Réalisateurs
- Alexis Dufrenne
- Alexis Sarrabayrouse
- Vincent Ruello (administrateur)

## Utilisateurs :
- administrateurs
- marque : proposant un catalogue de produits
- client : possédant une ou plusieurs boutiques et réalisant des commandes
- commercial : peut gérer des boutiques et des marques

## Architecture du projet

### `doc`

Dossier contenant l'ensemble des documents utiles pour le projet

Le dossier `mock-up` contient par exemple les maquettes en version Web pour les marques et les produits.

### `website`

Dossier contenant l'application Web réalisée avec Symfony.
Paramètres de l'application : `app/config/parameters.yml`.

## Mise en route

- date.timezone sous MacOS (php.ini) : http://www.teddypayet.com/Petits-reglages-pour-symfony-sous
- (`composer require --dev symfony/web-server-bundle`)
- `php bin/vendors install` ou `php bin/vendors install --reinstall`
- `php bin/console server:start`
- lancer le serveur MySQL
- configurer `app/config/parameters.yml` pour se connecter à la base
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:schema:update —-dump-sql`
- `php bin/console doctrine:schema:update —-force`
- installer les ressources : `php bin/console assets:install web`
- `composer require friendsofsymfony/jsrouting-bundle`
- `php bin/console assets:install --symlink web`
- `php bin/console fos:js-routing:dump`

Mettre à jour la base de données avec Doctrine :
- php bin/console doctrine:generate:entities B2bBundle:<Entity>
- php bin/console doctrine:schema:update --dump-sql
- php bin/console doctrine:schema:update --force

Déployer depuis le serveur :
- git clone <lien du dépôt>
- php bin/symfony_requirements
- curl -s http://getcomposer.org/installer | php
- php composer.phar install
- php bin/console cache:clear --env=prod --no-debug --no-warmup
- php bin/console cache:warmup --env=prod
- php bin/console assets:install web
- php composer.phar require symfony/web-server-bundle ^3.3
- mkdir web/uploads
- chmod 777 web/uploads
- chmod -R 777 var/

Si problème de `server_version`, dans `app/config/config.yml` :
```
doctrine:
       dbal:
           driver: pdo_mysql
           host: '%database_host%'
           port: '%database_port%'
           dbname: '%database_name%'
           user: '%database_user%'
           password: '%database_password%'
           server_version: 5.5
```

DataTimeZone :
- php -i | grep php.ini
-

### Serveur public

ssh : frs@junior-aei.com
mdp : mdp

ssh : frs@old.junior-aei.com
mdp : frs

### Google Maps API
https://developers.google.com/maps/documentation/geocoding/usage-limits?hl=FR


### PhpMyAdmin

login: frs
mdp: etudefrs2017

Affichage : frs.etudes.junior-aei.com

### Définition de la taille max des fichiers
Dans php.ini :
```
upload_max_filesize = 20M
max_input_vars = 200000
```
# frenchselect
