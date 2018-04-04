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
 * @ORM\Entity(repositoryClass="ChildUserRepository")
 * @ORM\Table(name="children_users")
 */
class ChildUser
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="childUsers", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $child;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", inversedBy="childUsers",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * Whether the user is approved by a parent or not
     *
     * @var boolean
     *
     * @ORM\Column(name="is_approved_by_parent", type="boolean")
     */
    private $isApprovedByParent;

    /**
     * Whether the user is authorized to access data
     *
     * @var boolean
     *
     * @ORM\Column(name="is_authorized_to_access_data", type="boolean")
     */
    private $isAuthorizedToAccessData = FALSE;

    /**
     * @var ChildUserRole
     *
     * @ORM\ManyToOne(targetEntity="ChildUserRole", cascade={"persist"})
     */
    private $role;

    /**
     * Set isApprovedByParent
     *
     * @param boolean $isApprovedByParent
     * @return ChildUser
     */
    public function setIsApprovedByParent($isApprovedByParent)
    {
        $this->isApprovedByParent = $isApprovedByParent;

        return $this;
    }

    /**
     * Get isApprovedByParent
     *
     * @return boolean 
     */
    public function getIsApprovedByParent()
    {
        return $this->isApprovedByParent;
    }

    /**
     * Set isAuthorizedToAccessData
     *
     * @param boolean $isAuthorizedToAccessData
     * @return ChildUser
     */
    public function setIsAuthorizedToAccessData($isAuthorizedToAccessData)
    {
        $this->isAuthorizedToAccessData = $isAuthorizedToAccessData;

        return $this;
    }

    /**
     * Get isAuthorizedToAccessData
     *
     * @return boolean
     */
    public function getIsAuthorizedToAccessData()
    {
        return $this->isAuthorizedToAccessData;
    }

    /**
     * Set child
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child
     * @return ChildUser
     */
    public function setChild(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child)
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
     * Set user
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user
     * @return ChildUser
     */
    public function setUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set role
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserRole $role
     * @return ChildUser
     */
    public function setRole(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserRole 
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getRoleCategory()
    {
        return ($this->getRole()) ? $this->getRole()->getRole() : null;
    }
}
