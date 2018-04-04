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
 * @author Giuseppe Chiazzese
 *
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity(repositoryClass="ChildBehaviorAssessmentBaselineRepository")
 * @ORM\Table(name="children_behaviors_assessments_baselines")
 */
class ChildBehaviorAssessmentBaseline
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
     * @var int
     *
     * @ORM\Column(name="minimum_number_of_observations", type="integer")
     *
     */
    private $minimumNumberOfObservations;

    /**
     * @var int
     *
     * @ORM\Column(name="observation_length", type="integer")
     *
     */
    private $observationLength;

    /**
     * @var int
     *
     * @ORM\Column(name="observation_type", type="string", length=50)
     *
     */
    private $observationType;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentABC", mappedBy="childBehaviorAssessmentBaseline", cascade={"persist"})
     */
    private $ABCs;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="children_behaviors_assessments_baselines_abcs_users",
     *     joinColumns = {@ORM\JoinColumn(name="child_behavior_assessment_baseline_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns = {@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    private $ABCUsers;

    /**
     * @var ChildBehaviorFunction
     *
     * @ORM\OneToOne(targetEntity="ChildBehaviorFunction", mappedBy="childBehaviorAssessmentBaseline", cascade={"persist", "remove"})
     *
     */
    private $childBehaviorFunction;

    /**
     * @todo
     */
    private $evaluation;

    /**
     * @var ChildBehaviorAssessment
     *
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessment", inversedBy="baselines", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorAssessment;

    /**
     * @var ChildBehaviorAssessmentIntervention
     *
     * @ORM\OneToOne(targetEntity="ChildBehaviorAssessmentIntervention", mappedBy="childBehaviorAssessmentBaseline", cascade={"persist", "remove"})
     */
    private $intervention;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="observer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $observer;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentObservationSession", mappedBy="baseline", cascade={"persist"})
     */
    private $observationSessions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="baseline_creator_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $baselineCreatorUser;

    public function __construct()
    {
        $this->ABCs = new ArrayCollection();
        $this->ABCUsers = new ArrayCollection();
        $this->observationSessions = new ArrayCollection();

    }

    /**
     * Copy the baseline settings in a new baseline object
     *
     * @return ChildBehaviorAssessmentBaseline
     */
    public function copyBaselineSettings()
    {
        $baseline = new ChildBehaviorAssessmentBaseline();
        $baseline->setIsContinuousOrInfrequent($this->getIsContinuousOrInfrequent());
        $baseline->setMinimumNumberOfObservations($this->getMinimumNumberOfObservations());
        $baseline->setObserver($this->getObserver());
        $baseline->setObservationLength($this->getObservationLength());
        $baseline->setChildBehaviorAssessment($this->getChildBehaviorAssessment());
        $baseline->setStartDate(new \DateTime());
        $baseline->setEndDate(new \DateTime());

        return $baseline;
    }

    /**
     * Check if the baseline is active now
     *
     * @return bool
     */
    public function isBaselineActive()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return ($this->startDate <= $today && $this->endDate >= $today && $this->isLocked == 1) ? true : false;
    }

    public function isBaselineComplete()
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return ($this->endDate < $today && $this->isLocked == 1) ? true : false;
    }

    /**
     * Shortcut for the method isInterventionActive
     * of the Intervention object
     *
     * @return bool
     */
    public function isInterventionActive()
    {
        return ($this->intervention) ? $this->intervention->isInterventionActive() : false;
    }

    public function isInterventionComplete()
    {
        return ($this->intervention) ? $this->intervention->isInterventionComplete() : false;
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
     * @return ChildBehaviorAssessmentBaseline
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
     * @return ChildBehaviorAssessmentBaseline
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
     * Set minimumNumberOfObservations
     *
     * @param integer $minimumNumberOfObservations
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setMinimumNumberOfObservations($minimumNumberOfObservations)
    {
        $this->minimumNumberOfObservations = $minimumNumberOfObservations;

        return $this;
    }

    /**
     * Get minimumNumberOfObservations
     *
     * @return integer 
     */
    public function getMinimumNumberOfObservations()
    {
        return $this->minimumNumberOfObservations;
    }

    /**
     * Set observationLength
     *
     * @param integer $observationLength
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setObservationLength($observationLength)
    {
        $this->observationLength = $observationLength;

        return $this;
    }

    /**
     * Get observationLength
     *
     * @return integer 
     */
    public function getObservationLength()
    {
        return $this->observationLength;
    }

    /**
     * Set observationType
     *
     * @param string $observationType
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setObservationType($observationType)
    {
        $this->observationType = $observationType;

        return $this;
    }

    /**
     * Get observationType
     *
     * @return string 
     */
    public function getObservationType()
    {
        return $this->observationType;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return ChildBehaviorAssessmentBaseline
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
     * @return ChildBehaviorAssessmentBaseline
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
     * @return ChildBehaviorAssessmentBaseline
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
     * Add ABCs
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC $aBCs
     * @return ChildBehaviorAssessmentBaseline
     */
    public function addABC(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC $aBCs)
    {
        $this->ABCs[] = $aBCs;

        return $this;
    }

    /**
     * Remove ABCs
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC $aBCs
     */
    public function removeABC(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC $aBCs)
    {
        $this->ABCs->removeElement($aBCs);
    }

    /**
     * Get ABCs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getABCs()
    {
        return $this->ABCs;
    }

    /**
     * Add ABCUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $aBCUsers
     * @return ChildBehaviorAssessmentBaseline
     */
    public function addABCUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $aBCUsers)
    {
        $this->ABCUsers[] = $aBCUsers;

        return $this;
    }

    /**
     * Remove ABCUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $aBCUsers
     */
    public function removeABCUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $aBCUsers)
    {
        $this->ABCUsers->removeElement($aBCUsers);
    }

    /**
     * Get ABCUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getABCUsers()
    {
        return $this->ABCUsers;
    }

    /**
     * Set childBehaviorFunction
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction $childBehaviorFunction
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setChildBehaviorFunction(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction $childBehaviorFunction = null)
    {
        $this->childBehaviorFunction = $childBehaviorFunction;

        return $this;
    }

    /**
     * Get childBehaviorFunction
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction 
     */
    public function getChildBehaviorFunction()
    {
        return $this->childBehaviorFunction;
    }

    /**
     * Set childBehaviorAssessment
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessment
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setChildBehaviorAssessment(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessment = null)
    {
        $this->childBehaviorAssessment = $childBehaviorAssessment;

        return $this;
    }

    /**
     * Get childBehaviorAssessment
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment 
     */
    public function getChildBehaviorAssessment()
    {
        return $this->childBehaviorAssessment;
    }

    /**
     * Set intervention
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention $intervention
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setIntervention(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention $intervention = null)
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * Get intervention
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention 
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    /**
     * Set observer
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $observer
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setObserver(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $observer = null)
    {
        $this->observer = $observer;

        return $this;
    }

    /**
     * Get observer
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getObserver()
    {
        return $this->observer;
    }

    /**
     * Add observationSessions
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSessions
     * @return ChildBehaviorAssessmentBaseline
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

    /**
     * Set baselineCreatorUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $baselineCreatorUser
     * @return ChildBehaviorAssessmentBaseline
     */
    public function setBaselineCreatorUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $baselineCreatorUser = null)
    {
        $this->baselineCreatorUser = $baselineCreatorUser;

        return $this;
    }

    /**
     * Get baselineCreatorUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getBaselineCreatorUser()
    {
        return $this->baselineCreatorUser;
    }
}
