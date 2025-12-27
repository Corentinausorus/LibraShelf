# LibraShelf - Système de Gestion de Bibliothèque

## 📚 À propos

LibraShelf est une application web complète de gestion de bibliothèque développée avec Symfony. Elle permet la gestion des ouvrages, des exemplaires, des emprunts, des réservations et des utilisateurs avec un système de rôles sophistiqué.

## ✨ Fonctionnalités principales

### 🔐 Gestion des utilisateurs et authentification

- **Inscription avec codes d'invitation**
  - Membres : accès public sans code
  - Bibliothécaires : code d'invitation requis (configuré dans `.env`)
- **Validation avancée du formulaire d'inscription**
  - Validation de l'adresse e-mail (format strict)
  - Contrainte de force du mot de passe (PasswordStrength)
  - Confirmation du mot de passe
  - Validation du nom (lettres, espaces, tirets uniquement)
  - Acceptation des conditions d'utilisation obligatoire
- **Exigences de mot de passe sécurisé**
  - Minimum 8 caractères
  - Au moins une majuscule et une minuscule
  - Au moins un chiffre
  - Au moins un caractère spécial
  - Score de force minimum : moyen
- **Système de rôles** : `ROLE_MEMBER`, `ROLE_LIBRARIAN`, `ROLE_ADMIN`
- **Authentification sécurisée** avec hashage bcrypt des mots de passe
- **Profil utilisateur** personnalisé
- **Redirection automatique** selon le rôle après connexion

### 📖 Gestion du catalogue

#### Ouvrages
- **CRUD complet** des ouvrages (création, lecture, modification, suppression)
- **Informations détaillées** :
  - Titre, ISBN (unique)
  - Auteur(s) - relation Many-to-Many
  - Éditeur - relation Many-to-One
  - Catégorie(s) - relation Many-to-Many
  - Tags - relation Many-to-Many
  - Langues (stockées en JSON)
  - Année de publication
  - Résumé
  - Créé par (utilisateur bibliothécaire)

- **Recherche avancée** avec filtres multiples :
  - Par titre
  - Par catégorie
  - Par langue
  - Par année de publication
  - Par disponibilité

#### Exemplaires
- **Gestion des exemplaires physiques** de chaque ouvrage
- **Suivi de l'état** : neuf, bon, usé, endommagé, etc.
- **Système de cote** pour l'organisation physique
- **Indicateur de disponibilité** en temps réel
- **Historique d'inventaire** avec tracking des changements de statut
- **Association** ouvrage-exemplaire (1 ouvrage → N exemplaires)

#### Métadonnées
- **Auteurs** : gestion centralisée avec relations multiples
- **Éditeurs** : organisation par maison d'édition
- **Catégories** : classification thématique (roman, essai, BD, etc.)
- **Tags** : étiquettes personnalisées pour recherche avancée

### 📝 Système de réservation

- **Réservation intelligente** :
  - Attribution automatique d'un exemplaire disponible
  - Mise en file d'attente si tous les exemplaires sont empruntés
  - Statuts : "À récupérer", "En attente"
  
- **Délai de récupération** : 48 heures pour venir chercher le livre réservé

- **Gestion des réservations** :
  - Visualisation des réservations actives
  - Annulation possible par l'utilisateur
  - Libération automatique de l'exemplaire lors de l'annulation

- **Notifications par email** :
  - Confirmation de réservation (synchrone)
  - Notification de disponibilité (asynchrone via Symfony Messenger)

### 📚 Gestion des emprunts

- **Création d'emprunts** avec dates automatiques :
  - Date de début (date du jour)
  - Date de retour calculée selon les paramètres configurés
  
- **Statuts d'emprunt** : 
  - `en_cours` : emprunt actif
  - `en_retard` : date de retour dépassée
  - `retourné` : livre rendu

- **Calcul automatique de pénalités** pour les retards :
  - Montant configurable par jour de retard
  - Jours de tolérance avant application des pénalités

- **Système de rappels automatiques** :
  - **J-3** : rappel 3 jours avant la date de retour
  - **J-0** : rappel le jour de la date de retour
  - **J+7** : rappel après 7 jours de retard

