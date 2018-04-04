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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\Common\Collections\Criteria;
use Gedmo\Mapping\Annotation as Gedmo;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="ChildRepository")
 * @ORM\Table(name="children")
 */
class Child
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", name="year_of_birth", length=255)
     */
    private $yearOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"nickname"})
     */
    private $slug;

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
     * @ORM\OneToMany(targetEntity="ChildUser", mappedBy="child", cascade={"persist"})
     */
    private $childUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildBehavior", mappedBy="child", cascade={"persist"})
     */
    private $behaviors;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildADHDDiagnosis", mappedBy="child", cascade={"persist"})
     */
    private $diagnoses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildDisciplineReferral", mappedBy="child", cascade={"persist"})
     */
    private $disciplineReferrals;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildGeneralEvent", mappedBy="child", cascade={"persist"})
     */
    private $generalEvents;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildMedication", mappedBy="child", cascade={"persist"})
     */
    private $medications;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildSchoolInformation", mappedBy="child", cascade={"persist"})
     */
    private $schoolsInformation;

    /**
     * @var Sex
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex", cascade={"persist"})
     */
    private $sex;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ChildSibling", mappedBy="child", cascade={"persist"})
     */
    private $siblings;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="child_creator_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $childCreatorUser;

    public function __construct()
    {
        $this->behaviors = new ArrayCollection();
        $this->childUsers = new ArrayCollection();
        $this->diagnoses = new ArrayCollection();
        $this->disciplineReferrals = new ArrayCollection();
        $this->generalEvents = new ArrayCollection();
        $this->medications = new ArrayCollection();
        $this->schoolsInformation = new ArrayCollection();
        $this->siblings = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNickname();
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
     * Set nickname
     *
     * @param string $nickname
     * @return Child
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set surname
     *
     * @param encrypted_string $surname
     * @return Child
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return encrypted_string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set name
     *
     * @param encrypted_string $name
     * @return Child
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return encrypted_string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set yearOfBirth
     *
     * @param $yearOfBirth
     * @return Child
     */
    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }

    /**
     * Get yearOfBirth
     *
     * @return string
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Child
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Child
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
     * @return Child
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
     * Add siblings
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSibling $siblings
     * @return Child
     */
    public function addSibling(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSibling $siblings)
    {
        $this->siblings[] = $siblings;

        return $this;
    }

    /**
     * Remove siblings
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSibling $siblings
     */
    public function removeSibling(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSibling $siblings)
    {
        $this->siblings->removeElement($siblings);
    }

    /**
     * Get siblings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSiblings()
    {
        return $this->siblings;
    }

    /**
     * Add generalEvents
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent $generalEvents
     * @return Child
     */
    public function addGeneralEvent(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent $generalEvents)
    {
        $this->generalEvents[] = $generalEvents;

        return $this;
    }

    /**
     * Remove generalEvents
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent $generalEvents
     */
    public function removeGeneralEvent(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent $generalEvents)
    {
        $this->generalEvents->removeElement($generalEvents);
    }

    /**
     * Get generalEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGeneralEvents()
    {
        return $this->generalEvents;
    }

    /**
     * Add medications
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication $medications
     * @return Child
     */
    public function addMedication(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication $medications)
    {
        $this->medications[] = $medications;

        return $this;
    }

    /**
     * Remove medications
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication $medications
     */
    public function removeMedication(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication $medications)
    {
        $this->medications->removeElement($medications);
    }

    /**
     * Get medications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedications()
    {
        return $this->medications;
    }

    /**
     * Add schoolsInformation
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation $schoolsInformation
     * @return Child
     */
    public function addSchoolsInformation(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation $schoolsInformation)
    {
        $this->schoolsInformation[] = $schoolsInformation;

        return $this;
    }

    /**
     * Remove schoolsInformation
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation $schoolsInformation
     */
    public function removeSchoolsInformation(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation $schoolsInformation)
    {
        $this->schoolsInformation->removeElement($schoolsInformation);
    }

    /**
     * Get schoolsInformation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchoolsInformation()
    {
        return $this->schoolsInformation;
    }

    /**
     * Add childUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser $childUsers
     * @return Child
     */
    public function addChildUser(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser $childUsers)
    {
        $this->childUsers[] = $childUsers;

        return $this;
    }

    /**
     * Remove childUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser $childUsers
     */
    public function removeChildUser(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser $childUsers)
    {
        $this->childUsers->removeElement($childUsers);
    }

    /**
     * Get childUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildUsers()
    {
        return $this->childUsers;
    }

    /**
     * Add diagnoses
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis $diagnoses
     * @return Child
     */
    public function addDiagnosis(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis $diagnoses)
    {
        $this->diagnoses[] = $diagnoses;

        return $this;
    }

    /**
     * Remove diagnoses
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis $diagnoses
     */
    public function removeDiagnosis(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis $diagnoses)
    {
        $this->diagnoses->removeElement($diagnoses);
    }

    /**
     * Get diagnoses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiagnoses()
    {
        return $this->diagnoses;
    }

    /**
     * Add disciplineReferrals
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral $disciplineReferrals
     * @return Child
     */
    public function addDisciplineReferral(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral $disciplineReferrals)
    {
        $this->disciplineReferrals[] = $disciplineReferrals;

        return $this;
    }

    /**
     * Remove disciplineReferrals
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral $disciplineReferrals
     */
    public function removeDisciplineReferral(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral $disciplineReferrals)
    {
        $this->disciplineReferrals->removeElement($disciplineReferrals);
    }

    /**
     * Get disciplineReferrals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDisciplineReferrals()
    {
        return $this->disciplineReferrals;
    }

    /**
     * Add behaviors
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $behaviors
     * @return Child
     */
    public function addBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $behaviors)
    {
        $this->behaviors[] = $behaviors;

        return $this;
    }

    /**
     * Remove behaviors
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $behaviors
     */
    public function removeBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior $behaviors)
    {
        $this->behaviors->removeElement($behaviors);
    }

    /**
     * Get behaviors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBehaviors()
    {
        return $this->behaviors;
    }

    /**
     * Set sex
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex $sex
     * @return Child
     */
    public function setSex(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex $sex = null)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set childCreatorUser
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $childCreatorUser
     * @return Child
     */
    public function setChildCreatorUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $childCreatorUser = null)
    {
        $this->childCreatorUser = $childCreatorUser;

        return $this;
    }

    /**
     * Get childCreatorUser
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getChildCreatorUser()
    {
        return $this->childCreatorUser;
    }

    /**
     * Return a specific user in the child user network
     *
     * @param User $user
     * @return mixed
     */
    public function getChildUser(User $user) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('child', $this))
            ->andWhere(Criteria::expr()->eq('user', $user));

        return ($this->getChildUsers()->matching($criteria)) ? $this->getChildUsers()->matching($criteria)->first() : null;
    }

    /**
     * Return the most recent diagnosis ordered by diagnosis date
     *
     * @return ChildADHDDiagnosis
     */
    public function getLastDiagnosis()
    {
        return $this->getDiagnosesOrderedByDateDesc()->first();
    }

    /**
     * Order the diagnoses by the most recent diagnosis dates
     *
     * @return ArrayCollection
     */
    public function getDiagnosesOrderedByDateDesc()
    {
        $criteria = Criteria::create()->orderBy(array('diagnosisDate' => 'DESC'));

        return $this->getDiagnoses()->matching($criteria);
    }

    /**
     * Return the most recent medication ordered by medication date
     *
     * @return ChildMedication
     */
    public function getLastMedication()
    {
        return $this->getMedicationsOrderedByDateDesc()->first();
    }

    /**
     * Order the medications by the most recent medication dates
     *
     * @return ArrayCollection
     */
    public function getMedicationsOrderedByDateDesc()
    {
        $criteria = Criteria::create()->orderBy(array('startDate' => 'DESC'));

        return $this->getMedications()->matching($criteria);
    }

    /**
     * Return the last discipline referral ordered by date
     *
     * @return ChildDisciplineReferral
     */
    public function getLastDisciplineReferral()
    {
        return $this->getDisciplineReferralOrderedByDateDesc()->first();
    }

    /**
     * Order the discipline referral by the most recent discipline referral dates
     *
     * @return ArrayCollection
     */
    public function getDisciplineReferralOrderedByDateDesc()
    {
        $criteria = Criteria::create()->orderBy(array('date' => 'DESC'));

        return $this->getDisciplineReferrals()->matching($criteria);
    }

    /**
     * Return the last school information ordered by date
     *
     * @return ChildSchoolInformation
     */
    public function getLastSchoolInformation()
    {
        return $this->getSchoolsInformationOrderedByDateDesc()->first();
    }

    /**
     * Order the school information by the most recent school information dates
     *
     * @return ArrayCollection
     */
    public function getSchoolsInformationOrderedByDateDesc()
    {
        $criteria = Criteria::create()->orderBy(array('year' => 'DESC'));

        return $this->getSchoolsInformation()->matching($criteria);
    }

    /**
     * Return the last general event ordered by date
     *
     * @return ChildADHDDiagnosis
     */
    public function getLastGeneralEvent()
    {
        return $this->getGeneralEventsOrderedByDateDesc()->first();
    }

    /**
     * Order the school information by the most recent school information dates
     *
     * @return ArrayCollection
     */
    public function getGeneralEventsOrderedByDateDesc()
    {
        $criteria = Criteria::create()->orderBy(array('date' => 'DESC'));

        return $this->getGeneralEvents()->matching($criteria);
    }

    public function getSurnameOrName() {
        if($this->getSurname() && $this->getName()) {
            return $this->getSurname() . ' ' . $this->getName();
        }

        if ($this->getSurname()) {
            return $this->getSurname();
        }

        if ($this->getName()) {
            return $this->getName();
        }
    }

    /**
     * Return a string with surname and name if filled in or nickname if they are blank
     */
    public function getSurnameNameOrNickname()
    {
        $this->getSurnameOrName();

        return $this->getNickname();
    }

    public function countYearsOld()
    {
        return date('Y') - $this->yearOfBirth;
    }
}
