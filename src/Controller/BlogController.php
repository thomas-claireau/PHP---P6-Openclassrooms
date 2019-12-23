<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class BlogController
 * Manages the Blog post list
 * @package App\Controller
 */
class BlogController extends MainController
{
    /**
     * Renders the View Blog
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $postsDb = $this->listPosts(['ORDER BY' => ['date' => 'DESC']]);

        $posts = [];

        foreach ($postsDb as $key => $post) {
            $userPost = $this->getUser(['id' => $post['id_user']]);
            $posts[$key]['prenom'] = $userPost['prenom'];
            $posts[$key]['nom'] = $userPost['nom'];
            $post['content'] = htmlspecialchars_decode($post['content']);
            $post['description'] = htmlspecialchars_decode($post['description']);
            $post['title'] = htmlspecialchars_decode($post['title']);

            $posts[$key] = $post;
        }

        return $this->render('blog.twig', [
            'nbPosts' => count($postsDb),
            'listPosts' => $posts,
        ]);
    }
}
