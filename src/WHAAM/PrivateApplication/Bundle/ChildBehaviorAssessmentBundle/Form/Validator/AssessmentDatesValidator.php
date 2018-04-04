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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AssessmentDatesValidator {

    public static function validate($object, ExecutionContextInterface $context)
    {
        if($object->getStartDate() >= $object->getEndDate()) {
            $context->buildViolation('baseline_dates.error')
                ->setTranslationDomain('validators')
                ->atPath('startDate')
                ->addViolation();
        }
    }

} 