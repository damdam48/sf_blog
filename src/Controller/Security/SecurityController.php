<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('Security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'lastUsername' => $authUtils->getLastUsername(),
        ]);
    }

    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On hash le mot de passe
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('password')->getData()),
            );

            // On envois le user en BDD
            $em->persist($user);
            $em->flush();

            // On définit un message flash
            $this->addFlash('success', 'Vous êtes bien inscrit à notre application');

            return $this->redirectToRoute('app.login');
        }

        return $this->render('Security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
