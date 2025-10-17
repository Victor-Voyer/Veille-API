<?php

namespace App\Command;

use App\Entity\Fact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-facts',
    description: 'Charge 30 facts sur le développement dans la base de données',
)]
class LoadFactsCommand extends Command
{
    private array $facts = [
        [
            'fact' => 'Le premier programme informatique a été écrit par Ada Lovelace en 1843.',
            'techno' => 'Histoire'
        ],
        [
            'fact' => 'Le terme "bug" informatique vient d\'un vrai insecte coincé dans un ordinateur en 1947.',
            'techno' => 'Histoire'
        ],
        [
            'fact' => 'JavaScript a été créé en seulement 10 jours par Brendan Eich en 1995.',
            'techno' => 'JavaScript'
        ],
        [
            'fact' => 'Le premier site web était info.cern.ch, créé par Tim Berners-Lee en 1991.',
            'techno' => 'Web'
        ],
        [
            'fact' => 'Python doit son nom à Monty Python\'s Flying Circus, pas au serpent.',
            'techno' => 'Python'
        ],
        [
            'fact' => 'Git a été créé par Linus Torvalds, le créateur de Linux.',
            'techno' => 'Git'
        ],
        [
            'fact' => 'Le premier email a été envoyé en 1971 par Ray Tomlinson.',
            'techno' => 'Email'
        ],
        [
            'fact' => 'React a été développé par Facebook et est open source depuis 2013.',
            'techno' => 'React'
        ],
        [
            'fact' => 'La première version de PHP était un ensemble de scripts CGI écrits en C.',
            'techno' => 'PHP'
        ],
        [
            'fact' => 'Docker utilise des conteneurs Linux pour isoler les applications.',
            'techno' => 'Docker'
        ],
        [
            'fact' => 'Node.js permet d\'exécuter JavaScript côté serveur grâce au moteur V8.',
            'techno' => 'Node.js'
        ],
        [
            'fact' => 'Symfony est un framework PHP mature utilisé par de nombreuses entreprises.',
            'techno' => 'Symfony'
        ],
        [
            'fact' => 'Le principe DRY (Don\'t Repeat Yourself) est fondamental en programmation.',
            'techno' => 'Concepts'
        ],
        [
            'fact' => 'API signifie Application Programming Interface.',
            'techno' => 'API'
        ],
        [
            'fact' => 'REST est un style architectural pour les services web.',
            'techno' => 'REST'
        ],
        [
            'fact' => 'MySQL est le système de gestion de base de données le plus populaire.',
            'techno' => 'MySQL'
        ],
        [
            'fact' => 'SQLite est une base de données embarquée sans serveur.',
            'techno' => 'SQLite'
        ],
        [
            'fact' => 'Composer est le gestionnaire de dépendances officiel de PHP.',
            'techno' => 'Composer'
        ],
        [
            'fact' => 'GitHub héberge plus de 100 millions de dépôts de code.',
            'techno' => 'GitHub'
        ],
        [
            'fact' => 'Le design pattern MVC sépare la logique métier de l\'affichage.',
            'techno' => 'Design Patterns'
        ],
        [
            'fact' => 'TypeScript ajoute le typage statique à JavaScript.',
            'techno' => 'TypeScript'
        ],
        [
            'fact' => 'Vue.js est un framework JavaScript progressif et adoptable.',
            'techno' => 'Vue.js'
        ],
        [
            'fact' => 'Laravel est un framework PHP élégant et expressif.',
            'techno' => 'Laravel'
        ],
        [
            'fact' => 'Redis est une base de données en mémoire très rapide.',
            'techno' => 'Redis'
        ],
        [
            'fact' => 'Elasticsearch permet la recherche full-text en temps réel.',
            'techno' => 'Elasticsearch'
        ],
        [
            'fact' => 'GraphQL permet aux clients de demander exactement les données nécessaires.',
            'techno' => 'GraphQL'
        ],
        [
            'fact' => 'Microservices décomposent les applications en services indépendants.',
            'techno' => 'Architecture'
        ],
        [
            'fact' => 'CI/CD automatise l\'intégration et le déploiement continu.',
            'techno' => 'DevOps'
        ],
        [
            'fact' => 'Kubernetes orchestre les conteneurs à grande échelle.',
            'techno' => 'Kubernetes'
        ],
        [
            'fact' => 'L\'accessibilité web (a11y) rend les sites utilisables par tous.',
            'techno' => 'Accessibilité'
        ]
    ];

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Chargement de 30 facts sur le développement');

        // Vider la table existante
        $this->entityManager->createQuery('DELETE FROM App\Entity\Fact')->execute();

        $progressBar = $io->createProgressBar(count($this->facts));
        $progressBar->start();

        foreach ($this->facts as $factData) {
            $fact = new Fact();
            $fact->setFact($factData['fact']);
            $fact->setTechno($factData['techno']);
            
            $this->entityManager->persist($fact);
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->newLine(2);
        $io->success(sprintf('30 facts ont été chargées avec succès dans la base de données !'));

        return Command::SUCCESS;
    }
}
