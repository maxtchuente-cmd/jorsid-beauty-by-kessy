# Jorsid Beauty by Kessy — V2 + V3 + V4

Ce paquet réunit le catalogue public enrichi (V2), le moteur de disponibilités et de réservation (V3), et une première administration sécurisée avec automatisations e-mail (V4).

## Fonctionnalités

- **V2 — Site public et catalogue :** pages publiques existantes, prestations administrables (prix, durée, description, salon/domicile, frais de déplacement, ordre et activation), galerie photo administrable avec publication/masquage, témoignages modérés et affichage sur l'accueil, animations et sélecteur DE/FR/EN existant.
- **V3 — Réservation :** créneaux calculés côté serveur selon les horaires hebdomadaires, pauses, congés, durée de chaque prestation et rendez-vous déjà enregistrés; choix du salon ou domicile; frais de déplacement; vérification serveur à l'enregistrement; verrouillage transactionnel par date pour éviter les réservations concurrentes qui se chevauchent.
- **V4 — Administration :** tableau de bord et chiffres principaux, agenda filtrable, gestion de statut (en attente, confirmé, terminé, annulé, absence), notes internes, gestion des prestations, horaires et congés, galerie, témoignages. Les e-mails de confirmation/annulation sont envoyés si SMTP est configuré. Une commande planifiée envoie les rappels des rendez-vous confirmés du lendemain.

## Installation

Prérequis : PHP 8.3+, Composer, base MySQL/MariaDB (ou un pilote Laravel compatible) et Node.js/npm pour compiler les ressources frontales.

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configurez DB_* dans .env avant la migration
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan jorsid:create-admin
php artisan serve
```

L'administration est accessible à `/administration/connexion`. La commande `jorsid:create-admin` demande un nom, un e-mail et un mot de passe d'au moins 12 caractères et crée/promouvoit le compte administrateur. Ne partagez jamais ce mot de passe.

## E-mails et rappels

Configurez `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` et les autres paramètres SMTP dans `.env`. Le fichier `.env.example` utilise le mailer `log` pour le développement : les e-mails ne sont alors pas livrés aux clientes.

Pour les rappels automatiques en production, le planificateur Laravel doit être exécuté chaque minute, par exemple avec cron :

```cron
* * * * * cd /chemin/vers/jorsid-beauty-by-kessy && php artisan schedule:run >> /dev/null 2>&1
```

Commande de test manuelle : `php artisan bookings:send-reminders`.

## Configuration opérationnelle à faire avant la mise en ligne

- Définir les horaires réels, les pauses et les congés dans Administration → Horaires & congés.
- Vérifier durées, tarifs, zones de déplacement et frais pour chaque prestation.
- Configurer et tester le vrai SMTP et le planificateur cron.
- Ajouter uniquement des photos pour lesquelles les droits de publication/consentements sont acquis.
- Tester les parcours de réservation sur la base cible, avec plusieurs réservations concurrentes, avant la production.
- Compléter la traduction des textes libres créés par l'administrateur : les textes de contenu ajoutés par une cliente/administrateur ne peuvent pas être traduits automatiquement de façon fiable sans saisir leurs versions DE/FR/EN.

## État des tests de ce paquet

Les fichiers PHP sont vérifiés par analyse syntaxique et l'archive est contrôlée. L'environnement de construction ne disposait pas d'une installation complète des dépendances Composer/npm; les tests d'intégration Laravel, la compilation Vite, les tests e-mail réels et le test navigateur ne sont donc pas déclarés comme réussis. Il faut exécuter les commandes d'installation et tester sur la configuration réelle avant le déploiement.
