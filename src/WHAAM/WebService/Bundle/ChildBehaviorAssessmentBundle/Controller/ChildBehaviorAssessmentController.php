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
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\Controller\Annotations\Get,
    FOS\RestBundle\Controller\Annotations\Post,
    FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData;

class ChildBehaviorAssessmentController extends FOSRestController
{
    /**
     * @Get("/child-behavior-assessment/abcs/{baselineId}/{sessionId}", requirements={"baselineId" = "\d+", "sessionId" = "\S+"})
     *
     * @ApiDoc(
     *   description = "Return JSON including the baseline ABCs",
     *   https = true,
     *   requirements = {
     *     {
     *       "name" = "baselineId",
     *       "dataType" = "integet",
     *       "requirement" = "\d+",
     *       "description" = "The baseline ID",
     *     },
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "requirement" = "\S+",
     *       "description" = "The user session ID",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Invalid phase name",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the baseline is not found"
     *   }
     * )
     *
     * @throws BadRequestHttpException when the session id are invalid
     *
     */
    public function getABCsAction($baselineId, $sessionId)
    {
        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($sessionId)) {
            throw new BadRequestHttpException('invalid session id');
        }

        $baseline = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline')
            ->find($baselineId);

        if (!$baseline) {
            throw $this->createNotFoundException(
                'Baseline with id ' . $baselineId . ' not found'
            );
        }

        $ABCs = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentABC')
            ->findByBaseline($baseline);

        $view = $this->view($this->get('common_util.abc_data_composer')
            ->compose($ABCs), 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/child-behavior-assessment/observations/{phaseId}/{phaseName}/{sessionId}", requirements={"phaseId" = "\d+", "sessionId" = "\S+", "phaseName" = "\S+"})
     *
     * @ApiDoc(
     *   description = "Return JSON including the observation sessions and observation data",
     *   https = true,
     *   requirements = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "requirement" = "\S+",
     *       "description" = "The user session ID",
     *     },
     *     {
     *       "name" = "phaseId",
     *       "dataType" = "integer",
     *       "requirement" = "\d+",
     *       "description" = "The assessment phase ID",
     *     },
     *     {
     *       "name" = "phaseName",
     *       "dataType" = "string",
     *       "requirement" = "\S+",
     *       "description" = "The assessment phase name",
     *     },
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Invalid phase name",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the phase is not found"
     *   }
     * )
     *
     * @throws BadRequestHttpException when the session id are invalid
     *
     */
    public function getObservationsAction($phaseId, $phaseName, $sessionId)
    {
        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($sessionId)) {
            throw new BadRequestHttpException('invalid session id');
        }

        if($phaseName != 'baseline' && $phaseName != 'intervention') {
            throw new BadRequestHttpException('invalid phase name');
        }

        $className = 'ChildBehaviorAssessment' . ucfirst($phaseName);

        $phase = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:' . $className)
            ->findObservationDataByPhaseId($phaseId);

        if (!$phase) {
            throw $this->createNotFoundException(
                $phaseName . ' with id ' . $phaseId . ' not found'
            );
        }

        $view = $this->view($phase, 200);

        return $this->handleView($view);
    }

    /**
     * @Post("/child-behavior-assessment/abcs")
     *
     * @ApiDoc(
     *   description = "Create new abcs and return the updated assessment data",
     *   https = true,
     *   parameters = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "The user sessionId",
     *     },
     *     {
     *       "name" = "ABC[][userId]",
     *       "dataType" = "integer",
     *       "requirement" = "\d+",
     *       "description" = "The user id of the ABC creator"
     *     },
     *     {
     *       "name" = "ABCs[][abcDateTimestamp]",
     *       "dataType" = "integer",
     *       "required" = true,
     *       "description" = "the abc date timestamp",
     *     },
     *     {
     *       "name" = "ABCs[][antecedentWhere]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the where antecedent factor",
     *     },
     *     {
     *       "name" = "ABCs[][antecedentWhat]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the what antecedent factor",
     *     },
     *     {
     *       "name" = "ABCs[][antecedentWho]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the who antecedent factor",
     *     },
     *     {
     *       "name" = "ABCs[][antecedentTrigger]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the trigger event factor",
     *     },
     *     {
     *       "name" = "ABCs[][consequenceChildReaction]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the child's reaction consequence",
     *     },
     *     {
     *       "name" = "ABCs[][consequenceOthersReaction]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the other people reaction consequence",
     *     },
     *     {
     *       "name" = "ABCs[][baselineId]",
     *       "dataType" = "integer",
     *       "required" = true,
     *       "description" = "the identifier of baseline phase",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when the resource has been created",
     *     400 = "Returned for the request doesn't pass validation rules",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the baseline is not found"
     *   }
     * )
     *
     * @throws NotFoundHttpException when the baseline is not found
     * @throws BadRequestHttpException when the sessionId is not valid
     */
    public function postABCsAction(Request $request)
    {
        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($request->get('sessionId'))) {
            throw new BadRequestHttpException('invalid session id');
        }

        if($assessments = $this->get('whaam_web_service_child_behavior_assessment.util.abc_creator')
            ->save($request->get('ABCs'))
        ) {
            $view = $this->view($assessments, 200);

            return $this->handleView($view);
        } else {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }

    /**
     * @Post("/child-behavior-assessment/observations")
     *
     * @ApiDoc(
     *   description = "Create new observation sessions and return the updated assessment data",
     *   https = true,
     *   parameters = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "The user sessionId",
     *     },
     *     {
     *       "name" = "observationSessions[][phaseId]",
     *       "dataType" = "integer",
     *       "required" = true,
     *       "description" = "the identifier of the assessment phase",
     *     },
     *     {
     *       "name" = "observationSessions[][phaseName]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the observation session phase name",
     *       "format" = "baseline | intervention"
     *     },
     *     {
     *       "name" = "observationSessions[][note]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the note associated to the observation",
     *     },
     *     {
     *       "name" = "observationSessions[][sessionStartTimestamp]",
     *       "dataType" = "string",
     *       "required" = true,
     *       "description" = "the timestamp of the moment in which the user starts an observation session",
     *     },
     *     {
     *       "name" = "observationSessions[][timestamps][]",
     *       "dataType" = "integer",
     *       "required" = true,
     *       "description" = "the observation timestamp",
     *     },
     *   },
     *   statusCodes = {
     *     200 = "Returned when the resource has been created",
     *     400 = "Returned for the request doesn't pass validation rules",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the baseline or intervention is not found"
     *   }
     * )
     *
     * @throws HttpException when the phase type is invalid
     * @throws NotFoundHttpException when the baseline is not found
     * @throws NotFoundHttpException when the intervention is not found
     */
    public function postObservationsAction(Request $request)
    {
        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($request->get('sessionId'))) {
            throw new BadRequestHttpException('invalid session id');
        }

        $response = new Response();

        if($assessments = $this->get('whaam_web_service_child_behavior_assessment.util.observation_session_creator')
            ->save($request->get('observationSessions'))
        ) {
            $view = $this->view($assessments, 200);

            return $this->handleView($view);
        } else {
            $response->setStatusCode(400);
        }

        return $response;
    }
}