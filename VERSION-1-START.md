# Jorsid Beauty by Kessy — V1 (fondation visuelle)

Cette version est une première remise à niveau du projet existant, pas une version finale de production.

## Changements inclus
- Logo fourni intégré au header et à la zone visuelle d’accueil.
- Favicon et icône Apple ajoutés.
- Page d’accueil réorientée vers l’allemand.
- Sélecteur DE / FR / EN pour les libellés communs de navigation et le pied de page.
- Animations discrètes au défilement, interactions douces et prise en compte de `prefers-reduced-motion`.
- Amélioration de l’accessibilité (lien d’évitement, libellés, focus visibles).
- Suppression de la route publique `/prestations` en double.
- Archive allégée : `node_modules`, `vendor`, `.env` et base SQLite locale exclus.

## Limites à traiter dans les prochaines versions
- Le sélecteur linguistique V1 traduit les libellés communs et ne constitue pas encore une traduction complète de chaque page.
- Les créneaux horaires de réservation sont encore une liste statique dans le formulaire d’origine : ils ne sont PAS encore calculés à partir du planning d’administration. Ne pas utiliser comme système de réservation final en production.
- La zone d’administration, les horaires configurables, les exceptions, les indisponibilités, les conflits de réservation, les rappels et statistiques restent à construire/valider.
- Les coordonnées réelles, les zones desservies, les prix et les règles commerciales restent à confirmer par la propriétaire.

## Roadmap en 5 versions
1. **V1 — Fondation & identité** : structure existante, identité visuelle, navigation, favicon, responsive, animations douces et base de langues.
2. **V2 — Catalogue & contenu** : services/images, modèles de coiffure, galerie, témoignages modérés, pages entièrement traduites DE/FR/EN.
3. **V3 — Réservation fiable** : planning administrable, calendrier des dates disponibles/indisponibles, créneaux générés selon horaires/durée/pauses/congés, contrôle serveur anti-double-réservation, salon/domicile et estimation des frais.
4. **V4 — Administration & automatisation** : agenda, clientes, statuts, confirmations, annulations/reports, e-mails et rappels configurables, historique des notifications.
5. **V5 — Pilotage & lancement** : tableaux de bord, statistiques et suivi des paiements, sécurité/RGPD, tests de bout en bout, responsive, sauvegardes et préparation production.

## Démarrage
1. Installer une version PHP compatible avec le `composer.json` et Composer.
2. Copier `.env.example` vers `.env` puis configurer la base de données.
3. `composer install`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `npm install` puis `npm run dev` (ou `npm run build` pour les assets de production).

Vérifier les versions requises dans `composer.json` avant l’installation. Aucun fichier `.env` personnel n’est distribué dans cette archive.
