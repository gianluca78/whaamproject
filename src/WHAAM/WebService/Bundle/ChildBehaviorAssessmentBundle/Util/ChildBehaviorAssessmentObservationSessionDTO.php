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

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession,
    WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\DTOInterface;

class ChildBehaviorAssessmentObservationSessionDTO implements DTOInterface {

    public function createObject(array $inputData)
    {
        $childBehaviorAssessmentObservationSession = new ChildBehaviorAssessmentObservationSession();
        $childBehaviorAssessmentObservationSession->setNote($inputData['note']);
        $childBehaviorAssessmentObservationSession->setSessionStartTimestamp(
            \DateTime::createFromFormat('U', $inputData['sessionStartTimestamp'])
        );

        foreach($inputData['timestamps'] as $timestamp) {
            $observationData = new ChildBehaviorAssessmentObservationData();
            $observationData->setObservationSession($childBehaviorAssessmentObservationSession);
            $observationData->setObservationTimestamp(\DateTime::createFromFormat('U', $timestamp));

            $childBehaviorAssessmentObservationSession->addObservation($observationData);
        }

        return $childBehaviorAssessmentObservationSession;
    }
} 