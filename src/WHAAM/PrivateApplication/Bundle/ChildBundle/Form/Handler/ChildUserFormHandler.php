<?php
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser;

class ChildUserFormHandler {

    private $entityManager;
    private $originalChildSiblings;
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

        $validChildUser = $form->getData();
        $this->persistChildUser($validChildUser, $message);

        return true;
    }

    public function persistChildUser(ChildUser $childUser, $message)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $this->entityManager->persist($childUser);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
        $this->session->getFlashBag()->get('warning-' . $childUser->getChild()->getSlug());
    }
} 