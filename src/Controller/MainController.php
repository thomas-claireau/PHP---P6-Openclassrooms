<?php

namespace App\Controller;

use App\Controller\Extension\PhpMvcExtension;
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

    /**
     * MainController constructor
     * Creates the Template Engine & adds its Extensions
     */
    public function __construct()
    {
        $this->twig = new Environment(new FilesystemLoader('../src/View'), array(
            'cache' => false,
            'debug' => true,
        ));
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new PhpMvcExtension());

        // add global variables
        $this->twig->addGlobal('isLocalhost', $this->isLocalhost());
        $this->twig->addGlobal('url', $this->getUrl());
        $this->twig->addGlobal('isDistFolder', $this->folder_exist('dist'));
        $this->twig->addGlobal('templateName', $this->getTemplateName());
        $this->twig->addGlobal('imgDir', $this->getImgDir());
    }

    /**
     * Returns the Page URL
     * @param string $page
     * @param array $params
     * @return string
     */
    public function url(string $page, array $params = [])
    {
        $params['access'] = $page;

        return 'index.php?' . http_build_query($params);
    }

    /**
     * Redirects to another URL
     * @param string $page
     * @param array $params
     */
    public function redirect(string $page, array $params = [])
    {
        header('Location: ' . $this->url($page, $params));

        exit;
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
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
    }

    public static function getCurrentPath()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    public static function folder_exist($folder)
    {
        return file_exists(self::getCurrentPath() . $folder);
    }

    public static function isLocalhost()
    {
        return self::getServerIP() == '127.0.0.1';
    }

    private static function getServerIP()
    {
        $adresse = '';

        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $adresse = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $adresse = $_SERVER["REMOTE_ADDR"];
        } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $adresse = $_SERVER["HTTP_CLIENT_IP"];
        }

        return $adresse;
    }

    public static function getTemplateName()
    {
        if (isset($_GET['access'])) {
            return htmlspecialchars($_GET["access"]);
        }

    }

    public static function getImgDir()
    {
        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        $isDist    = self::folder_exist('dist');
        $isDev     = self::isLocalhost();

        if ($isDist && !$isDev) {
            $publicPath = $HTTP_HOST . '/dist/' . 'assets/img/';
        } else {
            $publicPath = '//' . $HTTP_HOST . '/src/' . 'assets/img/';

        }

        return $publicPath;
    }
}
