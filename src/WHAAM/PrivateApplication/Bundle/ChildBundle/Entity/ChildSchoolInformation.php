<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ChildSchoolInformationRepository")
 * @ORM\Table(name="children_schools_information")
 */
class ChildSchoolInformation
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
     * @var string $year
     *
     * @ORM\Column(type="string", length=7)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="school_name", type="string", length=255)
     */
    private $schoolName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $grade;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $hasSpecialNeedSupportTeacher;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $hasIndividualEducationPlan;

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
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="schoolsInformation", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;


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
     * Set year
     *
     * @param string $year
     * @return ChildSchoolInformation
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set schoolName
     *
     * @param string $schoolName
     * @return ChildSchoolInformation
     */
    public function setSchoolName($schoolName)
    {
        $this->schoolName = $schoolName;

        return $this;
    }

    /**
     * Get schoolName
     *
     * @return string 
     */
    public function getSchoolName()
    {
        return $this->schoolName;
    }

    /**
     * Set grade
     *
     * @param string $grade
     * @return ChildSchoolInformation
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string 
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set hasSpecialNeedSupportTeacher
     *
     * @param boolean $hasSpecialNeedSupportTeacher
     * @return ChildSchoolInformation
     */
    public function setHasSpecialNeedSupportTeacher($hasSpecialNeedSupportTeacher)
    {
        $this->hasSpecialNeedSupportTeacher = $hasSpecialNeedSupportTeacher;

        return $this;
    }

    /**
     * Get hasSpecialNeedSupportTeacher
     *
     * @return boolean 
     */
    public function getHasSpecialNeedSupportTeacher()
    {
        return $this->hasSpecialNeedSupportTeacher;
    }

    /**
     * Set hasIndividualEducationPlan
     *
     * @param boolean $hasIndividualEducationPlan
     * @return ChildSchoolInformation
     */
    public function setHasIndividualEducationPlan($hasIndividualEducationPlan)
    {
        $this->hasIndividualEducationPlan = $hasIndividualEducationPlan;

        return $this;
    }

    /**
     * Get hasIndividualEducationPlan
     *
     * @return boolean 
     */
    public function getHasIndividualEducationPlan()
    {
        return $this->hasIndividualEducationPlan;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildSchoolInformation
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
     * @return ChildSchoolInformation
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
     * @return ChildSchoolInformation
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

    public function convertHasSpecialNeedSupportTeacherToString()
    {
        return ($this->hasSpecialNeedSupportTeacher==1) ? 'Yes' : 'No';
    }

    public function convertHasIndividualEducationPlanToString()
    {
        return ($this->hasIndividualEducationPlan==1) ? 'Yes' : 'No';
    }
}
