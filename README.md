# P6-SnowTricks
Création d'un site communautaire de partage de figures de snowboard via le framework Symfony.

## Environnement du développement :
* WampServer 3.1.6
* Apache 2.4.37
* PHP 7.3.0
* MySQL 5.7.19
* Symfony 4.2.1
* Composer 1.8.0
* Bootstrap 4.2.1
* jQuery 3.3.1
* PHPUnit 7.5.1

## Installation :
1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :

    git clone https://github.com/sorha/P6-SnowTricks.git

1. Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur mail SMTP dans le fichier `.env.local` situé à la racine du projet.
2. Télécharger et installer les dépendances du projet avec Composer :

    composer install

3. Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :

    php bin/console doctrine:database:create

4. Créez les différentes tables de la base de données en appliquant les migrations :

    php bin/console doctrine:migrations:migrate

5. (Optionnel) Installer les fixtures pour avoir une démo de données fictives :

    php bin/console doctrine:fixtures:load

6. Félications le projet est installé correctement, vous pouvez désormais commencer à l'utiliser à votre guise !