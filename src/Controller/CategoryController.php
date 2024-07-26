<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\Category;
use App\Form\CreateTopicType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(
    '/category', 
    name: 'category_'
)]
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/list/{id}/show', name:'show')]
    public function showCategory(Category $category): Response 
    {
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/list/{id}/show/create-topic', name:'create_topic')]
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
