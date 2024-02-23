<?php

namespace App\Controller;

use App\Repository\FruitRepository;
use App\Repository\MusiqueRepository;
use App\Repository\UtilisateurRepository;
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
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_fruit', ['nomFruit' => 'watermelon']);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'musiqueAleatoire' => null,
            'fruit' => null,
        ]);
    }

    #[Route('/home/{nomFruit}', name: 'app_home_fruit')]
    public function home(string $nomFruit): Response
    {
        // Récupérer le fruit
        $fruit = $this->fruitRepository->findOneBy(['nomEn' => $nomFruit]);
        if (!$fruit) {
            $fruit = $this->fruitRepository->findOneBy(['nomFr' => $nomFruit]);
        }

        // Récupérer une musique aléatoire
        $musiques = $this->musiqueRepository->findBy(['fruit' => $fruit]);
        $musiqueAleatoire = $musiques[array_rand($musiques)];

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'musiqueAleatoire' => $musiqueAleatoire,
            'fruit' => $fruit,
        ]);
    }
}
