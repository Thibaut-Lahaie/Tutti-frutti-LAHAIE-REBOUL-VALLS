<?php

namespace App\Controller;

use App\Repository\FruitRepository;
use App\Repository\MusiqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    private MusiqueRepository $musiqueRepository;
    private FruitRepository $fruitRepository;

    public function __construct(MusiqueRepository $musiqueRepository, FruitRepository $fruitRepository)
    {
        $this->musiqueRepository = $musiqueRepository;
        $this->fruitRepository = $fruitRepository;
    }

    //<form class="search-form" action="{{ path('app_search') }}" method="get">
    //                    <input type="text" name="titre" placeholder="Rechercher une musique...">
    //                    <input type="text" name="nomDeGroupe" placeholder="Rechercher un groupe...">
    //                    <input type="text" name="label" placeholder="Rechercher un label...">
    //                    <button type="submit">Rechercher</button>
    //                </form>

    #[Route('/search', name: 'app_search')]
    public function index(): Response
    {
        // Réception des données du formulaire
        $titre = $_GET['titre'] ?? null;
        $nomDeGroupe = $_GET['nomDeGroupe'] ?? null;
        $label = $_GET['label'] ?? null;
        $fruit = $_GET['fruit'] ?? null;

        // Récupérer les musiques de la base de données
        $musiques = $this->musiqueRepository->search($titre, $nomDeGroupe, $label, $fruit);

        // Récupérer les fruits de la base de données
        $fruits = $this->fruitRepository->findAll();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'musiques' => $musiques,
            'fruits' => $fruits,
        ]);
    }
}
