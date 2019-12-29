<?php

namespace App\Controller\Functions;

use App\Controller\MainController;
use App\Model\Factory\ModelFactory;

class MainFunctions extends MainController
{
    public static function inputGet($get)
    {
        return filter_input(INPUT_GET, $get);
    }

    public static function inputServer($key = false, $array = true)
    {
        return $array && !$key ? filter_input_array(INPUT_SERVER) : filter_input(INPUT_SERVER, $key);
    }

    public static function inputPost($key = false, $isArray = true)
    {
        if (!$isArray && $key) {
            $post = filter_input(INPUT_POST, $key);
            switch ($key) {
                case 'mail':
                    if (filter_var($post, FILTER_VALIDATE_EMAIL)) {
                        return $post;
                    }
                    return false;
                    break;
                case 'tel':
                    $post = str_replace(' ', '', $post);
                    $post = str_replace('-', '', $post);
                    $post = str_replace('.', '', $post);

                    if (preg_match("/^((\+)33|0)[1-9](\d{2}){4}$/", $post)) {
                        return $post;
                    }

                    return false;
                    break;
                default:
                    return $post;
            }
        }

        return filter_input_array(INPUT_POST);
    }

    public static function getTemplateName()
    {
        $access = self::inputGet('access');
        if (isset($access)) {
            return htmlspecialchars($access);
        } else {
            return 'home';
        }
    }

    private static function getServerIP()
    {
        $adresse = '';
        $server = filter_input_array(INPUT_SERVER);

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $server)) {
            $adresse = $server["HTTP_X_FORWARDED_FOR"];
        } else if (array_key_exists('REMOTE_ADDR', $server)) {
            $adresse = $server["REMOTE_ADDR"];
        } else if (array_key_exists('HTTP_CLIENT_IP', $server)) {
            $adresse = $server["HTTP_CLIENT_IP"];
        }

        return $adresse;
    }

    public static function getCurrentPath()
    {
        return self::inputServer('DOCUMENT_ROOT');
    }

    public static function getImgDir()
    {
        // $HTTP_HOST = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $HTTP_HOST = self::inputServer('HTTP_HOST');
        $isDist = self::folder_exist('dist');
        $isDev = self::isLocalhost();

        if ($isDist && !$isDev) {
            $publicPath = $HTTP_HOST . '/dist/' . 'assets/img/';
        } else {
            $publicPath = '//' . $HTTP_HOST . '/src/' . 'assets/img/';

        }

        return $publicPath;
    }
    public static function getUser(array $key)
    {
        return ModelFactory::getModel('User')->readData($key[key($key)], key($key));
    }
    public static function getHomeUrl()
    {
        return 'https://' . self::inputServer('HTTP_HOST');
    }
    public static function url(string $page, array $params = [])
    {
        $params['access'] = $page;

        return '/index.php?' . http_build_query($params);
    }

    public static function getUrl()
    {
        return self::inputServer('REQUEST_SCHEME') . '://' . self::inputServer('HTTP_HOST');
    }
    public static function redirect(string $page, array $params = [])
    {
        header('Location: ' . filter_input(INPUT_SERVER, 'HTTP_ORIGIN') . self::url($page, $params));
    }
    public static function isLocalhost()
    {
        return self::getServerIP() == '127.0.0.1';
    }
    public static function checkAllInput($context)
    {
        $post = MainController::getData();

        if ($context == 'contact') {
            $location = 'contact';
            $params = array();
        } elseif ($context == 'login') {
            $location = 'log';
            $params = ['type' => 'connexion'];
        } else {
            return false;
        }

        $getMail = self::inputPost('mail', false);
        $getTel = self::inputPost('tel', false);

        if (isset($post['email']) && $getMail == false) {
            array_push($params, ['error' => 'mail']);
            self::redirect($location, $params);
        } elseif (isset($post['tel']) && $getTel == false) {
            array_push($params, ['error' => 'tel']);
            self::redirect($location, $params);
        } else {
            if (isset($post) && !empty($post)) {
                $array = [];
                foreach ($post as $key => $item) {
                    if ($key == 'email') {
                        $array[$key] = $getMail;
                    } elseif ($key == 'tel') {
                        $array[$key] = $getTel;
                    } else {
                        $array[$key] = $item;
                    }
                }

                return $array;
            }
        }
    }
    public static function listPosts(array $params)
    {
        $posts = ModelFactory::getModel('Post')->listData(null, null, $params);

        foreach ($posts as $key => $post) {
            $idUser = $post['id_user'];

            $userOfPost = ModelFactory::getModel('User')->readData($post['id_user'], $idUser);

            $avatar = $userOfPost['avatar_img_path'];
            $posts[$key]['avatar_img_path'] = self::setRelativePathImg($avatar);

            $userPost = self::getUser(['id' => $post['id_user']]);
            $posts[$key]['prenom'] = $userPost['prenom'];
            $posts[$key]['nom'] = $userPost['nom'];
            $posts[$key]['content'] = htmlspecialchars_decode($post['content']);
            $posts[$key]['description'] = htmlspecialchars_decode($post['description']);
            $posts[$key]['title'] = htmlspecialchars_decode($post['title']);
        }

        return $posts;
    }

    public static function setRelativePathImg($string)
    {
        if (strpos($string, '<img')) {
            $replacement = self::isLocalhost() ? '<img src="./src/' : '<img src="./dist/';
            $regex = ["#<img src=\"src/#", "#<img src=\"dist/#"];
        } else {
            $replacement = self::isLocalhost() ? './src/' : './dist/';
            $regex = ["#./src/#", "#./dist/#"];
        }

        foreach ($regex as $item) {
            $string = preg_replace($item, $replacement, $string);
        }

        return $string;
    }

    public static function folder_exist($folder)
    {
        return file_exists(self::getCurrentPath() . $folder);
    }
}