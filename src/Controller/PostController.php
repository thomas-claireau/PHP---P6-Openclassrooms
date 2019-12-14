<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PostController
 * Manages the Post item
 * @package App\Controller
 */
class PostController extends MainController
{
    /**
     * Renders the View Post
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $action = self::getAction();

        if (isset($action) && !empty($action)) {
            self::$action();
        } else {
            return $this->render('post.twig', [
                'test' => 'PostController',
            ]);
        }
    }

    public function getAction()
    {
        return filter_input(INPUT_GET, 'action');
    }

    public function create()
    {
        echo '<pre>';
        var_dump($this->checkAllInput());
        echo '</pre>';
        exit;
    }

}
