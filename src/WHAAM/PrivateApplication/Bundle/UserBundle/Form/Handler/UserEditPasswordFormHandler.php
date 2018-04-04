<?php
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Security\Core\Encoder\EncoderFactory,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User,
    WHAAM\PrivateApplication\Common\Security\Encoder\OpenSslEncoder;

class UserEditPasswordFormHandler {

    private $encoderFactory;
    private $entityManager;
    private $session;
    private $translator;

    public function __construct(
        EncoderFactory $encoderFactory,
        EntityManager $entityManager,
        Session $session,
        Translator $translator
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->translator = $translator;
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

        $validUser = $form->getData();
        $this->updateUserPassword($validUser, $message);

        return true;
    }

    public function updateUserPassword(User $user, $message)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $encoder = $this->encoderFactory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
    }
} 