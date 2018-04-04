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
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="behaviors_categories")
 * @Gedmo\TranslationEntity(class="WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Translation\BehaviorCategoryTranslation")
 */
class BehaviorCategory
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
     * @Gedmo\Translatable
     */
    private $behaviorCategory;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"behaviorCategory"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Behavior", mappedBy="behaviorCategory")
     * @ORM\OrderBy({"behavior" = "ASC"})
     */
    private $behaviors;

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

    public function __toString()
    {
        return $this->behaviorCategory;
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
     * Constructor
     */
    public function __construct()
    {
        $this->behaviors = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set behaviorCategory
     *
     * @param string $behaviorCategory
     * @return BehaviorCategory
     */
    public function setBehaviorCategory($behaviorCategory)
    {
        $this->behaviorCategory = $behaviorCategory;

        return $this;
    }

    /**
     * Get behaviorCategory
     *
     * @return string 
     */
    public function getBehaviorCategory()
    {
        return $this->behaviorCategory;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return BehaviorCategory
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
     * @return BehaviorCategory
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
     * @return BehaviorCategory
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
     * Add behaviors
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behaviors
     * @return BehaviorCategory
     */
    public function addBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behaviors)
    {
        $this->behaviors[] = $behaviors;

        return $this;
    }

    /**
     * Remove behaviors
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behaviors
     */
    public function removeBehavior(\WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior $behaviors)
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
}
