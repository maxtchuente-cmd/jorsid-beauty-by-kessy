# Jorsid Beauty by Kessy — V7 (base de travail)

Cette version est construite à partir de l'archive V6 fournie. Elle conserve les fichiers existants et ajoute notamment :

- Calendrier mensuel/hebdomadaire interactif dans Agenda & disponibilités.
- Consultation des rendez-vous par date et réglages d'horaires exceptionnels distincts des horaires hebdomadaires.
- Gestion de catégories de prestations et de catégories de galerie séparées.
- Formulaire public de témoignages avec modération préalable.
- Point d'intégration pour une traduction automatique compatible LibreTranslate, configurable par variables d'environnement.
- Palette d'administration bleu ciel/blanc avec actions orange, distincte de l'identité violette/rose du site public.

## Installation / mise à jour locale

1. Faites une copie de sauvegarde du dossier et de la base MySQL V6.
2. Extrayez ce ZIP dans un nouveau dossier, sans remplacer directement votre V6.
3. Copiez votre `.env` local existant dans le nouveau dossier (ne le partagez pas et ne le publiez pas).
4. Depuis le dossier du projet :

```bash
composer install
npm install
php artisan config:clear
php artisan migrate
php artisan storage:link
npm run build
php artisan route:list
php artisan test
```

Ne lancez pas `migrate:fresh` : cette commande supprimerait les tables existantes.

## Traduction automatique

Les champs traduits manuellement restent disponibles. Pour la génération automatique, renseignez dans `.env` un endpoint compatible LibreTranslate, par exemple `JORSID_TRANSLATION_URL=https://votre-instance/translate`, et éventuellement `JORSID_TRANSLATION_API_KEY`. Puis lancez `php artisan config:clear`. Aucun service tiers ni aucune clé n'est présumé configuré par défaut.

## SMTP Brevo

La configuration existante du `.env` reste utilisée tant qu'aucune configuration n'est enregistrée depuis l'administration. La page E-mails et SMTP permet ensuite d'enregistrer les paramètres et de chiffrer la clé SMTP dans la base. Le mot de passe ne doit jamais être ajouté au code ou au dépôt. Validez l'envoi local et la production séparément.

## Vérifications importantes

Cette archive est une version de travail construite à partir de la V6. Elle a fait l'objet de contrôles statiques PHP dans l'environnement de préparation, mais le runtime Laravel complet n'a pas pu être exécuté ici car les dépendances Composer n'étaient pas installées dans l'environnement. Avant mise en production, exécutez les migrations et tests dans une copie de votre environnement, vérifiez les parcours complets et gardez une sauvegarde.
