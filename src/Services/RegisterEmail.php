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

    /**
     * @return \Swift_Mailer
     */
    public function getMailer(): \Swift_Mailer
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTemplating(): \Twig_Environment
    {
        return $this->templating;
    }

    /**
     * @param \Twig_Environment $templating
     */
    public function setTemplating(\Twig_Environment $templating): void
    {
        $this->templating = $templating;
    }

    /**
     * @return mixed
     */
    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    /**
     * @param mixed $adminEmail
     */
    public function setAdminEmail($adminEmail): void
    {
        $this->adminEmail = $adminEmail;
    }


    public function sendMail(User $user) {

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom($this->adminEmail)
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/registration.html.twig',
                    array('user' => $user)
                ),
                'text/html'
            );

        $this->mailer ->send($message);
    }

}