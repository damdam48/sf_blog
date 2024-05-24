<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('Backend/Articles/index.html.twig', [
            'articles' => $articleRepo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $article = new Article;

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $article->setUser($user);

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'Article créé avec succès');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render('Backend/Articles/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Article $article, Request $request): Response|RedirectResponse
    {
        if (!$article) {
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('admin.articles.index');
        }

        $form = $this->createForm(ArticleType::class, $article, ['isEdit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'Article mis à jour avec succès');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render('Backend/Articles/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Article $article, Request $request): RedirectResponse
    {
        if (!$article) {
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('admin.articles.index');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('token'))) {
            $this->em->remove($article);
            $this->em->flush();

            $this->addFlash('success', 'Article supprimé avec succès');
        } else {
            $this->addFlash('error', 'Token invalide');
        }

        return $this->redirectToRoute('admin.articles.index');
    }

    #[Route('/{id}/switch', name: '.switch', methods: ['GET'])]
    public function switch(?Article $article): JsonResponse
    {
        if (!$article) {
            return $this->json([
                'status' => 'error',
                'message' => 'Article non trouvé'
            ], 404);
        }

        $article->setEnable(!$article->isEnable());
        $this->em->persist($article);
        $this->em->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Article mis à jour avec succès',
            'enable' => $article->isEnable()
        ]);
    }
}
