<?php

namespace App\Controller;

use App\Controller\Extension\PhpMvcExtension;
use App\Controller\Functions\MainFunctions;
use App\Model\Factory\ModelFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Class MainController
 * Manages the Main Features
 * @package App\Controller
 */
abstract class MainController
{
    /**
     * @var Environment|null
     */
    protected $twig = null;
    protected $files = null; // file upload
    protected $session = null; // session user
    protected $data = null; // data of super global post
    protected $outputUser = null; // auth -> user interaction

    /**
     * MainController constructor
     * Creates the Template Engine & adds its Extensions
     */
    public function __construct()
    {
        session_start();
        $this->files = filter_var_array($_FILES);
        $this->session = filter_var_array($_SESSION);
        $this->data = MainFunctions::inputPost();
        $this->outputUser = self::checkAllInput('login');

        self::setupTwig();
        self::addGlobals();
    }

    public function setupTwig()
    {
        $this->twig = new Environment(new FilesystemLoader('../src/View'), array(
            'cache' => false,
            'debug' => true,
        ));
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new PhpMvcExtension());
    }

    public function addGlobals()
    {
        // add global variables
        $this->twig->addGlobal('isLocalhost', MainFunctions::isLocalhost());
        $this->twig->addGlobal('url', $this->getUrl());
        $this->twig->addGlobal('isDistFolder', $this->folder_exist('dist'));
        $this->twig->addGlobal('templateName', $this->getTemplateName());
        $this->twig->addGlobal('imgDir', $this->getImgDir());
        $this->twig->addGlobal('homeUrl', $this->getHomeUrl());
        $this->twig->addGlobal('avatar_default', MainFunctions::isLocalhost() ? './src/assets/img/pictos/default_avatar.png' : './dist/assets/img/pictos/default_avatar.png');
    }

    /**
     * Renders the Views
     * @param string $view
     * @param array $params
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $view, array $params = [])
    {
        return $this->twig->render($view, $params);
    }

    public function getUrl()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_SCHEME') . '://' . filter_input(INPUT_SERVER, 'HTTP_HOST');
    }

    public static function getCurrentPath()
    {
        return filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
    }

    public static function folder_exist($folder)
    {
        return file_exists(self::getCurrentPath() . $folder);
    }

    public static function getTemplateName()
    {
        $access = filter_input(INPUT_GET, 'access');
        if (isset($access)) {
            return htmlspecialchars($access);
        } else {
            return 'home';
        }

    }

    public static function getImgDir()
    {
        $HTTP_HOST = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $isDist = self::folder_exist('dist');
        $isDev = MainFunctions::isLocalhost();

        if ($isDist && !$isDev) {
            $publicPath = $HTTP_HOST . '/dist/' . 'assets/img/';
        } else {
            $publicPath = '//' . $HTTP_HOST . '/src/' . 'assets/img/';

        }

        return $publicPath;
    }

    public function checkAllInput($context)
    {
        $post = $this->data;

        if ($context == 'contact') {
            $location = 'contact';
            $params = array();
        } elseif ($context == 'login') {
            $location = 'log';
            $params = ['type' => 'connexion'];
        } else {
            return false;
        }

        if (isset($post['email']) && self::getMail() == false) {
            array_push($params, ['error' => 'mail']);
            MainFunctions::redirect($location, $params);
        } elseif (isset($post['tel']) && self::getTel() == false) {
            array_push($params, ['error' => 'tel']);
            MainFunctions::redirect($location, $params);
        } else {
            if (isset($post) && !empty($post)) {
                $array = [];
                foreach ($post as $key => $item) {
                    if ($key == 'email') {
                        $array[$key] = self::getMail();
                    } elseif ($key == 'tel') {
                        $array[$key] = self::getTel();
                    } else {
                        $array[$key] = $item;
                    }
                }

                return $array;
            }
        }
    }

    public function getMail()
    {
        $mail = htmlspecialchars(MainFunctions::inputPost('email', false));

        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return $mail;
        }

        return false;
    }

    public function getTel()
    {
        $tel = htmlspecialchars(MainFunctions::inputPost('tel', false));

        $tel = str_replace(' ', '', $tel);
        $tel = str_replace('-', '', $tel);
        $tel = str_replace('.', '', $tel);

        if (preg_match("/^((\+)33|0)[1-9](\d{2}){4}$/", $tel)) {
            return $tel;
        }

        return false;
    }

    public function getHomeUrl()
    {
        return 'https://' . filter_input(INPUT_SERVER, 'HTTP_HOST');
    }

    public function listPosts(array $params)
    {
        $posts = ModelFactory::getModel('Post')->listData(null, null, $params);

        foreach ($posts as $key => $post) {
            $idUser = $post['id_user'];

            $userOfPost = ModelFactory::getModel('User')->readData($post['id_user'], $idUser);

            $avatar = $userOfPost['avatar_img_path'];

            $posts[$key]['avatar_img_path'] = $this->setRelativePathImg($avatar);
        }

        return $posts;
    }

    public function getUser(array $key)
    {
        return ModelFactory::getModel('User')->readData($key[key($key)], key($key));
    }

    public function uploadImg($type = null, $id = null, $action = null)
    {
        $accepted_origins = array("http://localhost:3000", "http://82.64.201.160", "http://recette.thomas-claireau.fr", "https://recette.thomas-claireau.fr");
        $type = $type == null ? filter_input(INPUT_GET, 'type') : $type;

        if ($type) {
            if (MainFunctions::isLocalhost()) {
                $path = './src/assets/img';
            } else {
                $path = './dist/assets/img';
            }

            switch ($type) {
                case 'uploadTiny':
                    $path .= '/posts_images';
                    $id = filter_input(INPUT_GET, 'id');
                    break;
                case 'uploadMainImage':
                    $path .= '/posts_images';
                    $idUser = filter_input(INPUT_GET, 'idUser');
                    $id = filter_input(INPUT_GET, 'id');
                    break;
                case 'uploadAvatar':
                    $path .= '/avatars_images';
                    break;
            }

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (!file_exists($path . '/' . $id)) {
                mkdir($path . '/' . $id, 0777, true);
            }

            $imageFolder = $path . '/' . $id . '/';

            $temp = current($this->files);

            if (is_uploaded_file($temp['tmp_name'])) {
                $http_origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
                if (isset($http_origin)) {
                    // Same-origin requests won't set an origin. If the origin is set, it must be valid.
                    if (in_array($http_origin, $accepted_origins)) {
                        header('Access-Control-Allow-Origin: ' . $http_origin);
                    } else {
                        header("HTTP/1.1 403 Origin Denied");
                        return;
                    }
                }

                // Sanitize input
                if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                    header("HTTP/1.1 400 Invalid file name.");
                    return;
                }

                // Verify extension
                if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                    header("HTTP/1.1 400 Invalid extension.");
                    return;
                }

                // Accept upload if there was no origin, or if it is an accepted origin
                $filetowrite = $imageFolder . $temp['name'];
                move_uploaded_file($temp['tmp_name'], $filetowrite);

                // Respond to the successful upload with JSON.
                echo json_encode(array('location' => $filetowrite));

                if ($type == 'uploadMainImage') {
                    MainFunctions::redirect('post', [
                        'action' => $action,
                        'id' => $id,
                        'idUser' => $idUser,
                    ]
                    );
                }
            } else {
                // Notify editor that the upload failed
                header("HTTP/1.1 500 Server Error");
            }
        } else {
            // Notify editor that the upload failed
            header("HTTP/1.1 404 Url Error");
        }
    }

    public function setRelativePathImg($string)
    {
        if (strpos($string, '<img')) {
            $replacement = MainFunctions::isLocalhost() ? '<img src="./src/' : '<img src="./dist/';
            $regex = ["#<img src=\"src/#", "#<img src=\"dist/#"];
        } else {
            $replacement = MainFunctions::isLocalhost() ? './src/' : './dist/';
            $regex = ["#./src/#", "#./dist/#"];
        }

        foreach ($regex as $item) {
            $string = preg_replace($item, $replacement, $string);
        }

        return $string;
    }
}
