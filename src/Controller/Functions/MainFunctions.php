<?php

namespace App\Controller\Functions;

use App\Model\Factory\ModelFactory;

class MainFunctions
{
    public function test()
    {
        echo '<pre>';
        var_dump("Main function");
        echo '</pre>';
        exit;
    }
    public static function getType()
    {

    }
    public static function getAction()
    {

    }
    public static function getMail()
    {

    }
    public static function getTel()
    {

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
    public static function getImgDir()
    {

    }
    public static function getRequestUri()
    {

    }
    public static function getUser(array $key)
    {
        return ModelFactory::getModel('User')->readData($key[key($key)], key($key));
    }
    public static function getHomeUrl()
    {

    }
    public static function url(string $page, array $params = [])
    {
        $params['access'] = $page;

        return '/index.php?' . http_build_query($params);
    }
    public static function redirect(string $page, array $params = [])
    {
        header('Location: ' . filter_input(INPUT_SERVER, 'HTTP_ORIGIN') . self::url($page, $params));
    }
    public static function isLocalhost()
    {
        return self::getServerIP() == '127.0.0.1';
    }
    public static function checkAllInputs()
    {

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
    public static function uploadImg()
    {

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
}
