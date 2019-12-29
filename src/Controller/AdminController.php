<?php

namespace App\Controller;

use App\Controller\Functions\MainFunctions;
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
        // TestAdmin::test();
        $action = MainFunctions::inputGet('action');
        $type = MainFunctions::inputGet('type');

        if ($action !== 'newPassword') {
            self::redirectLogin();
        }

        if ($action == 'newPassword') {
            self::newPassword();
        }

        // controller posts
        if ($type == 'posts') {
            if ($action == 'view' || $action == 'remove') {
                $posts = self::getPost(['id_user' => $this->session['user']['id']]);
            } elseif ($action == 'update') {
                $posts = self::renderPost();
            } else {
                $posts = false;
            }
        }

        // controller comments
        if ($type == 'comments') {
            if ($action == 'view' || $action == 'update' || $action == 'remove') {
                $comments = self::getComment(['id_user' => $this->session['user']['id']]);
            } else {
                $comments = false;
            }
        } else {
            $comments = false;
        }

        return $this->render('admin.twig', [
            'isActif' => self::isActif(),
            'isAdmin' => self::isAdmin(),
            'user' => self::setUser(),
            'type' => $type,
            'action' => $action,
            'isError' => self::isError(),
            'requestUri' => self::getRequestUri(),
            'lastPostId' => self::getLastPostId(),
            'posts' => isset($posts) ? $posts : false,
            'comments' => isset($comments) ? $comments : false,
        ]);
    }

    public function getUserSession()
    {
        return $this->session['user'];
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

    public function setUser()
    {
        $userSession = self::getUserSession();

        if ($userSession !== null) {
            $array['id'] = $userSession['id'];
            $array['prenom'] = $userSession['prenom'];
            $array['nom'] = $userSession['nom'];
            $array['email'] = $userSession['mail'];
            $array['avatar_img_path'] = $this->setRelativePathImg($userSession['avatar_img_path']);
            return $array;
        }
    }

    public function getPost(array $key = null)
    {
        if (isset($key) && !empty($key)) {
            $posts = ModelFactory::getModel('Post')->listData($key[key($key)], key($key));

            foreach ($posts as $key => $post) {
                foreach ($post as $i => $item) {
                    if ($i == 'description') {
                        $posts[$key][$i] = htmlspecialchars_decode($item);
                    }
                }
            }

            return $posts;
        }

        $posts = ModelFactory::getModel('Post')->listData();
        return $posts;
    }

    public function getComment(array $key = null)
    {
        if (isset($key) && !empty($key)) {
            $commentsDb = ModelFactory::getModel('Comment')->listData($key[key($key)], key($key));

            foreach ($commentsDb as $key => $comment) {
                $idUser = $comment['id_user'];
                $user = ModelFactory::getModel('User')->readData($idUser, 'id');

                $commentsDb[$key]['prenom'] = $user['prenom'];
                $commentsDb[$key]['nom'] = $user['nom'];
                $commentsDb[$key]['avatar'] = $this->setRelativePathImg($user['avatar_img_path']);
                $commentsDb[$key]['content'] = htmlspecialchars_decode($comment['content']);
            }

            return $commentsDb;
        }
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
        }

        return 1;
    }

    public function getRequestUri()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_URI');
    }

    public function getToken()
    {
        return filter_input(INPUT_GET, 'token');
    }

    public function redirectLogin()
    {
        if (self::getUserSession() == null && !self::getToken()) {
            MainFunctions::redirect('log', ['type' => 'connexion']);
        }

        if (self::getToken()) {
            self::forgotPassword();
        }
    }

    public function forgotPassword()
    {
        $idUser = filter_input(INPUT_GET, 'id');

        $user = ModelFactory::getModel('User')->readData($idUser, 'id');

        if ($user) {
            $userToken = $user['token'];
            $userDateToken = $user['dateToken'];

            $dateToken = new DateTime($userDateToken, new DateTimeZone('Europe/Paris'));
            $dateNow = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateDiff = $dateToken->diff($dateNow)->format('%h');

            // // si le token a été initialité il y a plus d'une heure, on redirige
            if ($dateDiff > 0) {
                MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
            }

            MainFunctions::redirect('log', ['type' => 'reset-password', 'token' => $userToken, 'id' => $user['id']]);
            exit;
        }

        MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
    }

    public function newPassword()
    {
        if (isset($this->data)) {
            $email = $this->data['email'];
            $password = $this->data['password'];
            $confirmPassword = $this->data['confirm-password'];

            if ($password === $confirmPassword) {
                $newPassword = password_hash($password, PASSWORD_DEFAULT);
                ModelFactory::getModel('User')->updateData($newPassword, ['password' => $newPassword, 'token' => null, 'dateToken' => null], ['mail' => '"' . $email . '"']);
                MainFunctions::redirect('log', ['type' => 'password-forgot-ok']);
                exit;
            }

            MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
        }

        MainFunctions::redirect('log', ['type' => 'mot-de-passe-oublie']);
    }
}