### 📧 Système de notifications

- **Types de notifications** :
  - Email (via Symfony Mailer)
  - SMS (infrastructure prévue)

- **Notifications asynchrones** :
  - Système de queue avec Symfony Messenger
  - Transport configuré pour traitement différé
  - Worker dédié pour consommer les messages

- **Stockage en base de données** :
  - Historique complet de toutes les notifications envoyées
  - Type, destinataire, sujet, contenu
  - Traçabilité complète

- **Templates d'emails** personnalisés :
  - Confirmation de réservation
  - Livre disponible
  - Rappels d'emprunt (J-3, J-0, J+7)

### ⚙️ Configuration des règles d'emprunt

Paramètres configurables via l'entité `ParametreEmprunt` :
- **Durée d'emprunt** par défaut (en jours)
- **Montant de pénalité** par jour de retard (en centimes)
- **Jours de tolérance** avant application des pénalités
- **Historique** des configurations avec horodatage

### 🎨 Interface utilisateur

#### Page d'accueil publique (`/`)
- Présentation de la bibliothèque
- Liens vers inscription et connexion
- Redirection automatique selon le rôle si connecté

#### Espace Membre (`/member`)
- **Dashboard personnalisé** avec vue d'ensemble
- **Catalogue** avec recherche avancée et filtres
  - Visualisation des ouvrages disponibles
  - Détails complets de chaque ouvrage
  - Bouton de réservation direct
- **Mes réservations** :
  - Liste des réservations actives
  - Statut (à récupérer / en attente)
  - Possibilité d'annulation
- **Mes emprunts** en cours avec dates de retour

#### Espace Bibliothécaire (`/librarian`)
- **Dashboard de gestion** avec statistiques
- **Gestion du catalogue** :
  - CRUD complet des ouvrages
  - CRUD complet des exemplaires
  - Association ouvrages-exemplaires
  - Formulaires avec validation
- **Gestion des réservations** :
  - Vue d'ensemble de toutes les réservations
  - Filtrage par statut
- **Gestion des emprunts** :
  - Suivi des emprunts actifs
  - Traitement des retours
  - Calcul automatique des pénalités
- **Gestion des membres** :
  - Liste complète des utilisateurs
  - Détails et historique

### 🔧 Commandes console

```bash
# Envoyer les rappels d'emprunt automatiques
# À configurer en cron job pour exécution quotidienne
php bin/console app:send-loan-reminders

# Créer des emprunts de test pour les rappels (développement)
php bin/console app:test-loan-reminders

# Tester les notifications de réservation (développement)
php bin/console app:test-reservation-notifications

# Tester le dispatch asynchrone (développement)
php bin/console app:test-async-notification

# Consommer les messages asynchrones (production)
# -vv pour mode verbose
php bin/console messenger:consume async -vv
```

### 📊 Commandes Doctrine

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Créer/exécuter les migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Vider la base (attention : destructif)
php bin/console doctrine:database:drop --force
```

## 🗄️ Modèle de données

### Entités principales

- **User** : utilisateurs avec rôles et informations personnelles
- **Ouvrage** : œuvres littéraires avec métadonnées complètes
- **Exemplaires** : copies physiques des ouvrages avec état et disponibilité
- **Emprunt** : emprunts actifs et historique avec dates et pénalités
- **Reservation** : réservations avec file d'attente et assignation
- **Penalites** : gestion des pénalités utilisateurs
- **Notifications** : historique des notifications envoyées
- **ParametreEmprunt** : configuration dynamique du système
- **HistoriqueInventaire** : suivi des changements de statut des exemplaires
- **Auteur** : auteurs avec relations multiples aux ouvrages
- **Editeur** : maisons d'édition
- **Categorie** : catégories thématiques
- **Tags** : étiquettes personnalisées

### Relations principales

```
User 1 ----< N Emprunt
User 1 ----< N Reservation
User 1 ----< N Penalites

