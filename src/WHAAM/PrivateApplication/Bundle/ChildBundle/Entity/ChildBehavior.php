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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\Criteria;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ChildBehaviorRepository")
 * @ORM\Table(name="children_behaviors")
 */
class ChildBehavior
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
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $setting;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255, nullable=true)
     */
    private $otherBehavior;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasOtherBehavior = FALSE;

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
     * @var BehaviorCategory
     *
     * @ORM\ManyToOne(targetEntity="BehaviorCategory", cascade={"persist"})
     */
    private $behaviorCategory;

    /**
     * @var Behavior
     *
     * @ORM\ManyToOne(targetEntity="Behavior", cascade={"persist"})
     */
    private $behavior;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="behaviors", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment", mappedBy="childBehavior", cascade={"persist"})
     */
    private $childBehaviorAssessments;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="child_behavior_creator_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childBehaviorCreatorUser;

    public function __construct()
    {
        $this->childBehaviorAssessments = new ArrayCollection();
    }

    public function __toString()
    {
        return ($this->getHasOtherBehavior() === TRUE) ?
            (string) $this->getOtherBehavior() :
            (string) $this->behavior;
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
     * Set description
     *
     * @param string $description
     * @return ChildBehavior
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
     * Set place
     *
     * @param string $place
     * @return ChildBehavior
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set setting
     *
     * @param string $setting
     * @return ChildBehavior
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get setting
     *
     * @return string 
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Set hasOtherBehavior
     *
     * @param boolean $hasOtherBehavior
     * @return ChildBehavior
     */
    public function setHasOtherBehavior($hasOtherBehavior)
    {
        $this->hasOtherBehavior = $hasOtherBehavior;

        return $this;
    }

    /**
     * Get hasOtherBehavior
     *
     * @return boolean
     */
    public function getHasOtherBehavior()
    {
        return $this->hasOtherBehavior;
    }

    /**
     * Set otherBehavior
     *
     * @param encrypted_string $otherBehavior
     * @return Child
     */
    public function setOtherBehavior($otherBehavior)
    {
        $this->otherBehavior = $otherBehavior;

        return $this;
    }

    /**
     * Get otherBehavior
     *
     * @return encrypted_string
     */
    public function getOtherBehavior()
    {
        return $this->otherBehavior;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildBehavior
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
     * @return ChildBehavior
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
     * Set behaviorCategory
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\BehaviorCategory $behaviorCategory
     * @return ChildBehavior
     */
    public function setBehaviorCategory(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\BehaviorCategory $behaviorCategory = null)
    {
        $this->behaviorCategory = $behaviorCategory;

        return $this;
    }

    /**
     * Get behaviorCategory
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\BehaviorCategory
     */
    public function getBehaviorCategory()
    {
        return $this->behaviorCategory;
    }

    /**
     * Set behavior
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behavior
     * @return ChildBehavior
     */
    public function setBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behavior = null)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * Get behavior
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior 
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * Set child
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child
     * @return ChildBehavior
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
     * Add childBehaviorAssessments
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessments
     * @return ChildBehavior
     */
    public function addChildBehaviorAssessment(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessments)
    {
        $this->childBehaviorAssessments[] = $childBehaviorAssessments;

        return $this;
    }

    /**
     * Remove childBehaviorAssessments
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessments
     */
    public function removeChildBehaviorAssessment(\WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment $childBehaviorAssessments)
    {
        $this->childBehaviorAssessments->removeElement($childBehaviorAssessments);
    }

    /**
     * Get childBehaviorAssessments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildBehaviorAssessments()
    {
        return $this->childBehaviorAssessments;
    }

    /**
     * Set childBehaviorCreatorUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $childBehaviorCreatorUser
     * @return ChildBehavior
     */
    public function setChildBehaviorCreatorUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $childBehaviorCreatorUser = null)
    {
        $this->childBehaviorCreatorUser = $childBehaviorCreatorUser;

        return $this;
    }

    /**
     * Get childBehaviorCreatorUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getChildBehaviorCreatorUser()
    {
        return $this->childBehaviorCreatorUser;
    }

    /**
     * Return the role of the user who created the behavior
     *
     * @return string|null
     */
    public function getRoleChildBehaviorCreatorUser()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('user', $this->getChildBehaviorCreatorUser())
            );

        $childUserCreator = $this->getChild()->getChildUsers()->matching($criteria);

        return ($childUserCreator) ? $childUserCreator[0]->getRole() : null;
    }

    public function getShortBehavior()
    {
        $behavior = ($this->hasOtherBehavior === TRUE) ? $this->getOtherBehavior() : $this->behavior;

        return strlen((string) $behavior) < 30 ? (string) $behavior : substr((string) $behavior, 0, 30) . ' [...]';
    }

    /**
     * Return the behavior active assessments
     *
     * @return ArrayCollection
     */
    public function getActiveChildBehaviorAssessments() {
        $assessments = new ArrayCollection();

        foreach($this->getChildBehaviorAssessments() as $assessment) {
            if($assessment->getBaselines()) {
                foreach($assessment->getBaselines() as $baseline) {
                    if($baseline->isBaselineActive() || $baseline->isInterventionActive()) {
                        $assessments->add($assessment);
                    }
                }
            }
        }

        return $assessments;
    }

}
