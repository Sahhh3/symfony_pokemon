<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CompteController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte', name: 'app_compte')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(CompteFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte = $form->getData();

            // Utilisez password_hash pour hasher le mot de passe
            $password = password_hash($compte->getPassword(), PASSWORD_DEFAULT);
            $compte->setPassword($password);

            // Utilisez le gestionnaire d'entités injecté
            $entityManager = $this->entityManager;
            $entityManager->persist($compte);
            $entityManager->flush();
            

            return $this->redirectToRoute('home');
        }

        return $this->render('compte/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
