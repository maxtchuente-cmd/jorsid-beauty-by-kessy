# Jorsid Beauty by Kessy

**Plateforme de réservation et de gestion d’un salon de coiffure à domicile et en salon.**

Jorsid Beauty by Kessy est une application web développée avec Laravel et Livewire. Elle permet aux clients de consulter les prestations, de vérifier les disponibilités et de réserver un rendez-vous.

## Fonctionnalités

- Site public avec présentation des prestations, galerie photo et témoignages.
- Gestion des services, prix et durées.
- Réservation avec vérification des disponibilités.
- Gestion du calendrier et des rendez-vous depuis l’administration.
- Report des rendez-vous.
- Prestations en salon ou à domicile.
- Tarification des déplacements selon les zones configurées.
- Notifications par e-mail et invitations de calendrier au format `.ics`.
- Tableau de bord administratif.
- Gestion des catégories, des services et des paramètres du salon.

## Technologies

- PHP
- Laravel
- Livewire
- Blade
- Tailwind CSS
- JavaScript
- MySQL ou autre base de données prise en charge par la configuration Laravel.

## Installation

### Prérequis

- PHP compatible avec la version Laravel du projet.
- Composer.
- Node.js et npm.
- Une base de données configurée.

### Étapes

1. Cloner ou télécharger le dépôt.
2. Installer les dépendances PHP :

   ```bash
   composer install
   ```

3. Installer les dépendances JavaScript :

   ```bash
   npm install
   ```

4. Créer le fichier d’environnement :

   ```bash
   cp .env.example .env
   ```

5. Configurer la base de données et les paramètres e-mail dans `.env`.
6. Générer la clé de l’application :

   ```bash
   php artisan key:generate
   ```

7. Exécuter les migrations :

   ```bash
   php artisan migrate
   ```

8. Créer le lien vers les fichiers publics :

   ```bash
   php artisan storage:link
   ```

9. Compiler les ressources :

   ```bash
   npm run build
   ```

10. Démarrer le serveur local :

    ```bash
    php artisan serve
    ```

## Configuration

Configure les paramètres de la base de données, le fuseau horaire, les e-mails et les autres variables nécessaires dans `.env`.

Ne publie jamais de mot de passe, de clé API ou de donnée personnelle dans le dépôt.

## Statut du projet

Version de travail : V10.

Une validation complète avec la base de données, le navigateur et le serveur e-mail doit être effectuée avant toute mise en production.

## Auteur

Développé pour le projet Jorsid Beauty by Kessy.
