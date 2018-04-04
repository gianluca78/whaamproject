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

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AssessmentOverlap extends Constraint {
    public $message = 'The selected dates overlap with another assessment phase';

    public function validatedBy()
    {
        return 'assessment_overlap';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 