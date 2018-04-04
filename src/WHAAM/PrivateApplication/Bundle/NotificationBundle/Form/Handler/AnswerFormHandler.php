<?php
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer;
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\AnswerStatus;

class AnswerFormHandler {

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

        $validAnswer = $form->getData();

        if(!$validAnswer->getMessage()->getRecipientUsers()->contains($validAnswer->getMessage()->getSender())) {
            $validAnswer->getMessage()->addRecipientUser($validAnswer->getMessage()->getSender());
        }

        foreach ($validAnswer->getMessage()->getRecipientUsers() as $user) {
            $answerStatus = new AnswerStatus();
            $answerStatus->setAnswer($validAnswer);
            $answerStatus->setUser($user);

            $validAnswer->addStatus($answerStatus);
        }

        $answerStatus = new AnswerStatus();
        $answerStatus->setAnswer($validAnswer);
        $answerStatus->setUser($validAnswer->getMessage()->getSender());

        $validAnswer->addStatus($answerStatus);

        $this->persistAnswer($validAnswer, $message);

        return $validAnswer;
    }

    public function persistAnswer(Answer $answer, $successMessage)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($successMessage, array(), 'flash_messages');

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
    }
}