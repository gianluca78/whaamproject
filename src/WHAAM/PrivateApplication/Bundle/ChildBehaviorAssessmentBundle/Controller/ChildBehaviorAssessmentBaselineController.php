<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;

/**
 * @Route("/baseline")
 *
 * Class ChildBehaviorAssessmentBaselineController
 * @package WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBaselineController\Controller
 */
class ChildBehaviorAssessmentBaselineController extends Controller
{
    /**
     * @Route("/{baselineId}/delete", name="child_behavior_assessment_baseline_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $baselineId)
    {
        $baseline = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baselineId . ' not found'
            );
        }

        $childBehaviorAssessmentId = $baseline->getChildBehaviorAssessment()->getId();

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkBaselineCRUDActions($baseline)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();
        $behavior = $baseline->getChildBehaviorAssessment()->getChildBehavior();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($baseline);
            $em->flush();
            $message = $this->get('translator')->trans('baseline.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

        } catch (Exception $e) {

            $message = $this->get('translator')->trans('baseline.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            ))
        );
    }

    /**
     * @Route("/{baselineId}/edit", name="child_behavior_assessment_baseline_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editFirstAction(Request $request, $baselineId)
    {
        $baseline = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baseline . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkBaselineCRUDActions($baseline)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            )));
        }

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentBaseline:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior()
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $form = $this->createForm('child_behavior_assessment_baseline', $baseline, array(
            'action' => $this->generateUrl('child_behavior_assessment_baseline_edit', array(
                        'baselineId' => $baselineId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child.behavior_assessment.form.handler.baseline_form_handler');

        if ($formHandler->handle($form, $request, 'baseline.edit.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:firstBaselineFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_baseline', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{baselineId}/other/edit", name="child_behavior_assessment_baseline_other_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOtherAction(Request $request, $baselineId)
    {
        $baseline = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baseline . ' not found'
            );
        }

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentBaseline:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior()
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $form = $this->createForm('child_behavior_assessment_other_baseline', $baseline, array(
            'action' => $this->generateUrl('child_behavior_assessment_baseline_other_edit', array(
                        'baselineId' => $baselineId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child.behavior_assessment.form.handler.baseline_form_handler');

        if ($formHandler->handle($form, $request, 'baseline.edit.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:otherBaselineFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_baseline', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/new", name="child_behavior_assessment_baseline_new", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forwardNewAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkBaselineInsertion($childBehaviorAssessment)) {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t add a new baseline if you have not planned an intervention');

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        if($childBehaviorAssessment->getBaselines()->count() == 0) {
            return $this->forward('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:newFirst', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            ));
        }

        return $this->forward('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:newOther', array(
            'childBehaviorAssessmentId' => $childBehaviorAssessmentId
        ));
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/first/new", name="child_behavior_assessment_baseline_first_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFirstAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkBaselineInsertion($childBehaviorAssessment)) {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t add a new baseline if you have not planned an intervention');

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $child = $childBehaviorAssessment->getChildBehavior()->getChild();
        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentBaseline:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $childBehaviorAssessment->getChildBehavior()->getShortBehavior()
                ));

        $baseline = new ChildBehaviorAssessmentBaseline();
        $baseline->setChildBehaviorAssessment($childBehaviorAssessment);
        $baseline->setStartDate(new \DateTime());
        $baseline->setEndDate(new \DateTime());

        $form = $this->createForm('child_behavior_assessment_baseline', $baseline, array(
            'action' => $this->generateUrl('child_behavior_assessment_baseline_first_new', array(
                        'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child.behavior_assessment.form.handler.baseline_form_handler');

        if ($formHandler->handle($form, $request, 'baseline.new.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:firstBaselineFormPage.html.twig',
            array(
                'behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId(),
                'child' => $childBehaviorAssessment->getChildBehavior()->getChild(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_baseline', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/other/new", name="child_behavior_assessment_baseline_other_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newOtherAction(Request $request, $childBehaviorAssessmentId) {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkBaselineInsertion($childBehaviorAssessment)) {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t add a new baseline if you have not planned an intervention');

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $child = $childBehaviorAssessment->getChildBehavior()->getChild();
        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $baseline = $childBehaviorAssessment->getBaselines()->last()->copyBaselineSettings();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentBaseline:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $childBehaviorAssessment->getChildBehavior()->getShortBehavior()
                ));

        $form = $this->createForm('child_behavior_assessment_other_baseline', $baseline, array(
            'action' => $this->generateUrl('child_behavior_assessment_baseline_other_new', array(
                        'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child.behavior_assessment.form.handler.baseline_form_handler');

        if ($formHandler->handle($form, $request, 'baseline.new.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline:otherBaselineFormPage.html.twig',
            array(
                'behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId(),
                'child' => $childBehaviorAssessment->getChildBehavior()->getChild(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_baseline', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{baselineId}/baseline-notification", name="child_behavior_assessment_baseline_notification", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notificationAction(Request $request, $baselineId)
    {
        $baseline = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baselineId . ' not found'
            );
        }

        $baseline->setIsLocked(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($baseline);
        $em->flush();

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_notification.util.notification_manager')->send(
            $child->getChildUsers(),
            $this->get('translator')->trans('baseline_new_title', array(), 'notification'),
            sprintf(
                $this->get('translator')->trans('baseline_new', array(), 'notification'),
                $baseline->getChildBehaviorAssessment()->getChildBehavior(),
                $this->get('bcc_extra_tools.date_formatter')->format($baseline->getStartDate(), 'long', 'none'),
                $this->get('bcc_extra_tools.date_formatter')->format($baseline->getEndDate(), 'long', 'none')
            ),
            $this->generateUrl('child_behavior_assessment_baseline_view', array('baselineId' => $baselineId))
        );

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            ))
        );
    }

    /**
     * @Route("/{baselineId}/view", name="child_behavior_assessment_baseline_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $behaviorId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $baselineId)
    {
        $baseline = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baselineId . ' not found'
            );
        }

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentBaseline:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior()
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        return array(
            'child' => $child,
            'baseline' => $baseline
        );
    }
}