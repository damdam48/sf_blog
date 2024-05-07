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

#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ){
    }

    #[Route(' ', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('Backend/Articles/index.html.twig', [
            'articles' => $articleRepo->findAll(),
        ]);
    }






    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
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
}
