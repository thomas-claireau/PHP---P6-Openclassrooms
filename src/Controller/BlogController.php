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
        $posts = $this->listPosts(['ORDER BY' => ['date' => 'DESC']]);

        foreach ($posts as $key => $post) {
            $userPost = $this->getUser(['id' => $post['id_user']]);
            $posts[$key]['prenom'] = $userPost['prenom'];
            $posts[$key]['nom'] = $userPost['nom'];
        }

        return $this->render('blog.twig', [
            'nbPosts' => count($posts),
            'listPosts' => $posts,
        ]);
    }
}
