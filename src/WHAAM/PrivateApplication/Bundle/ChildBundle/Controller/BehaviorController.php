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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/behavior-categories")
 *
 * Class BehaviorController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class BehaviorController extends Controller
{
    /**
     * @Route("/behaviors", name="behavior-categories-list")
     * @Method({"POST"})
     * @Template
     */
    public function getBehaviorsAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();

        $behaviors = $em->getRepository('WHAAMPrivateApplicationChildBundle:Behavior')
            ->findBehaviorsByCategoryIdAndLocale($request->get('id'), $request->getLocale());

        return array('behaviors' => $behaviors);
    }
}
