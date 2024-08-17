<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\Category;
use App\Form\CreateTopicType;
use App\Form\EditCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(
    '/category', 
    name: 'category_'
)]
#[IsGranted('ROLE_USER')]
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'list')]
    #[IsGranted('ROLE_USER')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/list/{id}/show', name:'show')]
    #[IsGranted('ROLE_USER')]
    public function showCategory(Category $category): Response 
    {
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/list/{id}/edit-category', name:'edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editCategory(Category $category, EntityManagerInterface $entityManager, Request $request): Response 
    {
        $form = $this->createForm(EditCategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/edit-category.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/list/{id}/remove-category', name: 'remove')]
    #[IsGranted("ROLE_ADMIN")]
    public function removeCategory(Category $category, EntityManagerInterface $entityManager) {
        
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('category_list');
    }

    #[Route('/list/{id}/show/create-topic', name:'create_topic')]
    #[IsGranted('ROLE_USER')]
    public function createTopic(Category $category, EntityManagerInterface $entityManager, Request $request): Response 
    {

        $topic = new Topic(); 
        $user = $this->getUser();

        $form = $this->createForm(CreateTopicType::class, $topic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $topic = $form->getData();
            $topic->setUser($user);
            $topic->setCategory($category);

            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/create-topic.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }
}
