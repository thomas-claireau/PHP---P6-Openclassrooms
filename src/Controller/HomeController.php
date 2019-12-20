<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * Manages the Homepage
 * @package App\Controller
 */
class HomeController extends MainController
{
    /**
     * Renders the View Home
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $posts = $this->listPosts(['ORDER BY' => ['date' => 'DESC'], 'LIMIT' => 3]);

        foreach ($posts as $key => $post) {
            $userPost = $this->getUser($post['id_user']);
            $posts[$key]['prenom'] = $userPost['prenom'];
            $posts[$key]['nom'] = $userPost['nom'];
        }

        return $this->render('home.twig', [
            'nbPosts' => count($posts),
            'listPosts' => $posts,
        ]);
    }
}
