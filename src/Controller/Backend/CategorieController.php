<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function index(CategorieRepository $categorieRepo): Response
    {
        return $this->render('backend/Categories/index.html.twig', [
            'categories' => $categorieRepo->finAllOrderByName(),
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

    //delete
    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Categorie $categorie, Request $request): RedirectResponse
    {
        if (!$categorie) {
            $this->addFlash('error', 'categorie non trouvé');

            return $this->redirectToRoute('admin.categories.index');
        }
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('token'))) {
            $this->em->remove($categorie);
            $this->em->flush();

            $this->addFlash('success', 'categorie supprimé');
            return $this->redirectToRoute('admin.categories.index');

        }else {
            $this->addFlash('error', 'Token CSRF invalide');
        }
        return $this->redirectToRoute('admin.categories.index');

    }

        //switch
        #[Route('/{id}/switch', name: '.switch', methods: ['GET'])]
        public function switch(?Categorie $categorie): JsonResponse
        {
            if (!$categorie) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Categorie introuvable',
                ], 404 );
            }
    
            $categorie->setEnable(!$categorie->isEnable());
    
            $this->em->persist($categorie);
            $this->em->flush();
    
            return new JsonResponse([
                'status' => 'ok',
                'message' => 'Article mis à jour',
                'enable' => $categorie->isEnable(),
            ]);
        }
}