<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;

/**
 * @Route("/intervention")
 *
 * Class ChildBehaviorAssessmentInterventionController
 * @package WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentInterventionController\Controller
 */
class ChildBehaviorAssessmentInterventionController extends Controller
{
    /**
     * @Route("/{interventionId}/delete", name="child_behavior_assessment_intervention_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $interventionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $interventionId)
    {
        $intervention = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($interventionId);

        if (!$intervention) {
            throw $this->createNotFoundException(
                'Intervention with id ' . $interventionId . ' not found'
            );
        }

        $baseline = $intervention->getChildBehaviorAssessmentBaseline();
        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();
        $childBehaviorAssessmentId = $baseline->getChildBehaviorAssessment()->getId();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkInterventionCRUDActions($intervention)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            )));
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($intervention);
            $em->flush();
            $message = $this->get('translator')->trans('intervention.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('intervention.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            ))
        );
    }

    /**
     * @Route("/{interventionId}/edit", name="child_behavior_assessment_intervention_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param interventionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editFirstAction(Request $request, $interventionId)
    {
        $intervention = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($interventionId);

        if (!$intervention) {
            throw $this->createNotFoundException(
                'Intervention with id ' . $interventionId . ' not found'
            );
        }

        $baseline = $intervention->getChildBehaviorAssessmentBaseline();
        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkInterventionCRUDActions($intervention)) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
            )));
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentIntervention:edit')
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

        $form = $this->createForm('child_behavior_assessment_intervention', $intervention, array(
            'action' => $this->generateUrl('child_behavior_assessment_intervention_edit', array(
                        'interventionId' => $interventionId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $form->get('countInterventionStrategies')->setData(count($intervention->getStrategies()));

        $formHandler = $this->get('whaam_child_behavior_assessment.form.handler.intervention_form_handler');

        if ($formHandler->handle($form, $request, 'intervention.edit.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:firstInterventionFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_intervention', array(), 'interface'),
                'removeStrategyTranslation' => $this->get('translator')->trans('remove_strategy', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{interventionId}/other/edit", name="child_behavior_assessment_intervention_other_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param interventionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOtherAction(Request $request, $interventionId)
    {
        $intervention = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($interventionId);

        if (!$intervention) {
            throw $this->createNotFoundException(
                'Intervention with id ' . $interventionId . ' not found'
            );
        }

        $baseline = $intervention->getChildBehaviorAssessmentBaseline();
        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentIntervention:edit')
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

        $form = $this->createForm('child_behavior_assessment_other_intervention', $intervention, array(
            'action' => $this->generateUrl('child_behavior_assessment_intervention_other_edit', array(
                        'interventionId' => $interventionId
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child_behavior_assessment.form.handler.intervention_form_handler');

        if ($formHandler->handle($form, $request, 'intervention.edit.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:otherInterventionFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_intervention', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/new", name="child_behavior_assessment_intervention_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
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

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkInterventionInsertion($childBehaviorAssessment)) {
            $this->get('session')->getFlashBag()->add('error', 'You can\'t add a new baseline if you have not planned an intervention');

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        if($childBehaviorAssessment->hasAtLeastAnIntervention() == false) {
            return $this->forward('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:newFirst', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            ));
        }

        return $this->forward('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:newOther', array(
            'childBehaviorAssessmentId' => $childBehaviorAssessmentId
        ));
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/first/new", name="child_behavior_assessment_intervention_first_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $baselineId
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

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkInterventionInsertion($childBehaviorAssessment)) {
            $message = ($childBehaviorAssessment->getBaselines()->count() == 1) ?
                'You can\'t add a new intervention if you have not inserted the behavior function' :
                'You can\'t add a new intervention if you have not inserted a baseline';
            ;

            $this->get('session')->getFlashBag()->add('error', $message);

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $baseline = $childBehaviorAssessment->getBaselines()->last();

        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentIntervention:new')
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

        $intervention = new ChildBehaviorAssessmentIntervention();
        $intervention->setChildBehaviorAssessmentBaseline($baseline);
        $intervention->setInterventionCreatorUser($this->getUser());
        $intervention->setStartDate(new \DateTime());
        $intervention->setEndDate(new \DateTime());

        $form = $this->createForm('child_behavior_assessment_intervention', $intervention, array(
            'action' => $this->generateUrl('child_behavior_assessment_intervention_new', array(
                        'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $form->get('countInterventionStrategies')->setData('0');

        $formHandler = $this->get('whaam_child_behavior_assessment.form.handler.intervention_form_handler');

        if ($formHandler->handle($form, $request, 'intervention.new.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:firstInterventionFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_intervention', array(), 'interface'),
                'removeStrategyTranslation' => $this->get('translator')->trans('remove_strategy', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/other/new", name="child_behavior_assessment_intervention_other_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $baselineId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newOtherAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        if(!$this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')->checkInterventionInsertion($childBehaviorAssessment)) {
            $message = ($childBehaviorAssessment->getBaselines()->count() == 1) ?
                'You can\'t add a new intervention if you have not inserted the behavior function' :
                'You can\'t add a new intervention if you have not inserted a baseline';
            ;

            $this->get('session')->getFlashBag()->add('error', $message);

            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $childBehaviorAssessmentId
            )));
        }

        $baseline = $childBehaviorAssessment->getBaselines()->last();
        $child = $baseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentIntervention:new')
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

        $intervention = new ChildBehaviorAssessmentIntervention();
        $intervention->setChildBehaviorAssessmentBaseline($baseline);
        $intervention->setInterventionCreatorUser($this->getUser());
        $intervention->setStartDate(new \DateTime());
        $intervention->setEndDate(new \DateTime());

        $form = $this->createForm('child_behavior_assessment_other_intervention', $intervention, array(
            'action' => $this->generateUrl('child_behavior_assessment_intervention_other_new', array(
                        'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child_behavior_assessment.form.handler.intervention_form_handler');

        if ($formHandler->handle($form, $request, 'intervention.new.success')) {
            return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                    'childBehaviorAssessmentId' => $baseline->getChildBehaviorAssessment()->getId()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention:otherInterventionFormPage.html.twig',
            array(
                'behaviorId' => $baseline->getChildBehaviorAssessment()->getChildBehavior()->getId(),
                'child' => $child,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_intervention', array(), 'interface'),
                'removeStrategyTranslation' => $this->get('translator')->trans('remove_strategy', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{interventionId}/intervention-notification", name="child_behavior_assessment_intervention_notification", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notificationAction(Request $request, $interventionId)
    {
        $intervention = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($interventionId);

        if (!$intervention) {
            throw $this->createNotFoundException(
                'Intervention with id ' . $interventionId . ' not found'
            );
        }

        $intervention->setIsLocked(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($intervention);
        $em->flush();

        $child = $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_notification.util.notification_manager')->send(
            $child->getChildUsers(),
            $this->get('translator')->trans('intervention_new_title', array(), 'notification'),
            sprintf(
                $this->get('translator')->trans('intervention_new', array(), 'notification'),
                $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior(),
                $this->get('bcc_extra_tools.date_formatter')->format($intervention->getStartDate(), 'long', 'none'),
                $this->get('bcc_extra_tools.date_formatter')->format($intervention->getEndDate(), 'long', 'none')
            ),
            $this->generateUrl('child_behavior_assessment_intervention_view', array('interventionId' => $interventionId))
        );

        return $this->redirect($this->generateUrl('child_behavior_assessment_plan_list', array(
                'childBehaviorAssessmentId' => $intervention->getChildBehaviorAssessmentBaseline()
                    ->getChildBehaviorAssessment()->getId()
            ))
        );
    }

    /**
     * @Route("/strategy-data", name="child_behavior_assessment_intervention_strategy_data", schemes={"http", "https"})
     * @Method({"POST"})
     * @Template
     *
     * @param Request $request
     * @param $baselineId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function strategyDataAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($request->get('interventionId'));

        if(!$intervention) {
            throw $this->createNotFoundException(
                'No intervention found for id '.$request->get('id')
            );
        }

        $strategy = $intervention->getStrategies()->get($request->get('indexStrategy'));

        if($strategy) {
            $result = array(
                'indexStrategy' => $request->get('indexStrategy'),
                'name' => $strategy->getName(),
                'description' => $strategy->getDescription(),
                'assignedUsers' => $strategy->stringifyAssignedUsers(),
                'numberOfStrategies' => $intervention->getStrategies()->count()
            );

            return new Response(json_encode($result));

        }

        return new Response();
    }

    /**
     * @Route("/{interventionId}/view", name="child_behavior_assessment_intervention_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param interventionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $interventionId)
    {
        $intervention = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentIntervention')
            ->find($interventionId);

        if (!$intervention) {
            throw $this->createNotFoundException(
                'Intervention with id ' . $interventionId . ' not found'
            );
        }

        $child = $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentIntervention:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $intervention->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getShortBehavior()
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        return array(
            'child' => $child,
            'intervention' => $intervention
        );
    }
}
