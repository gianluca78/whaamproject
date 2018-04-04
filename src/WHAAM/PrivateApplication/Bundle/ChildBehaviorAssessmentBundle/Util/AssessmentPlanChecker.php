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

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;
use Symfony\Component\HttpFoundation\Session\Session;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;

class AssessmentPlanChecker {

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * CRUD actions on a baseline are allowed only if this is not locked
     *
     * @param ChildBehaviorAssessmentBaseline $baseline
     * @return bool
     */
    public function checkBaselineCRUDActions(ChildBehaviorAssessmentBaseline $baseline)
    {
        if($baseline->getIsLocked() == 1) {
            $this->session->getFlashBag()->add('error', 'Actions on the baseline are not allowed');

            return false;
        }

        return true;
    }

    /**
     * A baseline can be added only other assessment phases has not been inserted
     *
     * @param ChildBehaviorAssessment $childBehaviorAssessment
     * @return bool
     */
    public function checkBaselineInsertion(ChildBehaviorAssessment $childBehaviorAssessment)
    {
        $baselines = $childBehaviorAssessment->getBaselines();

        if($baselines->count() == 0) {
            return true;
        }

        return false;
    }

    /**
     * CRUD actions on a function are allowed only if this is not locked
     *
     * @param ChildBehaviorFunction $function
     * @return bool
     */
    public function checkFunctionCRUDActions(ChildBehaviorFunction $function)
    {
        if($function->getIsLocked() == 1) {
            $this->session->getFlashBag()->add('error', 'Actions on the function are not allowed');

            return false;
        }

        return true;
    }

    /**
     * A function can be added if only one baseline exists
     *
     * @param ChildBehaviorAssessment $childBehaviorAssessment
     * @return bool
     */
    public function checkFunctionInsertion(ChildBehaviorAssessment $childBehaviorAssessment)
    {
        $baselines = $childBehaviorAssessment->getBaselines();

        if($baselines->count() == 0 || ($baselines->count() == 1 && $baselines->last()->getChildBehaviorFunction())) {
            return false;
        }

        return true;
    }

    /**
     * CRUD actions on an intervention are allowed only if this is not locked
     *
     * @param ChildBehaviorAssessmentIntervention $intervention
     * @return bool
     */
    public function checkInterventionCRUDActions(ChildBehaviorAssessmentIntervention $intervention)
    {
        if($intervention->getIsLocked() == 1) {
            $this->session->getFlashBag()->add('error', 'Actions on the baseline are not allowed');

            return false;
        }

        return true;
    }

    /**
     * An intervention can be added if the first baseline is followed by a function
     *
     * @param ChildBehaviorAssessment $childBehaviorAssessment
     * @return bool
     */
    public function checkInterventionInsertion(ChildBehaviorAssessment $childBehaviorAssessment)
    {
        $baselines = $childBehaviorAssessment->getBaselines();

        $intervention = $baselines->first()->getIntervention();

        if($this->checkFunctionInsertion($childBehaviorAssessment) || $intervention) {
            return false;
        }

        return true;
    }

    /**
     * Return the name of the next phase available
     *
     * @param ChildBehaviorAssessment $childBehaviorAssessment
     * @return null|string
     */
    public function getNextPhaseAvailable(ChildBehaviorAssessment $childBehaviorAssessment) {

        if($this->checkBaselineInsertion($childBehaviorAssessment)) {
            return 'Baseline';
        }

        if($this->checkInterventionInsertion($childBehaviorAssessment)) {
            return 'Intervention';
        }

        if($this->checkFunctionInsertion($childBehaviorAssessment)) {
            return 'Function';
        }

        return 'None';
    }
} 