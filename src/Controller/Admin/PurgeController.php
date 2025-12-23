<?php

namespace App\Controller\Admin;

use App\Service\PurgeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration pour la purge des données.
 */
#[Route('/admin/purge')]
#[IsGranted('ROLE_ADMIN')]
class PurgeController extends AbstractController
{
    public function __construct(
        private readonly PurgeService $purgeService
    ) {
    }

    /**
     * Affiche la page de purge avec aperçu des données à supprimer.
     */
    #[Route('', name: 'admin_purge', methods: ['GET'])]
    public function index(): Response
    {
        $preview = $this->purgeService->previewPurge();

        return $this->render('admin/purge.html.twig', [
            'preview' => $preview,
            'defaults' => [
                'emprunts' => PurgeService::DEFAULT_EMPRUNT_RETENTION_MONTHS,
                'reservations' => PurgeService::DEFAULT_RESERVATION_RETENTION_MONTHS,
                'notifications' => PurgeService::DEFAULT_NOTIFICATION_RETENTION_DAYS,
            ],
        ]);
    }

    /**
     * Exécute la purge des données.
     */
    #[Route('/execute', name: 'admin_purge_execute', methods: ['POST'])]
    public function execute(Request $request): Response
    {
        // Vérification CSRF
        if (!$this->isCsrfTokenValid('purge_data', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_purge');
        }

        $empruntMonths = (int) $request->request->get('emprunts', PurgeService::DEFAULT_EMPRUNT_RETENTION_MONTHS);
        $reservationMonths = (int) $request->request->get('reservations', PurgeService::DEFAULT_RESERVATION_RETENTION_MONTHS);
        $notificationDays = (int) $request->request->get('notifications', PurgeService::DEFAULT_NOTIFICATION_RETENTION_DAYS);

        try {
            $results = $this->purgeService->purgeAll(
                $empruntMonths,
                $reservationMonths,
                $notificationDays
            );

            $total = $results['emprunts'] + $results['reservations'] + $results['notifications'];

            $this->addFlash('success', sprintf(
                'Purge effectuée avec succès ! %d emprunts, %d réservations et %d notifications supprimés (total: %d).',
                $results['emprunts'],
                $results['reservations'],
                $results['notifications'],
                $total
            ));
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de la purge : ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_purge');
    }

    /**
     * Actualise l'aperçu avec les paramètres personnalisés.
     */
    #[Route('/preview', name: 'admin_purge_preview', methods: ['POST'])]
    public function preview(Request $request): Response
    {
        $empruntMonths = (int) $request->request->get('emprunts', PurgeService::DEFAULT_EMPRUNT_RETENTION_MONTHS);
        $reservationMonths = (int) $request->request->get('reservations', PurgeService::DEFAULT_RESERVATION_RETENTION_MONTHS);
        $notificationDays = (int) $request->request->get('notifications', PurgeService::DEFAULT_NOTIFICATION_RETENTION_DAYS);

        $preview = $this->purgeService->previewPurge(
            $empruntMonths,
            $reservationMonths,
            $notificationDays
        );

        return $this->render('admin/purge.html.twig', [
            'preview' => $preview,
            'defaults' => [
                'emprunts' => $empruntMonths,
                'reservations' => $reservationMonths,
                'notifications' => $notificationDays,
            ],
        ]);
    }
}
