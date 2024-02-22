<?php

namespace App\Processor;

use App\Entity\Fruit;
use App\Entity\Musique;
use Discogs\DiscogsClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiscogsProcessor extends AbstractController
{

    private DiscogsClient $discogs;
    private EntityManagerInterface $entityManager;

    public function __construct(DiscogsClient $discogs, EntityManagerInterface $entityManager)
    {
        $this->discogs = $discogs;
        $this->entityManager = $entityManager;
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