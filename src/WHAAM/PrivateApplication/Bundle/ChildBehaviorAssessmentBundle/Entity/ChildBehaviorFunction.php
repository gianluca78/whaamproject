<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="children_behaviors_functions")
 *
 */
class ChildBehaviorFunction
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
     * @ORM\Column(name="behavior_when", type="text")
     *
     */
    private $behaviorWhen;

    /**
     * @var BehaviorFunction
     *
     * @ORM\ManyToOne(targetEntity="BehaviorFunction", cascade={"persist"})
     * @ORM\JoinColumn(name="behavior_function_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $behaviorFunction;

    /**
     * @var ChildBehaviorAssessmentBaseline
     *
     * @ORM\OneToOne(targetEntity="ChildBehaviorAssessmentBaseline", inversedBy="childBehaviorFunction", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_baseline_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorAssessmentBaseline;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     *
     */
    private $note;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set behaviorWhen
     *
     * @param string $behaviorWhen
     * @return ChildBehaviorFunction
     */
    public function setBehaviorWhen($behaviorWhen)
    {
        $this->behaviorWhen = $behaviorWhen;

        return $this;
    }

    /**
     * Get behaviorWhen
     *
     * @return string 
     */
    public function getBehaviorWhen()
    {
        return $this->behaviorWhen;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ChildBehaviorFunction
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
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return ChildBehaviorFunction
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
     * @return ChildBehaviorFunction
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
     * @return ChildBehaviorFunction
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
     * Set behaviorFunction
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\BehaviorFunction $behaviorFunction
     * @return ChildBehaviorFunction
     */
    public function setBehaviorFunction(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\BehaviorFunction $behaviorFunction = null)
    {
        $this->behaviorFunction = $behaviorFunction;

        return $this;
    }

    /**
     * Get behaviorFunction
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\BehaviorFunction 
     */
    public function getBehaviorFunction()
    {
        return $this->behaviorFunction;
    }

    /**
     * Set childBehaviorAssessmentBaseline
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline $childBehaviorAssessmentBaseline
     * @return ChildBehaviorFunction
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
}
