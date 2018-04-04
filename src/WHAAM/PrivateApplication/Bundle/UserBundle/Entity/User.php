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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child;
use Doctrine\Common\Collections\Criteria;

/**
 * WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements AdvancedUserInterface, \Serializable {
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="date")
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="encrypted_string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"salt"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=40)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="email_token", type="string", length=40)
     */
    private $emailToken;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_app_session_id", type="string", length=40, nullable=true)
     */
    private $mobileAppSessionId;

    /**
     * Whether the account is active or not
     *
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = false;

    /**
     * Whether the account is locked or not
     *
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean")
     */
    private $isLocked = false;

    /**
     * Whether the user is an health professional or not
     *
     * @var boolean
     *
     * @ORM\Column(name="is_health_professional", type="boolean")
     */
    private $isHealthProfessional;

    /**
     * @var string
     *
     * @ORM\Column(name="other_nation", type="string", length=50, nullable=true)
     */
    private $otherNation;

    /**
     * The locale selected by the user
     *
     * @var string
     *
     * @ORM\Column(name="selected_locale", type="string", length=5)
     */
    private $selectedLocale;

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
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser", mappedBy="user", cascade={"persist"})
     */
    private $childUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child", mappedBy="childCreatorUser")
     */
    private $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HealthProfessionalClientAgeRange", inversedBy="users")
     * @ORM\JoinTable(name="users_health_professionals_clients_age_ranges")
     **/
    private $healthProfessionalClientsAgeRange;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HealthProfessionalSpecialty", inversedBy="users")
     * @ORM\JoinTable(name="users_health_professionals_specialties")
     **/
    private $healthProfessionalSpecialties;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HealthProfessionalTreatmentApproach", inversedBy="users")
     * @ORM\JoinTable(name="users_health_professionals_treatments_approaches")
     **/
    private $healthProfessionalTreatmentApproaches;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HealthProfessionalTreatmentModality", inversedBy="users")
     * @ORM\JoinTable(name="users_health_professionals_treatments_modalities")
     **/
    private $healthProfessionalTreatmentModalities;

    /**
     * @var Nation
     *
     * @ORM\ManyToOne(targetEntity="Nation", cascade={"persist"})
     */
    private $nation;

    /**
     * @var Sex
     *
     * @ORM\ManyToOne(targetEntity="Sex", cascade={"persist"})
     */
    private $sex;

    /**
     * @var ApplicationRole
     *
     * @ORM\ManyToOne(targetEntity="ApplicationRole", cascade={"persist"})
     */
    private $role;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->childUsers = new ArrayCollection();
        $this->emailToken = $this->generateToken();
        $this->healthProfessionalClientsAgeRange = new ArrayCollection();
        $this->healthProfessionalSpecialties = new ArrayCollection();
        $this->healthProfessionalTreatmentApproaches = new ArrayCollection();
        $this->healthProfessionalTreatmentModalities = new ArrayCollection();
        $this->salt = $this->generateToken();

    }

    public function __toString()
    {
        return $this->getSurnameNameOrUsername();
    }

    /**
     * Set the locale for the translatable behavior
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale) {
        $this->locale = $locale;
    }

    public function eraseCredentials()
    {
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array((string)$this->getRole()->getRole());
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return ($this->isLocked==0)? true : false;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return ($this->isActive==0) ? false : true;
    }

    /**
     * Serialize the User object
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array($this->id, $this->username, $this->password, $this->salt));
    }

    /**
     * Unserialize the User object
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->username, $this->password, $this->salt) = unserialize($serialized);
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
     * Set surname
     *
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime 
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return User
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
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return User
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
     * Set isHealthProfessional
     *
     * @param boolean $isHealthProfessional
     * @return User
     */
    public function setIsHealthProfessional($isHealthProfessional)
    {
        $this->isHealthProfessional = $isHealthProfessional;

        return $this;
    }

    /**
     * Get isHealthProfessional
     *
     * @return boolean
     */
    public function getIsHealthProfessional()
    {
        return $this->isHealthProfessional;
    }

    /**
     * Set otherNation
     *
     * @param string $otherNation
     * @return User
     */
    public function setOtherNation($otherNation)
    {
        $this->otherNation = $otherNation;

        return $this;
    }

    /**
     * Get otherNation
     *
     * @return string
     */
    public function getOtherNation()
    {
        return $this->otherNation;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
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
     * @return User
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
     * Set role
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\ApplicationRole $role
     * @return User
     */
    public function setRole(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\ApplicationRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\ApplicationRole 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add healthProfessionalClientsAgeRange
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalClientAgeRange $healthProfessionalClientsAgeRange
     * @return User
     */
    public function addHealthProfessionalClientsAgeRange(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalClientAgeRange $healthProfessionalClientsAgeRange)
    {
        $this->healthProfessionalClientsAgeRange[] = $healthProfessionalClientsAgeRange;

        return $this;
    }

    /**
     * Remove healthProfessionalClientsAgeRange
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalClientAgeRange $healthProfessionalClientsAgeRange
     */
    public function removeHealthProfessionalClientsAgeRange(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalClientAgeRange $healthProfessionalClientsAgeRange)
    {
        $this->healthProfessionalClientsAgeRange->removeElement($healthProfessionalClientsAgeRange);
    }

    /**
     * Get healthProfessionalClientsAgeRange
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHealthProfessionalClientsAgeRange()
    {
        return $this->healthProfessionalClientsAgeRange;
    }

    /**
     * Add healthProfessionalSpecialties
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalSpecialty $healthProfessionalSpecialties
     * @return User
     */
    public function addHealthProfessionalSpecialty(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalSpecialty $healthProfessionalSpecialties)
    {
        $this->healthProfessionalSpecialties[] = $healthProfessionalSpecialties;

        return $this;
    }

    /**
     * Remove healthProfessionalSpecialties
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalSpecialty $healthProfessionalSpecialties
     */
    public function removeHealthProfessionalSpecialty(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalSpecialty $healthProfessionalSpecialties)
    {
        $this->healthProfessionalSpecialties->removeElement($healthProfessionalSpecialties);
    }

    /**
     * Get healthProfessionalSpecialties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHealthProfessionalSpecialties()
    {
        return $this->healthProfessionalSpecialties;
    }

    /**
     * Add healthProfessionalTreatmentApproaches
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentApproach $healthProfessionalTreatmentApproaches
     * @return User
     */
    public function addHealthProfessionalTreatmentApproach(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentApproach $healthProfessionalTreatmentApproaches)
    {
        $this->healthProfessionalTreatmentApproaches[] = $healthProfessionalTreatmentApproaches;

        return $this;
    }

    /**
     * Remove healthProfessionalTreatmentApproaches
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentApproach $healthProfessionalTreatmentApproaches
     */
    public function removeHealthProfessionalTreatmentApproach(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentApproach $healthProfessionalTreatmentApproaches)
    {
        $this->healthProfessionalTreatmentApproaches->removeElement($healthProfessionalTreatmentApproaches);
    }

    /**
     * Get healthProfessionalTreatmentApproaches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHealthProfessionalTreatmentApproaches()
    {
        return $this->healthProfessionalTreatmentApproaches;
    }

    /**
     * Add healthProfessionalTreatmentModalities
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentModality $healthProfessionalTreatmentModalities
     * @return User
     */
    public function addHealthProfessionalTreatmentModality(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentModality $healthProfessionalTreatmentModalities)
    {
        $this->healthProfessionalTreatmentModalities[] = $healthProfessionalTreatmentModalities;

        return $this;
    }

    /**
     * Remove healthProfessionalTreatmentModalities
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentModality $healthProfessionalTreatmentModalities
     */
    public function removeHealthProfessionalTreatmentModality(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentModality $healthProfessionalTreatmentModalities)
    {
        $this->healthProfessionalTreatmentModalities->removeElement($healthProfessionalTreatmentModalities);
    }

    /**
     * Get healthProfessionalTreatmentModalities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHealthProfessionalTreatmentModalities()
    {
        return $this->healthProfessionalTreatmentModalities;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nation
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Nation $nation
     * @return User
     */
    public function setNation(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Nation $nation = null)
    {
        $this->nation = $nation;

        return $this;
    }

    /**
     * Get nation
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Nation 
     */
    public function getNation()
    {
        return $this->nation;
    }

    /**
     * Add children
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $children
     * @return User
     */
    public function addChild(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $children
     */
    public function removeChild(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add childUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser $childUsers
     * @return User
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
     * Set sex
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex $sex
     * @return User
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
     * Set emailToken
     *
     * @param string $emailToken
     * @return User
     */
    public function setEmailToken($emailToken)
    {
        $this->emailToken = $emailToken;

        return $this;
    }

    /**
     * Get emailToken
     *
     * @return string
     */
    public function getEmailToken()
    {
        return $this->emailToken;
    }

    /**
     * Set mobileAppSessionId
     *
     * @param string $mobileAppSessionId
     * @return User
     */
    public function setMobileAppSessionId($mobileAppSessionId)
    {
        $this->mobileAppSessionId = $mobileAppSessionId;

        return $this;
    }

    /**
     * Get mobileAppSessionId
     *
     * @return string 
     */
    public function getMobileAppSessionId()
    {
        return $this->mobileAppSessionId;
    }

    /**
     * Set selectedLocale
     *
     * @param string $selectedLocale
     * @return User
     */
    public function setSelectedLocale($selectedLocale)
    {
        $this->selectedLocale = $selectedLocale;

        return $this;
    }

    /**
     * Get selectedLocale
     *
     * @return string
     */
    public function getSelectedLocale()
    {
        return $this->selectedLocale;
    }

    public function generateToken()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * Return a string with surname and name if filled in or username if they are blank
     */
    public function getSurnameNameOrUsername()
    {
        if($this->getSurname() && $this->getName()) {
            return $this->getSurname() . ' ' . $this->getName();
        }

        if ($this->getSurname()) {
            return $this->getSurname();
        }

        if ($this->getName()) {
            return $this->getName();
        }

        return $this->getUsername();
    }

    public function getChildUserByChild(Child $child) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('child', $child));

        return ($this->getChildUsers()->matching($criteria)) ? $this->getChildUsers()->matching($criteria)->first() : null;
    }

}
