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

use WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\AbstractParametersValidator;

class ABCParametersValidator extends AbstractParametersValidator {

    protected $requiredFields = array(
        'abcDateTimestamp',
        'antecedentWhere',
        'antecedentWhat',
        'antecedentWho',
        'antecedentTrigger',
        'consequenceChildReaction',
        'consequenceOthersReaction',
        'baselineId',
        'userId'
    );
}