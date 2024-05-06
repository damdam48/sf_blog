<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

#[Route('/admin/users', name: 'admin.users')]
class userController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(UserRepository $userRepos): Response
    {
        return $this->render('Backend/Users/index.html.twig', [
            'users' => $userRepos->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: '.update', methods: ['GET', 'POST'])]
    public function update(?User $user, Request $request): Response|RedirectResponse
    {
        // on vérifie que l'utilisateur existe
        if (!$user) {
            $this->addFlash('error', 'L\'utilisateur n\'existe pas');

            return $this->redirectToRoute('admin.users.index');
        }

        $form = $this->createForm(UserType::class, $user, ['isAdmin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été modifié');
            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('Backend/Users/update.html.twig', [
            'form' => $form,
        ]);
    }
}
