# Changements V7 — 13 octobre 2026

## Ajouts réalisés dans cette archive de travail
- Ajout d'un calendrier interactif mois/semaine avec navigation, ouverture d'une journée et affichage des rendez-vous.
- Ajout d'exceptions horaires par date et d'une table permettant plusieurs pauses par date.
- Les créneaux de réservation tiennent compte des horaires exceptionnels et des pauses enregistrées pour une date.
- Ajout de catégories de prestations et de catégories de galerie séparées, avec reprise des anciennes valeurs existantes lors de la migration.
- Ajout de l'édition des témoignages dans l'administration et d'un formulaire public modéré.
- Ajout d'un point d'intégration pour un service de traduction compatible LibreTranslate configurable côté serveur.
- Ajout d'une page d'administration SMTP avec stockage chiffré de la clé et envoi d'un e-mail de test ; les notifications de réservation utilisent les paramètres sauvegardés si disponibles.
- Mise à jour de la palette d'administration vers bleu ciel/blanc et actions orange, en conservant la palette violette/rose du site public.
- Ajout d'un contrôle de disponibilité côté serveur lorsque l'administration confirme ou reporte un rendez-vous.

## À vérifier encore avant production
- Exécuter les migrations et les tests dans l'environnement local avec les dépendances Composer installées.
- Configurer et tester un endpoint de traduction automatique réel ; il n'est pas fourni ni présumé actif.
- Valider les e-mails Brevo sur cette copie locale, puis séparément sur le serveur de production.
- Vérifier en runtime la compatibilité de `Mail::build()` avec la version installée, puis valider les paramètres SMTP enregistrés dans l'administration ; sans configuration sauvegardée, l'application conserve le SMTP du `.env`.
- Effectuer les tests manuels de non-régression avec une copie de la base MySQL V6.
