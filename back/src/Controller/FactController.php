<?php

namespace App\Controller;

use App\Entity\Fact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur pour gérer les Facts
 * 
 * Ce contrôleur démontre les opérations CRUD de base :
 * - CREATE : Créer une nouvelle fact
 * - READ : Lire une ou plusieurs facts
 * - UPDATE : Modifier une fact existante
 * - DELETE : Supprimer une fact
 * 
 * Chaque méthode correspond à une route HTTP spécifique.
 */
#[Route('/api/facts', name: 'api_fact_')]
class FactController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    /**
     * GET /api/facts
     * 
     * Récupère la liste de toutes les facts avec pagination
     * 
     * Paramètres de requête optionnels :
     * - page : numéro de page (défaut: 1)
     * - limit : nombre d'éléments par page (défaut: 10)
     * - techno : filtrer par technologie
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            // Récupération des paramètres de pagination
            $page = max(1, (int) $request->query->get('page', 1));
            $limit = max(1, min(100, (int) $request->query->get('limit', 10)));
            $offset = ($page - 1) * $limit;
            
            // Filtre par technologie si spécifié
            $techno = $request->query->get('techno');
            
            // Construction de la requête Doctrine
            $qb = $this->entityManager->createQueryBuilder()
                ->select('f')
                ->from(Fact::class, 'f');
            
            if ($techno) {
                $qb->where('f.techno = :techno')
                   ->setParameter('techno', $techno);
            }
            
            // Comptage total pour la pagination
            $totalQuery = clone $qb;
            $total = $totalQuery->select('COUNT(f.id)')->getQuery()->getSingleScalarResult();
            
            // Récupération des données avec pagination
            $facts = $qb->setFirstResult($offset)
                       ->setMaxResults($limit)
                       ->orderBy('f.dateEnregistrement', 'DESC')
                       ->getQuery()
                       ->getResult();
            
            // Calcul des métadonnées de pagination
            $totalPages = ceil($total / $limit);
            $hasNextPage = $page < $totalPages;
            $hasPrevPage = $page > 1;
            
            // Construction de la réponse
            $response = [
                'data' => $facts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'has_next_page' => $hasNextPage,
                    'has_prev_page' => $hasPrevPage,
                ],
                'filters' => [
                    'techno' => $techno
                ]
            ];
            
            return new JsonResponse($response, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des facts',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/facts/{id}
     * 
     * Récupère une fact spécifique par son ID
     * 
     * @param int $id L'ID de la fact à récupérer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            // Recherche de la fact par ID
            $fact = $this->entityManager->getRepository(Fact::class)->find($id);
            
            if (!$fact) {
                return new JsonResponse([
                    'error' => 'Fact non trouvée',
                    'message' => "Aucune fact avec l'ID $id n'a été trouvée"
                ], Response::HTTP_NOT_FOUND);
            }
            
            return new JsonResponse([
                'data' => $fact
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération de la fact',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/facts
     * 
     * Crée une nouvelle fact
     * 
     * Corps de la requête attendu (JSON) :
     * {
     *   "fact": "Le texte de la fact",
     *   "techno": "La technologie concernée"
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            // Désérialisation du JSON vers un objet Fact
            $fact = $this->serializer->deserialize(
                $request->getContent(), 
                Fact::class, 
                'json'
            );
            
            // Validation des données
            $errors = $this->validator->validate($fact);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = [
                        'field' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }
                
                return new JsonResponse([
                    'error' => 'Données invalides',
                    'validation_errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // La date d'enregistrement est automatiquement définie dans le constructeur
            // Sauvegarde en base de données
            $this->entityManager->persist($fact);
            $this->entityManager->flush();
            
            // Retour de la fact créée avec son ID généré
            return new JsonResponse([
                'message' => 'Fact créée avec succès',
                'data' => $fact
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création de la fact',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT /api/facts/{id}
     * 
     * Met à jour complètement une fact existante (remplacement)
     * 
     * @param int $id L'ID de la fact à modifier
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            // Recherche de la fact existante
            $fact = $this->entityManager->getRepository(Fact::class)->find($id);
            
            if (!$fact) {
                return new JsonResponse([
                    'error' => 'Fact non trouvée',
                    'message' => "Aucune fact avec l'ID $id n'a été trouvée"
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Désérialisation des nouvelles données
            $updatedFact = $this->serializer->deserialize(
                $request->getContent(), 
                Fact::class, 
                'json'
            );
            
            // Mise à jour des propriétés (remplacement complet)
            $fact->setFact($updatedFact->getFact());
            $fact->setTechno($updatedFact->getTechno());
            // Note: on ne modifie pas la date d'enregistrement pour conserver l'historique
            
            // Validation
            $errors = $this->validator->validate($fact);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = [
                        'field' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }
                
                return new JsonResponse([
                    'error' => 'Données invalides',
                    'validation_errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Sauvegarde
            $this->entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Fact mise à jour avec succès',
                'data' => $fact
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la mise à jour de la fact',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PATCH /api/facts/{id}
     * 
     * Met à jour partiellement une fact existante
     * 
     * @param int $id L'ID de la fact à modifier
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function patch(int $id, Request $request): JsonResponse
    {
        try {
            // Recherche de la fact existante
            $fact = $this->entityManager->getRepository(Fact::class)->find($id);
            
            if (!$fact) {
                return new JsonResponse([
                    'error' => 'Fact non trouvée',
                    'message' => "Aucune fact avec l'ID $id n'a été trouvée"
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Récupération des données JSON
            $data = json_decode($request->getContent(), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse([
                    'error' => 'JSON invalide',
                    'message' => json_last_error_msg()
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Mise à jour partielle des propriétés
            if (isset($data['fact'])) {
                $fact->setFact($data['fact']);
            }
            
            if (isset($data['techno'])) {
                $fact->setTechno($data['techno']);
            }
            
            // Validation
            $errors = $this->validator->validate($fact);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = [
                        'field' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }
                
                return new JsonResponse([
                    'error' => 'Données invalides',
                    'validation_errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Sauvegarde
            $this->entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Fact mise à jour partiellement avec succès',
                'data' => $fact
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la mise à jour de la fact',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/facts/{id}
     * 
     * Supprime une fact existante
     * 
     * @param int $id L'ID de la fact à supprimer
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        try {
            // Recherche de la fact existante
            $fact = $this->entityManager->getRepository(Fact::class)->find($id);
            
            if (!$fact) {
                return new JsonResponse([
                    'error' => 'Fact non trouvée',
                    'message' => "Aucune fact avec l'ID $id n'a été trouvée"
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Suppression
            $this->entityManager->remove($fact);
            $this->entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Fact supprimée avec succès',
                'deleted_id' => $id
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression de la fact',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/facts/stats
     * 
     * Récupère des statistiques sur les facts
     * 
     * @return JsonResponse
     */
    #[Route('/stats', name: 'stats', methods: ['GET'], priority: 100)]
    public function stats(): JsonResponse
    {
        try {
            // Statistiques générales
            $totalFacts = $this->entityManager->getRepository(Fact::class)
                ->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->getQuery()
                ->getSingleScalarResult();
            
            // Statistiques par technologie
            $technoStats = $this->entityManager->getRepository(Fact::class)
                ->createQueryBuilder('f')
                ->select('f.techno, COUNT(f.id) as count')
                ->groupBy('f.techno')
                ->orderBy('count', 'DESC')
                ->getQuery()
                ->getResult();
            
            // Facts récentes (dernières 5)
            $recentFacts = $this->entityManager->getRepository(Fact::class)
                ->createQueryBuilder('f')
                ->orderBy('f.dateEnregistrement', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
            
            $stats = [
                'total_facts' => $totalFacts,
                'technologies' => $technoStats,
                'recent_facts' => $recentFacts
            ];
            
            return new JsonResponse([
                'data' => $stats
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des statistiques',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
