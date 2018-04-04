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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_behaviors_assessments_observations_sessions")
 */
class ChildBehaviorAssessmentObservationSession
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
     * @ORM\Column(name="session_start_timestamp", type="datetime")
     *
     */
    private $sessionStartTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     */
    private $note;

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
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessmentBaseline", inversedBy="observationSessions", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_baseline_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $baseline;

    /**
     * @var ChildBehaviorAssessmentIntervention
     *
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessmentIntervention", inversedBy="observationSessions", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_intervention_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $intervention;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentObservationData", mappedBy="observationSession", cascade={"persist"})
     */
    private $observations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->observations = new ArrayCollection();
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
     * Set sessionStartTimestamp
     *
     * @param \DateTime $sessionStartTimestamp
     * @return ChildBehaviorAssessmentObservationSession
     */
    public function setSessionStartTimestamp(\DateTime $sessionStartTimestamp)
    {
        $this->sessionStartTimestamp = $sessionStartTimestamp;

        return $this;
    }

    /**
     * Get sessionStartTimestamp
     *
     * @return \DateTime 
     */
    public function getSessionStartTimestamp()
    {
        return $this->sessionStartTimestamp;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ChildBehaviorAssessmentObservationSession
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehaviorAssessmentObservationSession
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
     * Set baseline
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baseline
     * @return ChildBehaviorAssessmentObservationSession
     */
    public function setBaseline(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baseline = null)
    {
        $this->baseline = $baseline;

        return $this;
    }

    /**
     * Get baseline
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline 
     */
    public function getBaseline()
    {
        return $this->baseline;
    }

    /**
     * Set intervention
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention $intervention
     * @return ChildBehaviorAssessmentObservationSession
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
     * Add observations
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData $observations
     * @return ChildBehaviorAssessmentObservationSession
     */
    public function addObservation(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData $observations)
    {
        $this->observations[] = $observations;

        return $this;
    }

    /**
     * Remove observations
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData $observations
     */
    public function removeObservation(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData $observations)
    {
        $this->observations->removeElement($observations);
    }

    /**
     * Get observations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Check if baseline or intervention exists
     *
     * @param ExecutionContextInterface $context
     */
    public function isPhaseValid(ExecutionContextInterface $context)
    {
        if (!$this->getBaseline() && !$this->getIntervention()) {
            $context->buildViolation('Invalid phase')
                ->atPath('sessionStartTimestamp')
                ->addViolation();
        }
    }

}
