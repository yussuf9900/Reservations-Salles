# Changelog

Toutes les modifications notables apportées à ce projet sont documentées dans ce fichier selon les recommandations [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).

## [v1.0.0] - 2026-09-06
### Ajouté
- Style CSS soigné, moderne, responsive et accessible dans `public/assets/style.css`.
- Page d'erreur 500 pour la gestion des exceptions non interceptées.
- Document d'analyse complet `ARCHITECTURE.md` couvrant les 14 notions et les réponses à toutes les questions pédagogiques.
- `README.md` exhaustif avec instructions pas-à-pas et diagrammes.
- Validation des 8 scénarios de recette.

## [v0.12.0] - 2026-09-06
### Ajouté
- Suite de tests automatisés avec PHPUnit (`phpunit.xml`).
- Doublures mémoire `InMemorySalleRepository` et `InMemoryReservationRepository`.
- 8 tests unitaires couvrant l'ensemble des règles métier de `CreerReservationService`.
- 5 tests unitaires pour la validation syntaxique (`ValidationTest`).
- 4 tests d'intégration avec base de données et Eloquent (`EloquentIntegrationTest`).

## [v0.11.0] - 2026-09-06
### Ajouté
- Configuration du conteneur d'injection de dépendances PHP-DI dans `config/container.php`.
- Point d'entrée unique `public/index.php` (Front Controller).
- Résolution par constructeur et autowiring sans anti-pattern Service Locator.

## [v0.10.0] - 2026-09-06
### Ajouté
- Configuration des routes avec `nikic/fast-route` dans `routes/web.php`.
- Gestion des routes paramétrées avec contraintes regex (`{id:\d+}`).
- Gestion des codes HTTP 404 (Not Found) et 405 (Method Not Allowed avec en-tête `Allow`).

## [v0.9.0] - 2026-09-06
### Ajouté
- Moteur de rendu `ViewRenderer` avec support des layouts, protection XSS (`htmlspecialchars`) et messages flash.
- Contrôleurs HTTP `SalleController` et `ReservationController`.
- Templates HTML5 pour les salles, les réservations et les pages d'erreur.

## [v0.8.0] - 2026-09-06
### Ajouté
- Services métier : `CreerReservationService` et `AnnulerReservationService`.
- Algorithme de détection de chevauchement : `nouveauDebut < existante.dateFin && nouvelleFin > existante.dateDebut`.
- Exceptions de domaine : `SalleIndisponibleException` et `ReservationIntrouvableException`.

## [v0.7.0] - 2026-09-06
### Ajouté
- Interfaces d'accès aux données : `SalleRepositoryInterface` et `ReservationRepositoryInterface`.
- Implémentations concrètes Eloquent : `EloquentSalleRepository` et `EloquentReservationRepository`.
- Isolation absolue des requêtes de persistance hors des contrôleurs.

## [v0.6.0] - 2026-09-06
### Ajouté
- Objets de transfert de données : `CreerSalleDTO` et `CreerReservationDTO`.
- Conversion et typage strict des dates avec `DateTimeImmutable`.

## [v0.5.0] - 2026-09-06
### Ajouté
- Contrat de validation `ValidatorInterface` et objet de retour `ValidationResult`.
- Validateurs `SalleValidator` et `ReservationValidator` utilisant `respect/validation`.

## [v0.4.0] - 2026-09-06
### Ajouté
- Script de peuplement initial idempotent `database/seed.php`.
- Ajout des 5 salles de référence sans duplication.

## [v0.3.0] - 2026-09-06
### Ajouté
- Modèles Eloquent `App\Model\Salle` et `App\Model\Reservation`.
- Définition des relations `hasMany` et `belongsTo`.
- Configuration des attributs assignables (`$fillable`) et des conversions (`$casts`).

## [v0.2.0] - 2026-09-06
### Ajouté
- Configuration d'Eloquent autonome avec `Capsule\Manager` dans `config/database.php`.
- Variables d'environnement dans `.env.example`.
- Définition des tables `salles` et `reservations` dans `database/migrations/`.
- Fichier `docker-compose.yml` pour le service MySQL 8.0.

## [v0.1.0] - 2026-09-06
### Ajouté
- Fichier `composer.json` et configuration de l'autoloading PSR-4 (`App\` et `Tests\`).
- Installation des dépendances du projet et génération de `composer.lock`.
- Classe minimale `App\Application`.

## [v0.0.0] - 2026-09-06
### Initialisation
- Initialisation du dépôt Git avec la branche `main`.
- Fichier `.gitignore` configuré.
- Structure initiale de documentation.
