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
 * @ORM\Entity(repositoryClass="ChildDisciplineReferralRepository")
 * @ORM\Table(name="children_discipline_referrals")
 */
class ChildDisciplineReferral
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
     * @ORM\Column(type="string", length=255)
     */
    private $motivation;

    /**
     * @var \Datetime
     *
     * @ORM\Column(type="date")
     */
    private $date;

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
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="disciplineReferrals", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var DisciplineReferralType
     *
     * @ORM\ManyToOne(targetEntity="DisciplineReferralType")
     * @ORM\JoinColumn(name="discipline_referral_type_id", referencedColumnName="id")
     */
    private $disciplineReferralType;

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
     * Set motivation
     *
     * @param string $motivation
     * @return ChildDisciplineReferral
     */
    public function setMotivation($motivation)
    {
        $this->motivation = $motivation;

        return $this;
    }

    /**
     * Get motivation
     *
     * @return string 
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ChildDisciplineReferral
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildDisciplineReferral
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
     * @return ChildDisciplineReferral
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
     * @return ChildDisciplineReferral
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
     * Set disciplineReferralType
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\DisciplineReferralType $disciplineReferralType
     * @return ChildDisciplineReferral
     */
    public function setDisciplineReferralType(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\DisciplineReferralType $disciplineReferralType = null)
    {
        $this->disciplineReferralType = $disciplineReferralType;

        return $this;
    }

    /**
     * Get disciplineReferralType
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\DisciplineReferralType 
     */
    public function getDisciplineReferralType()
    {
        return $this->disciplineReferralType;
    }
}
