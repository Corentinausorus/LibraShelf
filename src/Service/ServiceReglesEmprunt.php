<?php
namespace App\Service;

use App\Entity\Emprunt;
use App\Entity\ParametreEmprunt;
use App\Repository\ParametreEmpruntRepository;

class ServiceReglesEmprunt
{
    public function __construct(private ParametreEmpruntRepository $settingsRepo) {}

    public function getSettings(): ParametreEmprunt
    {
        return $this->settingsRepo->findOneBy([]) ?? new ParametreEmprunt();
    }

    public function computePenalty(Emprunt $emprunt): float
    {
        $settings = $this->getSettings();
        $due = $emprunt->getDueAt();
        $returned = $emprunt->getReturnedAt();
        if (!$due || !$returned) {
            return 0.0;
        }

        $interval = $returned->diff($due);
        $daysLate = (int) $interval->format('%r%a'); // ca peut etre negatif
        if ($daysLate <= 0) {
            return 0.0;
        }

        $daysToCharge = max(0, $daysLate - $settings->getJoursTolerance());
        $penaltyEuros = ($settings->getPenaliteCentimesParJour() / 100.0) * $daysToCharge;
        return round($penaltyEuros, 2);
    }

    public function applyDefaultDueDateIfMissing(Emprunt $emprunt): void
    {
        $settings = $this->getSettings();
        $now = new \DateTimeImmutable();
        if (!$emprunt->getStartAt()) {
            $emprunt->setStartAt($now);
        }
        if (!$emprunt->getDueAt()) {
            $start = $emprunt->getStartAt() ?? $now;
            $due = $start->modify('+' . $settings->getEmpruntDureeJours() . ' days');
            $emprunt->setDueAt($due);
        }
    }
}