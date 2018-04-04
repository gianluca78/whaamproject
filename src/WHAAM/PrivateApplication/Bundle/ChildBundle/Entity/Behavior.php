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
 * @ORM\Entity(repositoryClass="BehaviorRepository")
 * @ORM\Table(name="behaviors")
 * @Gedmo\TranslationEntity(class="WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Translation\BehaviorTranslation")
 */
class Behavior
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
    private $behavior;

    /**
     * @ORM\ManyToOne(targetEntity="BehaviorCategory", inversedBy="behaviors")
     * @ORM\JoinColumn(name="behavior_category_id", referencedColumnName="id")
     */
    private $behaviorCategory;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"behavior"})
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
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

    public function __toString()
    {
        return $this->behavior;
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
     * Set behavior
     *
     * @param string $behavior
     * @return Behavior
     */
    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * Get behavior
     *
     * @return string 
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Behavior
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
     * @return Behavior
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
     * @return Behavior
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
     * @return Behavior
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

}
