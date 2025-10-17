# Guide Pédagogique - API REST avec Symfony

## 🎯 Objectifs d'apprentissage

Ce projet démontre les concepts fondamentaux d'une API REST avec Symfony :

1. **Architecture REST** - Principes et conventions
2. **Méthodes HTTP** - GET, POST, PUT, PATCH, DELETE
3. **Codes de statut HTTP** - Signification et utilisation
4. **Validation des données** - Sécurité et robustesse
5. **Gestion d'erreurs** - Bonnes pratiques
6. **Pagination** - Performance et UX
7. **Documentation API** - Swagger/OpenAPI

---

## 📚 Concepts Expliqués

### 1. Architecture REST

**REST** (Representational State Transfer) est un style architectural qui définit comment créer des services web.

#### Principes fondamentaux :
- **Stateless** : Chaque requête est indépendante
- **Resource-based** : Tout est une ressource (URI)
- **HTTP Methods** : Opérations sur les ressources
- **JSON** : Format d'échange de données

#### Exemple d'URI RESTful :
```
GET    /api/facts        → Liste toutes les facts
GET    /api/facts/123    → Récupère la fact #123
POST   /api/facts        → Crée une nouvelle fact
PUT    /api/facts/123    → Remplace complètement la fact #123
PATCH  /api/facts/123    → Modifie partiellement la fact #123
DELETE /api/facts/123    → Supprime la fact #123
```

### 2. Méthodes HTTP

| Méthode | Usage | Idempotent | Corps requis |
|---------|-------|------------|--------------|
| GET | Récupérer | ✅ | ❌ |
| POST | Créer | ❌ | ✅ |
| PUT | Remplacer | ✅ | ✅ |
| PATCH | Modifier partiellement | ❌ | ✅ |
| DELETE | Supprimer | ✅ | ❌ |

#### Exemples concrets :

**GET** - Récupération simple :
```http
GET /api/facts/1
```

**POST** - Création :
```http
POST /api/facts
Content-Type: application/json

{
  "fact": "Symfony est un framework PHP puissant",
  "techno": "PHP"
}
```

**PUT** - Remplacement complet :
```http
PUT /api/facts/1
Content-Type: application/json

{
  "fact": "Fact complètement modifiée",
  "techno": "JavaScript"
}
```

**PATCH** - Modification partielle :
```http
PATCH /api/facts/1
Content-Type: application/json

{
  "techno": "React"
}
```

### 3. Codes de Statut HTTP

Les codes de statut indiquent le résultat de la requête :

#### Codes de succès (2xx) :
- **200 OK** : Requête réussie
- **201 Created** : Ressource créée
- **204 No Content** : Succès sans contenu

#### Codes d'erreur client (4xx) :
- **400 Bad Request** : Requête malformée
- **401 Unauthorized** : Non authentifié
- **403 Forbidden** : Non autorisé
- **404 Not Found** : Ressource non trouvée
- **422 Unprocessable Entity** : Données invalides

#### Codes d'erreur serveur (5xx) :
- **500 Internal Server Error** : Erreur serveur
- **503 Service Unavailable** : Service indisponible

### 4. Validation des Données

#### Pourquoi valider ?
- **Sécurité** : Prévenir les attaques
- **Robustesse** : Gérer les erreurs gracieusement
- **UX** : Messages d'erreur clairs

#### Exemple de validation :
```php
// Dans le contrôleur
$errors = $this->validator->validate($fact);
if (count($errors) > 0) {
    return new JsonResponse([
        'error' => 'Données invalides',
        'validation_errors' => $errors
    ], Response::HTTP_BAD_REQUEST);
}
```

### 5. Pagination

#### Pourquoi paginer ?
- **Performance** : Éviter de charger trop de données
- **UX** : Navigation facilitée
- **Ressources** : Économie de bande passante

