<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Gianluca Merlo
 */
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Util;

use Swift_Mailer,
    Swift_Message;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface,
    Symfony\Component\Translation\Translator;

use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class EmailForgotPassword {

    private $mailer;
    private $translator;
    private $templating;

    public function __construct(Swift_Mailer $mailer, Translator $translator, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->templating = $templating;
    }

    public function send(User $user)
    {
        $message = Swift_Message::newInstance()
            ->setSubject('[WHAAM] ' . $this->translator->trans('subject', array(), 'email_forgot_password'))
            ->setFrom('noreply@whaamproject.eu')
            ->setTo($user->getEmail())
            ->setBody($this->templating->render(
                    'WHAAMPrivateApplicationUserBundle:User:forgotPasswordEmail.html.twig',
                    array('user' => $user)
                ),
                'text/html'
            )
            ->addPart($this->templating->render(
                    'WHAAMPrivateApplicationUserBundle:User:forgotPasswordEmail.txt.twig',
                    array('user' => $user)
                ),
                'text/plain');

        $this->mailer->send($message);
    }
} 