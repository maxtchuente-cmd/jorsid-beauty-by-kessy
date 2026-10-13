# Jorsid Beauty by Kessy — V2

## Ce qui change en V2
- Renforcement de l'identité visuelle violet / rose / bleu ciel et ajout de sections éditoriales plus riches.
- Animations d'entrée, révélations progressives au défilement, micro-interactions sur les cartes, boutons et formulaires.
- Respect de `prefers-reduced-motion` pour les personnes qui préfèrent réduire les animations.
- Traduction DE / FR / EN étendue au contenu des pages publiques, aux libellés de réservation, aux textes de contact et au pied de page ; la langue sélectionnée est conservée localement et le changement peut être répété sans recharger la page.
- Traductions de démonstration pour les prestations de départ dans les cartes et dans le sélecteur de réservation.
- Pages À propos et Contact enrichies et mise en page responsive améliorée.

## Démarrage
1. Copier `.env.example` vers `.env` et renseigner la base de données.
2. `composer install`
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `npm ci`
6. `npm run build` pour compiler les ressources, ou `npm run dev` pendant le développement.
7. `php artisan serve`

## Limites explicites à ce stade
- Les traductions des prestations initiales sont des libellés front-end de démonstration. Pour un catalogue administrable multilingue complet, il faudra ajouter des champs de traduction (ou des tables de traductions) et les écrans de gestion correspondants.
- Le formulaire Contact est encore une maquette et ne transmet pas de message.
- La disponibilité des rendez-vous n'est pas encore calculée à partir du calendrier administrateur ; ce chantier est prévu dans la version dédiée à la réservation.
- Les coordonnées, horaires, règles de déplacement et tarifs définitifs doivent être configurés avant mise en production.
