# Changelog

Toutes les modifications notables apportées à ce projet sont documentées dans ce fichier selon les recommandations [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).

## [v1.2.0] - 2026-09-09
### Corrigé
- Élimination complète des débordements horizontaux (overflow) sur mobile et desktop dans la liste des salles (`/salles`) et la liste des réservations (`/reservations`).
- Application de `min-width: 0; width: 100%` sur les conteneurs flex (`.container`, `.main-content`) pour neutraliser le débordement causé par les tables de données.
- Amélioration responsive des tableaux avec `.cell-motif` (troncature avec infobulle native), retour à la ligne automatique des métadonnées et des adresses email (`.cell-meta .cell-sub`), et badges temporels sans rupture inopinée (`white-space: nowrap`).
- Réorganisation responsive des formulaires de filtrage en grilles CSS adaptatives (`.filter-grid`, `.filter-card`).

### Ajouté
- Support du rendu multiformat unifié (HTML / JSON) configurable globalement via `APP_RESPONSE_FORMAT=html|json` dans le `.env` ou surchargé à la volée via le paramètre d'URL `?format=json` ou `?format=html`.
- Sérialisation automatique et robuste dans `ViewRenderer` des modèles Eloquent, paginators et collections vers du JSON structuré (`format`, `view`, `data`).
- Commutateur visuel de format (badge "Format : HTML | JSON") dans la barre de navigation pour un basculement immédiat.
- Gestion unifiée des erreurs 404, 405 et 500 retournant un JSON normalisé lorsque le format JSON est sélectionné.
- Suite de tests complète pour le multiformat (`Tests\Http\MultiformatHttpTest`), portant la couverture totale à 61 tests et 193 assertions réussis à 100%.

## [v1.1.1] - 2026-09-09
### Corrige
- Decouplage du port MySQL hote (`FORWARD_DB_PORT`) et du port interne Docker (`DB_PORT=3306`) dans `docker-compose.yml` et `docker-compose.hub.yml` pour eviter tout conflit de port 3306 sur la machine hote.
- Support de `APP_PORT` parametrable pour l'exposition web dans `docker-compose.yml`, `docker-run.sh` et `docker-run-hub.sh`.
- Resolution robuste des variables d'environnement dans `config/database.php` combinant `$_ENV` et `getenv()`.
- Inclusion de la migration `03_create_users_table.php` et du peuplement des utilisateurs initiaux dans le CLI `youssou:migrate` et `youssou:seed`.

## [v1.1.0] - 2026-09-09
### Ajoute
- Authentification securisee avec hachage bcrypt, gestion de session et controle d'acces base sur les roles (RBAC: Admin, Responsable).
- Boutons de connexion rapide sur la page de connexion pour basculer en un clic entre les profils Administrateur et Responsable.
- Protection CSRF synchronizer token avec jetons cryptographiques de 32 octets, validation en temps constant et inclusion dans tous les formulaires POST.
- Gestion de la concurrence avec transactions ACID et verrouillage pessimiste lockForUpdate lors de la creation et de l'annulation des reservations.
- Recherche multi-criteres pour les salles (nom, type, capacite minimale) et pour les reservations (salle, statut, responsable).
- Pagination parametree avec la classe Paginator, navigation accessible et conservation des criteres de filtrage dans les URLs.
- Tableau de bord de supervision (/dashboard) avec indicateurs cles (KPIs), top 5 des salles les plus sollicitees, repartition par type et prochaines reservations.
- API REST JSON complete (/api/salles, /api/reservations, /api/dashboard) avec negociation de contenu, pagination et gestion des erreurs normalisee.
- Architecture de pipeline de middlewares HTTP (LoggingMiddleware, CsrfMiddleware, AuthMiddleware).
- Journalisation structuree dans storage/logs/app.log enregistrant requetes HTTP, operations metier et exceptions.
- Pipeline d'integration continue GitHub Actions (.github/workflows/ci.yml) validant Composer et executant PHPUnit sur PHP 8.2 et 8.3.
- Suite de tests etendue atteignant 50 tests et 148 assertions (tests unitaires de services, tests d'integration et tests fonctionnels HTTP).
- Documentation d'architecture enrichie (Section 15 dans ARCHITECTURE.md, guide utilisateur dans README.md).

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
