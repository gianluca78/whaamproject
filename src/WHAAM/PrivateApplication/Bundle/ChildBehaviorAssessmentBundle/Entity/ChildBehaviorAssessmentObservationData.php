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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="children_behaviors_assessments_observations_data")
 */
class ChildBehaviorAssessmentObservationData
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
     * @ORM\Column(name="observation_timestamp", type="datetime")
     *
     */
    private $observationTimestamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     */
    private $createdAt;

    /**
     * @var ChildBehaviorAssessmentObservationSession
     *
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessmentObservationSession", inversedBy="observations", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_observation_session_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $observationSession;

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
     * Set observationTimestamp
     *
     * @param \DateTime $observationTimestamp
     * @return ChildBehaviorAssessmentObservationData
     */
    public function setObservationTimestamp($observationTimestamp)
    {
        $this->observationTimestamp = $observationTimestamp;

        return $this;
    }

    /**
     * Get observationTimestamp
     *
     * @return \DateTime
     */
    public function getObservationTimestamp()
    {
        return $this->observationTimestamp;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehaviorAssessmentObservationData
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
     * Set observationSession
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSession
     * @return ChildBehaviorAssessmentObservationData
     */
    public function setObservationSession(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession $observationSession = null)
    {
        $this->observationSession = $observationSession;

        return $this;
    }

    /**
     * Get observationSession
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession 
     */
    public function getObservationSession()
    {
        return $this->observationSession;
    }
}
