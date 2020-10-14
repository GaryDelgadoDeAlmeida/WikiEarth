# WikiEarth - Copyright All rights reserved
 
 <b>Ce projet est, pour le moment, en phase de développement.</b>

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

WikiEarth est un projet de genre encyclopédie (style Wikipédia) dont le sujet de recherche est la Terre (et ses différents systèmes), les métaux et le vivant (animaux, insectes, plantes, bactéries, etc ...). Etant donné le périmètre d'action de ce projet, il paraît approprié de catégorisé le domain d'action comme "géoscience".

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

## Base de données

### Installation

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

### Imports

Il existe un répertoire nommé "imports" dans "public/content/file". Ce répertoire servira exclusivement à importé son contenu (le contenu dans ce répertoire) dans la base de données comme "données initiales" (les données de base qu'elle (le site) contiendra au tout début du lancement). Dans ce répertoire, il y existe, pour le moment, 2 grands répertoires (types) contenant différents répertoires : 

  - le répertoire "Living Thing" contenant tous les êtres vivants
    - le répertoire "Animalia"
    - le répertoire "Plantae"
    - le répertoire "Fungi" (champignons)
    - le répertoire "Protista"
    - le répertoire "Archaea"
    - le répertoire "Archaebacteria"
    - le répertoire "Eubacteria"
    - le répertoire "Bacteria"
    - le répertoire "Virae"
    
  - le répertoire "Natural Elements" contenant tous les atomes (métaux et non-métaux), les minéraux.
    - le répertoire "Atome"
    - le répertoire "Minerals"

Pour le système d'import, en fonction de la commande appeler, il appellera le fichier et les images (s'ils en ont) dans le dossier cible pour les importés dans la base de données.

Pour importer les animaux
```bash
    symfony console app:import:animals
```

Pour importer les plantes
```bash
    symfony console app:import:plants
```

Pour importer les atomes
```bash
    symfony console app:import:atomes
```

Pour importer les minéraux
```bash
    symfony console app:import:minerals
```

Pour importer les pays
```bash
    symfony console app:import:country
```

## Compatibilité Apache 2

```bash
    composer require symfony/apache-pack 
```

Les configurations restantes (pour la mise en production) seront à faire à travers ce lien https://symfony.com/doc/current/setup/web_server_configuration.html
