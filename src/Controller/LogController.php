<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
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
        if (self::getType() == 'reset-password') {
            $user = self::getInfosNewPassword();
        }

        return $this->render('log.twig', [
            'type' => $this->getType(),
            'errorLog' => $this->isFormError(),
            'user' => isset($user) ? $user : false,
        ]);
    }

    public function getType()
    {
        if ($_GET['type']) {
            return filter_input(INPUT_GET, 'type');
        }
    }

    public function isFormError()
    {
        if (isset($_GET['error'])) {
            return true;
        }
    }

    public function getInfosNewPassword()
    {
        if ($_GET['id'] && $_GET['token']) {
            $userId = filter_input(INPUT_GET, 'id');
            $tokenGet = filter_input(INPUT_GET, 'token');

            $user = ModelFactory::getModel('User')->readData($userId, 'id');

            $array = [];

            if ($user && $tokenGet === $user['token']) {
                $array['prenom'] = $user['prenom'];
                $array['nom'] = $user['nom'];
                $array['mail'] = $user['mail'];

                return $array;
            } else {
                $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
            }
        } else {
            $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
        }
    }
}
