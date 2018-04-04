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
namespace WHAAM\PrivateApplication\Common\Util;

use Symfony\Component\Validator\Validator\RecursiveValidator;

class WebServiceEntityValidator {

    private $validator;
    private $validationErrors = array();

    public function __construct(RecursiveValidator $validator)
    {
        $this->validator = $validator;
    }

    public function getValidationErrors($entity)
    {
        $validationErrors = $this->validate($entity);

        foreach($validationErrors as $error) {
            $this->validationErrors[] = array($error->getPropertyPath() => $error->getMessage());
        }

        return $this->validationErrors;
    }

    public function hasErrors($entity)
    {
        return (count($this->validate($entity)) > 0) ? true : false;
    }

    private function validate($entity)
    {
        return $this->validator->validate($entity);
    }
} 