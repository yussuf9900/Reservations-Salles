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

- **Application Web** : accessible sur **http://localhost:8080** (personnalisable via `APP_PORT` dans `.env`)
- **Base MySQL** : accessible sur **127.0.0.1:3306** (personnalisable via `FORWARD_DB_PORT` dans `.env`, utilisateur `root`, sans mot de passe)
- Les migrations (`php youssou:migrate`) et le jeu de données initial (`php youssou:seed`) s'exécutent automatiquement au démarrage.
- Toutes les données sont conservées durablement dans le volume Docker `db_data`.

##### En cas de conflit de ports sur la machine hôte :
Si le port `3306` (MySQL local) ou `8080` est déjà utilisé sur votre machine, modifiez simplement les ports exposés dans votre fichier `.env` :
```env
APP_PORT=8081
FORWARD_DB_PORT=3307
```
> **Remarque importante :** Dans Docker, le port d'écoute interne du conteneur MySQL reste toujours `3306`. `FORWARD_DB_PORT` configure uniquement le port d'accès depuis votre machine hôte, sans perturber la communication interne entre l'application PHP et la base de données.

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

### Exécuter l'Application Directement depuis Docker Hub (avec MySQL)

> **Important :** L'image publiée sur Docker Hub (`devyussuf/reservations-salles`) est une image applicative (PHP 8.3 + Apache + Code). Conformément aux **bonnes pratiques Docker** ("un conteneur = une responsabilité"), elle n'embarque pas le serveur de base de données. Elle a donc besoin d'un conteneur MySQL 8.0 pour fonctionner.

#### Méthode 1 : Via Docker Compose Hub (Recommandé - 1 commande)
Sans cloner le code source, téléchargez simplement le fichier `docker-compose.hub.yml` (ou utilisez le script dédié) :

```bash
# Avec le script fourni
./docker-run-hub.sh devyussuf/reservations-salles:latest

# Ou manuellement via Docker Compose
DOCKER_IMAGE=devyussuf/reservations-salles:latest docker compose -f docker-compose.hub.yml up -d
```
- L'application est immédiatement accessible sur **http://localhost:8080**
- Le conteneur MySQL 8.0 officiel est automatiquement téléchargé et lié.
- Les migrations et seeders sont exécutés au démarrage.

#### Méthode 2 : Sans Docker Compose (avec `docker run`)
Si vous préférez exécuter les conteneurs manuellement avec la CLI Docker :

```bash
# 1. Créer un réseau Docker partagé
docker network create univ_net

# 2. Démarrer le conteneur MySQL
docker run -d \
  --name reservation_salles_db \
  --network univ_net \
  -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
  -e MYSQL_DATABASE=reservation_salles \
  -v db_data:/var/lib/mysql \
  mysql:8.0

# 3. Démarrer l'application connectée au réseau et à la base
docker run -d \
  --name reservation_salles_app \
  --network univ_net \
  -p 8080:80 \
  -e DB_HOST=reservation_salles_db \
  devyussuf/reservations-salles:latest
```

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

## Fonctionnalités Bonus (Partie 14)

L'application intègre l'ensemble des 14 fonctionnalités et optimisations prévues par le sujet pédagogique :

### 1. Authentification & Rôles
- Authentification par session avec mots de passe hachés via `PASSWORD_BCRYPT`.
- Deux rôles préconfigurés :
  - **Administrateur** : `admin@univ.sn` / mot de passe : `admin123` (gestion complète des salles, modifications, dashboard).
  - **Responsable** : `prof@univ.sn` / mot de passe : `prof123` (consultation et réservation simplifiée avec auto-complétion).

### 2. Boutons d'Authentification Rapide
Sur la page `/login`, deux boutons permettent de se connecter immédiatement en un clic en tant qu'Administrateur ou Responsable pour faciliter les démonstrations sans saisie manuelle.

### 3. Tableau de Bord & Statistiques (`/dashboard`)
- Vue analytique complète : total des salles, réservations actives, volume horaire et taux de fréquentation.
- **Top 5 des salles les plus utilisées** : classement ordonné par nombre de créneaux et cumul d'heures réelles d'occupation.
- Répartition par type de salle et affichage prévisionnel des 5 prochaines réservations à venir.

### 4. Recherche Multicritère & Pagination
- Sur `/salles` : filtre textuel (nom/bâtiment), filtre par type, seuil de capacité minimale et statut d'activité.
- Sur `/reservations` : filtre par salle, par statut (confirmée/annulée), par mot-clé (responsable/motif) et par date de créneau.
- Pagination paramétrable (`App\Pagination\Paginator`) avec conservation des paramètres de filtrage actifs.

### 5. Sécurité & Middlewares
- **Prévention CSRF** : génération de jetons de session cryptographiques et validation sur chaque requête POST web.
- **Pipeline de Middlewares** : `LoggingMiddleware`, `CsrfMiddleware`, `AuthMiddleware` orchestrés dans `Application`.
- **Journalisation structurée** : enregistrement de chaque action et anomalie dans `storage/logs/app.log`.

### 6. Concurrence & Transactions ACID
- Encapsulation des réservations et annulations dans des transactions de base de données.
- Verrouillage transactionnel pessimiste (`SELECT ... FOR UPDATE`) sur la salle lors de la création pour empêcher deux réservations simultanées conflictuelles.

