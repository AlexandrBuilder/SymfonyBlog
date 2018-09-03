<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 28.08.18
 * Time: 14:48
 */

namespace App\Services;


use App\Entity\User;

class RegisterEmail
{
    private $mailer;
    private $templating;
    private $adminEmail;

    public function __construct( $adminEmail, \Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->adminEmail = $adminEmail;
    }

    public function sendMail(User $user) {

        $message = (new \Swift_Message('Confirmation letter'))
            ->setFrom($this->adminEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/registration.html.twig',
                    ['user' => $user]
                ),
                'text/html'
            );

        $this->mailer ->send($message);
    }

}