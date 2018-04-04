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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util\BehavioralDataChart;

use Doctrine\ORM\PersistentCollection;

class BehavioralDataGenerator {

    CONST PARTIAL_INTERVAL_LENGTH = 15;

    public function generatePartialIntervalDataSet(PersistentCollection $observationSessions, $startingSessionNumber=1) {

        $dataSet = array();

        $sessionNumber = $startingSessionNumber;

        foreach($observationSessions as $observationSession) {
            $sessionStartDateTimestamp = $observationSession->getSessionStartTimestamp()->format('U');

            $baseline = ($observationSession->getBaseline()) ? $observationSession->getBaseline() : $observationSession->getIntervention()->getChildBehaviorAssessmentBaseline();

            $sessionEndDateTimestamp = $sessionStartDateTimestamp + $baseline->getObservationLength() * 60;

            $behaviorSessionOccurrence = 0;

            for($intervalStartTimestamp=$sessionStartDateTimestamp; $intervalStartTimestamp<=$sessionEndDateTimestamp; $intervalStartTimestamp+= self::PARTIAL_INTERVAL_LENGTH) {
                $intervalEndTimestamp = $intervalStartTimestamp + self::PARTIAL_INTERVAL_LENGTH;

                $sessionObservations = $observationSession->getObservations();

                for($j=0; $j<=$sessionObservations->count() - 1; $j+=2) {
                    $behaviorStartTimestamp = $sessionObservations->get($j)->getObservationTimestamp()->format('U');
                    $behaviorEndTimestamp = $sessionObservations->get($j+1)->getObservationTimestamp()->format('U');

                    if(($intervalStartTimestamp >= $behaviorStartTimestamp && $intervalStartTimestamp <= $behaviorEndTimestamp) ||
                        (($intervalEndTimestamp >= $behaviorStartTimestamp && $intervalEndTimestamp <= $behaviorEndTimestamp))
                    ) {

                        $behaviorSessionOccurrence++;

                        break;
                    }
                }
            }

            $dataSet[] = array(
                'x' => $sessionNumber,
                'y' => $behaviorSessionOccurrence,
                'note' => $observationSession->getNote()
            );

            $sessionNumber++;
        }

        return $dataSet;
    }

    public function generateFrequencyDataSet(PersistentCollection $observationSessions, $startingSessionNumber = 1) {
        $dataSet = array();

        $sessionNumber = $startingSessionNumber;

        foreach($observationSessions as $observationSession) {
            $dataSet[] = array(
                'x' => $sessionNumber,
                'y' => $observationSession->getObservations()->count(),
                'note' => $observationSession->getNote()
            );

            $sessionNumber++;
        }

        return $dataSet;
    }

    public function generateDurationDataSet(PersistentCollection $observationSessions, $startingSessionNumber = 1) {
        $dataSet = array();
        $sessionNumber = $startingSessionNumber;

        foreach($observationSessions as $observationSession) {
            $behaviorSessionDurationInMin = 0;
            $sessionObservations = $observationSession->getObservations();

                for($j=0; $j<=$sessionObservations->count() - 1; $j+=2) {
                    $behaviorStartTimestamp = $sessionObservations->get($j)->getObservationTimestamp()->format('U');
                    $behaviorEndTimestamp = $sessionObservations->get($j+1)->getObservationTimestamp()->format('U');
                    $behaviorSessionDurationInMin += $behaviorEndTimestamp-$behaviorStartTimestamp;
                }

            $dataSet[] = array(
                'x' => $sessionNumber,
                'y' => $behaviorSessionDurationInMin,
                'note' => $observationSession->getNote()
            );

            $sessionNumber++;
        }

        return $dataSet;
    }

}