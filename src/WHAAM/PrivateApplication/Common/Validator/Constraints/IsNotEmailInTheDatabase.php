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


namespace WHAAM\PrivateApplication\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsNotEmailInTheDatabase extends Constraint {

    public $message = 'The inserted email is not valid';

    public function validatedBy()
    {
        return 'email_not_in_the_database';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 