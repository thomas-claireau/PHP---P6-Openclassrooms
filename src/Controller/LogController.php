<?php

namespace App\Controller;

use App\Controller\Functions\MainFunctions;
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
        $type = filter_input(INPUT_GET, 'type');
        if ($type) {
            return $type;
        }
    }

    public function isFormError()
    {
        $error = filter_input(INPUT_GET, 'error');
        if (isset($error)) {
            return true;
        }
    }

    public function getInfosNewPassword()
    {
        $userId = filter_input(INPUT_GET, 'id');
        $tokenGet = filter_input(INPUT_GET, 'token');
        if ($userId && $tokenGet) {
            $user = ModelFactory::getModel('User')->readData($userId, 'id');

            $array = [];

            if ($user && $tokenGet === $user['token']) {
                $array['prenom'] = $user['prenom'];
                $array['nom'] = $user['nom'];
                $array['mail'] = $user['mail'];

                return $array;
            }

            MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
        }

        MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
    }
}
