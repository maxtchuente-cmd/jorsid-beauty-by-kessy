# Jorsid Beauty by Kessy — V6

Cette version reprend le projet V5 et met l’accent sur le design public fourni en référence, les photos de coiffures, le calendrier de réservation et l’administration douce en violet/rose/lilas/bleu.

## Nouveautés V6

- Accueil public réorganisé : grand hero photo, prestations avec photo, bandeau d’avantages, philosophie, galerie de modèles et section témoignages.
- Six photos de démonstration de coiffures ajoutées dans `public/images/hairstyles/`. Elles servent de modèles visuels initiaux; remplacez-les par les photos autorisées de Kessy dans la galerie ou les prestations.
- La gestion des prestations accepte maintenant une photo (JPG, PNG, WebP, 5 Mo maximum). La photo est montrée dans l’administration, le catalogue public et les cartes d’accueil. Une migration additive et protégée ajoute `services.image_path` sans recréer la table.
- Calendrier mensuel public, dates passées désactivées, sélection de la date puis chargement des créneaux réels par le serveur. Les créneaux restent calculés selon les horaires, pauses, congés, durée de prestation et réservations actives; la validation serveur et le verrouillage contre les doubles réservations restent actifs.
- Administration redesignée : sidebar douce avec logo, navigation à icônes, navbar, cartes, transitions, retours d’action, boutons d’action compacts avec infobulles et formulaires d’ajout/modification en fenêtres modales pour le catalogue, la galerie et les témoignages. La configuration de l’agenda hebdomadaire reste un tableau afin de modifier plusieurs jours efficacement.
- Sélecteur de langue DE/FR/EN public et admin, partageant la préférence locale. La langue choisie lors de la réservation est enregistrée pour envoyer les e-mails client en allemand, français ou anglais.
- Les messages de réservation et de changement de statut ont des versions allemandes, françaises et anglaises.

## Installation / mise à jour

1. Sauvegardez votre base de données et le `.env` avant de remplacer les fichiers.
2. Installez les dépendances PHP : `composer install`.
3. Configurez `.env` à partir de `.env.example`, puis vérifiez `APP_KEY`, la base de données et `APP_URL`.
4. Lancez les migrations : `php artisan migrate`.
5. Créez le lien des photos : `php artisan storage:link`.
6. Videz les caches : `php artisan optimize:clear`.
7. Si vous modifiez le CSS/JS source : `npm install` puis `npm run build`.
8. Connectez-vous à l’administration, ajoutez les prestations avec leurs photos, puis configurez les horaires et congés avant d’ouvrir la réservation publique.

## E-mails : configuration unique

Le code utilise le système mail intégré à Laravel (Symfony Mailer) via `Mail::raw`. Il évite de répéter les réglages dans les contrôleurs et convient au projet Laravel; ajouter PHPMailer n’éliminerait pas le besoin d’un transport d’envoi. Sur un hébergement disposant d’un serveur de messagerie local, `sendmail` peut être utilisable; sinon configurez le SMTP du fournisseur. Les identifiants ne doivent jamais être enregistrés dans le code ni distribués dans le ZIP.

```dotenv
JORSID_ADMIN_EMAIL="votre-adresse@exemple.com"
JORSID_ADMIN_NAME="Kessy"
JORSID_ADMIN_LANGUAGE=de
JORSID_BUSINESS_NAME="Jorsid Beauty by Kessy"
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=serveur-smtp-de-votre-fournisseur
MAIL_PORT=587
MAIL_USERNAME="identifiant-smtp"
MAIL_PASSWORD="mot-de-passe-smtp"
MAIL_FROM_ADDRESS="adresse-expediteur-verifiee@exemple.com"
MAIL_FROM_NAME="Jorsid Beauty by Kessy"
```

Ces valeurs sont des exemples. Les valeurs exactes dépendent de l’hébergeur et du fournisseur e-mail. Pour tester sans envoyer réellement, utilisez `MAIL_MAILER=log`; dans ce mode, les messages sont enregistrés dans les journaux et ne sont pas remis aux clientes. Après modification, lancez `php artisan config:clear`.

## Vérifications importantes

La syntaxe PHP et JavaScript ainsi que l’intégrité de l’archive ont été vérifiées. Une exécution complète de Laravel, un rendu navigateur et la livraison réelle d’e-mails nécessitent votre environnement PHP, votre base de données et votre transport e-mail. Les photos de démonstration ne sont pas des photos réelles de clientes; publiez uniquement des images dont vous avez le droit et le consentement de diffusion.
