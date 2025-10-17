<?php
/**
 * Script de déploiement pour O2Switch
 * À exécuter une seule fois après l'upload des fichiers
 */

echo "<h1>Déploiement API Veille - O2Switch</h1>";

try {
    // Vérifier que nous sommes en production
    if (!file_exists('.env.prod.local')) {
        throw new Exception("Fichier .env.prod.local manquant");
    }

    // Charger l'environnement
    $dotenv = new \Symfony\Component\Dotenv\Dotenv();
    $dotenv->load('.env.prod.local');

    // Initialiser Symfony
    require_once 'vendor/autoload.php';
    $kernel = new App\Kernel('prod', false);
    $kernel->boot();
    $container = $kernel->getContainer();

    echo "<h2>✅ Symfony initialisé</h2>";

    // Créer la base de données
    $entityManager = $container->get('doctrine.orm.entity_manager');
    $connection = $entityManager->getConnection();
    
    // Vérifier si la table existe
    $schemaManager = $connection->createSchemaManager();
    if (!$schemaManager->tablesExist(['fact'])) {
        echo "<h2>📊 Création de la base de données...</h2>";
        
        // Créer le schéma
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $schemaTool->createSchema($metadata);
        
        echo "<p>✅ Base de données créée</p>";
    } else {
        echo "<p>✅ Base de données existe déjà</p>";
    }

    // Charger les facts de test
    echo "<h2>📝 Chargement des facts...</h2>";
    
    $command = new App\Command\LoadFactsCommand($entityManager);
    $input = new \Symfony\Component\Console\Input\ArrayInput([]);
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    
    $command->run($input, $output);
    
    echo "<p>✅ Facts chargées</p>";
    echo "<pre>" . $output->fetch() . "</pre>";

    // Vérifier le nombre de facts
    $count = $entityManager->createQuery('SELECT COUNT(f.id) FROM App\Entity\Fact f')->getSingleScalarResult();
    echo "<p>📊 Nombre de facts en base : <strong>$count</strong></p>";

    echo "<h2>🎉 Déploiement terminé avec succès !</h2>";
    echo "<p><strong>Votre API sécurisée est maintenant accessible :</strong></p>";
    echo "<ul>";
    echo "<li><a href='https://veilleapi.webatif.fr/api/facts' target='_blank'>https://veilleapi.webatif.fr/api/facts</a> - Liste des facts</li>";
    echo "<li><a href='https://veilleapi.webatif.fr/api/docs' target='_blank'>https://veilleapi.webatif.fr/api/docs</a> - Documentation API</li>";
    echo "</ul>";

    echo "<h3>⚠️ IMPORTANT :</h3>";
    echo "<p>Supprimez ce fichier deploy.php après le déploiement pour des raisons de sécurité !</p>";

} catch (Exception $e) {
    echo "<h2>❌ Erreur lors du déploiement</h2>";
    echo "<p><strong>Erreur :</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Ligne :</strong> " . $e->getLine() . "</p>";
}
?>
