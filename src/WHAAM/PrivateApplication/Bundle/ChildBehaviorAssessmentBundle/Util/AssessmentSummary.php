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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;

class AssessmentSummary {

    private $assessmentsSummary;
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->assessmentsSummary = array();
        $this->translator = $translator;
    }

    public function createSummary($assessments) {

        foreach($assessments as $assessment) {
            if($this->isDataNotAvailable($assessment)) {
                $this->assessmentsSummary[] = array(
                    'id' => $assessment->getId(),
                    'createdAt' => $assessment->getCreatedAt(),
                    'phaseName' => $this->translator->trans('NA', array(), 'interface'),
                    'phaseState' => $this->translator->trans('NA', array(), 'interface'),
                    'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                    'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                    'startDate' => $this->translator->trans('NA', array(), 'interface'),
                    'endDate' => $this->translator->trans('NA', array(), 'interface')
                );
            }

            foreach($assessment->getBaselines() as $baseline) {
                if($baseline->isBaselineActive()) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('Baseline', array(), 'interface'),
                        'phaseState' => $this->translator->trans('data_collection', array(), 'interface'),
                        'observationNumber' => $baseline->getObservationSessions()->count() . '/' .
                            $baseline->getMinimumNumberOfObservations(),
                        'ABCNumber' => $baseline->getABCs()->count(),
                        'startDate' => $baseline->getStartDate(),
                        'endDate' => $baseline->getEndDate()
                    );
                }

                if($baseline->isInterventionActive()) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('Intervention', array(), 'interface'),
                        'phaseState' => $this->translator->trans('data_collection', array(), 'interface'),
                        'observationNumber' => $baseline->getIntervention()->getObservationSessions()->count() . '/' .
                            $baseline->getMinimumNumberOfObservations(),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $baseline->getIntervention()->getStartDate(),
                        'endDate' => $baseline->getIntervention()->getEndDate()
                    );
                }

                if(!$baseline->isBaselineActive() && !$baseline->isBaselineComplete()
                    && !$baseline->getChildBehaviorFunction() && !$assessment->hasAtLeastAnIntervention()
                ) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('Baseline', array(), 'interface'),
                        'phaseState' => ucfirst($this->translator->trans('planning', array(), 'interface')),
                        'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $baseline->getStartDate(),
                        'endDate' => $baseline->getEndDate()
                    );
                }

                if(!$baseline->isBaselineActive() && $baseline->isBaselineComplete()
                    && !$baseline->getChildBehaviorFunction() && !$assessment->hasAtLeastAnIntervention()
                ) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('Baseline', array(), 'interface'),
                        'phaseState' => ucfirst($this->translator->trans('completed', array(), 'interface')),
                        'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $baseline->getStartDate(),
                        'endDate' => $baseline->getEndDate()
                    );
                }

                if(!$baseline->isBaselineActive() && $baseline->getChildBehaviorFunction() &&
                    !$assessment->hasAtLeastAnIntervention()) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('function', array(), 'interface'),
                        'phaseState' => ($baseline->getChildBehaviorFunction()->getIsLocked() == 0) ?
                            ucfirst($this->translator->trans('planning', array(), 'interface')):
                            ucfirst($this->translator->trans('shared', array(), 'interface')),
                        'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $this->translator->trans('NA', array(), 'interface'),
                        'endDate' => $this->translator->trans('NA', array(), 'interface')
                    );
                }

                if(!$baseline->isBaselineActive() && $baseline->getChildBehaviorFunction() &&
                    $assessment->hasAtLeastAnIntervention() && !$baseline->isInterventionActive() &&
                    !$baseline->isInterventionComplete()
                    ) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('Intervention', array(), 'interface'),
                        'phaseState' => ucfirst($this->translator->trans('planning', array(), 'interface')),
                        'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $baseline->getIntervention()->getStartDate(),
                        'endDate' => $baseline->getIntervention()->getEndDate()
                    );
                }

                if($assessment->isComplete()) {
                    $this->assessmentsSummary[] = array(
                        'id' => $assessment->getId(),
                        'createdAt' => $assessment->getCreatedAt(),
                        'phaseName' => $this->translator->trans('evaluation', array(), 'interface'),
                        'phaseState' => $this->translator->trans('NA', array(), 'interface'),
                        'observationNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'ABCNumber' => $this->translator->trans('NA', array(), 'interface'),
                        'startDate' => $this->translator->trans('NA', array(), 'interface'),
                        'endDate' => $this->translator->trans('NA', array(), 'interface')
                    );
                }
            }
        }

        return $this->assessmentsSummary;
    }

    private function isDataNotAvailable(ChildBehaviorAssessment $assessment) {
        return ($assessment->getBaselines()->count() == 0) ? true : false;
    }

    public function getAssessmentsSummary() {
        return $this->assessmentsSummary;
    }
} 