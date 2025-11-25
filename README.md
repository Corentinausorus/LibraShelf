# LibraShelf

Application de gestion de bibliothèque développée avec Symfony 6.4/7.x et PHP ≥ 8.2.

## Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#️-configuration)
- [Lancement de l'application](#-lancement-de-lapplication)
- [Utilisation](#-utilisation)
- [Tests](#-tests)
- [Qualité du code](#-qualité-du-code)
- [Architecture](#-architecture)
- [Sécurité](#-sécurité)

## Fonctionnalités

### Gestion du catalogue
- **Ouvrages** : titre, auteurs multiples, éditeur, ISBN/ISSN, catégories, tags, langues, année de publication, résumé
- **Exemplaires** : cote, état physique, disponibilité en temps réel
- **Recherche avancée** : filtres par titre, catégories, langues, année, disponibilité
- **Gestion multi-auteurs** : association flexible d'auteurs aux ouvrages

### Système d'emprunt et réservations
- **Flux d'emprunt** : création, suivi, retour, gestion des retards
- **Réservations** : file d'attente avec priorité par ancienneté
- **Pénalités automatiques** : calcul basé sur un barème configurable
- **Règles métier** : durée d'emprunt par catégorie, nombre maximum d'emprunts simultanés

### Notifications automatiques
- **Rappels d'emprunt** : envoi à J-3, J0 (échéance), J+7 (retard)
- **Confirmations de réservation** : notification lors de la réservation
- **Disponibilité** : email lorsqu'un ouvrage réservé devient disponible
- **Traitement asynchrone** : envoi via messenger/queue

### Planification (Scheduler)
- **Rappels batch** : envoi automatique des emails de rappel
- **Purge des données** : nettoyage automatique des emprunts après 30 jours
- **Gestion des logs** : cycle de vie limité à 50 jours maximum

### Gestion des rôles et sécurité
- **Rôles** : `ROLE_ADMIN`, `ROLE_LIBRARIAN`, `ROLE_MEMBER`
- **Authentification** : session Symfony classique avec cycle de vie des mots de passe
- **RBAC fin** : voters personnalisés pour toutes les actions sensibles
- **Protection** : CSRF tokens, rate limiting, headers HTTP sécurisés

### Interfaces utilisateur
- **Back-office** : gestion complète du catalogue, usagers, emprunts (Twig)
- **Tableau de bord KPI** : pourcentage de livres empruntés, délais moyens
- **Recherche publique** : interface accessible et responsive
- **Accessibilité** : respect des standards WCAG

### Audit et conformité
- **Journal d'audit immuable** : traçabilité complète (qui/quoi/quand) via logs
- **Protection des données** : cycle de vie contrôlé des données sensibles
- **Validation stricte** : contraintes personnalisées (ISBN, objets valeur)

## Prérequis

- PHP ≥ 8.2
- Composer
- Symfony CLI (recommandé)
- PostgreSQL/MySQL ≥ 8.0 / SQLite (dev)
- Node.js & npm (si front séparé)
- Extension PHP : `pdo`, `intl`, `opcache`, `apcu`

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/Corentinausorus/LibraShelf.git
cd LibraShelf
```

### 2. Installer les dépendances

```bash
composer install
npm install  # Si utilisation d'assets front
```

### 3. Configurer l'environnement

Copier le fichier `.env` et ajuster les paramètres :

```bash
cp .env .env.local
```

Éditer `.env.local` :

```env
# Base de données
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"

# Messenger (async)
MESSENGER_TRANSPORT_DSN=doctrine://default

# APP
APP_ENV=dev
APP_SECRET=votre_secret_genere

LIBRARIAN_INVITE_CODE=BIBLIO2025SECRET
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test

**Utiliser les fixtures Doctrine**

```bash
php bin/console doctrine:fixtures:load
```

### 6. Créer un utilisateur admin

Ou utiliser les fixtures qui créent automatiquement :
- Admin : `admin@librashelf.local` / `admin123`
- Librarian : `librarian1@librashelf.local` / `librarian123`
- Member : utiliser un des emails générés (ex: voir en base) / `member123`

## Configuration

### Règles métier

Configurer les durées d'emprunt par catégorie dans `config/services.yaml` :

```yaml
parameters:
    emprunt.durees:
        Roman: 21
        Science: 14
        Informatique: 30
        default: 14
    
    emprunt.max_simultanes: 5
    
    penalites.bareme:
        par_jour: 0.50
        max: 50.00
```

## Lancement de l'application

### Développement

**Option 1 : Symfony CLI (recommandé)**

```bash
symfony server:start
```

Application disponible sur `https://127.0.0.1:8000`

**Option 2 : Serveur PHP intégré**

```bash
php -S localhost:8000 -t public/
```

### Worker Messenger (pour emails asynchrones)

Dans un terminal séparé :

```bash
php bin/console messenger:consume async -vv
```

### Scheduler (tâches planifiées)

```bash
php bin/console messenger:consume scheduler_default -vv
```

### Production

```bash
# Build assets
npm run build

# Optimisations
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Lancer avec un serveur web (Nginx/Apache + PHP-FPM)
```

### Docker (bonus)

```bash
docker-compose up -d
```

Services disponibles :
- App : `http://localhost:8000`
- Database : `localhost:5432`
- MailCatcher : `http://localhost:1080`

## Utilisation

### Connexion

- **Admin** : accès complet (gestion utilisateurs, configuration, statistiques)
- **Librarian** : gestion catalogue, emprunts, réservations
- **Member** : recherche, emprunts personnels, réservations

### Workflows principaux

**1. Créer un ouvrage (Librarian/Admin)**
- Menu : Catalogue → Nouvel ouvrage
- Renseigner titre, auteurs, ISBN, catégories
- Ajouter exemplaires avec cotes et états

**2. Effectuer un emprunt (Librarian)**
- Menu : Emprunts → Nouvel emprunt
- Sélectionner membre et exemplaire disponible
- Date de retour calculée automatiquement selon les règles

**3. Réserver un ouvrage (Member)**
- Rechercher l'ouvrage
- Cliquer sur "Réserver"
- Notification email envoyée lors de la disponibilité

**4. Gérer les retours (Librarian)**
- Menu : Emprunts → Liste
- Marquer comme retourné
- Pénalités calculées automatiquement si retard

**5. Consulter les statistiques (Admin)**
- Menu : Tableau de bord
- KPI : taux d'emprunt, délais moyens, réservations en attente

## Tests

### Tests unitaires

```bash
php bin/phpunit tests/Unit
```

### Tests d'intégration

```bash
php bin/phpunit tests/Integration
```

### Tests end-to-end

```bash
php bin/phpunit tests/E2E
```

### Couverture de code

```bash
XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage
```

Rapport disponible dans `var/coverage/index.html`

## Qualité du code

### PHP-CS-Fixer

Vérifier le style :

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

Corriger automatiquement :

```bash
vendor/bin/php-cs-fixer fix
```

### PHPStan (analyse statique)

```bash
vendor/bin/phpstan analyse src tests --level=8
```

## 🏗 Architecture

### Structure

```
src/
├── Command/          # Commandes CLI (génération données, admin, scheduler)
├── Controller/       # Contrôleurs légers (délégation aux services)
├── DataFixtures/     # Fixtures Doctrine pour seeding
├── Entity/           # Entités Doctrine (Ouvrage, Auteur, Emprunt, etc.)
├── Form/             # Formulaires Symfony
├── Repository/       # Repositories Doctrine
├── Security/         # Voters, authenticators
│   └── Voter/       # OuvrageVoter, EmpruntVoter, etc.
├── Service/          # Logique métier (ServiceReglesEmprunt, NotificationService)
├── Validator/        # Contraintes personnalisées (ISBN, etc.)
└── EventSubscriber/  # Écouteurs d'événements
```

### Principes

- **Separation of Concerns** : contrôleurs fins, logique dans les services
- **Dependency Injection** : autowiring Symfony
- **Single Responsibility** : une classe = une responsabilité
- **Voters** : centralisation des règles d'autorisation (pas de if/else dispersés)

## Sécurité

### Mesures implémentées

- **RBAC** : voters pour chaque action sensible (édition ouvrage, emprunt, etc.)
- **CSRF Protection** : tokens sur tous les formulaires
- **Rate Limiting** : sur les endpoints d'authentification et API
- **Headers HTTP** : CSP, X-Frame-Options, HSTS
- **Password Policy** : hachage bcrypt, cycle de vie, réinitialisation sécurisée
- **Validation stricte** : contraintes sur ISBN, email, objets valeur
- **Audit trail** : logs immuables (qui/quoi/quand) avec rétention 50 jours

### Configuration de sécurité

Voir `config/packages/security.yaml` pour le firewall et les access controls.

## Licence

Ce projet est un exercice académique dans le cadre d'une formation Symfony.

## Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit (`git commit -m 'Add AmazingFeature'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

