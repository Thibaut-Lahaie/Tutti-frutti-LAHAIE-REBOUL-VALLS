<?php

namespace App\Command;

use App\Entity\Fruit;
use App\Entity\Musique;
use App\Processor\DiscogsProcessor;
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
    private DiscogsClient $discogs;

    private DiscogsProcessor $discogsProcessor;
    private EntityManagerInterface $entityManager;

    public function __construct(DiscogsClient $discogs, EntityManagerInterface $entityManager, DiscogsProcessor $discogsProcessor)
    {
        $this->discogs = $discogs;
        $this->entityManager = $entityManager;
        $this->discogsProcessor = $discogsProcessor;

        ini_set('max_execution_time', 300); // 300 secondes (5 minutes) par exemple

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('fruit', null, InputOption::VALUE_REQUIRED, 'Specify the fruit for database update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fruitOption = $input->getOption('fruit');

        if ($fruitOption) {
            $io->note(sprintf('You specified the fruit: %s', $fruitOption));

            // Appeler la méthode pour mettre à jour la base de données avec le fruit spécifié
            $this->updateDatabaseWithFruit($fruitOption);
        } else {
            $io->note('You run the command for all fruits');

            // Appeler la méthode pour mettre à jour la base de données avec tout les fruits de la base de données
            $this->updateDatabaseWithAllFruit();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function updateDatabaseWithFruit(string $fruit): void
    {
        // Récupérer tous les fruits en base de données
        $fruit = $this->entityManager->getRepository(Fruit::class)->findOneBy(['nomEn' => $fruit]);

        $this->discogsProcessor->updateDatabaseWithFruit($fruit->getNomEn(), true);
        $this->discogsProcessor->updateDatabaseWithFruit($fruit->getNomFr(), false);
    }
    public function updateDatabaseWithAllFruit(): void
    {
        // Récupérer tous les fruits en base de données
        $fruits = $this->entityManager->getRepository(Fruit::class)->findAll();

        // Pour chaque fruit, appeler la méthode pour mettre à jour la base de données
        foreach ($fruits as $fruit) {
            $this->discogsProcessor->updateDatabaseWithFruit($fruit->getNomEn(), true);
            $this->discogsProcessor->updateDatabaseWithFruit($fruit->getNomFr(), false);
        }
    }
}