#### Implémentation :
```php
$page = $request->query->get('page', 1);
$limit = $request->query->get('limit', 10);
$offset = ($page - 1) * $limit;

// Requête avec LIMIT et OFFSET
$facts = $repository->findBy([], [], $limit, $offset);
```

---

## 🛠️ Structure du Code

### Contrôleur Principal : `FactController`

```php
#[Route('/api/facts', name: 'api_fact_')]
class FactController extends AbstractController
{
    // Injection de dépendances
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}
    
    // Méthodes CRUD
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    
    // ... autres méthodes
}
```

### Entité : `Fact`

```php
#[ORM\Entity]
#[ApiResource]  // Pour API Platform
class Fact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::TEXT)]
    private ?string $fact = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnregistrement = null;
    
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $techno = null;
    
    // Getters et setters...
}
```

---

## 🧪 Tests et Exemples

### Routes disponibles :

#### Routes CRUD de base :
- `GET /api/facts` - Liste avec pagination
- `GET /api/facts/{id}` - Récupération par ID
- `POST /api/facts` - Création
- `PUT /api/facts/{id}` - Remplacement
- `PATCH /api/facts/{id}` - Modification partielle
- `DELETE /api/facts/{id}` - Suppression

#### Routes d'exemple :
- `GET /api/examples/hello` - Route simple
- `GET /api/examples/search?q=javascript` - Recherche
- `GET /api/examples/technologies` - Liste des technologies
- `POST /api/examples/bulk-create` - Création en lot
- `GET /api/examples/random` - Fact aléatoire
- `GET /api/examples/health` - Santé de l'API

#### Routes spéciales :
- `GET /api/facts/stats` - Statistiques

### Exemples de requêtes :

#### 1. Lister les facts avec pagination :
```http
GET /api/facts?page=1&limit=5
```

#### 2. Filtrer par technologie :
```http
GET /api/facts?techno=PHP
```

#### 3. Créer une fact :
```http
POST /api/facts
Content-Type: application/json

{
  "fact": "Symfony utilise le pattern MVC",
  "techno": "Symfony"
}
```

#### 4. Rechercher :
```http
GET /api/examples/search?q=javascript&techno=PHP
```

#### 5. Créer en lot :
```http
POST /api/examples/bulk-create
Content-Type: application/json

{
  "facts": [
    {"fact": "PHP 8.2 introduit les readonly classes", "techno": "PHP"},
    {"fact": "React 18 améliore le rendu concurrent", "techno": "React"}
  ]
}
```

---

## 📖 Points Pédagogiques

### 1. Gestion d'erreurs
- **Try-catch** : Capturer les exceptions
- **Codes HTTP appropriés** : Indiquer le type d'erreur
- **Messages clairs** : Aider le développeur client

### 2. Injection de dépendances
- **EntityManager** : Gestion de la base de données
- **Serializer** : Conversion JSON ↔ Objet
- **Validator** : Validation des données

### 3. Annotations Symfony
- **#[Route]** : Définition des routes
- **#[ORM\Entity]** : Mapping Doctrine
- **#[ApiResource]** : Configuration API Platform

### 4. Bonnes pratiques
- **Validation** : Toujours valider les entrées
- **Pagination** : Pour les listes importantes
- **Gestion d'erreurs** : Messages informatifs
- **Documentation** : Code auto-documenté

---

## 🚀 Prochaines Étapes

### Niveau débutant :
1. Comprendre les méthodes HTTP
2. Tester avec Postman
3. Analyser les réponses JSON

### Niveau intermédiaire :
1. Créer des entités personnalisées
2. Ajouter de la validation
3. Implémenter l'authentification

### Niveau avancé :
1. Cache et performance
2. Tests automatisés
3. Documentation OpenAPI
4. Déploiement et monitoring

---

## 📚 Ressources

- [Documentation Symfony](https://symfony.com/doc)
- [API Platform](https://api-platform.com/)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [RFC 7231 - HTTP Methods](https://tools.ietf.org/html/rfc7231)
- [RESTful API Design](https://restfulapi.net/)
