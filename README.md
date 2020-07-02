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

WikiEarth est un projet de genre encyclopédie (style Wikipédia) dont le sujet de recherche est la Terre (et ses différents systèmes), les métaux et le vivant (animaux, insectes, plantes, bactéries, etc ...). Etant donné le périmètre d'action de ce projet, il paraît approprié de dire que le domain d'action est la "géoscience".

## Documents

Dans le répertoire public/content/file :
- Cahier des charges : cdc-wikiearth.docx
- Cachier des charges fonctionnels : cdcf-wikiearth.docx
- UX / UI : à définir
- Schema Base de données (BDD) : WikiEarth.vuerd.json

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
