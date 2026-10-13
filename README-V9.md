# Jorsid Beauty V9

## Correctifs V9
- Disponibilités publiques colorées en vert dans le calendrier, dates sans créneau désactivées après chargement des données du serveur.
- Contrôle de date ET d’heure avant envoi du formulaire.
- Les pauses exceptionnelles enregistrées dans le calendrier sont prises en compte dans le calcul des créneaux.
- Tableau de bord avec graphiques de statut et activité mensuelle (Chart.js chargé depuis CDN, avec affichage de secours si le CDN est indisponible).
- Tableaux de catégories et galerie plus structurés; galerie en tableau avec photo, titre, catégorie, visibilité et actions à icônes cohérentes.
- Seeder de catalogue de démonstration idempotent.

## Installation / mise à jour
Dans un dossier séparé, copier son `.env`, puis exécuter `composer install`, `npm install`, `php artisan config:clear`, `php artisan migrate`, `php artisan storage:link`, `npm run build`.

Pour insérer les catégories de démonstration et associer les services existants sans créer de faux clients ni rendez-vous :
`php artisan db:seed --class=DemoCatalogSeeder`

Ne pas utiliser `migrate:fresh` sur une base qui contient des données. Aucun test complet du runtime Laravel n'a été exécuté dans l'environnement de préparation; vérifier les écrans et les scénarios de réservation avant la mise en production.
