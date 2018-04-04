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
 * @ORM\Entity(repositoryClass="ChildBehaviorAssessmentABCRepository")
 * @ORM\Table(name="children_behaviors_assessments_abcs")
 */
class ChildBehaviorAssessmentABC
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
     * @ORM\Column(name="abc_date", type="datetime")
     *
     */
    private $ABCDate;

    /**
     * @var string
     *
     * @ORM\Column(name="antecedent_where", type="text")
     *
     */
    private $antecedentWhere;

    /**
     * @var string
     *
     * @ORM\Column(name="antecedent_what", type="text")
     *
     */
    private $antecedentWhat;

    /**
     * @var string
     *
     * @ORM\Column(name="antecedent_who", type="text")
     *
     */
    private $antecedentWho;

    /**
     * @var string
     *
     * @ORM\Column(name="antecedent_trigger", type="text")
     *
     */
    private $antecedentTrigger;

    /**
     * @var string
     *
     * @ORM\Column(name="consequence_child_reaction", type="text")
     *
     */
    private $consequenceChildReaction;

    /**
     * @var string
     *
     * @ORM\Column(name="consequence_others_reaction", type="text")
     *
     */
    private $consequenceOthersReaction;

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
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessmentBaseline", inversedBy="ABCs", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_baseline_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorAssessmentBaseline;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

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
     * Set ABCDate
     *
     * @param \DateTime $aBCDate
     * @return ChildBehaviorAssessmentABC
     */
    public function setABCDate($aBCDate)
    {
        $this->ABCDate = $aBCDate;

        return $this;
    }

    /**
     * Get ABCDate
     *
     * @return \DateTime 
     */
    public function getABCDate()
    {
        return $this->ABCDate;
    }

    /**
     * Set antecedentWhere
     *
     * @param string $antecedentWhere
     * @return ChildBehaviorAssessmentABC
     */
    public function setAntecedentWhere($antecedentWhere)
    {
        $this->antecedentWhere = $antecedentWhere;

        return $this;
    }

    /**
     * Get antecedentWhere
     *
     * @return string 
     */
    public function getAntecedentWhere()
    {
        return $this->antecedentWhere;
    }

    /**
     * Set antecedentWhat
     *
     * @param string $antecedentWhat
     * @return ChildBehaviorAssessmentABC
     */
    public function setAntecedentWhat($antecedentWhat)
    {
        $this->antecedentWhat = $antecedentWhat;

        return $this;
    }

    /**
     * Get antecedentWhat
     *
     * @return string 
     */
    public function getAntecedentWhat()
    {
        return $this->antecedentWhat;
    }

    /**
     * Set antecedentWho
     *
     * @param string $antecedentWho
     * @return ChildBehaviorAssessmentABC
     */
    public function setAntecedentWho($antecedentWho)
    {
        $this->antecedentWho = $antecedentWho;

        return $this;
    }

    /**
     * Get antecedentWho
     *
     * @return string 
     */
    public function getAntecedentWho()
    {
        return $this->antecedentWho;
    }

    /**
     * Set antecedentTrigger
     *
     * @param string $antecedentTrigger
     * @return ChildBehaviorAssessmentABC
     */
    public function setAntecedentTrigger($antecedentTrigger)
    {
        $this->antecedentTrigger = $antecedentTrigger;

        return $this;
    }

    /**
     * Get antecedentTrigger
     *
     * @return string 
     */
    public function getAntecedentTrigger()
    {
        return $this->antecedentTrigger;
    }

    /**
     * Set consequenceChildReaction
     *
     * @param string $consequenceChildReaction
     * @return ChildBehaviorAssessmentABC
     */
    public function setConsequenceChildReaction($consequenceChildReaction)
    {
        $this->consequenceChildReaction = $consequenceChildReaction;

        return $this;
    }

    /**
     * Get consequenceChildReaction
     *
     * @return string 
     */
    public function getConsequenceChildReaction()
    {
        return $this->consequenceChildReaction;
    }

    /**
     * Set consequenceOthersReaction
     *
     * @param string $consequenceOthersReaction
     * @return ChildBehaviorAssessmentABC
     */
    public function setConsequenceOthersReaction($consequenceOthersReaction)
    {
        $this->consequenceOthersReaction = $consequenceOthersReaction;

        return $this;
    }

    /**
     * Get consequenceOthersReaction
     *
     * @return string 
     */
    public function getConsequenceOthersReaction()
    {
        return $this->consequenceOthersReaction;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehaviorAssessmentABC
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
     * @return ChildBehaviorAssessmentABC
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
     * Set user
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user
     * @return ChildBehaviorAssessmentABC
     */
    public function setUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user = null)
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
}
