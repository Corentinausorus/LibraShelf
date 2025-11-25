# Notifications de Réservations

Ce document explique le système de notifications par email pour les réservations de livres.

## 📧 Types de Notifications

### 1. Confirmation de Réservation
**Quand ?** Immédiatement après qu'un membre crée une réservation

**Email envoyé :** 
- ✅ Confirmation de votre réservation
- Détails : titre du livre, date de réservation, statut
- Information : "Vous recevrez un email dès qu'un exemplaire sera disponible"

**Implémentation :**
- Service : `ReservationNotificationService::sendReservationConfirmation()`
- Appelé depuis : `ReservationController::add()`
- Type : Email synchrone

### 2. Notification de Disponibilité
**Quand ?** Lorsqu'un exemplaire devient disponible pour une réservation en attente

**Email envoyé :**
- 🎉 Votre réservation est disponible !
- Détails : titre du livre, cote de l'exemplaire, délai de récupération (48h)
- Action requise : Venir récupérer le livre à la bibliothèque

**Implémentation :**
- Service : `ReservationNotificationService::sendReservationAvailableEmail()`
- Message asynchrone : `ReservationAvailableNotification`
- Handler : `ReservationAvailableNotificationHandler`
- Type : Email asynchrone (via Symfony Messenger)

## 🔄 Workflow de Notification

### Cas 1 : Exemplaire Disponible Immédiatement
```
1. Membre réserve un livre avec exemplaires dispos
2. ✉️ Email de confirmation envoyé (synchrone)
3. Exemplaire marqué comme "À récupérer"
4. 📨 Message async dispatché pour notification de disponibilité
5. ✉️ Email de disponibilité envoyé (async)
```

### Cas 2 : Aucun Exemplaire Disponible
```
1. Membre réserve un livre sans exemplaires dispos
2. ✉️ Email de confirmation envoyé (synchrone)
3. Réservation en statut "En attente"
4. [FUTUR] Lors du retour d'un livre :
   - Exemplaire assigné à la première réservation en attente
   - Statut changé vers "À récupérer"
   - 📨 Message async dispatché
   - ✉️ Email de disponibilité envoyé
```

## 🛠️ Configuration Technique

### Services Créés
- `src/Service/ReservationNotificationService.php`
- `src/Message/ReservationAvailableNotification.php`
- `src/MessageHandler/ReservationAvailableNotificationHandler.php`

### Configuration Messenger
Les messages `ReservationAvailableNotification` sont traités de manière asynchrone via le transport configuré dans `config/packages/messenger.yaml`.

### Base de Données
Toutes les notifications envoyées sont enregistrées dans la table `notifications` avec :
- Type : EMAIL
- Destinataire
- Sujet
- Corps HTML
- Timestamp

## 📝 TODO - Fonctionnalités à Ajouter

### 1. Retour de Livre avec Attribution Automatique
**Objectif :** Quand un livre est retourné, vérifier s'il y a des réservations en attente et notifier le premier membre

**Implémentation suggérée :**
```php
// Dans EmpruntController::returnBook() (à créer)
public function returnBook(Emprunt $emprunt, MessageBusInterface $messageBus): Response
{
    $exemplaire = $emprunt->getExemplaire();
    $emprunt->setReturnedAt(new \DateTime());
    
    // Marquer l'exemplaire comme disponible
    $exemplaire->setDisponible(true);
    
    // Chercher la première réservation en attente pour cet ouvrage
    $reservation = $this->reservationRepository->findOneBy([
        'ouvrage' => $exemplaire->getOuvrage(),
        'statut' => 'En attente'
    ], ['creationDate' => 'ASC']);
    
    if ($reservation) {
        // Assigner l'exemplaire à la réservation
        $reservation->setExemplaire($exemplaire);
        $reservation->setStatut('À récupérer');
        $exemplaire->setDisponible(false);
        
        // Envoyer la notification async
        $messageBus->dispatch(new ReservationAvailableNotification($reservation->getId()));
    }
    
    $this->entityManager->flush();
    // ...
}
```

### 2. Expiration de Réservation
**Objectif :** Annuler automatiquement les réservations "À récupérer" non retirées après 48h

**Implémentation suggérée :**
```php
// Command à créer : src/Command/ExpireReservationsCommand.php
php bin/console app:expire-reservations
```

### 3. Rappel de Récupération
**Objectif :** Envoyer un rappel 24h avant l'expiration d'une réservation "À récupérer"

**Email :**
- ⏰ Rappel : Récupérez votre livre avant expiration
- Délai restant : 24h
- Action : Venir chercher le livre à la bibliothèque

## 🧪 Tests

### Test Manuel
```bash
# 1. Créer une réservation avec exemplaire dispo
# Naviguer vers /reservation/add/{id} avec un ouvrage ayant des exemplaires

# 2. Vérifier les emails dans la table notifications
sqlite3 var/data.db "SELECT id, to_email, Subject FROM notifications ORDER BY id DESC LIMIT 5;"

# 3. Traiter les messages async (si configuré)
php bin/console messenger:consume async -vv
```

### Test Programmatique
```php
// À créer : tests/Service/ReservationNotificationServiceTest.php
```

## 📊 Monitoring

### Vérifier les Notifications Envoyées
```sql
SELECT 
    id,
    to_email,
    Subject,
    created_at
FROM notifications 
WHERE Subject LIKE '%réservation%'
ORDER BY created_at DESC 
LIMIT 10;
```

### Vérifier les Messages en Attente
```bash
php bin/console messenger:stats
```

## 🔐 Sécurité

- Les emails contiennent uniquement les informations nécessaires (pas de données sensibles)
- Vérification que l'utilisateur a un email valide avant envoi
- Logs d'erreur pour traçabilité
- Pas de données personnelles dans les logs (sauf email pour debug)
