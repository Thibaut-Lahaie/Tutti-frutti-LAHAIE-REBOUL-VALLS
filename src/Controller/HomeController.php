<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function getFruitsFromDiscogs(): array
    {
        // Créer une instance du client HttpClient
        $httpClient = HttpClient::create();

        // Définir l'URL de l'API Discogs
        $apiUrl = 'https://api.discogs.com/database/search?q=fruit&type=release&per_page=3&page=1';
        // Ajouter un token d'accès à l'entête de la requête
        $apiUrl .= '&token=' . $_ENV['DISCOGS_API_TOKEN'];

        // Effectuer la requête GET
        $response = $httpClient->request('GET', $apiUrl);

        // Récupérer le contenu de la réponse
        return $response->toArray();
    }
}
