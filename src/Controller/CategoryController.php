<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        return $this->render('category/showCategory.html.twig');
    }

    
}
