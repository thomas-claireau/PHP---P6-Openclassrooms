<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use DateTime;
use DateTimeZone;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AuthController
 * Manage the authentication of website
 * @package App\Controller
 */
class AuthController extends MainController
{
    protected $outputUser = null;
    protected $data = null;
    /**
     * Manage the authentication of website
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __construct()
    {
        $this->outputUser = self::checkAllInput('login');
        $this->data = filter_input_array(INPUT_POST);
    }

    public function defaultMethod()
    {
        $action = self::getAction();

        if (isset($action) && !empty($action)) {
            self::$action();
        } else {
            $this->redirect('home');
        }
    }

    public function connexion()
    {
        $user = $this->getUser(['mail' => $this->outputUser['mail']]);

        if (isset($user) && !empty($user)) {
            $outputPassword = $this->outputUser['password'];
            $passwordHash = $user['password'];

            if (self::checkPassword($outputPassword, $passwordHash)) {
                $this->redirect('admin');
                self::createSession($user);
            } else {
                $this->redirect('log', ['type' => 'connexion', 'error' => true]);
            }
        } else {
            $this->redirect('log', ['type' => 'connexion', 'error' => true]);
        }
    }

    public function checkPassword($outputPassword, $passwordHash)
    {
        if ($outputPassword && $passwordHash) {
            return password_verify($outputPassword, $passwordHash);
        }
    }

    public function createSession($user)
    {
        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'mail' => $user['mail'],
            'actif' => $user['actif'],
            'admin' => $user['admin'],
            'avatar_img_path' => $this->setRelativePathImg($user['avatar_img_path']),
        ];
    }

    public function deconnexion()
    {
        setcookie("PHPSESSID", "", time() - 3600, "/");
        session_destroy();
        $this->redirect('home');
    }

    public function getAction()
    {
        return filter_input(INPUT_GET, 'action');
    }

    public function getType()
    {
        return filter_input(INPUT_GET, 'type');
    }

    public function addAccount()
    {
        session_start();

        require_once 'setup/configMail.php';
        $serveurName = $configMail['smtp'];
        $port = $configMail['port'];
        $username = $configMail['username'];
        $password = $configMail['password'];
        // output
        $array = [];
        $array['nom'] = $this->data['nom'];
        $array['prenom'] = $this->data['prenom'];
        $array['mail'] = $this->data['mail'];
        $array['password'] = password_hash($this->data['password'], PASSWORD_DEFAULT);
        $array['actif'] = 1;
        $array['admin'] = 0;

        ModelFactory::getModel('User')->createData($array);
        $user = $this->getUser(['mail' => $array['mail']]);

        $avatarImgPath = 'src/assets/img/avatars_images/' . $user['id'] . '/' . $this->files['avatar']['name'];
        ModelFactory::getModel('User')->updateData($user['id'], ['avatar_img_path' => $avatarImgPath], ['id' => $user['id']]);

        self::createSession($user);
        $this->uploadImg('uploadAvatar', $user['id']);

        // Create the Transport
        $transport = new \Swift_SmtpTransport($serveurName, $port);
        $transport->setUsername($username);
        $transport->setPassword($password);

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        $prenom = $array['prenom'];
        $nom = $array['nom'];

        // Create a confirmation message
        $bodyConfirmation = "
            <p>Bonjour $prenom $nom ! Vous êtes désormais inscris sur le blog !</p>
            Vous pouvez y accéder en vous rendant dans le footer et en cliquant sur 'Lien vers l'admin'.
        ";
        $messageConfirmation = (new \Swift_Message('Confirmation d\'inscription'))
            ->setFrom([$username => 'Thomas Claireau'])
            ->setTo($array['mail'])
            ->addPart($bodyConfirmation, 'text/html')
        ;

        // Send the message
        $result = $mailer->send($messageConfirmation);
        
        $this->redirect('admin');
    }

    public function updateAccount()
    {
        session_start();
        $outputData = $this->data;

        $actualData = $this->getUser(['id' => $_SESSION['user']['id']]);

        // output
        $name = $outputData['nom'];
        $firstname = $outputData['prenom'];
        $email = $outputData['email'];
        $pass = $outputData['password'];

        // actual
        $actualId = $actualData['id'];
        $actualPassHash = $actualData["password"];

        $isCorrectPass = self::checkPassword($pass, $actualPassHash);

        if ($isCorrectPass) {
            if (isset($files) && !empty($files)) {
                $avatarImgPath = $this->files['avatar_img_path']['name'];

                if ($avatarImgPath) {
                    $pathImg = $this->isLocalhost() ? './src/' : './dist/';
                    $outputData['avatar_img_path'] = $pathImg . 'assets/img/avatars_images/' . $actualId . '/' . $avatarImgPath;
                    $this->uploadImg('uploadAvatar', $actualId);
                }
            }

            $updateArray = array_diff($outputData, $actualData);

            if (isset($updateArray) && !empty($updateArray)) {
                foreach ($updateArray as $key => $item) {
                    if ($key !== "password") {
                        ModelFactory::getModel('User')->updateData($actualData[$key], [$key => $outputData[$key]], ['id' => $actualId]);
                        $_SESSION['user'][$key] = $outputData[$key];
                    }
                }
            }

            $this->redirect('admin', ['type' => 'account', 'action' => 'view']);

        } else {
            $this->redirect('admin', ['type' => 'account', 'action' => 'view', 'error' => true]);
        }
    }

    public function removeAccount()
    {
        session_start();
        $actualData = $this->getUser(['id' => $_SESSION['user']['id']]);
        $actualId = $actualData['id'];
        ModelFactory::getModel('User')->deleteData('id', ['id' => $actualId]);
        $lastUserId = ModelFactory::getModel('User')->getLastId('id')[0]['id'];
        ModelFactory::getModel('User')->setIndex($lastUserId);

        self::deconnexion();

        $this->redirect('home');
    }

    public function sendForgotPassword()
    {
        require_once 'setup/configMail.php';
        $serveurName = $configMail['smtp'];
        $port = $configMail['port'];
        $username = $configMail['username'];
        $password = $configMail['password'];
        $mail = $this->checkAllInput('login')['email'];

        $user = ModelFactory::getModel('User')->readData($mail, 'mail');

        if ($user) {
            $idUser = $user['id'];
            $firstname = $user['prenom'];
            $name = $user['nom'];

            //Generate a random string.
            $token = openssl_random_pseudo_bytes(32);
            $token = bin2hex($token);
            $dateToken = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $dateToken = $dateToken->format('Y-m-d H:i:s');
            $link = $_SERVER['HTTP_ORIGIN'] . "/index.php?access=admin&id=$idUser&token=$token";

            // send token in bdd
            ModelFactory::getModel('User')->updateData($token, ['token' => $token], ['id' => $idUser]);
            ModelFactory::getModel('User')->updateData($dateToken, ['dateToken' => $dateToken], ['id' => $idUser]);

            // exit;

            $transport = new \Swift_SmtpTransport($serveurName, $port);
            $transport->setUsername($username);
            $transport->setPassword($password);

            $mailer = new \Swift_Mailer($transport);

            // Create a reset password message
            $bodyConfirmation = "
                <p>Bonjour $firstname $name ! Vous avez demandé à changer votre mot de passe</p>
                <p>Pour le modifier, merci de suivre le <a href='$link'>lien suivant</a></p>
                <p>Attention, ce lien ne sera plus actif dans une heure.</p>
                ";

            $messageConfirmation = (new \Swift_Message('Réinitialisation de mot de passe'))
                ->setFrom([$username => 'Thomas Claireau'])
                ->setTo($mail)
                ->addPart($bodyConfirmation, 'text/html')
            ;

            $result = $mailer->send($messageConfirmation);
            $this->redirect('log', ['type' => 'send-forgot-ok']);
        } else {
            $this->redirect('log', ['type' => 'mot-de-passe-oublie']);
        }
    }
}
