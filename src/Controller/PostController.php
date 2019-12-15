<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use DateTime;
use DateTimeZone;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PostController
 * Manages the Post item
 * @package App\Controller
 */
class PostController extends MainController
{
    /**
     * Renders the View Post
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        session_start();
        $action = self::getAction();

        $isUpload = filter_input(INPUT_GET, 'uploadImage');

        if ($isUpload) {
            self::uploadImage();
        }

        if (isset($action) && !empty($action)) {
            self::$action();
        } else {
            return $this->render('post.twig', [
                'test' => 'PostController',
            ]);
        }
    }

    public function getUserId()
    {
        return filter_input(INPUT_GET, 'idUser');
    }

    public function getId()
    {
        return filter_input(INPUT_GET, 'id');
    }

    public function getAction()
    {
        return filter_input(INPUT_GET, 'action');
    }

    public function saveSessionPost()
    {
        $post = filter_input_array(INPUT_POST);
        $_SESSION['post']['content'] = $post;
        $_SESSION['post']['mainImg'] = $_FILES;
    }

    public function deleteSessionPost()
    {
        unset($_SESSION['post']);
        session_destroy();
    }

    public function uploadImage()
    {
        self::saveSessionPost();
        include 'php ../../src/assets/img/upload.php';
    }

    public function create()
    {
        $post = $_SESSION['post'];

        $titlePost = htmlspecialchars($post['content']['titre']);
        $contentPost = htmlspecialchars($post['content']['editor']);
        $datePost = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $datePost = $datePost->format('Y-m-d H:i:s');
        $mainImagePath = 'src/assets/img/posts_images/' . self::getId() . '/' . $post['mainImg']['image']['name'];

        $array = [
            'id_user' => self::getUserId(),
            'title' => $titlePost,
            'date' => $datePost,
            'content' => $contentPost,
            'main_img_path' => $mainImagePath,
        ];

        ModelFactory::getModel('Post')->createData($array);
    }

}
