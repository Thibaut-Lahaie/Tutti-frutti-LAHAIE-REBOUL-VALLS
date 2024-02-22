<?php

namespace App\Command;

use App\Entity\Fruit;
use App\Entity\Musique;
use Discogs\DiscogsClient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'updateDatabase',
    description: 'Add a short description for your command',
)]
class UpdateDatabaseCommand extends Command
{
    private $discogs; // Assume this is your Discogs service
    private $entityManager; // Assume this is your Doctrine EntityManager

    public function __construct(DiscogsClient $discogs, EntityManagerInterface $entityManager)
    {
        $this->discogs = $discogs;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->addOption('fruit', null, InputOption::VALUE_REQUIRED, 'Specify the fruit for database update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        $fruitOption = $input->getOption('fruit');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($fruitOption) {
            $io->note(sprintf('You specified the fruit: %s', $fruitOption));

            // Appeler la méthode pour mettre à jour la base de données avec le fruit spécifié
            $this->updateDatabaseWithFruit($fruitOption);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    public function updateDatabaseWithFruit(string $fruit): void
    {
        // Recherche sur discogs avec le fruit donné
        $response = $this->discogs->search(['q' => $fruit]);
        $results = $response['results'];

        // Récupérer le fruit en base de données
        $fruit = $this->entityManager->getRepository(Fruit::class)->findOneBy(['nomEn' => $fruit]);

        // Créer un objet de type Musique avec les informations récupérées
        $musics = [];
        foreach ($results as $result) {
            $music = new Musique();
            $music->setReference($result['title'] ?? null);
            $music->setAnnee($result['year'] ?? null);
            $music->setNomDeGroupe(explode('-', $result['title'])[0] ?? null);
            $music->setLabel(explode('-', $result['title'])[1] ?? null);
            $music->setGenre($result['genre'][0] ?? null);
            $music->setFormat($result['format'][0] ?? null);
            $music->setStyle($result['style'][0] ?? null);
            $music->setImage($result['thumb'] ?? null);
            $music->setFruit($fruit);

            // Récupérer le lien de la vidéo youtube
            $id = $result['id'];
            var_dump($result['id']);
            if ($id) {
                try {
                    $master = $this->discogs->getMaster(['id' => $id]);
                } catch (\Exception $e) {
                    $musics[] = $music;
                    continue;
                }
                try {
                    var_dump($master);
                    $uri = $master['videos'][0]['uri'];
                } catch (\Exception $e) {
                    $musics[] = $music;
                    continue;
                }
                $music->setLien($uri);
            }
            $musics[] = $music;
        }


        // Ajouter les musiques en base de données
        foreach ($musics as $music) {
            // Vérifier si la musique existe déjà en base de données
            $musicEnBase = $this->entityManager->getRepository(Musique::class)->findOneBy(['reference' => $music->getReference()]);
            if ($musicEnBase) {
                continue;
            }
            $this->entityManager->persist($music);
            $this->entityManager->flush();
        }
    }
}
