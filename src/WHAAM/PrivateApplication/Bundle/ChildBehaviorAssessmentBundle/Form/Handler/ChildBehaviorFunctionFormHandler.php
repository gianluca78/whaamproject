<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction;


class ChildBehaviorFunctionFormHandler {

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

        $validChildBehaviorFunction = $form->getData();
        $this->createChildBehaviorFunction($validChildBehaviorFunction, $message);

        return true;
    }

    public function createChildBehaviorFunction(ChildBehaviorFunction $childBehaviorFunction, $message)
    {
        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $this->entityManager->persist($childBehaviorFunction);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
    }


} 