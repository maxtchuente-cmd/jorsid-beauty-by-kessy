# Jorsid Beauty by Kessy — V8

## Principales évolutions
- Refonte visuelle de l'espace d'administration : navigation compacte, tableaux et cartes plus aérés, boutons moins volumineux, états visuels, transitions et animations discrètes.
- Les formulaires d'administration contenant des champs sont présentés dans des fenêtres modales centrées. Les actions simples (publier/masquer, supprimer, marquer comme lu) restent des actions directes avec confirmation quand nécessaire.
- Refonte du tableau de bord avec statistiques, raccourcis, notifications et prochains rendez-vous.
- Agenda en vue mois/semaine avec navigation précédent/suivant/aujourd'hui. Une date n'est disponible à la réservation que si elle est explicitement marquée « Geöffnet » dans l'agenda.
- L'agenda affiche « Geöffnet », les heures configurées et le nombre de rendez-vous uniquement lorsqu'il y en a. Le panneau des horaires hebdomadaires et les pauses a été retiré de la page agenda.
- Après une demande de réservation, la cliente voit un bon de réservation imprimable, qu'elle peut imprimer ou enregistrer en PDF.
- Ajout d'un champ dans les paramètres e-mail pour choisir l'adresse qui reçoit les alertes de nouvelles réservations. La cliente reçoit son accusé de réception et l'adresse administrateur reçoit une alerte séparée.

## Mise à jour locale
1. Décompresser dans un nouveau dossier, sans écraser V6/V7.
2. Copier le fichier `.env` personnel (il n'est pas inclus dans l'archive).
3. Exécuter `composer install`.
4. Exécuter `npm install` puis `npm run build`.
5. Exécuter `php artisan config:clear`.
6. Exécuter `php artisan migrate` (ne pas utiliser `migrate:fresh` sur une base contenant des données).
7. Exécuter `php artisan storage:link` si nécessaire.
8. Démarrer l'application et tester : disponibilité dans l'agenda, réservation, impression du bon, envoi client et alerte administrateur.

## Configuration e-mail
Dans Administration → E-mails et SMTP, enregistrer les identifiants SMTP Brevo, l'expéditeur et l'adresse e-mail qui doit recevoir les alertes. Le mot de passe SMTP enregistré dans la base est chiffré. Le champ vide conserve la clé existante.

## Validation
La syntaxe PHP et l'intégrité de l'archive sont contrôlées lors de la préparation. Une exécution complète de Laravel, les tests de base de données et le build Vite doivent être effectués dans l'environnement de développement du projet, avec ses dépendances installées.
