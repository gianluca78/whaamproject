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

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\Translation\Translator;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

/**
 * Compose an array with user data
 * @package WHAAM\PrivateApplication\Common\Util
 */
class UserDataComposer {

    private $entityManager;
    private $translator;

    public function __construct(EntityManager $entityManager, Translator $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Compose the user data array
     *
     * @param User $user
     * @param $locale
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function compose(User $user, $locale)
    {
        $userData = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->findOneFilteredUserDataByUserIdAndLocale($user->getId(), $locale);

        if(!$userData) {
            throw new BadRequestHttpException('invalid user');
        }

        $userData['children'] = $this->entityManager->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findApprovedChildrenByUserIdAndLocale($user->getId(), $locale);

        foreach($userData['children'] as $key => &$child) {
            if(!array_key_exists('behaviors', $child)) {
                $child['behaviors'] = array();
            }

            if(!array_key_exists('childNetwork', $child)) {
                $child['childNetwork'] = array();
            }

            if(!array_key_exists('diagnoses', $child)) {
                $child['diagnoses'] = array();
            }

            if(!array_key_exists('disciplineReferrals', $child)) {
                $child['disciplineReferrals'] = array();
            }

            if(!array_key_exists('generalEvents', $child)) {
                $child['generalEvents'] = array();
            }

            if(!array_key_exists('medications', $child)) {
                $child['medications'] = array();
            }

            if(!array_key_exists('schoolInformation', $child)) {
                $child['schoolInformation'] = array();
            }

            if(!array_key_exists('siblings', $child)) {
                $child['siblings'] = array();
            }

            $userData['children'][$key]['behaviors'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
                ->findBehaviorsWithActiveAssessmentsByUserIdAndChildNickname($user->getId(), $child['nickname']);

            foreach ($userData['children'][$key]['behaviors'] as $behaviorKey => &$behavior) {
                if(!empty($behavior['otherBehavior'])) {
                    $behavior['behavior'] = $behavior['otherBehavior'];
                }

                $behavior['assessments'] = $this->createBehaviorAssessmentsData($behavior['id'], $user);
            }

            $userData['children'][$key]['diagnoses'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis')
                ->findDiagnosesByChildNicknameAndLocale($child['nickname'], $locale);

            foreach ($userData['children'][$key]['diagnoses'] as $diagnosisKey => $diagnosis) {
                $userData['children'][$key]['diagnoses'][$diagnosisKey]['comorbidities'] = $this->entityManager
                    ->getRepository('WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis')
                    ->findComorbiditiesByIdAndLocale($diagnosis['id'], $locale);
            }

            $userData['children'][$key]['disciplineReferrals'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral')
                ->findDisciplineReferralsByChildNicknameAndLocale($child['nickname'], $locale);

            $userData['children'][$key]['generalEvents'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildGeneralEvent')
                ->findGeneralEventsByChildNickname($child['nickname']);

            $userData['children'][$key]['medications'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildMedication')
                ->findMedicationsByChildNickname($child['nickname']);

            $userData['children'][$key]['schoolInformation'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildSchoolInformation')
                ->findSchoolInformationByChildNickname($child['nickname']);

            $userData['children'][$key]['siblings'] = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildSibling')
                ->findSiblingsByChildNicknameAndLocale($child['nickname'], $locale);

            $childUsers = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:ChildUser')
                ->findUsersByChildNickname($child['nickname']);

            foreach($childUsers as $childUser) {
                $childUserData = $this->entityManager
                    ->getRepository('WHAAMPrivateApplicationUserBundle:User')
                    ->findOneFilteredUserDataByUserIdAndLocale($childUser['id'], $locale);

                $childUserData['role'] = $childUser['role'];

                $userData['children'][$key]['childNetwork'][] = $childUserData;
            }
        }

        return $userData;
    }

    /**
     * Compose the assessment part of the data
     *
     * @param $behaviorId
     * @param User $user
     * @return array
     */
    public function createBehaviorAssessmentsData($behaviorId, $user)
    {
        $result = array();

        $behavior = $this->entityManager
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        $childBehaviorAssessmentCollection = $behavior->getActiveChildBehaviorAssessments();

        foreach($childBehaviorAssessmentCollection as $key => $childBehaviorAssessment) {
            $baseline = $childBehaviorAssessment->getBaselines()->get(0);

            $userIsObserver = ($user === $baseline->getObserver()) ? true : false;
            $userIsAllowedToTakeABCs = ($baseline->getABCUsers()->contains($user)) ? true : false;
            $observationType = ($baseline->getObservationType());

            $id = $baseline->isBaselineActive() ? $baseline->getId() : $baseline->getIntervention()->getId();

            $phase = $baseline->isBaselineActive() ? $this->translator->trans('Baseline', array(), 'interface') :
                $this->translator->trans('Intervention', array(), 'interface');

            $phaseStartDate = $baseline->isBaselineActive() ?
                $baseline->getStartDate() :
                $baseline->getIntervention()->getStartDate();

            $phaseEndDate = $baseline->isBaselineActive() ?
                $baseline->getEndDate() :
                $baseline->getIntervention()->getEndDate();

            $plannedNumberOfObservations = $baseline->getMinimumNumberOfObservations();
            $gatheredNumberOfObservations = $baseline->isBaselineActive() ?
                count($baseline->getObservationSessions()) :
                count($baseline->getIntervention()->getObservationSessions())
            ;

            $gatheredNumberOfABCs = count($baseline->getABCs());

            $result[$key] = array(
                'phaseId' => $id,
                'phase' => $phase,
                'phaseStartDate' => $phaseStartDate,
                'phaseEndDate' => $phaseEndDate,
                'userIsObserver' => $userIsObserver,
                'userIsAllowedToTakeABCs' => $userIsAllowedToTakeABCs,
                'observationLengthInMinutes' => $baseline->getObservationLength(),
                'observationType' => $observationType,
                'plannedNumberOfObservations' => $plannedNumberOfObservations,
                'gatheredNumberOfObservations' => $gatheredNumberOfObservations,
                'gatheredNumberOfABCs' => $gatheredNumberOfABCs
            );
        }

        return $result;

    }

    public function hasPhaseIdInAssessmentArray($phaseId, $phaseName, $assessments) {
        foreach ($assessments as $key => $val) {
            if (isset($val[0]) &&
                array_key_exists('phaseId', $val[0]) &&
                array_key_exists('phase', $val[0]) &&
                $val[0]['phaseId'] == $phaseId &&
                $val[0]['phase'] == ucwords($phaseName)
            ) {
                return $key;
            }
        }

        return false;
    }
}