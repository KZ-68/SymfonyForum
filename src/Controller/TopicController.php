<?php

namespace App\Controller;

use Exception;
use App\Entity\Post;
use App\Entity\Topic;
use App\Form\AddPostType;
use App\Form\EditTopicType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route(
    '/topic', 
    name: 'topic_'
)]
#[IsGranted('ROLE_USER')]
class TopicController extends AbstractController
{
    #[Route('/list', name: 'list')]
    #[IsGranted('ROLE_USER')]
    public function index(TopicRepository $topicRepository): Response
    {
        $topics = $topicRepository->findAll();

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    #[Route('/{id}/show', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(Topic $topic, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['topic' => $topic]);

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'posts' => $posts
        ]);
    }

    #[Route('/{id}/show/add-post', name:'add_post')]
    #[IsGranted('ROLE_USER')]
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

    #[Route('/list/{id}/edit-topic', name:'edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edittopic(Topic $topic, EntityManagerInterface $entityManager, Request $request): Response 
    {
        $form = $this->createForm(EditTopicType::class, $topic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $topic = $form->getData();

            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('topic_list');
        }

        return $this->render('topic/edit-topic.html.twig', [
            'form' => $form,
            'topic' => $topic
        ]);
    }

    #[Route('/list/{id}/remove-topic', name: 'remove')]
    #[IsGranted("ROLE_ADMIN")]
    public function removetopic(Topic $topic, EntityManagerInterface $entityManager) {
        
        $entityManager->remove($topic);
        $entityManager->flush();

        return $this->redirectToRoute('topic_list');
    }
}
