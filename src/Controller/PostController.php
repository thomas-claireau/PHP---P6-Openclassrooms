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
                'post' => self::getPost(),
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
    }

    public function uploadImage()
    {
        self::saveSessionPost();
        $action = filter_input(INPUT_GET, 'action');
        $this->uploadImg(null, null, $action);
    }

    public function create()
    {
        $post = $_SESSION['post'];

        $titlePost = $post['content']['titre'];
        $description = $post['content']['description'];
        $contentPost = $post['content']['editor'];
        $datePost = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $datePost = $datePost->format('Y-m-d H:i:s');
        $mainImagePath = 'src/assets/img/posts_images/' . self::getId() . '/' . $post['mainImg']['image']['name'];

        $array = [
            'id_user' => self::getUserId(),
            'title' => $titlePost,
            'date' => $datePost,
            'description' => $description,
            'content' => $contentPost,
            'main_img_path' => $mainImagePath,
        ];

        ModelFactory::getModel('Post')->createData($array);
        self::deleteSessionPost();
        $this->redirect('admin', ['type' => 'posts', 'action' => 'view']);
    }

    public function update()
    {
        $post = $_SESSION['post'];
        $idPost = filter_input(INPUT_GET, 'id');

        $titlePost = $post['content']['titre'];
        $description = $post['content']['description'];
        $contentPost = htmlspecialchars($post['content']['editor']);
        $datePost = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $datePost = $datePost->format('Y-m-d H:i:s');
        $mainImagePath = 'src/assets/img/posts_images/' . self::getId() . '/' . $post['mainImg']['image']['name'];

        $array = [
            'id_user' => self::getUserId(),
            'title' => $titlePost,
            'date' => $datePost,
            'description' => $description,
            'content' => $contentPost,
            'main_img_path' => $mainImagePath,
        ];

        ModelFactory::getModel('Post')->updateData($titlePost, ['title' => $titlePost, 'date' => $datePost, 'description' => $description, 'content' => $contentPost, 'main_img_path' => $mainImagePath], ['id' => $idPost]);

        self::deleteSessionPost();
        $this->redirect('admin', ['type' => 'posts', 'action' => 'view']);
    }

    public function remove()
    {
        $idPost = filter_input(INPUT_GET, 'id');
        ModelFactory::getModel('Post')->deleteData('id', ['id' => $idPost]);
        $allPosts = ModelFactory::getModel('Post')->listData();

        if (isset($allPosts) && empty($allPosts)) {
            ModelFactory::getModel('Post')->resetIndex();
        }
        $this->redirect('admin', ['type' => 'posts', 'action' => 'remove']);
    }

    public function getPost()
    {
        $id = self::getId();
        $post = ModelFactory::getModel('Post')->readData($id, 'id');
        $userOfPost = ModelFactory::getModel('User')->readData($post['id_user'], 'id');
        $post['nom'] = $userOfPost['nom'];
        $post['prenom'] = $userOfPost['prenom'];
        $post['avatar_img_path'] = $userOfPost['avatar_img_path'];

        return $post;
    }
}
