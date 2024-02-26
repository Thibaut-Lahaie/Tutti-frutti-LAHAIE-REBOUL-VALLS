<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// Ajout de l'importation nécessaire

#[Route('/inscription')]
class InscriptionController extends AbstractController
{
    #[Route('/', name: 'app_inscription')]
    public function index(
        Request                $request,
        EntityManagerInterface $entityManager,
        UtilisateurRepository  $utilisateurRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $password = $utilisateur->getPassword();

            // Encodage du mot de passe avec l'algorithme bcrypt
            $encodedPassword = $passwordHasher->hashPassword($utilisateur, $password);
            $utilisateur->setPassword($encodedPassword);

            // Vérifier si l'utilisateur existe déjà
            $utilisateurEnBase = $utilisateurRepository->findOneBy(['username' => $utilisateur->getUsername()]);
            if ($utilisateurEnBase) {
                $this->addFlash('error', 'Cet utilisateur existe déjà');
                return $this->redirectToRoute('app_inscription');
            }

            // Persiste l'utilisateur en base de données
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Redirige vers la page de connexion ou une autre page après l'inscription
            return $this->redirectToRoute('app_login');
        }

        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
