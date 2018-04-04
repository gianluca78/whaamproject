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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\Translator;

use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\ORM\EntityManager;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Util\ChildUserInvitationEmail,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Util\ChildUserManager,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitationModeration;

class ChildUserInvitationFormHandler {

    private $childUserManager;
    private $entityManager;
    private $invitationEmail;
    private $session;
    private $securityContext;
    private $translator;

    public function __construct(
        ChildUserManager $childUserManager,
        EntityManager $entityManager,
        Session $session,
        Translator $translator,
        ChildUserInvitationEmail $invitationEmail,
        SecurityContext $securityContext
    )
    {
        $this->childUserManager = $childUserManager;
        $this->entityManager = $entityManager;
        $this->invitationEmail = $invitationEmail;
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->translator = $translator;
    }

    public function handle(FormInterface $form, Request $request)
    {
        if(!$request->isMethod('POST')) {
            return false;
        }

        $form->bind($request);

        if(!$form->isValid()) {
            return false;
        }

        $childUserInvitation = $form->getData();
        $this->persistChildUserInvitation($childUserInvitation);

        return true;
    }

    public function persistChildUserInvitation(ChildUserInvitation $childUserInvitation)
    {
        $this->entityManager->persist($childUserInvitation);
        $this->entityManager->flush();

        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->findOneByEmail($childUserInvitation->getEmail());

        if($childUserInvitation->getSenderUser()
            ->getChildUserByChild($childUserInvitation->getChild())
            ->getIsAuthorizedToAccessData()
            ||
            $childUserInvitation->getSenderUser()
                ->getChildUserByChild($childUserInvitation->getChild())
                ->getRole()
                ->getRole() == 'ROLE_PARENT'
            ||
            $this->securityContext->getToken()->getUser() == $childUserInvitation->getChild()->getChildCreatorUser()
        ) {
            if($user) {
                $message = $this->invitationEmail->sendToRegisteredUser($childUserInvitation);
                $this->childUserManager->addUserToChildNetwork($childUserInvitation->getChild(), $user);
            } else {
                $message = $this->invitationEmail->sendToUnregisteredUser($childUserInvitation);
            }
        } else {
            $childUserInvitationModeration = new ChildUserInvitationModeration();
            $childUserInvitationModeration->setChildUserInvitation($childUserInvitation);
            $childUserInvitationModeration->setModeratorUser($childUserInvitation->getChild()->getChildCreatorUser());
            $this->entityManager->persist($childUserInvitationModeration);
            $this->entityManager->flush();

            $message = $this->invitationEmail->sendToModerator($childUserInvitation, $childUserInvitationModeration);
        }

        if(array_key_exists('success', $message)) {
            $this->session->getFlashBag()->add('success', $message['success']);
        } else {
            $this->session->getFlashBag()->add('error', $message['error']);
        }

    }
} 