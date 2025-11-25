<?php

namespace App\Repository;

use App\Entity\Ouvrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ouvrage>
 */
class OuvrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouvrage::class);
    }

    /**
     * Recherche d'ouvrages avec filtres multiples
     * 
     * @param array $filters Tableau de filtres: titre, categorie, langue, annee, disponible
     * @return Ouvrage[]
     */
    public function searchWithFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.categories', 'c')
            ->leftJoin('o.exemplaires', 'e')
            ->groupBy('o.id');

        // Filtre par titre (recherche partielle)
        if (!empty($filters['titre'])) {
            $qb->andWhere('LOWER(o.titre) LIKE LOWER(:titre)')
               ->setParameter('titre', '%' . $filters['titre'] . '%');
        }

        // Filtre par catégorie
        if (!empty($filters['categorie'])) {
            $qb->andWhere('c.id = :categorieId')
               ->setParameter('categorieId', $filters['categorie']);
        }

        // Filtre par langue (recherche simple dans la représentation JSON)
        if (!empty($filters['langue'])) {
            // JSON functions are not available in DQL by default, so fallback to LIKE on JSON text
            $qb->andWhere('o.Langues LIKE :langue')
               ->setParameter('langue', '%"' . $filters['langue'] . '"%');
        }

        // Filtre par année
        if (!empty($filters['annee'])) {
            // Doctrine DQL does not expose YEAR() by default. Compare by a range for the requested year.
            try {
                $start = new \DateTimeImmutable($filters['annee'] . '-01-01');
                $end = new \DateTimeImmutable($filters['annee'] . '-12-31 23:59:59');
                     $qb->andWhere('o.annee BETWEEN :startYear AND :endYear')
                   ->setParameter('startYear', $start)
                   ->setParameter('endYear', $end);
            } catch (\Exception $e) {
                // If the provided value isn't a valid year, ignore the filter
            }
        }

        // Filtre par disponibilité (au moins un exemplaire disponible)
        if (isset($filters['disponible']) && $filters['disponible'] !== '') {
            if ($filters['disponible']) {
                $qb->andHaving('SUM(CASE WHEN e.disponible = 1 THEN 1 ELSE 0 END) > 0');
            } else {
                $qb->andHaving('SUM(CASE WHEN e.disponible = 1 THEN 1 ELSE 0 END) = 0 OR COUNT(e.id) = 0');
            }
        }

        return $qb->orderBy('o.titre', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Récupère toutes les langues distinctes
     */
    public function findAllLangues(): array
    {
        $results = $this->createQueryBuilder('o')
            ->select('o.Langues')
            ->where('o.Langues IS NOT NULL')
            ->getQuery()
            ->getResult();

        $langues = [];
        foreach ($results as $result) {
            if (is_array($result['Langues'])) {
                $langues = array_merge($langues, $result['Langues']);
            }
        }

        return array_unique($langues);
    }

    /**
     * Récupère toutes les années distinctes
     */
    public function findAllAnnees(): array
    {
        $results = $this->createQueryBuilder('o')
            ->select('o.annee')
            ->where('o.annee IS NOT NULL')
            ->groupBy('o.annee')
            ->orderBy('o.annee', 'DESC')
            ->getQuery()
            ->getResult();

        $annees = [];
        foreach ($results as $row) {
            // Result may be an array with key 'Année' or numeric index depending on platform
            $date = null;
            if (is_array($row)) {
                $vals = array_values($row);
                $date = $vals[0] ?? null;
            } else {
                $date = $row;
            }

            if ($date instanceof \DateTimeInterface) {
                $annees[] = (int) $date->format('Y');
            }
        }

        return array_values(array_unique($annees));
    }
}
