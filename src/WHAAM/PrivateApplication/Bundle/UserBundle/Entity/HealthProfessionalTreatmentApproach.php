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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="health_professionals_treatment_approaches")
 * @Gedmo\TranslationEntity(class="WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Translation\HealthProfessionalTreatmentApproachTranslation")
 */
class HealthProfessionalTreatmentApproach
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
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=100)
     *
     */
    private $approach;

    /**
     * @var string $slug
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"approach"})
     */
    private $slug;

    /**
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

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
     * @ORM\ManyToMany(targetEntity="User", mappedBy="healthProfessionalTreatmentApproaches")
     **/
    private $users;

    public function __toString()
    {
        return $this->approach;
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Set the locale for the translatable behavior
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale) {
        $this->locale = $locale;
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
     * Set approach
     *
     * @param string $approach
     * @return HealthProfessionalTreatmentApproach
     */
    public function setApproach($approach)
    {
        $this->approach = $approach;

        return $this;
    }

    /**
     * Get approach
     *
     * @return string 
     */
    public function getApproach()
    {
        return $this->approach;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return HealthProfessionalTreatmentApproach
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
     * @return HealthProfessionalTreatmentApproach
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
     * Add users
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $users
     * @return HealthProfessionalTreatmentApproach
     */
    public function addUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $users
     */
    public function removeUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return HealthProfessionalTreatmentApproach
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
}
