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
            'errorContact' => $this->isFormError(),
        ]);
    }

    public function isFormSuccess()
    {
        $success = filter_input(INPUT_GET, 'success');
        if (isset($success)) {
            return true;
        } else {
            return false;
        }
    }

    public function isFormError()
    {
        $error = filter_input(INPUT_GET, 'error');
        if (isset($error)) {
            return $error;
        }

        return false;
    }
}
