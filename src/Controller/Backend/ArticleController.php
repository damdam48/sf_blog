<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }




    // index
    #[Route(' ', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('Backend/Articles/index.html.twig', [
            'articles' => $articleRepo->findAll(),
        ]);
    }




    // update
    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?article $article, Request $request): Response|RedirectResponse
    {
        if (!$article) {
            $this->addFlash('error', 'L\'article n\'existe pas');
            return $this->redirectToRoute('admin.articles.index');
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'L\'article a bien été mise a jour');
            return $this->redirectToRoute('admin.articles.index');
        }
        return $this->render('Backend/Articles/update.html.twig', [
            'form' => $form,
        ]);
    }




    // create
    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {


        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'L\'article a bien été créé');

            return $this->redirectToRoute('admin.articles.index');
        }


        return $this->render('Backend/Articles/create.html.twig', [
            'form' => $form,
        ]);
    }

    //delete
    #[Route('/{id}/delete', name: '.delete', methods: ['GET', 'POST'])]

    public function delete(?Article $article, Request $request): RedirectResponse
    {
        if (!$article) {
            $this->addFlash('error', 'L\'article n\'existe pas');
            return $this->redirectToRoute('admin.articles.index');
        }
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('token'))) {
            $this->em->remove($article);
            $this->em->flush();

            $this->addFlash('success', 'L\'article a bien été supprimé');
            return $this->redirectToRoute('admin.articles.index');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }
        return $this->redirectToRoute('admin.articles.index');
    }
}
