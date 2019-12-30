<?php

namespace App\Controller;

use App\Controller\Extension\PhpMvcExtension;
use App\Controller\Functions\MainFunctions;
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
abstract class MainController extends MainFunctions
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
        $this->data = self::getData();
        $this->outputUser = $this->checkAllInput('login');

        self::setupTwig();
        self::addGlobals();
    }

    public static function getData()
    {
        return MainFunctions::inputPost();
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
        $this->twig->addGlobal('isLocalhost', $this->isLocalhost());
        $this->twig->addGlobal('url', $this->getUrl());
        $this->twig->addGlobal('isDistFolder', $this->folder_exist('dist'));
        $this->twig->addGlobal('templateName', $this->getTemplateName());
        $this->twig->addGlobal('imgDir', $this->getImgDir());
        $this->twig->addGlobal('homeUrl', $this->getHomeUrl());
        $this->twig->addGlobal('avatar_default', $this->isLocalhost() ? './src/assets/img/pictos/default_avatar.png' : './dist/assets/img/pictos/default_avatar.png');
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

    public function uploadImg($type = null, $id = null, $action = null)
    {
        $accepted_origins = array("http://localhost:3000", "http://82.64.201.160", "http://recette.thomas-claireau.fr", "https://recette.thomas-claireau.fr");
        $type = $type == null ? $this->inputGet('type') : $type;

        if ($type) {
            if ($this->isLocalhost()) {
                $path = './src/assets/img';
            } else {
                $path = './dist/assets/img';
            }

            switch ($type) {
                case 'uploadTiny':
                    $path .= '/posts_images';
                    $id = $this->inputGet('id');
                    break;
                case 'uploadMainImage':
                    $path .= '/posts_images';
                    $idUser = $this->inputGet('idUser');
                    $id = $this->inputGet('id');
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
                $http_origin = $this->inputServer('HTTP_ORIGIN');
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

                if ($type == 'uploadMainImage') {
                    $this->redirect('post', [
                        'action' => $action,
                        'id' => $id,
                        'idUser' => $idUser,
                    ]
                    );
                }

                // Respond to the successful upload with JSON.
                echo json_encode(array('location' => $filetowrite));

                if ($type == "uploadTiny") {
                    exit;
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
}
