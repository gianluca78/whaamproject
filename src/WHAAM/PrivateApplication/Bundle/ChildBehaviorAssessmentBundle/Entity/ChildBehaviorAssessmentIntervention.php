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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_behaviors_assessments_interventions")
 */
class ChildBehaviorAssessmentIntervention
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     *
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date")
     *
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean")
     *
     */
    private $isLocked = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     */
    private $createdAt;

    /**
     * @var ChildBehaviorAssessmentBaseline
     *
     * @ORM\OneToOne(targetEntity="ChildBehaviorAssessmentBaseline", inversedBy="intervention", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_baseline_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorAssessmentBaseline;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="intervention_creator_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $interventionCreatorUser;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentInterventionStrategy", mappedBy="childBehaviorAssessmentIntervention", cascade={"persist"})
     */
    private $strategies;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentObservationSession", mappedBy="intervention", cascade={"persist"})
     */
    private $observationSessions;

    public function __construct()
    {
        $this->observationSessions = new ArrayCollection();
        $this->strategies = new ArrayCollection();
    }

    /**
     * Check if the intervention is active
     *
     * @return bool
     */
    public function isInterventionActive()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return ($this->startDate <= $today && $this->endDate >= $today && $this->isLocked == 1) ? true : false;
    }

    public function isInterventionComplete()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return ($this->endDate < $today && $this->isLocked == 1) ? true : false;
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return ChildBehaviorAssessmentIntervention
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return ChildBehaviorAssessmentIntervention
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return ChildBehaviorAssessmentIntervention
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean 
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehaviorAssessmentIntervention
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
     * Set childBehaviorAssessmentBaseline
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $childBehaviorAssessmentBaseline
     * @return ChildBehaviorAssessmentIntervention
     */
    public function setChildBehaviorAssessmentBaseline(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $childBehaviorAssessmentBaseline = null)
    {
        $this->childBehaviorAssessmentBaseline = $childBehaviorAssessmentBaseline;

        return $this;
    }

    /**
     * Get childBehaviorAssessmentBaseline
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline 
     */
    public function getChildBehaviorAssessmentBaseline()
    {
        return $this->childBehaviorAssessmentBaseline;
    }

    /**
     * Set interventionCreatorUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $interventionCreatorUser
     * @return ChildBehaviorAssessmentIntervention
     */
    public function setInterventionCreatorUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $interventionCreatorUser = null)
    {
        $this->interventionCreatorUser = $interventionCreatorUser;

        return $this;
    }

    /**
     * Get interventionCreatorUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getInterventionCreatorUser()
    {
        return $this->interventionCreatorUser;
    }

    /**
     * Add strategies
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentInterventionStrategy $strategies
     * @return ChildBehaviorAssessmentIntervention
     */
    public function addStrategy(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentInterventionStrategy $strategies)
    {
        $this->strategies[] = $strategies;

        return $this;
    }

    /**
     * Remove strategies
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentInterventionStrategy $strategies
     */
    public function removeStrategy(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentInterventionStrategy $strategies)
    {
        $this->strategies->removeElement($strategies);
    }

    /**
     * Get strategies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * Add observationSessions
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSessions
     * @return ChildBehaviorAssessmentIntervention
     */
    public function addObservationSession(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSessions)
    {
        $this->observationSessions[] = $observationSessions;

        return $this;
    }

    /**
     * Remove observationSessions
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSessions
     */
    public function removeObservationSession(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSessions)
    {
        $this->observationSessions->removeElement($observationSessions);
    }

    /**
     * Get observationSessions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObservationSessions()
    {
        return $this->observationSessions;
    }
}
