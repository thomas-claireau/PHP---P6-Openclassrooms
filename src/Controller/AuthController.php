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
    protected $data = null;
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
        $this->data = filter_input_array(INPUT_POST);
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
        $user = self::getUser(['mail' => $this->outputUser['mail']]);

        if (isset($user) && !empty($user)) {
            $outputPassword = $this->outputUser['password'];
            $passwordHash = $user['password'];

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

    public function getUser(array $key)
    {
        return ModelFactory::getModel('User')->readData($key[key($key)], key($key));
    }

    public function createSession($user)
    {
        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'mail' => $user['mail'],
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

    public function getType()
    {
        return filter_input(INPUT_GET, 'type');
    }

    // Admin Account
    public function addAccount()
    {
    }

    public function updateAccount()
    {
        session_start();
        $outputData = $this->data;
        $actualData = self::getUser(['id' => $_SESSION['user']['id']]);

        // output
        $name = $outputData['nom'];
        $firstname = $outputData['prenom'];
        $email = $outputData['email'];
        $pass = $outputData['password'];

        // actual
        $actualId = $actualData['id'];
        $actualName = $actualData["nom"];
        $actualFirstname = $actualData["prenom"];
        $actualEmail = $actualData["email"];
        $actualPassHash = $actualData["password"];

        $isCorrectPass = self::checkPassword($pass, $actualPassHash);

        if ($isCorrectPass) {
            $updateArray = array_diff($outputData, $actualData);

            if (isset($updateArray) && !empty($updateArray)) {
                foreach ($updateArray as $key => $item) {
                    if ($key !== "password") {
                        ModelFactory::getModel('User')->updateData($actualData[$key], [$key => $outputData[$key]], ['id' => $actualId]);
                        $_SESSION['user'][$key] = $outputData[$key];
                    }
                }
            }

            $this->redirect('admin', ['type' => 'account', 'action' => 'view']);

        } else {
            $this->redirect('admin', ['type' => 'account', 'action' => 'view', 'error' => true]);
        }
    }

    public function removeAccount()
    {
    }

    // Admin comment

    // Admin Post
}
