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

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\DTOInterface;

class ChildBehaviorAssessmentABCDTO implements DTOInterface {

    public function createObject(array $inputData)
    {
        $ABC = new ChildBehaviorAssessmentABC();
        $ABC->setABCDate(\DateTime::createFromFormat('U', $inputData['abcDateTimestamp']));
        $ABC->setAntecedentWhere($inputData['antecedentWhere']);
        $ABC->setAntecedentWhat($inputData['antecedentWhat']);
        $ABC->setAntecedentWho($inputData['antecedentWho']);
        $ABC->setAntecedentTrigger($inputData['antecedentTrigger']);
        $ABC->setConsequenceChildReaction($inputData['consequenceChildReaction']);
        $ABC->setConsequenceOthersReaction($inputData['consequenceOthersReaction']);

        return $ABC;
    }
} 