<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Ob\HighchartsBundle\Highcharts\Highchart;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;
use Zend\Json\Expr;

/**
 * @Route("/assessment-plan")
 *
 * Class ChildBehaviorAssessmentPlanController
 * @package WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Controller
 */
class ChildBehaviorAssessmentPlanController extends Controller
{
    /**
     * @Route("/abc-data", name="child_behavior_assessment_abc_data", schemes={"http", "https"})
     * @Method({"POST"})
     * @Template
     *
     * @param Request $request
     * @param $ABCid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ABCdataAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();
        $ABC = $em->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentABC')
            ->find($request->get('ABCid'));

        if(!$ABC) {
            throw $this->createNotFoundException(
                'No ABC found for id '.$request->get('id')
            );
        }

        $ABCdata = array(
            'date' => $ABC->getABCDate(),
            'where' => $ABC->getAntecedentWhere(),
            'what' => $ABC->getAntecedentWhat(),
            'who' => $ABC->getAntecedentWho(),
            'trigger' => $ABC->getAntecedentTrigger(),
            'consequence' => $ABC->getConsequenceChildReaction(),
            'consequenceOther' => $ABC->getConsequenceOthersReaction(),
            'observer' => (string) $ABC->getUser()
        );

        return new Response(json_encode($ABCdata));
    }

    /**
     * @Route("/{childBehaviorAssessmentId}", name="child_behavior_assessment_plan_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check(
            $this->getUser(),
            $childBehaviorAssessment->getChildBehavior()->getChild()
        );

        $behaviorName = $childBehaviorAssessment->getChildBehavior()->getShortBehavior();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:index')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childBehaviorAssessment->getChildBehavior()->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $childBehaviorAssessment->getChildBehavior()->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $childBehaviorAssessment->getChildBehavior()->getChild()->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $analysis = $this->get('whaam_child_behavior_assessment.util.r.r_manager')
            ->calculateTau($childBehaviorAssessment);

        $templateVariables = array(
            'baselines' => $childBehaviorAssessment->getBaselines(),
            'child' => $childBehaviorAssessment->getChildBehavior()->getChild(),
            'childBehaviorAssessmentId' => $childBehaviorAssessmentId,
            'tauUAnalysis' => null,
            'tauUAnalysisMessage' => null,
            'tauUAnalysisEffectSizeMessage' => null
        );

        if ($analysis) {
            $jsonAnalysis = json_decode($analysis);
            $tauUAnalysis = $jsonAnalysis->TAU_U_Analysis[10]->AvsBTrendBTrendA;
            $tauUAnalysisMessage = $this->get('whaam_child_behavior_assessment.util.r.r_manager')
                ->getAVsBPlusTrendBMinusTrendATauSentence($childBehaviorAssessment);
            $tauUAnalysisEffectSizeMessage = $this->get('whaam_child_behavior_assessment.util.r.r_manager')
                ->getTauEffectSizeSentence($tauUAnalysis);

            $templateVariables['tauUAnalysis'] = $tauUAnalysis;
            $templateVariables['tauUAnalysisMessage'] = $tauUAnalysisMessage;
            $templateVariables['tauUAnalysisEffectSizeMessage'] = $tauUAnalysisEffectSizeMessage;
        }

        return $templateVariables;
    }

    /**
     * @Route("/abcs/{baselineId}", name="child_behavior_assessment_plan_abcs_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexABCAction(Request $request, $baselineId)
    {
        $childBehaviorAssessmentBaseline = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$childBehaviorAssessmentBaseline) {
            throw $this->createNotFoundException(
                'Child behavior assessment baseline with id ' . $baselineId . ' not found'
            );
        }

        $child = $childBehaviorAssessmentBaseline->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check(
            $this->getUser(),
            $child
        );

        $behavior = $childBehaviorAssessmentBaseline->getChildBehaviorAssessment()->getChildBehavior();

        $phaseNameTranslation = $this->get('translator')->trans('Baseline', array(), 'interface');

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:viewAbc')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behavior->getId()),
                    'assessments' => array('behaviorId' => $behavior->getId()),
                    '%phase_name%' => array('baselineId' => $childBehaviorAssessmentBaseline->getId())

                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $behavior->getShortBehavior(),
                    '%phase_name%' => $phaseNameTranslation
                ));

        $ABC = ($childBehaviorAssessmentBaseline->getABCs()) ? $childBehaviorAssessmentBaseline->getABCs()->last() : null;

        return array(
            'ABCs' => $childBehaviorAssessmentBaseline->getABCs(),
            'ABC' => $ABC,
            'child' => $child
        );
    }

    /**
     * @Route("/{childBehaviorAssessmentId}/render-menu", name="child_behavior_assessment_plan_render_menu", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @param int $childBehaviorId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderMenuAction(Request $request, $childBehaviorAssessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($childBehaviorAssessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $childBehaviorAssessmentId . ' not found'
            );
        }

        $nextPhase = $this->get('whaam_child_behavior_assessment.util.assessment_plan_checker')
            ->getNextPhaseAvailable($childBehaviorAssessment);

        return $this->render('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentPlan:menu' . $nextPhase . '.html.twig',
            array('id' => $childBehaviorAssessmentId)
        );
    }

    /**
     * @Route("/{assessmentId}/analysis-details", name="child_behavior_assessment_view_analysis_details", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAnalysisDetailsAction(Request $request, $assessmentId)
    {
        $childBehaviorAssessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($assessmentId);

        if (!$childBehaviorAssessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $assessmentId . ' not found'
            );
        }

        $behaviorName = $childBehaviorAssessment->getChildBehavior()->getShortBehavior();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:viewAnalysisDetails')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childBehaviorAssessment->getChildBehavior()->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $childBehaviorAssessment->getChildBehavior()->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $childBehaviorAssessment->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $childBehaviorAssessment->getChildBehavior()->getChild()->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $analysis = $this->get('whaam_child_behavior_assessment.util.r.r_manager')->calculateTau($childBehaviorAssessment);
        $jsonAnalysis = json_decode($analysis);

        return array(
            'data' => $jsonAnalysis,
            'aVsBPlusTrendBMinusTrendATauMessage' => $this->get('whaam_child_behavior_assessment.util.r.r_manager')
                ->getAVsBPlusTrendBMinusTrendATauSentence($childBehaviorAssessment),
            'aVsBTauSentenceMessage' => $this->get('whaam_child_behavior_assessment.util.r.r_manager')
                ->getAVsBTauSentence($childBehaviorAssessment),
            'aVsBPlusTrendBTauSentence' => $this->get('whaam_child_behavior_assessment.util.r.r_manager')
                ->getAVsBPlusTrendBTauSentence($childBehaviorAssessment)
        );
    }

    /**
     * @Route("/{phaseName}/{assessmentPhaseId}/view-chart", name="child_behavior_assessment_view_chart", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewChartAction(Request $request, $phaseName, $assessmentPhaseId)
    {
        $phase = $this->get('whaam_web_service_child_behavior_assessment.util.observation_session_creator')
            ->findBaselineOrIntervention($phaseName, $assessmentPhaseId);

        $child = ($phaseName == 'baseline') ? $phase->getChildBehaviorAssessment()->getChildBehavior()->getChild() :
            $phase->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $this->get('common_util.child_network_members_checker')->check(
            $this->getUser(),
            $child
        );

        $phaseNameTranslation = ($phaseName == 'baseline') ?  $this->get('translator')->trans('Baseline', array(), 'interface') : $this->get('translator')->trans('Intervention', array(), 'interface');

        $interventionBaselineId = ($phaseName == 'baseline') ? array('baselineId' => $phase->getId()) :
            array('interventionId' => $phase->getId());

        $behavior = ($phaseName == 'baseline') ? $phase->getChildBehaviorAssessment()->getChildBehavior() :
            $phase->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior();

        $breadcrumbName = ($phaseName == 'baseline') ? 'ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:viewChart' : 'ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:viewInterventionChart';

        $this->get('whaam_breadcrumbs')->load($breadcrumbName)
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $child->getSlug()),
                    'behaviours' => array('childSlug' => $child->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behavior->getId()),
                    'assessments' => array('behaviorId' => $behavior->getId()),
                    '%phase_name%' => $interventionBaselineId
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                    '%behaviour_name%' => $behavior->getShortBehavior(),
                    '%phase_name%' => $phaseNameTranslation
                ));

        $chart = $this->get('whaam_child_behavior_assessment.util.behavioral_data_chart.behavioral_chart_generator')
            ->generateScatterPlotForAssessmentPhase($phase);

        return array(
            'chart' => $chart
        );
    }

    /**
     * @Route("/{assessmentId}/view-evaluation-chart", name="child_behavior_assessment_view_evaluation_chart", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewEvaluationChartAction(Request $request, $assessmentId)
    {
        $assessment = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment')
            ->find($assessmentId);

        if (!$assessment) {
            throw $this->createNotFoundException(
                'Child behavior assessment with id ' . $assessmentId . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check(
            $this->getUser(),
            $assessment->getChildBehavior()->getChild()
        );

        $behaviorName =  $assessment->getChildBehavior()->getShortBehavior();

        $this->get('whaam_breadcrumbs')->load('ChildBehaviorAssessment:ChildBehaviorAssessmentPlan:viewEvaluationChart')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $assessment->getChildBehavior()->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $assessment->getChildBehavior()->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $assessment->getChildBehavior()->getId()),
                    'assessments' => array('behaviorId' => $assessment->getChildBehavior()->getId())

                ),
                array(
                    '%child_name%' => $assessment->getChildBehavior()->getChild()->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $chart = $this->get('whaam_child_behavior_assessment.util.behavioral_data_chart.behavioral_chart_generator')
            ->generateScatterPlotForAllAssessmentPhases($assessment);

        return array(
            'chart' => $chart
        );
    }
}