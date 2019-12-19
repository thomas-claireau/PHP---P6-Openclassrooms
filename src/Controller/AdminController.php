<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use DateTime;
use DateTimeZone;
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

        if (self::getAction() !== 'newPassword') {
            self::redirectLogin();
        }

        if (self::getAction() == 'newPassword') {
            self::newPassword();
        }

        if (self::getType() == 'posts' && self::getAction() == 'view' || self::getType() == 'posts' && self::getAction() == 'remove') {
            $posts = self::getPost(['id_user' => $_SESSION['user']['id']]);
        } else {
            $posts = false;
        }

        if (self::getType() == 'posts' && self::getAction() == 'update') {
            $posts = self::renderPost();
        }

        if (self::getType() == 'posts') {
            self::getLastPostId();
        }

        return $this->render('admin.twig', [
            'isActif' => self::isActif(),
            'isAdmin' => self::isAdmin(),
            'user' => self::getUser(),
            'type' => self::getType(),
            'action' => self::getAction(),
            'isError' => self::isError(),
            'requestUri' => self::getRequestUri(),
            'lastPostId' => self::getLastPostId(),
            'posts' => $posts ? $posts : false,
        ]);
    }

    public function getUserSession()
    {
        return $_SESSION['user'];
    }

    public function isAdmin()
    {
        if (self::getUserSession() != null) {
            return self::getUserSession()['admin'];
        }
    }

    public function isActif()
    {
        if (self::getUserSession() != null) {
            return self::getUserSession()['actif'];
        }
    }

    public function isError()
    {
        return filter_input(INPUT_GET, 'error');
    }

    public function getUser()
    {
        $userSession = self::getUserSession();
        if ($userSession !== null) {
            $array['id'] = $userSession['id'];
            $array['prenom'] = $userSession['prenom'];
            $array['nom'] = $userSession['nom'];
            $array['email'] = $userSession['mail'];
            return $array;
        }
    }

    public function getPost(array $key = null)
    {
        if (isset($key) && !empty($key)) {
            return ModelFactory::getModel('Post')->listData($key[key($key)], key($key));
        }

        return ModelFactory::getModel('Post')->listData();
    }

    public function renderPost()
    {
        $idPost = filter_input(INPUT_GET, 'id');
        $post = self::getPost(['id' => $idPost]);
        return $post[0];
    }

    public function getLastPostId()
    {
        $posts = self::getPost();

        if (isset($posts) && !empty($posts)) {
            return $posts[count($posts) - 1]['id'] + 1;
        } else {
            return 1;
        }
    }

    public function getType()
    {
        if (isset($_GET['type'])) {
            return $_GET['type'];
        }
    }

    public function getAction()
    {
        if (isset($_GET['action'])) {
            return $_GET['action'];
        }
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getToken()
    {
        return filter_input(INPUT_GET, 'token');
    }

    public function redirectLogin()
    {
        if (self::getUserSession() == null && !self::getToken()) {
            $this->redirect('log', ['type' => 'connexion']);
        }

        if (self::getToken()) {
            self::forgotPassword();
        }
    }

    public function forgotPassword()
    {
        $idUser = filter_input(INPUT_GET, 'id');
        $getToken = filter_input(INPUT_GET, 'token');

        $user = ModelFactory::getModel('User')->readData($idUser, 'id');

        if ($user) {
            $userToken = $user['token'];
            $userDateToken = $user['dateToken'];

            $dateToken = new DateTime($userDateToken, new DateTimeZone('Europe/Paris'));
            $dateNow = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateDiff = $dateToken->diff($dateNow)->format('%h');

            // // si le token a été initialité il y a plus d'une heure, on redirige
            if ($dateDiff > 0) {
                $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
            } else {
                $this->redirect('log', ['type' => 'reset-password', 'token' => $userToken, 'id' => $user['id']]);
            }

        } else {
            $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
        }
    }

    public function newPassword()
    {
        if (isset($_POST)) {
            $post = filter_input_array(INPUT_POST);
            $email = $post['email'];
            $password = $post['password'];
            $confirmPassword = $post['confirm-password'];

            if ($password === $confirmPassword) {
                $newPassword = password_hash($password, PASSWORD_DEFAULT);
                ModelFactory::getModel('User')->updateData($newPassword, ['password' => $newPassword, 'token' => null, 'dateToken' => null], ['mail' => '"' . $email . '"']);
                $this->redirect('log', ['type' => 'password-forgot-ok']);
            } else {
                $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
            }
        } else {
            $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
        }
    }
}
