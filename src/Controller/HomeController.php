<?php

namespace App\Controller;

use App\Processor\DiscogsProcessor;
use App\Repository\FruitRepository;
use App\Repository\MusiqueRepository;
use Discogs\DiscogsClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private MusiqueRepository $musiqueRepository;

    public function __construct(MusiqueRepository $musiqueRepository, FruitRepository $fruitRepository)
    {
        $this->musiqueRepository = $musiqueRepository;
        $this->fruitRepository = $fruitRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index(DiscogsClient $discogs, DiscogsProcessor $discogsProcessor, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            // Récupérer un fruit aléatoire
            $fruits = $this->fruitRepository->findAll();
            $fruitAleatoire = $fruits[array_rand($fruits)];

            // Mettre à jour la base de données avec le fruit aléatoire
            $discogsProcessor->updateDatabaseWithFruit($fruitAleatoire->getNomEn());

            // Récupérer une musique aléatoire
            $musiques = $this->musiqueRepository->findBy(['fruit' => $fruitAleatoire]);
            $musiqueAleatoire = $musiques[array_rand($musiques)];

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'musiqueAleatoire' => $musiqueAleatoire,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
