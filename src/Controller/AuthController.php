<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AuthController
 * Manage the authentication of website
 * @package App\Controller
 */
class AuthController extends MainController
{
    protected $outputUser = null;
    /**
     * Manage the authentication of website
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __construct()
    {
        $this->outputUser = self::checkAllInput('login');
    }

    public function defaultMethod()
    {
        self::connectOrNot();
    }

    public function connectOrNot()
    {
        $user = self::getUserInDb();

        if ($user) {
            $outputPassword = $this->outputUser['password'];
            $passwordHash   = $user['password'];

            if (self::checkPassword($outputPassword, $passwordHash)) {
                header('Location: /index.php?access=admin');
                self::createSession($user);

            } else {
                header('Location: /index.php?access=log&type=connexion&error=true');
            }
        } else {
            header('Location: /index.php?access=log&type=connexion&error=true');
        }

    }

    public function checkPassword($outputPassword, $passwordHash)
    {
        if ($outputPassword && $passwordHash) {
            return password_verify($outputPassword, $passwordHash);
        }
    }

    public function getUserInDb()
    {
        return ModelFactory::getModel('User')->readData($this->outputUser['email'], 'mail');
    }

    public function createSession($user)
    {
        session_start();
        $_SESSION['id']     = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom']    = $user['nom'];
        $_SESSION['email']  = $user['mail'];
        $_SESSION['actif']  = $user['actif'];
        $_SESSION['admin']  = $user['admin'];
    }

    public function deleteSession()
    {
        session_destroy();
    }
}
