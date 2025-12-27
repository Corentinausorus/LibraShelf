<?php

namespace App\DataFixtures;

use App\Entity\Notifications;
use App\Enum\NotificationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NotificationsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Templates de sujets
        $subjects = [
            'Rappel : Votre emprunt arrive à échéance',
            'Nouveau livre disponible',
            'Votre réservation est prête',
            'Retard de retour - Action requise',
            'Bienvenue à la bibliothèque',
            'Confirmation de votre inscription',
            'Récapitulatif de vos emprunts',
        ];
        
        // Créer 10-15 notifications
        $nbNotifications = $faker->numberBetween(10, 15);
        
        for ($i = 0; $i < $nbNotifications; $i++) {
            $notification = new Notifications();
            
            // Type(s) de notification avec Enum
            $nbTypes = $faker->boolean(70) ? 1 : $faker->numberBetween(1, 2);
            $typesDisponibles = [
                NotificationType::EMAIL,
                NotificationType::MESSAGE,
                NotificationType::INTERNE,
            ];
            $typesChoisis = $faker->randomElements($typesDisponibles, $nbTypes);
            $notification->setType($typesChoisis);
            
            // Sujet
            $subject = $faker->randomElement($subjects);
            $notification->setSubject($subject);
            
            // Corps du message
            $body = $faker->paragraph(3);
            $notification->setBody($body);
            
            // Email destinataire (si type EMAIL)
            if (in_array(NotificationType::EMAIL, $typesChoisis)) {
                $notification->setToEmail($faker->safeEmail());
            }
            
            // SMS destinataire (si type MESSAGE)
            if (in_array(NotificationType::MESSAGE, $typesChoisis)) {
                $notification->setToSms($faker->phoneNumber());
            }
            
            $manager->persist($notification);
        }

        $manager->flush();
    }
}