Ouvrage 1 ----< N Exemplaires
Ouvrage 1 ----< N Reservation
Ouvrage N ----< N Auteur
Ouvrage N ----< N Categorie
Ouvrage N ----< N Tags
Ouvrage N ----< 1 Editeur

Exemplaires 1 ----< 1 Emprunt
Exemplaires 1 ----< 1 Reservation (nullable)
Exemplaires 1 ----< N HistoriqueInventaire
```

## 🚀 Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer 2.x
- MySQL 5.7+ / MariaDB 10.3+
- Symfony CLI (recommandé)
- Extension PHP : pdo_mysql, intl, mbstring

### Étapes d'installation

```bash
# 1. Cloner le projet
git clone https://github.com/votre-username/LibraShelf.git
cd LibraShelf

# 2. Installer les dépendances
composer install
symfony console importmap:install
# 3. Configurer les variables d'environnement
cp .env .env.local

# Éditer .env.local avec vos paramètres :
# - DATABASE_URL
# - MAILER_DSN
# - LIBRARIAN_INVITE_CODE
```

### Configuration de la base de données

```env
# .env.local
DATABASE_URL="mysql://username:password@127.0.0.1:3306/librashelf?serverVersion=8.0&charset=utf8mb4"
```

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### Configuration des emails

```env
# .env.local
# Exemple avec Gmail
MAILER_DSN=gmail://username:password@default

# Exemple avec Mailtrap (développement)
MAILER_DSN=smtp://username:password@smtp.mailtrap.io:2525
```

### Configuration du code d'invitation

```env
# .env.local
LIBRARIAN_INVITE_CODE="BIBLIO2025SECRET"
```

### Configuration de Symfony Messenger

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\ReservationAvailableNotification': async
```

```env
# .env.local
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

### Lancer l'application

```bash
# Démarrer le serveur Symfony
symfony server:start

# Ou avec le serveur PHP intégré
php -S localhost:8000 -t public/

# Démarrer le worker pour les messages asynchrones (dans un autre terminal)
php bin/console messenger:consume async -vv
```

L'application est maintenant accessible sur `http://localhost:8000`

## 📦 Technologies utilisées

### Backend
- **Framework** : Symfony 7.x
- **ORM** : Doctrine
- **Base de données** : MySQL / MariaDB
- **Validation** : Symfony Validator
- **Sécurité** : Symfony Security Component
- **Emails** : Symfony Mailer
- **Messages asynchrones** : Symfony Messenger

### Frontend
- **Templating** : Twig
- **Formulaires** : Symfony Forms
- **CSS** : Bootstrap (via CDN)
- **Assets** : Symfony AssetMapper
- **UX** : Symfony UX (Turbo prévu)

### Développement
- **Maker Bundle** : génération de code
- **Profiler** : débogage et performance
- **Debug Bundle** : outils de développement

## 🔒 Sécurité

### Authentification
- **Hashage bcrypt** des mots de passe
- **Politique de mot de passe forte** avec PasswordStrength Symfony
- **Session sécurisée** avec CSRF
- **Remember me** optionnel

### Validation
- **Protection CSRF** sur tous les formulaires
- **Validation des données** côté serveur avec contraintes Doctrine
  - Email : format strict validé
  - Nom : caractères autorisés (lettres, espaces, tirets)
  - Mot de passe : force minimale requise (medium)
- **Contraintes d'unicité** : email, ISBN
- **Validation personnalisée** selon les règles métier
- **Messages d'erreur explicites** en français

### Bonnes pratiques
- **Pas de données sensibles** dans le contrôle de version
- **Variables d'environnement** pour la configuration
- **Préparation des requêtes** SQL (protection injection)
- **Échappement automatique** dans Twig (protection XSS)

## 🔄 Workflows

### Workflow de réservation

1. **Membre sélectionne un ouvrage** dans le catalogue
2. **Clic sur "Réserver"**
3. **Système vérifie la disponibilité** :
   - Si exemplaire disponible → assignation immédiate (statut "À récupérer")
   - Sinon → mise en file d'attente (statut "En attente")
