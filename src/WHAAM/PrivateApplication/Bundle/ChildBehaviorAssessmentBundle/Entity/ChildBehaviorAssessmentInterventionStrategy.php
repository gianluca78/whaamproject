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
 * @author Gianluca Merlo
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;
/**
 * @ORM\Entity
 * @ORM\Table(name="children_behaviors_assessments_interventions_strategies")
 *
 */
class ChildBehaviorAssessmentInterventionStrategy
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
     * @ORM\Column(name="name", type="string", length=255)
     *
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     */
    private $description;

    /**
     * @var ChildBehaviorAssessmentIntervention
     *
     * @ORM\ManyToOne(targetEntity="ChildBehaviorAssessmentIntervention", inversedBy="strategies", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_assessment_intervention_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorAssessmentIntervention;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="children_behaviors_assessments_interventions_strategies_users",
     *     joinColumns = {@ORM\JoinColumn(name="child_behavior_assessment_intervention_strategy_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns = {@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    private $assignedUsers;

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

    public function __construct()
    {
        $this->assignedUsers = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ChildBehaviorAssessmentInterventionStrategy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ChildBehaviorAssessmentInterventionStrategy
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehaviorAssessmentInterventionStrategy
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
     * @return ChildBehaviorAssessmentInterventionStrategy
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
     * Set childBehaviorAssessmentIntervention
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention $childBehaviorAssessmentIntervention
     * @return ChildBehaviorAssessmentInterventionStrategy
     */
    public function setChildBehaviorAssessmentIntervention(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention $childBehaviorAssessmentIntervention = null)
    {
        $this->childBehaviorAssessmentIntervention = $childBehaviorAssessmentIntervention;

        return $this;
    }

    /**
     * Get childBehaviorAssessmentIntervention
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention 
     */
    public function getChildBehaviorAssessmentIntervention()
    {
        return $this->childBehaviorAssessmentIntervention;
    }

    /**
     * Add assignedUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $assignedUsers
     * @return ChildBehaviorAssessmentInterventionStrategy
     */
    public function addAssignedUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $assignedUsers)
    {
        $this->assignedUsers[] = $assignedUsers;

        return $this;
    }

    /**
     * Remove assignedUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $assignedUsers
     */
    public function removeAssignedUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $assignedUsers)
    {
        $this->assignedUsers->removeElement($assignedUsers);
    }

    /**
     * Get assignedUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignedUsers()
    {
        return $this->assignedUsers;
    }

    public function stringifyAssignedUsers()
    {
        $string = '';

        foreach ($this->assignedUsers as $key => $user) {
            $string .= $user->getSurnameNameOrUsername();

            if($this->assignedUsers->last() != $user) {
                $string .= '; ';
            }
        }

        return $string;
    }
}
