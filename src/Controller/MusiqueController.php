<?php

namespace App\Controller;

use App\Entity\Musique;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MusiqueController extends AbstractController
{
    #[Route('/musique', name: 'app_musique')]
    public function index(): Response
    {
        return $this->render('musique/index.html.twig', [
            'controller_name' => 'MusiqueController',
        ]);
    }

    #[Route('/musique/{id}', name: 'musique_show', methods: ['GET'])]
    public function show(Musique $musique): Response
    {
        return $this->render('playlist/show.html.twig', [
            'musique' => $musique,
        ]);
    }

}
