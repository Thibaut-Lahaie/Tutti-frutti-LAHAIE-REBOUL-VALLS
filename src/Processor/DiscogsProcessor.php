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

    public function updateDatabaseWithFruit(string $fruit, bool $isEn): void
    {
        // Recherche sur discogs avec le fruit donné
        $response = $this->discogs->search(['q' => $fruit]);
        $results = $response['results'];

        // Récupérer le fruit en base de données
        if ($isEn) {
            $fruit = $this->entityManager->getRepository(Fruit::class)->findOneBy(['nomEn' => $fruit]);
        } else {
            $fruit = $this->entityManager->getRepository(Fruit::class)->findOneBy(['nomFr' => $fruit]);
        }

        echo "/////////////////////////////////////////\n";
        echo "////////Récupération des musiques////////\n";
        echo "/////////////////////////////////////////\n";
        echo "\n";
        // Créer un objet de type Musique avec les informations récupérées
        $musics = [];
        foreach ($results as $result) {
            echo json_encode($result) . "\n";

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
            $id = $result['id'] ?? null;
            if ($id === null) {
                $master = $this->discogs->getMaster(['id' => $id]) ?? null;
                $uri = $master['videos'][0]['uri'] ?? null;
                $music->setLien($uri);
            } else {
                $music->setLien(null);
            }

            // Afficher la référence de la musique dans la console
            echo "Musique trouvée : " . $music->getReference() . "\n";

            $musics[] = $music;
        }

        echo "\n";
        echo "/////////////////////////////////////////\n";
        echo "////////Ajout des musiques en base////////\n";
        echo "/////////////////////////////////////////\n";
        echo "\n";

        // Ajouter les musiques en base de données
        foreach ($musics as $music) {
            // Vérifier si la musique existe déjà en base de données
            $musicEnBase = $this->entityManager->getRepository(Musique::class)->findOneBy(['reference' => $music->getReference()]);
            if ($musicEnBase) {
                echo "La musique " . $music->getReference() . " existe déjà en base de données\n";
                continue;
            }
            $this->entityManager->persist($music);
            $this->entityManager->flush();
            echo "La musique " . $music->getReference() . " a été ajoutée en base de données\n";
        }
        echo "\n";
    }
}