### 7. API JSON REST
Endpoints disponibles sous le préfixe `/api/` :
- `GET /api/salles` : liste paginée et filtrée des salles.
- `GET /api/salles/{id}` : détail d'une salle avec liste de ses réservations.
- `POST /api/salles` : création d'une salle (retourne 201 Created ou 422).
- `GET /api/reservations` : liste paginée et filtrée des réservations.
- `GET /api/reservations/{id}` : détail d'une réservation.
- `POST /api/reservations` : création d'une réservation avec détection de conflit (201 ou 422).
- `POST /api/reservations/{id}/cancel` : annulation d'une réservation.
- `GET /api/stats` : exportation des données du tableau de bord.

### 8. Rendu Multiformat Unifié (HTML / JSON)
Toutes les pages de l'application peuvent être rendues en format HTML traditionnel ou en format JSON structuré :
- **Configuration globale via `.env`** : `APP_RESPONSE_FORMAT=html` (défaut) ou `APP_RESPONSE_FORMAT=json`.
- **Surcharge à la volée via l'URL** : ajoutez `?format=json` ou `?format=html` sur n'importe quelle page (`/salles?format=json`, `/reservations?format=json`).
- **Commutateur dans l'interface** : un badge interactif dans la barre de navigation permet de basculer instantanément entre HTML et JSON.
- **Réponse JSON normalisée** : contient `success: true`, `format: "json"`, `view: string` et le payload de données sérialisé sous `data: { ... }`.

### 9. Administration de la Base de Données (phpMyAdmin)
L'infrastructure conteneurisée inclut une instance officielle de **phpMyAdmin** prête à l'emploi :
- **URL d'accès** : `http://localhost:8081` (configurable via `PMA_PORT` dans `.env`).
- **Connexion simplifiée** : serveur `database`, utilisateur `root`, mot de passe vide.
- **Raccourci Administrateur** : un bouton d'accès direct vers phpMyAdmin est présent dans le Tableau de Bord (`/dashboard`) pour les administrateurs connectés.

---

## Exécution de la Suite de Tests

Pour lancer l'ensemble des 61 tests automatisés (tests unitaires métier, validation de formulaires, composants de pagination/CSRF/statistiques, tests fonctionnels HTTP et tests multiformat) :

```bash
./vendor/bin/phpunit --testdox
```

> *Les tests unitaires et HTTP utilisent des doublures en mémoire (`InMemorySalleRepository`, `InMemoryReservationRepository`) et s'exécutent instantanément sans aucune dépendance obligatoire à un serveur MySQL actif.*

---

## Structure du Répertoire

```
ReservationSalleUniversite/
├── .github/
│   └── workflows/
│       ├── ci.yml                # Intégration continue GitHub Actions (PHP 8.2 & 8.3)
│       └── docker-publish.yml    # Publication conteneurs Docker Hub
├── config/
│   ├── container.php             # Définitions PHP-DI (autowiring, middlewares, factories)
│   └── database.php              # Configuration Capsule\Manager Eloquent
├── database/
│   ├── migrations/               # Salles, réservations, utilisateurs
│   ├── migrate.php               # Exécuteur séquentiel des migrations
│   └── seed.php                  # Données initiales idempotentes (salles et comptes test)
├── public/
│   ├── assets/
│   │   └── style.css             # Feuille de style moderne et responsive
│   └── index.php                 # Point d'entrée HTTP unique (Front Controller)
├── routes/
│   └── web.php                   # Déclaration des routes Web et API (FastRoute)
├── src/
│   ├── Application.php           # Pipeline de middlewares et routeur HTTP
│   ├── Controller/               # Contrôleurs Web et sous-dossier Api/
│   │   ├── Api/                  # ApiSalleController, ApiReservationController, ApiDashboardController
│   │   ├── AuthController.php    # Connexion standard et boutons d'authentification rapide
│   │   ├── DashboardController.php
│   │   ├── ReservationController.php
│   │   └── SalleController.php
│   ├── DTO/                      # DTOs et Builders typés
│   ├── Http/                     # JsonResponse normalisée
│   ├── Middleware/               # LoggingMiddleware, CsrfMiddleware, AuthMiddleware
│   ├── Model/                    # Modèles Eloquent Salle, Reservation, User
│   ├── Pagination/               # Paginator générique
│   ├── Repository/               # Interfaces et implémentations Eloquent
│   ├── Service/                  # AuthService, CsrfService, LoggerService, StatistiquesService
│   ├── Validation/               # Validateurs Respect\Validation et ValidationResult
│   └── View/                     # ViewRenderer avec layout, helpers et icônes vectorielles SVG
├── storage/
│   └── logs/                     # Journal d'application horodaté (app.log)
├── templates/
│   ├── auth/                     # login.php (avec boutons rapides)
│   ├── dashboard/                # index.php (métriques et top 5 salles)
│   ├── error/                    # 404, 405, 500
│   ├── layout/                   # base.php
│   ├── reservation/              # index.php, show.php, form.php
│   ├── salle/                    # index.php, show.php, form.php
│   └── shared/                   # pagination.php
├── tests/
│   ├── Double/                   # Doublures en mémoire (InMemoryRepository)
│   ├── Http/                     # Tests fonctionnels HTTP (Salles, Réservations, Auth, CSRF, API)
│   ├── Integration/              # Tests d'intégration Eloquent
│   ├── Unit/                     # Tests unitaires métier, validation, CSRF, pagination, stats
│   └── bootstrap.php             # Bootstrap PHPUnit autonome
├── ARCHITECTURE.md               # Étude théorique des 14 concepts et des 14 bonus
├── CHANGELOG.md                  # Journal des versions
├── composer.json                 # Dépendances et PSR-4
├── docker-compose.yml            # Environnement complet conteneurisé
└── README.md                     # Ce document
```

