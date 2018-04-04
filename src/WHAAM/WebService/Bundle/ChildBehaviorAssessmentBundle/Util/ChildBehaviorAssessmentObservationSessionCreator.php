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
namespace WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\SecurityContext;

use WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ChildBehaviorAssessmentObservationSessionDTO,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\AbstractObjectCreator,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ObservationSessionParametersValidator,
    WHAAM\PrivateApplication\Common\Util\UserDataComposer,
    WHAAM\PrivateApplication\Common\Util\WebServiceEntityValidator;

class ChildBehaviorAssessmentObservationSessionCreator extends AbstractObjectCreator {

    private $childBehaviorAssessmentObservationSessionDTO;
    private $entityManager;
    private $observationSessions = array();
    private $observationSessionParametersValidator;
    private $securityContext;
    private $userDataComposer;
    private $webServiceEntityValidator;

    public function __construct(
        ChildBehaviorAssessmentObservationSessionDTO $childBehaviorAssessmentObservationSessionDTO,
        EntityManager $entityManager,
        ObservationSessionParametersValidator $observationSessionParametersValidator,
        SecurityContext $securityContext,
        UserDataComposer $userDataComposer,
        WebServiceEntityValidator $webServiceEntityValidator
    )
    {
        $this->childBehaviorAssessmentObservationSessionDTO = $childBehaviorAssessmentObservationSessionDTO;
        $this->entityManager = $entityManager;
        $this->observationSessionParametersValidator = $observationSessionParametersValidator;
        $this->securityContext = $securityContext;
        $this->userDataComposer = $userDataComposer;
        $this->webServiceEntityValidator = $webServiceEntityValidator;
    }

    public function findBaselineOrIntervention($phaseName, $phaseId)
    {
        $entityName = 'ChildBehaviorAssessment' . ucfirst($phaseName);

        $entity = $this->entityManager
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:' . $entityName)
            ->find($phaseId);

        if(!$entity) {
            throw new \Exception($entityName . ' not found with id ' . $phaseId);
        }

        return $entity;
    }

    public function save(array $observationSessionData)
    {
        if($this->areValidObservationSessions($observationSessionData)) {
            if($this->prepare($observationSessionData) && $this->areValidEntities())
            {
                $results = array();

                foreach($this->observationSessions as $observationSession) {
                    $this->entityManager->persist($observationSession);

                    if($observationSession->getBaseline()) {
                        $this->entityManager->refresh($observationSession->getBaseline());
                    }

                    if($observationSession->getIntervention()) {
                        $this->entityManager->refresh($observationSession->getIntervention());
                    }

                    $this->entityManager->flush();

                    $phaseName = ($observationSession->getIntervention()) ? 'intervention' : 'baseline';

                    $baseline = ($observationSession->getBaseline()) ?
                        $observationSession->getBaseline() :
                        $observationSession->getIntervention()->getChildBehaviorAssessmentBaseline();

                    $behaviorId = $baseline->getChildBehaviorAssessment()
                        ->getChildBehavior()
                        ->getId();

                    $key = $this->userDataComposer->hasPhaseIdInAssessmentArray(
                        $baseline->getId(),
                        $phaseName,
                        $results);

                    if($key === FALSE) {
                        $results[$key] = $this->userDataComposer->createBehaviorAssessmentsData(
                            $behaviorId, $this->securityContext->getToken()->getUser()
                        );
                    } else {
                        $results[] = $this->userDataComposer->createBehaviorAssessmentsData(
                            $behaviorId, $this->securityContext->getToken()->getUser()
                        );
                    }

                }

                return $results;
            }
        }

        return false;
    }

    private function areValidObservationSessions(array $observationsSessionsData)
    {
        if($this->areValidRequiredFields($observationsSessionsData)) {
            foreach($observationsSessionsData as $observationSessionData) {
                if(!$this->validateSessionParameters($observationSessionData)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    protected function areValidEntities()
    {
        foreach($this->observationSessions as $observationSession) {
            if($this->webServiceEntityValidator->hasErrors($observationSession)) {
                return false;
            }

            foreach($observationSession->getObservations() as $observation) {
                if($this->webServiceEntityValidator->hasErrors($observation)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function areValidRequiredFields(array $observationsSessions)
    {
        return $this->observationSessionParametersValidator->areValidRequiredFields($observationsSessions);
    }

    protected function prepare(array $observationSessionsData)
    {
        foreach($observationSessionsData as $observationSessionData) {
            $observationSession = $this->childBehaviorAssessmentObservationSessionDTO->createObject($observationSessionData);

            $phaseName = $observationSessionData['phaseName'];
            $phaseId = $observationSessionData['phaseId'];
            $setterName = 'set' . ucfirst($phaseName);

            try {
                $baselineOrIntervention = $this->findBaselineOrIntervention($phaseName, $phaseId);
            } catch (\Exception $e) {
                return false;
            }

            $observationSession->$setterName($baselineOrIntervention);

            $this->observationSessions[] = $observationSession;
        }

        return true;
    }

    protected function validateSessionParameters(array $observationSession)
    {
        return ($this->observationSessionParametersValidator->isValidPhase($observationSession['phaseName']) &&
            $this->observationSessionParametersValidator->areValidTimestamps($observationSession)
        ) ? true : false;
    }
}