<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PostController
 * Manages the Login page
 * @package App\Controller
 */
class LogController extends MainController
{
    /**
     * Renders the View Log
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        return $this->render('log.twig', [
            'test' => 'LogController',
        ]);
    }
}
