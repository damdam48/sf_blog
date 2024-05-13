<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/categories', name: 'admin.categories')]
class CategorieController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }
    #[Route(' ', name: '.index', methods: ['GET'])]
    public function index(CategorieRepository $articleRepo): Response
    {
        return $this->render('backend/Categories/index.html.twig', [
            'categories' => $articleRepo->findAll(),
        ]);
    }

    //create
    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($categorie);
            $this->em->flush();

            $this->addFlash('success', 'Categorie créer');

            return $this->redirectToRoute('admin.categories.index');

        }

        return $this->render('backend/Categories/create.html.twig', [
            'form' => $form,
        ]);
    }

    //update
    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Categorie $categorie, Request $request): Response|RedirectResponse
    {
        if (!$categorie) {
            $this->addFlash('error', 'categorie non trouvé');

            return $this->redirectToRoute('admin.categories.index');
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($categorie);
            $this->em->flush();

            $this->addFlash('success', 'categorie modifié');

            return $this->redirectToRoute('admin.categories.index');
    
        }
        return $this->render('backend/Categories/update.html.twig', [
            'form' => $form,
        ]);
    }
}