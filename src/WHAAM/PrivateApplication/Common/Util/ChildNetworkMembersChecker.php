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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Bundle\FrameworkBundle\Templating\EngineInterface,
    Symfony\Component\HttpFoundation\Session\Session;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserRepository,
    WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class ChildNetworkMembersChecker {

    private $childUserRepository;
    private $session;
    private $templating;


    public function __construct(
        ChildUserRepository $childUserRepository,
        Session $session,
        EngineInterface $templating
    )
    {
        $this->childUserRepository = $childUserRepository;
        $this->session = $session;
        $this->templating = $templating;
    }

    /**
     * Check if an user is included in the child network
     *
     * @param User $user
     * @param Child $child
     *
     * @return boolean | AccessDeniedException if the user is not found
     */
    public function check(User $user, Child $child)
    {
        $childUser = $this->childUserRepository->findOneByChildUserParentApproval($child, $user, 1);

        if(!$childUser) {
            throw new AccessDeniedHttpException('Access denied to the network of the child ' . $child->getNickname() . '
                for the user ' . $user->getUsername());
        }

        if(!$childUser->getRole()) {
            $warningMessage = $this->templating->render(
                'WHAAMPrivateApplicationChildBundle:ChildUser:missingRoleWarning.html.twig',
                array('child' => $child));

            $warningMessagesInSession = $this->session->getFlashBag()->peek('warning-' . $child->getSlug());

            if(in_array($warningMessage, $warningMessagesInSession)==false)
            {
                $this->session->getFlashBag()->add('warning-' . $child->getSlug(), $warningMessage);
            }
        }

        return true;
    }

    public function isGranted(User $user, Child $child, $role) {
        $childUser = $this->childUserRepository->findOneByChildUserParentApproval($child, $user, 1);

        if(!$childUser || ($childUser->getRole() && $childUser->getRole()->getRole()!=$role)) {
            throw new AccessDeniedHttpException('Access denied to the network of the child ' . $child->getNickname() . '
                for the user ' . $user->getUsername());
        }

        return true;
    }
}