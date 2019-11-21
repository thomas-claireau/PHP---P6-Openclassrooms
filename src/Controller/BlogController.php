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
        return $this->render('blog.twig', [
            'test' => 'BlogController',
        ]);
    }
}