4. **Email de confirmation** envoyé immédiatement
5. **Si assignation immédiate** : email de disponibilité envoyé (asynchrone)
6. **Délai de 48h** pour récupérer le livre
7. **Membre peut annuler** sa réservation à tout moment

### Workflow d'emprunt

1. **Bibliothécaire crée l'emprunt** (via interface ou scan)
2. **Dates calculées automatiquement** selon configuration
3. **Exemplaire marqué comme indisponible**
4. **Statut** : `en_cours`
5. **Rappel J-3** : email 3 jours avant échéance
6. **Rappel J-0** : email le jour de l'échéance
7. **Si retard** : statut passe à `en_retard`
8. **Rappel J+7** : email après 7 jours de retard
9. **Retour** : bibliothécaire traite le retour
10. **Calcul pénalités** si applicable
11. **Statut** : `retourné`

### Workflow de notification asynchrone

1. **Événement déclenché** (ex: réservation disponible)
2. **Message dispatché** dans la queue Messenger
3. **Message stocké** dans la table `messenger_messages`
4. **Worker consomme** le message
5. **Handler traite** le message (envoi email)
6. **Notification enregistrée** en base de données
7. **Message supprimé** de la queue

## 📈 Administration

### Gestion des paramètres d'emprunt

Les paramètres se configurent directement en base de données via l'entité `ParametreEmprunt` :

```sql
-- Exemple de configuration
INSERT INTO parametre_emprunt (emprunt_duree_jours, penalite_centimes_par_jour, jours_tolerance, configuration)
VALUES (14, 50, 2, NOW());
```

- `emprunt_duree_jours` : durée par défaut (14 jours = 2 semaines)
- `penalite_centimes_par_jour` : 50 centimes = 0,50€ par jour
- `jours_tolerance` : 2 jours de grâce avant pénalités

### Tâches planifiées (Cron)

```bash
# Ajoutez ces lignes à votre crontab
# Rappels quotidiens à 8h du matin
0 8 * * * cd /path/to/librashelf && php bin/console app:send-loan-reminders

# Worker permanent (redémarre si crash)
* * * * * cd /path/to/librashelf && php bin/console messenger:consume async --limit=100
```

### Supervision

```bash
# Vérifier les messages en attente
php bin/console messenger:stats

# Vérifier les logs
tail -f var/log/dev.log

# Nettoyer le cache
php bin/console cache:clear
```

## 🧪 Tests

### Commandes de test disponibles

```bash
# Tester les rappels d'emprunt
php bin/console app:test-loan-reminders
php bin/console app:send-loan-reminders

# Tester les notifications de réservation
php bin/console app:test-reservation-notifications

# Tester le système asynchrone
php bin/console app:test-async-notification
php bin/console messenger:consume async -vv
```

## 🐛 Dépannage

### Problème de connexion à la base de données

```bash
# Vérifier la configuration
php bin/console debug:config doctrine

# Tester la connexion
php bin/console doctrine:database:create
```

### Emails non envoyés

```bash
# Vérifier la configuration mailer
php bin/console debug:config framework mailer

# Tester l'envoi
php bin/console app:test-reservation-notifications
```

### Messages non consommés

```bash
# Vérifier les transports
php bin/console messenger:stats

# Consommer manuellement
php bin/console messenger:consume async -vv

# Vérifier la table messenger_messages
SELECT * FROM messenger_messages;
```

### Erreurs de permissions

```bash
# Donner les bonnes permissions
chmod -R 777 var/
```

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Commitez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 👥 Auteurs

- **Meersseman Gilles** - *Développement initial*

## 📞 Support

Pour toute question ou problème :
- Ouvrez une issue sur GitHub
- Consultez la documentation Symfony : https://symfony.com/doc

### Réinitialisation complète de la base de données (développement)

Si la base de données est corrompue ou incohérente :

```bash
# Supprimer et recréer la base
symfony console doctrine:database:drop --force
symfony console doctrine:database:create
symfony console doctrine:schema:create

# Marquer les migrations comme exécutées
symfony console doctrine:migrations:version --add --all --no-interaction

# Vérifier
symfony console doctrine:schema:validate
```




