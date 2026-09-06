# Gestion des Réservations de Salles Universitaires

Application web développée en PHP 8.3 orienté objet sans framework complet, avec des composants spécialisés (FastRoute, Eloquent, PHP-DI, Respect\Validation) pour la gestion et la réservation de salles à l'université.

## Architecture
- **Front Controller** : `public/index.php`
- **Routage** : `nikic/fast-route`
- **Conteneur d'injection de dépendances** : `php-di/php-di`
- **ORM** : `illuminate/database` (Capsule)
- **Validation** : `respect/validation`
- **Tests** : `phpunit/phpunit`

Documentation détaillée disponible dans [ARCHITECTURE.md](ARCHITECTURE.md).
