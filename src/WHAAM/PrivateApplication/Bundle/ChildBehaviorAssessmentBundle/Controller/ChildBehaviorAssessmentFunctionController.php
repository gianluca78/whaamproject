<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior,
    WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;

/**
 * @Route("/function")
 *
 * Class ChildBehaviorAssessmentFunctionController
 * @package WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller
 */

class ChildBehaviorAssessmentFunctionController extends Controller
{
    /**
     * @Route("/{baselineId}/delete", name="child_behavior_assessment_function_delete", schemes={"http", "https"})
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

        $childBehaviorFunction = $baseline->getChildBehaviorFunction();

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkFunctionCRUDActions($childBehaviorFunction)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            )));
        }

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($childBehaviorFunction);
            $message = $this->get('translator')->trans('function.delete.success', array(), 'flash_messages');
            $this->get('session')->getFlashBag()->add('success', $message);

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('function.delete.error', array(), 'flash_messages');
            $this->get('session')->getFlashBag()->add('error', $message);
        }

        $intervention = $baseline->getIntervention();

        if ($intervention) {
            try {
                $em->remove($intervention);
                $message = $this->get('translator')->trans('intervention.delete.success', array(), 'flash_messages');
                $this->get('session')->getFlashBag()->add('success', $message);

            } catch (Exception $e) {
                $message = $this->get('translator')->trans('intervention.delete.error', array(), 'flash_messages');
                $this->get('session')->getFlashBag()->add('error', $message);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            ))
        );
    }

    /**
     * @Route("/{baselineId}/edit", name="child_behavior_assessment_function_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $baselineId)
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
        $behaviorFunction = $baseline->getChildBehaviorFunction();
        $childBehaviorAssessmentId = $baseline->getChildBehaviorAssessment()->getId();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkFunctionCRUDActions($behaviorFunction)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            )));
        }

        $behaviorName = (string) $baseline->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior();


        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorFunction:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $form = $this->createForm('childBehaviorFunction', $behaviorFunction, array(
            'action' => $this->generateUrl('child_behavior_assessment_function_edit', array(
                        'baselineId' => $baselineId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child_behavior_function.form.handler.child_behavior_function_form_handler');

        if ($formHandler->handle($form, $request, 'function.edit.success')) {

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentFunction:formPage.html.twig',
            array(
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_function', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/new", name="child_behavior_assessment_function_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkFunctionInsertion($childBehaviorAssessment)) {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t add a new function if you have not inserted a baseline');

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $baseline = $childBehaviorAssessment->getBaselines()->last();

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();
        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $behaviorName = (string) $baseline->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior();

        $behaviorFunction = new ChildBehaviorFunction();
        $behaviorFunction->setChildBehaviorAssessmentBaseline($baseline);

        $childBehaviorAssessmentId = $baseline->getChildBehaviorAssessment()->getId();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorFunction:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $form = $this->createForm('childBehaviorFunction', $behaviorFunction, array(
            'action' => $this->generateUrl('child_behavior_assessment_function_new', array(
                        'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_behavior_function.form.handler.child_behavior_function_form_handler');

        if ($formHandler->handle($form, $request, 'function.new.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $childBehaviorAssessmentId
                ))
            );
        }

        return $this->render(
                'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentFunction:formPage.html.twig',
                array(
                    'child' => $child,
                    'form' => $form->createView(),
                    'formTitle' => $this->get('translator')->trans('new_function', array(), 'interface')
                )
        );
    }

    /**
     * @Route("/{baselineId}/function-notification", name="child_behavior_assessment_function_notification", schemes={"http", "https"})
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

        $baseline->getChildBehaviorFunction()->setIsLocked(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($baseline);
        $em->flush();

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_notification.util.notification_manager')->send(
            $child->getChildUsers(),
            $this->get('translator')->trans('behavior_function_title', array(), 'notification'),
            sprintf(
                $this->get('translator')->trans('behavior_function_new', array(), 'notification'),
                $baseline->getChildBehaviorAssessment()->getChildBehavior()
            ),
            $this->generateUrl(
                'child_behavior_assessment_plan_list',
                array('childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId())
            )
        );

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            ))
        );
    }
}
