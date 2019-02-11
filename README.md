# P6-SnowTricks

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/59ffab92a1794ff9858cafce05f91f6b)](https://app.codacy.com/app/sorha/P6-SnowTricks?utm_source=github.com&utm_medium=referral&utm_content=sorha/P6-SnowTricks&utm_campaign=Badge_Grade_Dashboard)

Création d'un site communautaire de partage de figures de snowboard via le framework Symfony.

## Environnement utilisé durant le développement
* Symfony 4.2.1
* Composer 1.8.0
* Bootstrap 4.2.1
* jQuery 3.3.1
* PHPUnit 7.5.1
* WampServer 3.1.6
    * Apache 2.4.37
    * PHP 7.3.0
    * MySQL 5.7.19

## Installation
1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :
```
    git clone https://github.com/sorha/P6-SnowTricks.git
```
2. Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier `.env.local` qui devra être crée à la racine du projet en réalisant une copie du fichier `.env`.

3. Téléchargez et installez les dépendances back-end du projet avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4. Téléchargez et installez les dépendances front-end du projet avec [Npm](https://www.npmjs.com/get-npm) :
```
    npm install
```
5. Créer un build d'assets (grâce à Webpack Encore) avec [Npm](https://www.npmjs.com/get-npm) :
```
    npm run build
```
6. Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :
```
    php bin/console doctrine:database:create
```
7. Créez les différentes tables de la base de données en appliquant les migrations :
```
    php bin/console doctrine:migrations:migrate
```
8. (Optionnel) Installer les fixtures pour avoir une démo de données fictives :
```
    php bin/console doctrine:fixtures:load
```
9. Félications le projet est installé correctement, vous pouvez désormais commencer à l'utiliser à votre guise !
