<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'show_post')]
    public function index(Post $post): Response
    {
        return $this->render('post/post.html.twig', [
            'post' => $post,
        ]);
    }
}
