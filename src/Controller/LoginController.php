<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginFormType::class);

        $form->handleRequest($request);

        $error = null;
        $lastUsername = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->entityManager->getRepository(Compte::class)->findOneBy(['pseudo' => $data['pseudo']]);

            if ($user && password_verify($data['password'], $user->getPassword())) {
                $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);

                return $this->redirectToRoute('home');
            } else {
                $error = 'Invalid credentials';
                $lastUsername = $data['pseudo'];
            }
        }

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // cette méthode ne sera jamais exécutée
    }
}
