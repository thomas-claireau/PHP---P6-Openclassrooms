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
        $action = self::getAction();

        if (isset($action) && !empty($action)) {
            self::$action();
        } else {
            $this->redirect('home');
        }
    }

    public function connexion()
    {
        $user = self::getUserInDb();

        if (isset($user) && !empty($user)) {
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
        $_SESSION['user'] = [
            'id' => $user['id'],
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'email' => $user['mail'],
            'actif' => $user['actif'],
            'admin' => $user['admin'],
        ];
    }

    public function deconnexion()
    {
        setcookie("PHPSESSID", "", time() - 3600, "/");
        session_destroy();
        $this->redirect('home');
    }

    public function getAction()
    {
        return filter_input(INPUT_GET, 'action');
    }
}
