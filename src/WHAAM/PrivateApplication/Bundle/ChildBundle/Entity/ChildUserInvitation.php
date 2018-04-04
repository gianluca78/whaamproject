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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_users_invitation")
 */
class ChildUserInvitation
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    private $isPending = 1;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="behaviors", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="sender_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $senderUser;

    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isPending
     *
     * @param boolean $isPending
     * @return ChildUserInvitation
     */
    public function setIsPending($isPending)
    {
        $this->isPending = $isPending;

        return $this;
    }

    /**
     * Get isPending
     *
     * @return boolean 
     */
    public function getIsPending()
    {
        return $this->isPending;
    }

    /**
     * Set email
     *
     * @param encrypted_string $email
     * @return ChildUserInvitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return encrypted_string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildUserInvitation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ChildUserInvitation
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set child
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child
     * @return ChildUserInvitation
     */
    public function setChild(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set senderUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $senderUser
     * @return ChildUserInvitation
     */
    public function setSenderUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $senderUser = null)
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    /**
     * Get senderUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getSenderUser()
    {
        return $this->senderUser;
    }

    /**
     * Add a violation if the user has not yet specified his role in the network
     *
     * @param ExecutionContextInterface $context
     */
    public function hasSpecifiedRole(ExecutionContextInterface $context)
    {
        if(!$this->getSenderUser()->getChildUserByChild($this->child)->getRole()) {
            $context->buildViolation('no_role_invitation.error')
                ->setTranslationDomain('validators')
                ->atPath('email')
                ->addViolation();
        }
    }

    /**
     * Add a violation if the user invite himself
     *
     * @param ExecutionContextInterface $context
     */
    public function isSelfInvitation(ExecutionContextInterface $context)
    {
        if($this->getEmail() == $this->getSenderUser()->getEmail()) {
            $context->buildViolation('self_invitation.error')
                ->setTranslationDomain('validators')
                ->atPath('email')
                ->addViolation();
        }
    }
}
