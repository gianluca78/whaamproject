<?php
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session;

use WHAAM\PrivateApplication\Bundle\UserBundle\Util\EmailForgotPassword,
    WHAAM\PrivateApplication\Bundle\UserBundle\Util\UserManager;

class UserForgotPasswordFormHandler {

    private $entityManager;
    private $emailForgotPassword;
    private $session;
    private $translator;
    private $userManager;

    public function __construct(
        EntityManager $entityManager,
        EmailForgotPassword $emailForgotPassword,
        Session $session,
        Translator $translator,
        UserManager $userManager
    )
    {
        $this->entityManager = $entityManager;
        $this->emailForgotPassword = $emailForgotPassword;
        $this->session = $session;
        $this->translator = $translator;
        $this->userManager = $userManager;
    }

    public function handle(FormInterface $form, Request $request, $message)
    {
        if(!$request->isMethod('POST')) {
            return false;
        }

        $form->bind($request);

        if(!$form->isValid()) {
            return false;
        }

        $email = $form->getData()->getEmail();

        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->findOneByEmail($email);

        $user = $this->userManager->refreshEmailToken($user);

        $this->sendForgotPasswordEmail($user, $message);

        return true;
    }

    public function sendForgotPasswordEmail($user, $successMessage)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($successMessage, array(), 'flash_messages');


        $this->emailForgotPassword->send($user);

        $this->session->getFlashBag()->add('success', $successMessage);
    }
} 