<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
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
        $allUsers = $this->getAllUsers();

        return $this->render('home.twig', [
            'allUsers' => $this->getAllUsers(),
        ]);
    }

    public function getAllUsers()
    {
        return ModelFactory::getModel('User')->listData();
    }
}
