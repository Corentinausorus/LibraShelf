# Configuration des Rappels Automatiques d'Emprunt

## Vue d'ensemble

Le système envoie automatiquement 3 types de rappels par email :
- **J-3** : 3 jours avant la date de retour
- **J-0** : Le jour même de la date de retour
- **J+7** : 7 jours après la date de retour (retard)

## Fichiers créés

1. **`src/Service/NotificationService.php`**
   - Service de gestion des notifications
   - Génère et envoie les emails
   - Enregistre les notifications dans la base de données

2. **`src/Command/SendLoanRemindersCommand.php`**
   - Commande Symfony pour l'exécution des rappels
   - Recherche les emprunts concernés
   - Envoie les notifications appropriées

## Test manuel

Pour tester la commande manuellement :

```bash
php bin/console app:send-loan-reminders
```

La commande affichera :
- Le nombre de rappels J-3, J-0 et J+7 envoyés
- Une barre de progression pour chaque type
- Un résumé final

## Configuration du Mailer

Vérifiez que le mailer est configuré dans `.env` :

```env
# Pour les tests locaux (affiche dans la console Symfony)
MAILER_DSN=smtp://localhost:1025

# Pour l'environnement de production
MAILER_DSN=smtp://user:pass@smtp.example.com:587
```

## Configuration CRON (Production)

Pour exécuter automatiquement les rappels tous les jours à 9h00 du matin :

### Linux/Mac

Ajouter au crontab (`crontab -e`) :

```cron
# Rappels d'emprunt quotidiens à 9h00
0 9 * * * cd /chemin/vers/LibraShelf && php bin/console app:send-loan-reminders >> /var/log/librashelf-reminders.log 2>&1
```

### Windows (Task Scheduler)

1. Ouvrir le **Planificateur de tâches**
2. Créer une nouvelle tâche :
   - Déclencheur : Tous les jours à 9h00
   - Action : `php.exe`
   - Arguments : `C:\chemin\vers\LibraShelf\bin\console app:send-loan-reminders`

### Docker/Kubernetes

Ajouter un CronJob :

```yaml
apiVersion: batch/v1
kind: CronJob
metadata:
  name: loan-reminders
spec:
  schedule: "0 9 * * *"
  jobTemplate:
    spec:
      template:
        spec:
          containers:
          - name: loan-reminders
            image: librashelf:latest
            command:
            - php
            - bin/console
            - app:send-loan-reminders
          restartPolicy: OnFailure
```

## Monitoring

Les logs sont enregistrés dans :
- `var/log/dev.log` (environnement dev)
- `var/log/prod.log` (environnement prod)

Rechercher les entrées avec :
```bash
grep "Rappel envoyé" var/log/dev.log
grep "Erreur lors de l'envoi" var/log/dev.log
```

## Personnalisation

### Modifier les délais

Éditer `src/Command/SendLoanRemindersCommand.php` :

```php
// Pour J-5 au lieu de J-3
$dateMinus5 = $today->modify('+5 days');

// Pour J+14 au lieu de J+7
$datePlus14 = $today->modify('-14 days');
```

### Modifier les templates d'email

Éditer `src/Service/NotificationService.php`, méthode `generateEmailContent()`.

### Ajouter SMS ou notifications internes

Dans `NotificationService::sendLoanReminder()`, ajouter :

```php
// Pour SMS
if ($user->getPhoneNumber()) {
    $this->sendSms($user->getPhoneNumber(), $subject);
}

// Pour notification interne
$this->saveNotification($user->getEmail(), $subject, $body, NotificationType::INTERNE);
```

## Tests

### Créer un emprunt de test

```sql
-- Via SQL pour tester J-3
UPDATE emprunt SET due_at = DATE('now', '+3 days') WHERE id = 1;

-- Via SQL pour tester J-0
UPDATE emprunt SET due_at = DATE('now') WHERE id = 2;

-- Via SQL pour tester J+7
UPDATE emprunt SET due_at = DATE('now', '-7 days') WHERE id = 3;
```

Puis exécuter la commande :
```bash
php bin/console app:send-loan-reminders -v
```

## Troubleshooting

### Les emails ne sont pas envoyés

1. Vérifier la configuration du mailer :
   ```bash
   php bin/console debug:config framework mailer
   ```

2. Tester manuellement l'envoi :
   ```bash
   php bin/console mailer:test votre@email.com
   ```

### La commande ne trouve aucun emprunt

Vérifier que des emprunts existent avec les bonnes dates :
```bash
php bin/console doctrine:query:sql "SELECT id, due_at, status FROM emprunt WHERE status='en_cours'"
```

### Permission denied sur les logs

```bash
chmod -R 777 var/log/
```
