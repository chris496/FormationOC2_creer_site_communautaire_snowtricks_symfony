# Projet 6 - Parcours Développeur d'application - PHP / Symfony

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/21ec5709f24f4f1ba0a9e15eacc9600b)](https://www.codacy.com/gh/chris496/ChristopheDumas_BlogSnow/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=chris496/ChristopheDumas_BlogSnow&amp;utm_campaign=Badge_Grade)

<p align="center">
<img src="./public/img/CaptureAccueil.png" alt="capture d'écran" width="600px"/>
</p>

## Démarrage &#x1F3C1;

* Ce site doit être exécuté en local (phase de développement)

## Environnemnt de développement &#x1F4DC;

* Symfony 5.4
* Composer 2.1.14
* Bootstrap 5.0.2
* WampServer 3.2.9
* Apache (version utilisé : 2.4.46)  
* Php (version utilisé : 7.4.26)
* Mysql (version utilisé : 8.0.21) ou autre bdd sql

## Installation &#x1F4BE;

* Installer Apache, Php et Mysql
* Cloner le projet git en local (se positionner dans le répertoire souhaité)

```bash
git clone https://github.com/chris496/ChristopheDumas_BlogSnow.git
```

* Copier-coller à la racine du projet le fichier .env et le renommer .env.local. Entrer vos infos de configuration (bdd, ...).

* Dans un terminal, positionnez vous à la racine du projet et exécuter :

```bash
composer install
```

* Créez la base de données si elle n'existe pas déjà :

```bash
php bin/console doctrine:database:create
```

* Créez les différentes tables de la base de données :

```bash
php bin/console doctrine:migrations:migrate
```

* Installer les fixtures si vous souhaitez démarrer avec un jeu de données :

```bash
php bin/console doctrine:fixtures:load
```

Démarrer votre serveur local :

```bash
symfony server:start
```

* dans l'application pour accéder à la partie administration, inscrivez-vous et valider votre compte par mail.

## Authors &#x1F60E;

* **Christophe Dumas** - [chris496](https://github.com/chris496)