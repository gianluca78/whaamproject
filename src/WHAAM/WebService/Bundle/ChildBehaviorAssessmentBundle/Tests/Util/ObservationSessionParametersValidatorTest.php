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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ObservationSessionParametersValidator;

class ObservationSessionParametersValidatorTest extends \PHPUnit_Framework_TestCase {

    private $observationSessionParametersValidator;
    
    protected function setUp() {
        $this->observationSessionParametersValidator = new ObservationSessionParametersValidator();
    }
    
    public function testAreValidTimestamps()
    {
        $validInputData = array(
            'phaseId' => 1,
            'phaseNames' => 'baseline',
            'sessionStartTimestamp' => time(),
            'note' => '',
            'timestamps' => array(time(), time())
        );

        $invalidInputData = array(
            'phaseId' => 1,
            'sessionStartTimestamp' => time(),
            'note' => '',
            'timestamps' => time()
        );

        $this->assertTrue($this->observationSessionParametersValidator->areValidTimestamps($validInputData));
        $this->assertFalse($this->observationSessionParametersValidator->areValidTimestamps($invalidInputData));
    }

    /**
     * @expectedException Exception
     */
    public function testIsValidPhase()
    {
        $this->observationSessionParametersValidator->isValidPhase('wrongPhaseName');
    }
}
 