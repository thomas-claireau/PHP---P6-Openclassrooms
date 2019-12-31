<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailController
 * Check if form contact is OK and send mail
 * @package App\Controller
 */
class MailController extends MainController
{
    /**
     * Check if form contact is OK and send mail
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        self::sendEmail();
    }

    public function sendEmail()
    {
        require_once 'setup/configMail.php';
        $serveurName = $configMail['smtp'];
        $port = $configMail['port'];
        $username = $configMail['username'];
        $password = $configMail['password'];

        $infos = $this->checkAllInput('contact');
        $prenom = $infos['prenom'];
        $nom = $infos['nom'];
        $mail = $infos['email'];
        $tel = $infos['tel'];
        $message = $infos['message'];

        // Create the Transport
        $transport = new \Swift_SmtpTransport($serveurName, $port);
        $transport->setUsername($username);
        $transport->setPassword($password);

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a confirmation message
        $bodyConfirmation = "
            <p>Bonjour $prenom $nom ! J'ai bien reçu votre message et je le traiterais dans les plus brefs délais.</p>
            <p>En attendant, voici un récapitulatif de votre message :</p>
            <ul>
                <li>Prénom : $prenom</li>
                <li>Nom : $nom</li>
                <li>Email : $mail</li>
                <li>Télephone : $tel</li>
                <li>Votre demande : $message</li>
            </ul>
        ";
        $messageConfirmation = (new \Swift_Message('Confirmation de demande de contact'))
            ->setFrom([$username => 'Thomas Claireau'])
            ->setTo($mail)
            ->addPart($bodyConfirmation, 'text/html')
        ;

        // Create my message confirmation
        $bodyMySelf = "
            <p>Nouvelle demande de contact !</p>
            <p>Récapitulatif de la demande</p>
            <ul>
                <li>Prénom : $prenom</li>
                <li>Nom : $nom</li>
                <li>Email : $mail</li>
                <li>Télephone : $tel</li>
                <li>Demande : $message</li>
            </ul>
        ";

        $messageMySelf = (new \Swift_Message('Confirmation de demande de contact'))
            ->setFrom([$username => 'Thomas Claireau'])
            ->setTo($username)
            ->addPart($bodyMySelf, 'text/html')
        ;

        // Send the message
        $mailer->send($messageConfirmation);
        $mailer->send($messageMySelf);

        $this->redirect('contact', ['success' => true]);
    }
}
