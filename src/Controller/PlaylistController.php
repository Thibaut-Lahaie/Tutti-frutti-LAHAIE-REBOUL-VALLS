<?php

namespace App\Controller;

use App\Entity\Musique;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\MusiqueRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlaylistController extends AbstractController
{


    private MusiqueRepository $musiqueRepository;
    private EntityManagerInterface $entityManager;
    private PlaylistRepository $playlistRepository;

    public function __construct(MusiqueRepository $musiqueRepository, EntityManagerInterface $entityManager, PlaylistRepository $playlistRepository)
    {
        $this->musiqueRepository = $musiqueRepository;
        $this->entityManager = $entityManager;
        $this->playlistRepository = $playlistRepository;
    }

    #[\Symfony\Component\Routing\Annotation\Route('/playlist', name: 'app_playlist', methods: ['GET'])]
    public function index(Request $request, PlaylistRepository $playlistRepository): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $filters = $request->query->all();
            $arr = $playlistRepository->findByFilters($user, $filters);

            return $this->render('playlist/index.html.twig', [
                'playlists' => $arr,
                'user' => $user->getUsername(),
            ]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'musiqueAleatoire' => null,
            'fruit' => null,
        ]);
    }

    #[Route('/playlist/{id}', name: 'app_playlist_show', methods: ['GET'])]
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    #[Route('/playlist/edit/{id}', name: 'app_playlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_playlist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/playlist/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($playlist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_playlist', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/playlist/add/{id}', name:'app_playlist_add', methods: ['GET'])]
    public function add(int $id){
        $user = $this->getUser();
        $playlist = $this->playlistRepository->findOneBy(['utilisateur' => $user->getId(),'musique' => $id]);
        if($playlist == null) {
            $musique = $this->musiqueRepository->findOneBy(['id' => $id]);

            $playlist = new Playlist();
            $playlist->setUtilisateur($user);
            $playlist->setMusique($musique);

            $this->entityManager->persist($playlist);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_playlist', [], Response::HTTP_SEE_OTHER);
    }
}
