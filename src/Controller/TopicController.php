<?php

namespace App\Controller;

use Exception;
use App\Entity\Post;
use App\Entity\Topic;
use App\Form\AddPostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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

    #[Route('/{id}/show/add-post', name:'add_post')]
    public function addPost(Topic $topic, EntityManagerInterface $entityManager, Request $request): Response 
    {
        $post = new Post(); 
        $user = $this->getUser();
        if($user == NULL) {
            $form = $this->createForm(AddPostType::class, $post);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $post = $form->getData();
                $post->setUser($user);
                $post->setTopic($topic);

                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
            }
        } else {
            throw new AuthenticationException("You have to login in order to post in the forum", 1);
        }
        
        return $this->render('topic/add-post.html.twig', [
            'form' => $form,
            'topic' => $topic
        ]);
    }
}
