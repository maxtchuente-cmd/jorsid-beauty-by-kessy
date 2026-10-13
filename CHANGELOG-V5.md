# V5 — Cycle de réservation, notifications et fermeture d’agenda

- Centralisation de l’adresse de Kessy dans `config/jorsid.php` via `JORSID_ADMIN_EMAIL`.
- Enregistrement des notifications administratives dans `admin_notifications`.
- Alerte dans l’administration avec compteur actualisé toutes les 30 secondes.
- E-mail détaillé à Kessy et accusé de réception à la cliente dès la création d’une demande.
- E-mail distinct à la cliente et à Kessy lors d’une confirmation, d’un refus ou d’une annulation.
- Vérification en direct des rendez-vous avant fermeture d’une date et choix obligatoire entre refuser et annuler quand des rendez-vous sont présents.
- Mise à jour atomique des rendez-vous et de la fermeture de date, puis envoi des e-mails après validation de la transaction.
- Nouveau statut `rejected` pour distinguer un refus d’une annulation.
