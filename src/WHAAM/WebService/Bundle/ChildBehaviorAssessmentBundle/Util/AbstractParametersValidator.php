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

use WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ParametersValidatorInterface;

abstract class AbstractParametersValidator implements ParametersValidatorInterface {

    protected $requiredFields = array();

    /**
     * {@inheritdoc }
     */
    public function hasRequiredFields(array $inputData)
    {
        return (!array_diff($this->requiredFields, array_keys($inputData))) ? true : false;
    }

    /**
     * {@inheritdoc }
     */
    public function areValidRequiredFields(array $inputData)
    {
        foreach($inputData as $data) {
            if(!$this->hasRequiredFields($data, $this->requiredFields)) {
                return false;
            }
        }

        return true;
    }

    public function setRequiredFields(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }
} 