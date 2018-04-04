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
namespace WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Tests\Util;

class AbstractParametersValidatorTest extends \PHPUnit_Framework_TestCase {

    private $parametersValidatorMock;

    protected function setUp()
    {
        $this->parametersValidatorMock = $this->getMockForAbstractClass(
            'WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\AbstractParametersValidator'
        );

        $this->parametersValidatorMock->setRequiredFields(array(
            'phaseId', 'phaseName', 'sessionStartTimestamp', 'note', 'timestamps')
        );
    }

    public function testHasRequiredFields()
    {
        $validInputData = array(
            'phaseId' => 1,
            'phaseName' => 'baseline',
            'sessionStartTimestamp' => time(),
            'note' => '',
            'timestamps' => array(time(), time())
        );

        $invalidInputData = array(
            'phaseId' => 1,
            'sessionStartTimestamp' => time(),
            'note' => '',
            'timestamps' => array(time(), time())
        );


        $this->assertTrue($this->parametersValidatorMock->hasRequiredFields($validInputData));
        $this->assertFalse($this->parametersValidatorMock->hasRequiredFields($invalidInputData));
    }

    public function testAreValidRequiredFields() {

        $validInputData = array(
            array(
                'phaseId' => 1,
                'phaseName' => 'baseline',
                'sessionStartTimestamp' => time(),
                'note' => '',
                'timestamps' => array(time(), time())
            ),
            array(
                'phaseId' => 2,
                'phaseName' => 'intervention',
                'sessionStartTimestamp' => time(),
                'note' => '',
                'timestamps' => array(time(), time())
            ),
        );

        $invalidInputData = array(
            array(
                'phaseId' => 1,
                'phaseName' => 'baseline',
                'sessionStartTimestamp' => time(),
                'note' => '',
                'timestamps' => array(time(), time())
            ),
            array(
                'phaseId' => 2,
                'sessionStartTimestamp' => time(),
                'note' => '',
                'timestamps' => array(time(), time())
            ),
        );

        $this->assertTrue($this->parametersValidatorMock->areValidRequiredFields($validInputData));
        $this->assertFalse($this->parametersValidatorMock->areValidRequiredFields($invalidInputData));
    }
}
 