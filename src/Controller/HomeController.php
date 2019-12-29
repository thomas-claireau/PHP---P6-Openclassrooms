<?php

namespace App\Controller;

use App\Controller\Functions\MainFunctions;
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
        $posts = MainFunctions::listPosts(['ORDER BY' => ['date' => 'DESC'], 'LIMIT' => 3]);

        return $this->render('home.twig', [
            'nbPosts' => count($posts),
            'listPosts' => $posts,
        ]);
    }
}
