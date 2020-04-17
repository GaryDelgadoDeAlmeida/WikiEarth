# WikiEarth

## Version

Version PHP minimum => 7.3.9

pour vérifié la version de PHP installer sur votre ordinateur, tapez dans le terminal (Linux & Mac) / cmd (Windows) : 

```bash 
    php -v
```

Version Symfony => 4.4 (LTS)

## Introduction

WikiEarth, projet qui sera présenter en tant que projet de fin d'année (Bac +4).

Ce projet étant personnel, je conserve donc tous mes droits d'auteurs sur l'entièreté du projet WikiEarth et de son code source.

## Installation

Cette commande installera toutes les dépendances que cette solution web à besoin pour fonctionner.

```bash
    composer install
```

Cette commande est nécessaire pour mettre à jour toutes les dépendances. Elle est aussi très importante pour les mise à jours de sécurité.

```bash
    composer update
```

## Création de la base de données (terminal)

Créer la database :
```bash
    symfony console doctrine:database:create
```

Générer les tables (pour la database) :
```bash
    symfony console make:migration
```

Sauvegarder les modifications dans la database :
```bash
    symfony console doctrine:migrations:migrate
```

## Compatibilité Apache 2

```bash
    composer require symfony/apache-pack 
```

Les configurations restantes (pour la mise en production) seront à faire à travers ce lien https://symfony.com/doc/current/setup/web_server_configuration.html