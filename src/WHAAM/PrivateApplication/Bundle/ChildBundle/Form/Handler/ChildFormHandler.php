<?php
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collections\ArrayCollection;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Util\ChildUserManager;

class ChildFormHandler {

    private $childUserManager;
    private $entityManager;
    private $originalChildSiblings;
    private $session;
    private $translator;

    public function __construct(
        ChildUserManager $childUserManager,
        EntityManager $entityManager,
        Session $session,
        Translator $translator
    )
    {
        $this->childUserManager = $childUserManager;
        $this->entityManager = $entityManager;
        $this->originalChildSiblings = new ArrayCollection();
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

        $validChild = $form->getData();
        $validChild = $this->removeChildSiblings($validChild);
        $this->persistChild($validChild, $message);

        return $validChild;
    }

    public function persistChild(Child $child, $message)
    {
        foreach($child->getSiblings() as $sibling) {
            $sibling->setChild($child);
        }

        $isChildNew = ($child->getId()) ? false : true;

        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $this->entityManager->persist($child);
        $this->entityManager->flush();

        if($isChildNew === true) {
            $this->childUserManager->addUserToChildNetwork($child, $child->getChildCreatorUser());
        }

        $this->session->getFlashBag()->add('success', $successMessage);
    }

    public function removeChildSiblings(Child $child) {
        foreach($this->originalChildSiblings as $childSibling) {
            if (false === $child->getSiblings()->contains($childSibling)) {
                $this->entityManager->remove($childSibling);
                $this->entityManager->flush();
            }
        }

        return $child;
    }

    public function setOriginalChildSiblingsFromChild(Child $child) {
        $originalChildSiblings = new ArrayCollection();

        foreach($child->getSiblings() as $childSibling) {
            $originalChildSiblings->add($childSibling);
        }

        $this->originalChildSiblings = $originalChildSiblings;
    }
} 