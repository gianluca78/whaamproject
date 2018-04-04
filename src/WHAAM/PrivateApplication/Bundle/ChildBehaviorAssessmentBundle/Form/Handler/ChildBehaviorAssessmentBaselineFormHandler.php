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
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;

class ChildBehaviorAssessmentBaselineFormHandler {

    private $entityManager;
    private $securityContext;
    private $session;
    private $translator;

    public function __construct(
        EntityManager $entityManager,
        Session $session,
        SecurityContext $securityContext,
        Translator $translator
    )
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
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

        $validBaseline = $form->getData();
        $this->createChildBehaviorAssessmentBaseline($validBaseline, $message);

        return true;
    }

    public function createChildBehaviorAssessmentBaseline(ChildBehaviorAssessmentBaseline $childBehaviorAssessmentBaseline, $message)
    {
        $isBaselineNew = ($childBehaviorAssessmentBaseline->getId()) ? false : true;

        if($isBaselineNew) {
            $user = $this->securityContext->getToken()->getUser();
            $childBehaviorAssessmentBaseline->setBaselineCreatorUser($user);
        }

        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $this->entityManager->persist($childBehaviorAssessmentBaseline);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('success', $successMessage);
    }
} 