<?php

namespace App\Controller;

use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setSlug(strtolower($slugger->slug($category->getCategory())));

            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "La catégorie à bien été ajoutée !");

            return $this->redirectToRoute('category_list');

        }

        $formview = $form->createView();

        return $this->render('category/create.html.twig', [
            'formview' => $formview,
        ]);
    }

    #[Route('/admin/category/list', name: 'category_list')]
    public function list(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'category_edit')]
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            $this->addFlash("danger", "La catégorie n'a pas été trouvée");
            return $this->redirectToRoute('category_list');
        }

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getCategory())));
            $em->flush();

            $this->addFlash("success", "La catégorie à bien été modifiée !");
            return $this->redirectToRoute('category_list');
        }
        $formview = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formview' => $formview
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'category_delete')]
    public function delete($id, CategoryRepository $categoryRepository, EntityManagerInterface $em)
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            $this->addFlash("danger", "La catégorie n'a pas été trouvée");
            return $this->redirectToRoute('category_list');
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash("success", "La catégorie à bien été supprimée !");
        return $this->redirectToRoute('category_list');
    }
}
