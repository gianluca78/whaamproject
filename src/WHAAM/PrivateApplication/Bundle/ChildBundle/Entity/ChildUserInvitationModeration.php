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

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_users_invitation_moderation")
 */
class ChildUserInvitationModeration
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
     * Whether the moderator accepted the invitation request or not
     *
     * @var boolean
     *
     * @ORM\Column(name="is_accepted", type="boolean", nullable=true)
     */
    private $isAccepted;

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
     * @var ChildUserInvitation
     *
     * @ORM\ManyToOne(targetEntity="ChildUserInvitation", cascade={"persist"})
     * @ORM\JoinColumn(name="child_user_invitation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childUserInvitation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="moderator_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $moderatorUser;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildUserInvitationModeration
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
     * @return ChildUserInvitationModeration
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
     * Set childUserInvitation
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation $childUserInvitation
     * @return ChildUserInvitationModeration
     */
    public function setChildUserInvitation(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation $childUserInvitation = null)
    {
        $this->childUserInvitation = $childUserInvitation;

        return $this;
    }

    /**
     * Get childUserInvitation
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation 
     */
    public function getChildUserInvitation()
    {
        return $this->childUserInvitation;
    }

    /**
     * Set moderatorUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $moderatorUser
     * @return ChildUserInvitationModeration
     */
    public function setModeratorUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $moderatorUser = null)
    {
        $this->moderatorUser = $moderatorUser;

        return $this;
    }

    /**
     * Get moderatorUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getModeratorUser()
    {
        return $this->moderatorUser;
    }

    /**
     * Set isAccepted
     *
     * @param boolean $isAccepted
     * @return ChildUserInvitationModeration
     */
    public function setIsAccepted($isAccepted)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get isAccepted
     *
     * @return boolean 
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }
}
