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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;


class ChildBehaviorAssessmentInterventionFormHandler {

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
        $this->originalStrategies = new ArrayCollection();
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

        $validChildBehaviorAssessmentIntervention = $form->getData();
        $validChildBehaviorAssessmentIntervention = $this->removeInterventionStrategies($validChildBehaviorAssessmentIntervention);
        $this->persistBehaviorIntervention($validChildBehaviorAssessmentIntervention, $message);

        return true;
    }

    public function persistBehaviorIntervention(ChildBehaviorAssessmentIntervention $childBehaviorAssessmentIntervention, $message)
    {
        foreach($childBehaviorAssessmentIntervention->getStrategies() as $strategies) {
            $strategies->setChildBehaviorAssessmentIntervention($childBehaviorAssessmentIntervention);
        }

        $successMessage = $this->translator->trans('success', array(), 'flash_messages') . ' ' .
            $this->translator->trans($message, array(), 'flash_messages');

        $this->entityManager->persist($childBehaviorAssessmentIntervention);
        $this->entityManager->flush();


        $this->session->getFlashBag()->add('success', $successMessage);
    }

    public function removeInterventionStrategies(ChildBehaviorAssessmentIntervention $childBehaviorAssessmentIntervention) {
        foreach($this->originalStrategies as $interventionStrategy) {
            if (false === $childBehaviorAssessmentIntervention->getStrategies()->contains($interventionStrategy)) {
                $this->entityManager->remove($interventionStrategy);
                $this->entityManager->flush();
            }
        }

        return $childBehaviorAssessmentIntervention;
    }

    public function setOriginalChildStrategiesFromIntervention(ChildBehaviorAssessmentIntervention $childBehaviorAssessmentIntervention) {
        $OriginalChildStrategies = new ArrayCollection();

        foreach($childBehaviorAssessmentIntervention->getStrategies() as $interventionStrategies) {
            $OriginalChildStrategies->add($interventionStrategies);
        }

        $this->originalStrategies = $OriginalChildStrategies;
    }

} 