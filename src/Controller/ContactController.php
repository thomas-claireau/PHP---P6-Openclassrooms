<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ContactController
 * Manages the ContactPage
 * @package App\Controller
 */
class ContactController extends MainController
{
    /**
     * Renders the View Contact
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {

        return $this->render('contact.twig', [
            'success' => $this->isFormSuccess(),
            'error'   => $this->isFormError(),
        ]);
    }

    public function isFormSuccess()
    {
        if (isset($_GET['success'])) {
            return true;
        } else {
            return false;
        }
    }

    public function isFormError()
    {
        if (isset($_GET['error'])) {
            return $_GET['error'];
        } else {
            return false;
        }
    }
}
