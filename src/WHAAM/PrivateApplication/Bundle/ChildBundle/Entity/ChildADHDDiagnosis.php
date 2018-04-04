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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="ChildADHDDiagnosisRepository")
 * @ORM\Table(name="children_adhd_diagnoses")
 */
class ChildADHDDiagnosis
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
     * @ORM\Column(name="diagnosis_date", type="date")
     */
    private $diagnosisDate;

    /**
     * @var int
     *
     * @ORM\Column(name="onset_age", type="integer")
     */
    private $onsetAge;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_secondary_disorder", type="boolean")
     */
    private $isSecondaryDisorder;

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
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="diagnoses", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var ADHDSubtype
     *
     * @ORM\ManyToOne(targetEntity="ADHDSubtype", cascade={"persist"})
     * @ORM\JoinColumn(name="adhd_subtype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $subtype;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ADHDComorbidity", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="children_adhd_diagnosis_comorbidities",
     *     joinColumns = {@ORM\JoinColumn(name="child_adhd_diagnosis_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns = {@ORM\JoinColumn(name="adhd_comorbidity_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    private $comorbidities;

    public function __construct()
    {
        $this->comorbidities = new ArrayCollection();
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
     * Set diagnosisDate
     *
     * @param \DateTime $diagnosisDate
     * @return ChildADHDDiagnosis
     */
    public function setDiagnosisDate($diagnosisDate)
    {
        $this->diagnosisDate = $diagnosisDate;

        return $this;
    }

    /**
     * Get diagnosisDate
     *
     * @return \DateTime 
     */
    public function getDiagnosisDate()
    {
        return $this->diagnosisDate;
    }

    /**
     * Set onsetAge
     *
     * @param integer $onsetAge
     * @return ChildADHDDiagnosis
     */
    public function setOnsetAge($onsetAge)
    {
        $this->onsetAge = $onsetAge;

        return $this;
    }

    /**
     * Get onsetAge
     *
     * @return integer 
     */
    public function getOnsetAge()
    {
        return $this->onsetAge;
    }

    /**
     * Set isSecondaryDisorder
     *
     * @param boolean $isSecondaryDisorder
     * @return ChildADHDDiagnosis
     */
    public function setIsSecondaryDisorder($isSecondaryDisorder)
    {
        $this->isSecondaryDisorder = $isSecondaryDisorder;

        return $this;
    }

    /**
     * Get isSecondaryDisorder
     *
     * @return boolean 
     */
    public function getIsSecondaryDisorder()
    {
        return $this->isSecondaryDisorder;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildADHDDiagnosis
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
     * @return ChildADHDDiagnosis
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
     * @return ChildADHDDiagnosis
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
     * Set subtype
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDSubtype $subtype
     * @return ChildADHDDiagnosis
     */
    public function setSubtype(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDSubtype $subtype = null)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * Get subtype
     *
     * @return \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDSubtype 
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Add comorbidities
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDComorbidity $comorbidities
     * @return ChildADHDDiagnosis
     */
    public function addComorbidity(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDComorbidity $comorbidities)
    {
        $this->comorbidities[] = $comorbidities;

        return $this;
    }

    /**
     * Remove comorbidities
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDComorbidity $comorbidities
     */
    public function removeComorbidity(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDComorbidity $comorbidities)
    {
        $this->comorbidities->removeElement($comorbidities);
    }

    /**
     * Get comorbidities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComorbidities()
    {
        return $this->comorbidities;
    }

    /**
     * Convert isSecondaryDisorder to string
     *
     * @return string
     */
    public function convertIsSecondaryDisorderToString()
    {
        return ($this->isSecondaryDisorder==1) ? 'Yes' : 'No';
    }
}
