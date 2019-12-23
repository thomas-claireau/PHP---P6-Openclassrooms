<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use DateTime;
use DateTimeZone;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class CommentController
 * Manages the Comment item
 * @package App\Controller
 */
class CommentController extends MainController
{
    /**
     * Renders the View Post
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */

    private $data;

    public function __construct()
    {
        $this->data = filter_input_array(INPUT_POST);
    }

    public function defaultMethod()
    {
        $action = self::getAction();

        if (isset($action) && !empty($action)) {
            self::$action();
        }
    }

    public function getAction()
    {
        return filter_input(INPUT_GET, 'action');
    }

    public function getIdPost()
    {
        return filter_input(INPUT_GET, 'idPost');
    }

    public function create()
    {
        session_start();
        $array = [];

        $array['id_user'] = $_SESSION['user']['id'];
        $array['id_post'] = self::getIdPost();
        $array['title'] = htmlspecialchars($this->data['titre']);
        $array['content'] = htmlspecialchars($this->data['commentaire']);
        $array['date'] = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $array['date'] = $array['date']->format('Y-m-d H:i:s');

        ModelFactory::getModel('Comment')->createData($array);
        $this->redirect('post', ['id' => self::getIdPost()]);
    }

    public function update()
    {
        $idCom = $this->data['commentId'];
        $title = htmlspecialchars($this->data['titre']);
        $content = htmlspecialchars($this->data['commentaire-' . $idCom]);

        ModelFactory::getModel('Comment')->updateData($idCom, ['title' => $title, 'content' => $content], ['id' => $idCom]);

        $this->redirect('admin', ['type' => 'comments', 'action' => 'update']);
    }

    public function remove()
    {
        $id = filter_input(INPUT_GET, 'id');
        ModelFactory::getModel('Comment')->deleteData('id', ['id' => $id]);
    }

}
