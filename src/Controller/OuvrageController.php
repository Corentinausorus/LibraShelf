<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OuvrageRepository;
use App\Repository\CategorieRepository;

final class OuvrageController extends AbstractController
{
    #[Route('/ouvrage', name: 'ouvrage_liste')]
    public function index(
        Request $request,
        OuvrageRepository $ouvrageRepository,
        CategorieRepository $categorieRepository
    ): Response {
        // Récupération des filtres depuis la requête
        $filters = [
            'titre' => $request->query->get('titre', ''),
            'categorie' => $request->query->get('categorie', ''),
            'langue' => $request->query->get('langue', ''),
            'annee' => $request->query->get('annee', ''),
            'disponible' => $request->query->get('disponible', ''),
        ];

        // Recherche avec filtres ou récupération de tous les ouvrages
        $hasFilters = array_filter($filters, fn($value) => $value !== '');
        
        if ($hasFilters) {
            $ouvrages = $ouvrageRepository->searchWithFilters($filters);
        } else {
            $ouvrages = $ouvrageRepository->findAll();
        }

        // Récupération des données pour les filtres
        $categories = $categorieRepository->findAll();
        $langues = $ouvrageRepository->findAllLangues();
        $annees = $ouvrageRepository->findAllAnnees();

        return $this->render('ouvrage/index.html.twig', [
            'ouvrages' => $ouvrages,
            'categories' => $categories,
            'langues' => $langues,
            'annees' => $annees,
            'filters' => $filters,
            'resultCount' => count($ouvrages),
        ]);
    }
}
