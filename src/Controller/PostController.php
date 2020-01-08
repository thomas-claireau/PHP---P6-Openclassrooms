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
        $action = $this->inputGet('action');

        $isUpload = filter_input(INPUT_GET, 'uploadImage');

        if ($isUpload) {
            self::uploadImage();
        }

        if (isset($action) && !empty($action)) {
            self::$action();
        }

        return $this->render('post.twig', [
            'post' => self::getPost(),
            'user' => self::isLog(),
            'comments' => self::getComment(),
        ]);

    }

    /**
     * isLog
     *
     * @return void
     */
    public function isLog()
    {
        if (isset($this->session['user'])) {
            $user = $this->session['user'];
            if (isset($user) && !empty($user)) {
                return $user;
            }
        }
    }

    /**
     * getUserId
     *
     * @return void
     */
    public function getUserId()
    {
        return filter_input(INPUT_GET, 'idUser');
    }

    /**
     * getId
     *
     * @return void
     */
    public function getId()
    {
        return filter_input(INPUT_GET, 'id');
    }

    /**
     * saveSessionPost
     *
     * @return void
     */
    public function saveSessionPost()
    {
        $this->session['post']['content'] = $this->data;
        $this->session['post']['mainImg'] = $this->files;

        $_SESSION['post'] = $this->session['post'];
    }

    /**
     * deleteSessionPost
     *
     * @return void
     */
    public function deleteSessionPost()
    {
        unset($this->session['post']);
    }

    /**
     * uploadImage
     *
     * @return void
     */
    public function uploadImage()
    {
        self::saveSessionPost();
        $action = filter_input(INPUT_GET, 'action');
        $this->uploadImg(null, null, $action);
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        $post = $this->session['post'];

        $titlePost = htmlspecialchars($post['content']['titre']);
        $description = htmlspecialchars($post['content']['description']);
        $contentPost = htmlspecialchars($post['content']['editor']);
        $datePost = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $datePost = $datePost->format('Y-m-d H:i:s');
        $mainImagePath = 'assets/img/posts_images/' . self::getId() . '/' . $post['mainImg']['image']['name'];

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

    /**
     * update
     *
     * @return void
     */
    public function update()
    {
        $post = $this->session['post'];
        $idPost = filter_input(INPUT_GET, 'id');

        $titlePost = htmlspecialchars($post['content']['titre']);
        $description = htmlspecialchars($post['content']['description']);
        $contentPost = htmlspecialchars($post['content']['editor']);
        $datePost = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $datePost = $datePost->format('Y-m-d H:i:s');
        $mainImagePath = 'assets/img/posts_images/' . self::getId() . '/' . $post['mainImg']['image']['name'];

        ModelFactory::getModel('Post')->updateData($titlePost, ['title' => $titlePost, 'date' => $datePost, 'description' => $description, 'content' => $contentPost, 'main_img_path' => $mainImagePath], ['id' => $idPost]);

        self::deleteSessionPost();
        $this->redirect('admin', ['type' => 'posts', 'action' => 'view']);
    }

    /**
     * remove
     *
     * @return void
     */
    public function remove()
    {
        $idPost = filter_input(INPUT_GET, 'id');
        ModelFactory::getModel('Post')->deleteData('id', ['id' => $idPost]);
        ModelFactory::getModel('Comment')->deleteData('id_post', ['id_post' => $idPost]);
        $allPosts = ModelFactory::getModel('Post')->listData();

        if (isset($allPosts) && empty($allPosts)) {
            ModelFactory::getModel('Post')->resetIndex();
        }
        $this->redirect('admin', ['type' => 'posts', 'action' => 'remove']);
    }

    /**
     * getPost
     *
     * @return void
     */
    public function getPost()
    {
        $id = self::getId();
        $post = ModelFactory::getModel('Post')->readData($id, 'id');
        $userOfPost = ModelFactory::getModel('User')->readData($post['id_user'], 'id');
        $post['nom'] = $userOfPost['nom'];
        $post['prenom'] = $userOfPost['prenom'];
        $post['avatar_img_path'] = $this->setRelativePathImg($userOfPost['avatar_img_path']);
        $post['content'] = $this->setRelativePathImg(htmlspecialchars_decode($post['content']));
        $post['description'] = htmlspecialchars_decode($post['description']);
        $post['title'] = htmlspecialchars_decode($post['title']);

        return $post;
    }

    /**
     * getComment
     *
     * @return void
     */
    public function getComment()
    {
        $commentsDb = ModelFactory::getModel('Comment')->listData(self::getId(), 'id_post');

        $comments = [];

        if (isset($commentsDb) && !empty($commentsDb)) {
            foreach ($commentsDb as $comment) {
                $idUser = $comment['id_user'];
                $user = ModelFactory::getModel('User')->readData($idUser, 'id');

                $array['prenom'] = $user['prenom'];
                $array['nom'] = $user['nom'];
                $array['avatar'] = $this->setRelativePathImg($user['avatar_img_path']);

                $comment['title'] = htmlspecialchars_decode($comment['title']);
                $comment['content'] = htmlspecialchars_decode($comment['content']);

                $comment = array_merge($array, $comment);
                array_push($comments, $comment);
            }
        }

        return $comments;
    }
}
