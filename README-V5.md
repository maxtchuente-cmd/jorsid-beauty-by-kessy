# Jorsid Beauty by Kessy — V5 (notifications et agenda sécurisés)

Cette version part de V2+V3+V4 et ajoute le cycle de notification de réservation ainsi que le contrôle des rendez-vous lorsqu'une date doit être fermée.

## Ce qui est ajouté

- **Une seule adresse de notification pour Kessy** : configurez `JORSID_ADMIN_EMAIL` dans `.env`. Le code récupère cette valeur depuis `config/jorsid.php` plutôt que de répéter l'adresse dans plusieurs contrôleurs.
- **À chaque nouvelle demande** : création d'une notification persistante dans l'administration, compteur de notifications non lues, alerte visuelle dans l'administration (contrôle toutes les 30 secondes), e-mail détaillé à Kessy et accusé de réception à la cliente. Le message à la cliente précise que la demande reste en attente jusqu'à confirmation.
- **Après décision de Kessy** : lorsque le statut passe à confirmé, refusé ou annulé, la cliente reçoit un e-mail distinct avec la référence, la prestation, la date, l'heure, le lieu et le montant. Une notification est aussi enregistrée dans l'application.
- **Fermeture d'une date** : dans Administration → Horaires & congés, choisir une date déclenche une vérification des rendez-vous existants. Si des rendez-vous actifs sont trouvés, il faut choisir explicitement « refuser » ou « annuler » avant de fermer la date. Les clientes concernées sont informées et le système garde un historique des notifications.
- **Protection des réservations** : le contrôle des créneaux reste côté serveur avec transaction/verrou par date pour prévenir les doubles réservations concurrentes.

## Installation / mise à jour

```bash
composer install
cp .env.example .env   # uniquement pour une nouvelle installation
php artisan key:generate
# Renseignez DB_* dans .env
php artisan migrate --seed
php artisan config:clear
php artisan jorsid:create-admin
npm install
npm run build
php artisan storage:link
```

Sur une installation existante, sauvegardez d'abord la base et le projet, déployez les fichiers puis exécutez `php artisan migrate` et `php artisan config:clear`. Ne remplacez pas votre `.env` existant.

## Configurer l'adresse e-mail de Kessy

Dans le fichier `.env`, renseignez une seule fois :

```dotenv
JORSID_ADMIN_EMAIL="votre-adresse@exemple.com"
JORSID_ADMIN_NAME="Kessy"
JORSID_BUSINESS_NAME="Jorsid Beauty by Kessy"
```

Configurez aussi le SMTP du fournisseur de messagerie. Exemple de variables à renseigner avec les valeurs réelles fournies par votre hébergeur :

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=serveur-smtp-fourni-par-votre-hebergeur
MAIL_PORT=587
MAIL_USERNAME="votre-identifiant-smtp"
MAIL_PASSWORD="votre-mot-de-passe-smtp"
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS="votre-adresse-d-envoi@exemple.com"
MAIL_FROM_NAME="Jorsid Beauty by Kessy"
```

Les valeurs ci-dessus sont des exemples de noms de variables, pas des identifiants SMTP fonctionnels. Ne mettez jamais votre mot de passe dans le code ou dans le ZIP. Après modification : `php artisan config:clear` (ou `php artisan config:cache` en production). Tant que `MAIL_MAILER=log`, les e-mails sont écrits dans les journaux et ne sont pas réellement livrés.

## Alertes et agenda

- Les notifications sont persistées dans la table `admin_notifications` et peuvent être marquées comme lues.
- Le compteur et l'alerte d'administration sont actualisés périodiquement pendant que l'interface est ouverte. Ce mécanisme est du polling toutes les 30 secondes, pas une connexion WebSocket.
- La fermeture d'une date refuse les créneaux publics et demande une décision si des rendez-vous `pending` ou `confirmed` existent.
- `rejected` est un statut de refus distinct de `cancelled`.

## Rappels automatiques

Le rappel de la veille déjà présent reste piloté par le planificateur Laravel. En production, configurez cron pour exécuter `php artisan schedule:run` chaque minute :

```cron
* * * * * cd /chemin/vers/jorsid-beauty-by-kessy && php artisan schedule:run >> /dev/null 2>&1
```

## Vérifications et limites

La syntaxe PHP et JavaScript ainsi que l'intégrité de l'archive sont contrôlées avant livraison. Les tests complets de Laravel, la compilation Vite, l'envoi SMTP réel et les scénarios navigateur doivent être exécutés sur un environnement avec les dépendances installées et les identifiants SMTP réels. Ne considérez pas l'e-mail comme testé en production avant un essai réel vers l'adresse de Kessy et une adresse cliente de test.
