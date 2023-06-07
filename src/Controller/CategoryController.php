<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Manager\FileManager;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    #[Route('/admin/category', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository, FileManager $fileManager, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('category')['icon'];
            if ($file) {
                $uploadedFile = $fileManager->uploadFile($file, false);
                $entityManager->persist($uploadedFile);
                $category->setIcon($uploadedFile);
            }
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository, FileManager $fileManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('category')['icon'];
            if ($file) {
                $uploadedFile = $fileManager->uploadFile($file, false);
                $entityManager->persist($uploadedFile);
                $category->setIcon($uploadedFile);
            }

            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }


    #[Route('/admin/category/{id}/media/{mediaId}', name: 'app_category_media_delete', methods: ['GET'])]
    public function mediaDelete(Request $request, Category $category, MediaRepository $mediaRepository, EntityManagerInterface $entityManager): Response
    {
        $mediaId = $request->attributes->get('mediaId');
        $media = $mediaRepository->find($mediaId);
        if ($media and $category->getIcon() === $media) {
            $category->setIcon(null);
            $mediaRepository->remove($media);
            $entityManager->flush();
            $this->addSuccessFlash();
            return $this->redirectToRoute('app_category_edit', ['id' => $category->getId()]);
        }

        $this->addWarningFlash('Vous ne pouvez pas supprimer ce média');
        return $this->redirectToRoute('app_category_edit', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
    }
}
