<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminController
 * Manages the admin page
 * @package App\Controller
 */
class AdminController extends MainController
{
    /**
     * Renders the View Admin
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        session_start();

        return $this->render('admin.twig', [
            'isActif' => self::isActif(),
            'isAdmin' => self::isAdmin(),
            'user'    => self::getUser(),
        ]);
    }

    public function isAdmin()
    {
        if (isset($_SESSION['admin'])) {
            return $_SESSION['admin'];
        }
    }

    public function isActif()
    {
        if (isset($_SESSION['actif'])) {
            return $_SESSION['actif'];
        }
    }

    public function getUser()
    {
        if (isset($_SESSION['id'])) {
            $array['prenom'] = $_SESSION['prenom'];
            $array['nom']    = $_SESSION['nom'];
            $array['email']  = $_SESSION['email'];
        }

        return $array;
    }
}
