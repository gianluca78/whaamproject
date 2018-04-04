<?php
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message;
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\MessageStatus;

class MessageFormHandler {

    private $entityManager;
    private $session;
    private $translator;

    public function __construct(
        EntityManager $entityManager,
        Session $session,
        Translator $translator
    )
    {
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

        $validMessage = $form->getData();

        foreach ($validMessage->getRecipientUsers() as $user) {
            $messageStatus = new MessageStatus();
            $messageStatus->setMessage($validMessage);
            $messageStatus->setUser($user);

            $validMessage->addStatus($messageStatus);
        }

        $this->persistMessage($validMessage, $message);

        return $validMessage;
    }

    public function persistMessage(Message $message, $successMessage)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($successMessage, array(), 'flash_messages');

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
    }
}