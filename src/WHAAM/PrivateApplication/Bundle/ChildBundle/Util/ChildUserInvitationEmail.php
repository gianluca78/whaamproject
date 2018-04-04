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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Util;

use Swift_Mailer,
    Swift_Message;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface,
    Symfony\Component\Translation\Translator;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitationModeration;

class ChildUserInvitationEmail {

    private $mailer;
    private $translator;
    private $templating;

    public function __construct(Swift_Mailer $mailer, Translator $translator, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->templating = $templating;
    }

    public function sendModerationApproval(ChildUserInvitation $childUserInvitation)
    {
        $subject = '[WHAAM] ' . $this->translator->trans('email_moderation_approved_subject', array(), 'email_invitation');

        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('noreply@whaamproject.eu')
            ->setTo($childUserInvitation->getSenderUser()->getEmail())
            ->setBody($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModerationApproval.html.twig',
                array('childUserInvitation' => $childUserInvitation)
            ),
                'text/html'
            )
            ->addPart($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModerationApproval.txt.twig',
                array('childUserInvitation' => $childUserInvitation)
            ),
                'text/plain');

        $this->mailer->send($message);

    }

    public function sendModerationRefusal(ChildUserInvitation $childUserInvitation)
    {
        $subject = '[WHAAM] ' . $this->translator->trans('email_moderation_refused_subject', array(), 'email_invitation');

        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('noreply@whaamproject.eu')
            ->setTo($childUserInvitation->getSenderUser()->getEmail())
            ->setBody($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModerationRefusal.html.twig',
                array('childUserInvitation' => $childUserInvitation)
            ),
                'text/html'
            )
            ->addPart($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModerationRefusal.txt.twig',
                array('childUserInvitation' => $childUserInvitation)
            ),
                'text/plain');

        $this->mailer->send($message);

    }

    public function sendToModerator(ChildUserInvitation $childUserInvitation, ChildUserInvitationModeration $childUserInvitationModeration)
    {
        try {
            $subject = '[WHAAM] ' . $this->translator->trans('email_invitation_moderation_subject', array(), 'email_invitation');

            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('noreply@whaamproject.eu')
                ->setTo($childUserInvitation->getChild()->getChildCreatorUser()->getEmail())
                ->setBody($this->templating->render(
                    'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModeration.html.twig',
                    array(
                        'childUserInvitation' => $childUserInvitation,
                        'childUserInvitationModeration' => $childUserInvitationModeration
                    )
                ),
                    'text/html'
                )
                ->addPart($this->templating->render(
                    'WHAAMPrivateApplicationChildBundle:Child:emailInvitationModeration.txt.twig',
                    array(
                        'childUserInvitation' => $childUserInvitation,
                        'childUserInvitationModeration' => $childUserInvitationModeration
                    )
                ),
                    'text/plain');

            $this->mailer->send($message);

            return array('success' => $this->translator->trans('email_moderation.success', array(), 'email_invitation'));
        }
        catch (\Exception $e) {
            return array('error' => $this->translator->trans('email.error', array(), 'email_invitation'));
        }

    }

    public function sendToRegisteredUser(ChildUserInvitation $childUserInvitation)
    {
        try {
            $subject = '[WHAAM] ' . $childUserInvitation->getSenderUser()->getSurnameNameOrUsername() . ' ' .
                $this->translator->trans('invitation_sentence', array(), 'email_invitation') .
                ' ' . $childUserInvitation->getChild();

            $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('noreply@whaamproject.eu')
            ->setTo($childUserInvitation->getEmail())
            ->setBody($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationRegisteredUser.html.twig',
                array('childUserInvitation' => $childUserInvitation)
                ),
                'text/html'
            )
            ->addPart($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationRegisteredUser.txt.twig',
                    array('childUserInvitation' => $childUserInvitation)
                ),
                'text/plain');

            $this->mailer->send($message);

            return array('success' => $this->translator->trans('email.success', array(), 'email_invitation'));
        }
        catch (\Exception $e) {
            return array('error' => $this->translator->trans('email.error', array(), 'email_invitation'));
        }
    }

    public function sendToUnregisteredUser(ChildUserInvitation $childUserInvitation)
    {
        try {
            $subject = '[WHAAM] ' . $childUserInvitation->getSenderUser()->getSurnameNameOrUsername() . ' ' .
                $this->translator->trans('invitation_sentence', array(), 'email_invitation') .
                ' ' . $childUserInvitation->getChild();

            $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('noreply@whaamproject.eu')
            ->setTo($childUserInvitation->getEmail())
            ->setBody($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationUnregisteredUser.html.twig',
                array(
                    'childUserInvitation' => $childUserInvitation,
                    'locale' => $this->translator->getLocale()
                )
                ),
                'text/html'
            )
            ->addPart($this->templating->render(
                'WHAAMPrivateApplicationChildBundle:Child:emailInvitationUnregisteredUser.txt.twig',
                    array(
                        'childUserInvitation' => $childUserInvitation,
                        'locale' => $this->translator->getLocale()
                    )
                ),
                'text/plain');

            $this->mailer->send($message);

            return array('success' => $this->translator->trans('email.success', array(), 'email_invitation'));
        }
        catch (\Exception $e) {
            return array('error' => $this->translator->trans('email.error', array(), 'email_invitation'));
        }
    }
}