<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(
    '/topic', 
    name: 'topic_'
)]
class TopicController extends AbstractController
{
    #[Route('/{id}/show', name: 'show')]
    public function index(Topic $topic, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['topic' => $topic]);

        return $this->render('topic/index.html.twig', [
            'topic' => $topic,
            'posts' => $posts
        ]);
    }
}
