<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral;


/**
 * @Route("/case-data")
 *
 * Class ChildCaseDataController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildCaseDataController extends Controller
{
    /**
     * @Route("/{childSlug}", name="case_data_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $childSlug)
    {
        $child = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($childSlug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child with slug ' . $childSlug . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:ChildCaseData:index')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                ),
                array('%child_name%' => $child->getNickname()));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        return array(
           'childUsers' => $child->getChildUsers(),
           'diagnoses' => $child->getDiagnosesOrderedByDateDesc(),
           'medications'=> $child->getMedicationsOrderedByDateDesc(),
           'generalEvents'=> $child->getGeneralEventsOrderedByDateDesc(),
           'schoolsInformation'=> $child->getSchoolsInformationOrderedByDateDesc(),
           'disciplineReferrals'=> $child->getDisciplineReferralOrderedByDateDesc(),
           'child' => $child,
           'childSlug' => $childSlug
        );
    }
}
