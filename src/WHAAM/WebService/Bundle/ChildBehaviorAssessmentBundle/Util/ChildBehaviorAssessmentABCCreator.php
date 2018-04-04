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

use Doctrine\ORM\EntityManager;

use WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ABCParametersValidator,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\AbstractObjectCreator,
    WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Util\ChildBehaviorAssessmentABCDTO,
    WHAAM\PrivateApplication\Common\Util\UserDataComposer,
    WHAAM\PrivateApplication\Common\Util\WebServiceEntityValidator;

class ChildBehaviorAssessmentABCCreator extends AbstractObjectCreator {

    private $childBehaviorAssessmentABCDTO;
    private $entityManager;
    private $ABCs = array();
    private $ABCParametersValidator;
    private $userDataComposer;
    private $webServiceEntityValidator;

    public function __construct(
        ChildBehaviorAssessmentABCDTO $childBehaviorAssessmentObservationSessionDTO,
        EntityManager $entityManager,
        ABCParametersValidator $ABCParametersValidator,
        UserDataComposer $userDataComposer,
        WebServiceEntityValidator $webServiceEntityValidator
    )
    {
        $this->childBehaviorAssessmentABCDTO = $childBehaviorAssessmentObservationSessionDTO;
        $this->entityManager = $entityManager;
        $this->ABCParametersValidator = $ABCParametersValidator;
        $this->userDataComposer = $userDataComposer;
        $this->webServiceEntityValidator = $webServiceEntityValidator;
    }

    public function save(array $ABCData)
    {
        if($this->areValidRequiredFields($ABCData) && $this->prepare($ABCData) && $this->areValidEntities())
        {
            $results = array();

            foreach($this->ABCs as $ABC) {
                $this->entityManager->persist($ABC);
                $this->entityManager->refresh($ABC->getChildBehaviorAssessmentBaseline());
                $this->entityManager->flush();

                $behaviorId = $ABC->getChildBehaviorAssessmentBaseline()
                    ->getChildBehaviorAssessment()
                    ->getChildBehavior()
                    ->getId();

                $key = $this->userDataComposer->hasPhaseIdInAssessmentArray(
                    $ABC->getChildBehaviorAssessmentBaseline()->getId(),
                    'baseline',
                    $results);

                if($key === FALSE) {
                    $results[$key] = $this->userDataComposer->createBehaviorAssessmentsData(
                        $behaviorId, $ABC->getUser()
                    );
                } else {
                    $results[] = $this->userDataComposer->createBehaviorAssessmentsData(
                        $behaviorId, $ABC->getUser()
                    );
                }
            }

            return $results;
        }

        return false;
    }

    protected function areValidEntities()
    {
        foreach($this->ABCs as $ABC) {
            if($this->webServiceEntityValidator->hasErrors($ABC)) {
                return false;
            }
        }

        return true;
    }

    protected function areValidRequiredFields(array $ABCs)
    {
        return $this->ABCParametersValidator->areValidRequiredFields($ABCs);
    }

    protected function prepare(array $ABCsData)
    {
        foreach($ABCsData as $ABCData) {

            $user = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationUserBundle:User')
                ->find($ABCData['userId']);

            if(!$user) {
                return false;
            }

            $baseline = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
                ->find($ABCData['baselineId']);

            if(!$baseline) {
                return false;
            }

            $ABC = $this->childBehaviorAssessmentABCDTO->createObject($ABCData);
            $ABC->setUser($user);
            $ABC->setChildBehaviorAssessmentBaseline($baseline);

            $this->ABCs[] = $ABC;
        }

        return true;
    }
}