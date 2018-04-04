<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior,
    WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;

/**
 * @Route("/assessments")
 *
 * Class ChildBehaviorAssessmentController
 * @package WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller
 */
class ChildBehaviorAssessmentController extends Controller
{
    /**
     * @Route("/{assessmentId}/delete", name="child_behavior_assessments_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param $assessmentId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $assessmentId)
    {
        $assessment = $this->getDoctrine()->
        getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($assessmentId);

        if (!$assessment) {
            throw $this->createNotFoundException(
                'Assessment with id ' . $assessmentId . ' not found'
            );
        }

        $child = $assessment->getChildBehavior()->getChild();
        $behaviorId = $assessment->getChildBehavior()->getId();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($assessment);
            $em->flush();
            $message = $this->get('translator')->trans('assessment.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('assessment.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('child_behavior_assessments_list', array(
                'behaviorId' => $behaviorId
            ))
        );
    }

    /**
     * @Route("/{behaviorId}", name="child_behavior_assessments_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $behaviorId)
    {
        $behavior = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        if (!$behavior) {
            throw $this->createNotFoundException(
                'Behavior with id ' . $behaviorId . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessment:index')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $behavior->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $behavior->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behaviorId),
                    'assessments' => array('behaviorId' => $behaviorId)

                ),
                array(
                    '%child_name%' => $behavior->getChild()->getNickname(),
                    '%behaviour_name%' => $behavior->getShortBehavior()
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $behavior->getChild());

        return array(
            'assessments' => $this->get('whaam_child_behavior_assessment.util.assessment_summary')
                            ->createSummary($behavior->getChildBehaviorAssessments()),
            'childBehavior' =>$behavior,
            'child' => $behavior->getChild(),
            'childSlug'=> $behavior->getChild()->getSlug()
        );
    }

    /**
     * @Route("/{behaviorId}/new", name="child_behavior_assessments_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $behaviorId)
    {
        $behavior = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        if (!$behavior) {
            throw $this->createNotFoundException(
                'Behavior with id ' . $behaviorId . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessment:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $behavior->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $behavior->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behaviorId),
                    'assessments' => array('behaviorId' => $behaviorId)

                ),
                array(
                    '%child_name%' => $behavior->getChild()->getNickname(),
                    '%behaviour_name%' => (string) $behavior
                ));

        $child = $behavior->getChild();
        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $em = $this->getDoctrine()->getManager();

        $childBehaviorAssessment = new ChildBehaviorAssessment();
        $childBehaviorAssessment->setChildBehavior($behavior);
        $behavior->addChildBehaviorAssessment($childBehaviorAssessment);
        $em->persist($behavior);
        $em->flush();

        $this->get('whaam_notification.util.notification_manager')->send(
            $child->getChildUsers(),
            $this->get('translator')->trans('assessment_new_title', array(), 'notification'),
            sprintf(
                $this->get('translator')->trans('assessment_new', array(), 'notification'),
                (string) $child,
                (string) $this->getUser()
            ),
            $this->generateUrl('child_behavior_assessment_plan_list', array('childBehaviorAssessmentId' => $childBehaviorAssessment->getId()))
        );

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
            'childBehaviorAssessmentId' => $childBehaviorAssessment->getId()
            ))
        );
    }
}
