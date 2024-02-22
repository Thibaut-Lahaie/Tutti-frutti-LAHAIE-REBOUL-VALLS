<?php

namespace App\Controller;

use App\Processor\DiscogsProcessor;
use App\Repository\FruitRepository;
use App\Repository\MusiqueRepository;
use App\Repository\UtilisateurRepository;
use Discogs\DiscogsClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private MusiqueRepository $musiqueRepository;
    private UtilisateurRepository $utilisateurRepository;
    private FruitRepository $fruitRepository;

    public function __construct(MusiqueRepository $musiqueRepository, FruitRepository $fruitRepository, UtilisateurRepository $utilisateurRepository)
    {
        $this->musiqueRepository = $musiqueRepository;
        $this->fruitRepository = $fruitRepository;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index() : Response
    {
        if ($this->getUser()) {
            // Récupérer un fruit aléatoire
            $fruits = $this->fruitRepository->findAll();
            $fruitAleatoire = $fruits[array_rand($fruits)];

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
            'musiqueAleatoire' => null,
        ]);
    }

}
