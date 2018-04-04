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
namespace WHAAM\WebService\Bundle\UserBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpKernel\Exception\HttpException,
    Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Get,
    FOS\RestBundle\Controller\Annotations\Post,
    FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 *
 * Class UserController
 *
 * @package WHAAM\WebService\Bundle\UserBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * @Get("/user-data/{sessionId}/{locale}", requirements={"sessionId" = "\S+", "locale" = "\S{5}"})
     *
     * @ApiDoc(
     *   description = "Return JSON with the complete dataset for a specific user, including the active assessments on the current date or JSON that indicates an error occurred",
     *   https = true,
     *   filters = {
     *     {
     *       "name"="locale",
     *       "requirement" = "\S{5}",
     *       "dataType"="string",
     *       "pattern"="el_GR|en_GB|it_IT|pt_PT"
     *     }
     *   },
     *   requirements = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "requirement" = "\S+",
     *       "description" = "The user session ID",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Invalid locale",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @throws BadRequestHttpException when the locale or the session id are invalid
     *
     */
    public function getUserDataAction($sessionId, $locale)
    {
        if(!$this->get('common_util.locale_validator')->isValidLocale($locale))
        {
            throw new BadRequestHttpException('invalid locale');
        }

        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($sessionId)) {
            throw new BadRequestHttpException('invalid session id');
        }

        $view = $this->view($this->get('common_util.user_data_composer')
            ->compose($this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->getLoggedUser(), $locale), 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/is-valid-session-id/{sessionId}", requirements={"sessionId" = "\S+"})
     *
     * @ApiDoc(
     *   description = "Check whether the mobile sessionId is valid and return a boolean value",
     *   https = true,
     *   requirements = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "requirement" = "\S+",
     *       "description" = "The user session ID",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     */
    public function isValidSessionIdAction(Request $request, $sessionId)
    {
        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($request->get('sessionId'))) {
            return $this->handleView($this->view(false));
        }

        return $this->handleView($this->view(true));

    }

    /**
     * @Post("/login")
     *
     * @ApiDoc(
     *   description = "Return JSON with both the session id and the user id for the logged user or JSON that indicates an error occurred",
     *   https = true,
     *   parameters = {
     *     {
     *       "name" = "username",
     *       "dataType" = "string",
     *       "required" = "true",
     *       "description" = "The user username",
     *     },
     *     {
     *       "name" = "password",
     *       "dataType" = "string",
     *       "required" = "true",
     *       "description" = "The user password",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the credentials are invalid or there are missing parameters",
     *     403 = "Returned when the client is not authorized",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @throws HttpException when required parameters are missing
     * @throws BadRequestHttpException when the credentials are invalid
     */
    public function loginAction(Request $request)
    {
        if(!$request->get('username') || !$request->get('password'))
        {
            throw new HttpException(400, 'missing required parameters');
        }

        $user = $this->getDoctrine()->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->findOneByUsername($request->get('username'));

        if (!$user) {
            throw $this->createNotFoundException(
                'User with username ' . $request->get('username') . ' not found'
            );
        }

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($request->get('password'), $user->getSalt());

        if($user->getPassword() == $encodedPassword) {
            $user->setMobileAppSessionId($user->generateToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $view = $this->view(array(
                'sessionId' => $user->getMobileAppSessionId(),
                'userId' => $user->getId()
            ), 200);

            return $this->handleView($view);
        } else {
            throw new BadRequestHttpException('invalid credentials');
        }
    }

    /**
     * @Post("/logout")
     *
     * @ApiDoc(
     *   description = "Return JSON with the status code 200 when successful or JSON that indicates an error occurred",
     *   https = true,
     *   parameters = {
     *     {
     *       "name" = "sessionId",
     *       "dataType" = "string",
     *       "required" = "true",
     *       "description" = "The user sessionId",
     *     }
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the sessionId is invalid or there are missing parameters",
     *     403 = "Returned when the client is not authorized"
     *   }
     * )
     *
     * @throws HttpException when required parameters are missing
     * @throws BadRequestHttpException when the session id is invalid
     */
    public function logoutAction(Request $request)
    {
        if(!$request->get('sessionId'))
        {
            throw new HttpException(400, 'missing required parameter');
        }

        if(!$this->get('whaam_web_service_user.util.mobile_app_session_id_validator')
            ->isValidSessionId($request->get('sessionId'))) {
            throw new BadRequestHttpException('invalid session id');
        }

        $em = $this->getDoctrine()->getManager();

        $user = $this->get('whaam_web_service_user.util.mobile_app_session_id_validator')->getLoggedUser();
        $user->setMobileAppSessionId(null);

        $em->persist($user);
        $em->flush();

        $view = $this->view(array(), 200);

        return $this->handleView($view);
    }
}