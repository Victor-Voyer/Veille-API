<?php

namespace App\Controller;

use App\Entity\Fact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur d'exemples pédagogiques
 * 
 * Ce contrôleur démontre des concepts avancés :
 * - Gestion des erreurs
 * - Validation des paramètres
 * - Requêtes complexes
 * - Formats de réponse différents
 */
#[Route('/api/examples', name: 'api_example_')]
class ExampleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/examples/hello
     * 
     * Exemple simple d'une route API
     * 
     * @return JsonResponse
     */
    #[Route('/hello', name: 'hello', methods: ['GET'])]
    public function hello(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Bonjour ! Bienvenue dans l\'API Veille',
            'timestamp' => new \DateTime(),
            'version' => '1.0.0'
        ]);
    }

    /**
     * GET /api/examples/search
     * 
     * Exemple de recherche avec paramètres
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        try {
            // Récupération des paramètres
            $query = $request->query->get('q', '');
            $techno = $request->query->get('techno', '');
            $limit = min(50, max(1, (int) $request->query->get('limit', 10)));
            
            // Validation des paramètres
            if (empty($query) && empty($techno)) {
                return new JsonResponse([
                    'error' => 'Paramètres manquants',
                    'message' => 'Vous devez fournir au moins un terme de recherche (q) ou une technologie (techno)',
                    'example' => '/api/examples/search?q=javascript&techno=PHP'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Construction de la requête
            $qb = $this->entityManager->createQueryBuilder()
                ->select('f')
                ->from(Fact::class, 'f');
            
            $conditions = [];
            $parameters = [];
            
            if (!empty($query)) {
                $conditions[] = 'f.fact LIKE :query';
                $parameters['query'] = '%' . $query . '%';
            }
            
            if (!empty($techno)) {
                $conditions[] = 'f.techno = :techno';
                $parameters['techno'] = $techno;
            }
            
            if (!empty($conditions)) {
                $qb->where(implode(' AND ', $conditions));
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
            
            $facts = $qb->setMaxResults($limit)
                       ->orderBy('f.dateEnregistrement', 'DESC')
                       ->getQuery()
                       ->getResult();
            
            return new JsonResponse([
                'query' => [
                    'search_term' => $query,
                    'techno' => $techno,
                    'limit' => $limit
                ],
                'results' => $facts,
                'count' => count($facts)
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur de recherche',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/examples/technologies
     * 
     * Liste toutes les technologies disponibles
     * 
     * @return JsonResponse
     */
    #[Route('/technologies', name: 'technologies', methods: ['GET'])]
    public function technologies(): JsonResponse
    {
        try {
            $technologies = $this->entityManager->getRepository(Fact::class)
                ->createQueryBuilder('f')
                ->select('DISTINCT f.techno as techno, COUNT(f.id) as count')
                ->groupBy('f.techno')
                ->orderBy('count', 'DESC')
                ->getQuery()
                ->getResult();
            
            return new JsonResponse([
                'technologies' => $technologies,
                'total_technologies' => count($technologies)
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des technologies',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/examples/bulk-create
     * 
     * Exemple de création en lot (bulk)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/bulk-create', name: 'bulk_create', methods: ['POST'])]
    public function bulkCreate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['facts']) || !is_array($data['facts'])) {
                return new JsonResponse([
                    'error' => 'Format invalide',
                    'message' => 'Le corps de la requête doit contenir un tableau "facts"',
                    'example' => [
                        'facts' => [
                            ['fact' => 'Texte 1', 'techno' => 'PHP'],
                            ['fact' => 'Texte 2', 'techno' => 'JavaScript']
                        ]
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
            
            $createdFacts = [];
            $errors = [];
            
            foreach ($data['facts'] as $index => $factData) {
                try {
                    $fact = new Fact();
                    $fact->setFact($factData['fact'] ?? '');
                    $fact->setTechno($factData['techno'] ?? '');
                    
                    // Validation simple
                    if (empty($fact->getFact()) || empty($fact->getTechno())) {
                        $errors[] = [
                            'index' => $index,
                            'message' => 'Les champs "fact" et "techno" sont obligatoires'
                        ];
                        continue;
                    }
                    
                    $this->entityManager->persist($fact);
                    $createdFacts[] = $fact;
                    
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => $e->getMessage()
                    ];
                }
            }
            
            // Sauvegarde en lot
            $this->entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Création en lot terminée',
                'created_count' => count($createdFacts),
                'created_facts' => $createdFacts,
                'errors' => $errors
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création en lot',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/examples/random
     * 
     * Récupère une fact aléatoire
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/random', name: 'random', methods: ['GET'])]
    public function random(Request $request): JsonResponse
    {
        try {
            $techno = $request->query->get('techno');
            
            $qb = $this->entityManager->createQueryBuilder()
                ->select('f')
                ->from(Fact::class, 'f');
            
            if ($techno) {
                $qb->where('f.techno = :techno')
                   ->setParameter('techno', $techno);
            }
            
            // Utilisation de ORDER BY RAND() pour MySQL/SQLite
            $facts = $qb->orderBy('RANDOM()')
                       ->setMaxResults(1)
                       ->getQuery()
                       ->getResult();
            
            if (empty($facts)) {
                return new JsonResponse([
                    'message' => 'Aucune fact trouvée',
                    'suggestion' => 'Essayez avec une autre technologie'
                ], Response::HTTP_NOT_FOUND);
            }
            
            return new JsonResponse([
                'random_fact' => $facts[0],
                'filter' => $techno ? "Technologie: $techno" : 'Toutes technologies'
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération d\'une fact aléatoire',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/examples/health
     * 
     * Vérification de la santé de l'API
     * 
     * @return JsonResponse
     */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            // Test de la connexion à la base de données
            $dbStatus = 'OK';
            $dbError = null;
            
            try {
                $this->entityManager->getConnection()->executeQuery('SELECT 1');
            } catch (\Exception $e) {
                $dbStatus = 'ERROR';
                $dbError = $e->getMessage();
            }
            
            // Statistiques rapides
            $totalFacts = $this->entityManager->getRepository(Fact::class)
                ->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->getQuery()
                ->getSingleScalarResult();
            
            $health = [
                'status' => 'OK',
                'timestamp' => new \DateTime(),
                'database' => [
                    'status' => $dbStatus,
                    'error' => $dbError
                ],
                'statistics' => [
                    'total_facts' => $totalFacts
                ],
                'version' => '1.0.0'
            ];
            
            $httpStatus = $dbStatus === 'OK' ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;
            
            return new JsonResponse($health, $httpStatus);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'timestamp' => new \DateTime()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
