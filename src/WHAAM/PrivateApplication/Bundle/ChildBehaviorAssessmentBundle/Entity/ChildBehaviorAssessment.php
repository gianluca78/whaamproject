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
 * @ORM\Entity(repositoryClass="ChildBehaviorAssessmentRepository")
 * @ORM\Table(name="children_behaviors_assessments")
 */
class ChildBehaviorAssessment
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
     * @var ChildBehavior
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior", inversedBy="childBehaviorAssessments", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehavior;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehaviorAssessmentBaseline", mappedBy="childBehaviorAssessment", cascade={"persist"})
     */
    private $baselines;

    public function __construct()
    {
        $this->baselines = new ArrayCollection();
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
     * @return ChildBehaviorAssessment
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
     * @return ChildBehaviorAssessment
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
     * Set childBehavior
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $childBehavior
     * @return ChildBehaviorAssessment
     */
    public function setChildBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $childBehavior = null)
    {
        $this->childBehavior = $childBehavior;

        return $this;
    }

    /**
     * Get childBehavior
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior 
     */
    public function getChildBehavior()
    {
        return $this->childBehavior;
    }

    /**
     * Add baselines
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baselines
     * @return ChildBehaviorAssessment
     */
    public function addBaseline(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baselines)
    {
        $this->baselines[] = $baselines;

        return $this;
    }

    /**
     * Remove baselines
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baselines
     */
    public function removeBaseline(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $baselines)
    {
        $this->baselines->removeElement($baselines);
    }

    /**
     * Get baselines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBaselines()
    {
        return $this->baselines;
    }

    public function hasAtLeastAnIntervention() {
        foreach($this->getBaselines() as $baseline) {
            if($baseline->getIntervention()) {
                return true;
            }
        }

        return false;
    }

    public function isComplete() {
        foreach($this->getBaselines() as $baseline) {
            if($baseline->isBaselineComplete() && $baseline->isInterventionComplete()) {
                return true;
            }

            return false;
        }
    }
}
