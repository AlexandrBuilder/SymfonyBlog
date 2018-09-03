<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 28.08.18
 * Time: 14:48
 */

namespace App\Services;


use App\Entity\User;

class EmailService
{
    const CONST_TEXT_HTML = 'text/html';
    const CONST_TEXT_PLAIN = 'text/plain';

    private $mailer;
    private $templating;
    private $adminEmail;

    public function __construct( $adminEmail, \Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->adminEmail = $adminEmail;
    }

    public function sendMail(User $user, string $title, array $renderOptions, string $typeRender) {

        $message = (new \Swift_Message($title))
            ->setFrom($this->adminEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    $renderOptions['template'],
                    $renderOptions['options']
                ),
                $typeRender
            );

        $this->mailer ->send($message);
    }

}