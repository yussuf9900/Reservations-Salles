# Système de Gestion des Réservations de Salles Universitaires

Application web complète développée dans le cadre du projet **ODC-P8 (PHP Orienté Objet)**. Le système permet la consultation, l'administration des salles de cours et la réservation de créneaux sans doublons ni chevauchements.

L'application est construite **sans framework complet**, en assemblant des composants spécialisés de haute qualité avec **Composer** selon une **architecture en couches**, les principes **SOLID** et le patron **Inversion de Contrôle**.

---

## Architecture & Composants Clés

- **Front Controller & Bootstrap** : `public/index.php` et `App\Application`
- **Routeur HTTP** : `nikic/fast-route` (avec gestion explicite des erreurs 404 et 405 avec en-tête `Allow`)
- **Conteneur d'Injection de Dépendances (IoC)** : `php-di/php-di` (autowiring, injection par constructeur)
- **Couche Persistance (ORM)** : `illuminate/database` (Eloquent autonome via `Capsule\Manager`)
- **Couche Données Découplée** : Patron `Repository` avec interfaces et implémentations en mémoire pour les tests
- **Validation Formulaires** : `respect/validation` avec contrat commun `ValidatorInterface` et `ValidationResult`
- **Transport de Données** : `DTO` (Data Transfer Objects) immuables et typés
- **Règles Métier** : Services isolés (`CreerReservationService`, `AnnulerReservationService`)
- **Suite de Tests** : `phpunit/phpunit` (tests unitaires en mémoire indépendants de MySQL + tests d'intégration)

> Pour une analyse approfondie des choix techniques, des 14 concepts requis et des réponses aux questions de cours, consultez le document **[ARCHITECTURE.md](ARCHITECTURE.md)**.

---

## Prérequis

- **PHP 8.2 ou 8.3** avec les extensions : `pdo`, `pdo_mysql`, `mbstring`, `intl`
- **Composer 2.x**
- **Docker & Docker Compose** (ou un serveur MySQL 8.0 local)

---

## Guide d'Installation Pas-à-Pas

### 1. Cloner le dépôt et se placer dans le projet
```bash
git clone <url-du-depot>
cd ReservationSalleUniversite
```

### 2. Installer les dépendances Composer
```bash
composer install
```

### 3. Configurer les variables d'environnement
Copiez le fichier d'exemple `.env.example` vers `.env` :
```bash
cp .env.example .env
```
Par défaut, le fichier est configuré pour se connecter au port `3306` de MySQL avec l'utilisateur `root` et la base `reservation_salles`.

### 4. Démarrer avec Docker Compose (Architecture 2 conteneurs)

L'application est orchestrée via `docker-compose.yml` avec 2 conteneurs dédiés :
1. **`app`** : Serveur web Apache + PHP 8.3 exécutant l'application UnivSalles.
2. **`database`** : Serveur MySQL 8.0 officiel avec volume persistant `db_data`.

#### Lancement en une commande (Recommandé)
```bash
docker compose up -d --build
```
ou via le script dédié :
```bash
./docker-run.sh
```

- **Application Web** : accessible sur **http://localhost:8080**
- **Base MySQL** : accessible sur **127.0.0.1:3306** (utilisateur `root`, sans mot de passe)
- Les migrations (`php youssou:migrate`) et le jeu de données initial (`php youssou:seed`) s'exécutent automatiquement au démarrage.
- Toutes les données sont conservées durablement dans le volume Docker `db_data`.

---

### Déploiement Continu & Synchronisation des Tags Docker Hub

Un workflow GitHub Actions (`.github/workflows/docker-publish.yml`) publie automatiquement l'image Docker applicative sur **Docker Hub** et **GitHub Container Registry (GHCR)** à chaque nouveau tag Git :

```bash
# Release automatique d'une nouvelle version
git tag v1.0.2
git push origin v1.0.2
```

Le workflow génère automatiquement les tags Docker synchronisés :
- `devyussuf/reservations-salles:v1.0.2`
- `devyussuf/reservations-salles:1.0.2`
- `devyussuf/reservations-salles:latest`

#### Synchroniser l'ensemble des tags Git historiques d'un seul coup
Pour propager l'intégralité des tags Git existants (`v0.0.0` à `v1.0.1`) vers Docker Hub en une seule commande :
```bash
./scripts/docker-push-all-tags.sh devyussuf
```
*(Cette opération peut également être déclenchée à la demande depuis l'onglet **Actions** de GitHub via le bouton **Run workflow**).*

> **Secrets GitHub requis :** `DOCKER_USERNAME` et `DOCKER_PASSWORD` configurés dans les Secrets Actions du dépôt.

---

### 5. Exécuter les migrations (Création des tables)
```bash
php database/migrate.php
```
Cette commande crée les tables `salles` et `reservations` avec leurs contraintes d'intégrité et index.

### 6. Peupler la base avec les données initiales (Idempotent)
```bash
php database/seed.php
```
Cette commande initialise les 5 salles requises (Amphithéâtre A, Salle B12, Laboratoire Chimie, Salle Informatique 1, Salle de réunion) sans créer de doublons lors des exécutions répétées.

---

## Lancement du Serveur de Développement

Lancez le serveur web intégré de PHP en pointant le document root sur le dossier `public` :

```bash
php -S localhost:8000 -t public
```

Rendez-vous ensuite sur votre navigateur à l'adresse :  
**http://localhost:8000** (ou directement sur **http://localhost:8000/salles**)

---

## Exécution de la Suite de Tests

Pour lancer l'ensemble des 17 tests (unitaires, validation et intégration) :

```bash
./vendor/bin/phpunit --testdox
```

### Détail de la couverture des tests :
- **Tests unitaires du service métier** :
  1. Réservation valide confirmée
  2. Salle inexistante refusée
  3. Salle inactive refusée
  4. Date de fin antérieure au début refusée
  5. Durée supérieure à 4 heures refusée
  6. Date passée refusée
  7. Conflit de chevauchement refusé
  8. Réservations voisines contiguës acceptées
- **Tests unitaires de validation** :
  - Email invalide, responsable vide, capacité négative, type inconnu, date incorrecte
- **Tests d'intégration Eloquent** :
  - Création de salle, relations Modèle, recherche de chevauchement SQL et annulation

> *Les tests unitaires utilisent des doublures en mémoire (`InMemorySalleRepository`, `InMemoryReservationRepository`) et s'exécutent instantanément sans aucune dépendance à MySQL.*

---

## Structure du Répertoire

```
ReservationSalleUniversite/
├── config/
│   ├── container.php         # Définitions PHP-DI (autowiring, factories)
│   └── database.php          # Configuration Capsule\Manager Eloquent
├── database/
│   ├── migrations/           # Définitions des tables DDL
│   ├── migrate.php           # Script d'exécution des migrations
│   └── seed.php              # Seeder des 5 salles initiales (idempotent)
├── public/
│   ├── assets/
│   │   └── style.css         # Feuille de style moderne et responsive
│   └── index.php             # Point d'entrée HTTP unique (Front Controller)
├── routes/
│   └── web.php               # Déclaration des routes (FastRoute)
├── src/
│   ├── Application.php       # Chef d'orchestre HTTP et dispatching
│   ├── Controller/           # SalleController, ReservationController
│   ├── DTO/                  # CreerSalleDTO, CreerReservationDTO
│   ├── Exception/            # SalleIndisponibleException, ReservationIntrouvableException
│   ├── Model/                # Modèles Eloquent Salle et Reservation
│   ├── Repository/           # Interfaces et implémentations Eloquent
│   ├── Service/              # Services de création et d'annulation métier
│   ├── Validation/           # Validateurs Respect\Validation et ValidationResult
│   └── View/                 # ViewRenderer avec layout, escape et flash
├── templates/
│   ├── error/                # 404, 405, 500
│   ├── layout/               # base.php
│   ├── reservation/          # index.php, show.php, form.php
│   └── salle/                # index.php, show.php, form.php
├── tests/
│   ├── Double/               # Doublures mémoire InMemoryRepository
│   ├── Integration/          # Tests d'intégration avec base Eloquent
│   ├── Unit/                 # Tests unitaires métier et validation
│   └── bootstrap.php         # Bootstrap PHPUnit sans dépendance MySQL
├── ARCHITECTURE.md           # Étude théorique et réponses aux questions
├── CHANGELOG.md              # Journal des versions et évolutions
├── composer.json             # Dépendances et PSR-4
├── docker-compose.yml        # Service MySQL 8.0
└── README.md                 # Ce document
```
