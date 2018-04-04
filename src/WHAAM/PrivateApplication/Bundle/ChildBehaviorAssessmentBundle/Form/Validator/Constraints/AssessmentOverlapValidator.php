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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;

class AssessmentOverlapValidator extends ConstraintValidator
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($object, Constraint $constraint)
    {
        $assessmentId = ($object instanceof ChildBehaviorAssessmentBaseline) ?
            $object->getChildBehaviorAssessment()->getId() :
            $object->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getId();

        $baselineId = ($object instanceof ChildBehaviorAssessmentBaseline) ? $object->getId() : null;
        $interventionId = ($object instanceof ChildBehaviorAssessmentIntervention) ? $object->getId() : null;

        if($this->entityManager
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->findBaselineOrInterventionInDateRange(
                $assessmentId,
                $object->getStartDate(),
                $object->getEndDate(),
                $baselineId,
                $interventionId
            )
        ) {
            $this->context->buildViolation('assessment_dates_overlap.error')
                ->setTranslationDomain('validators')
                ->atPath('startDate')
                ->addViolation();
        }
    }
}