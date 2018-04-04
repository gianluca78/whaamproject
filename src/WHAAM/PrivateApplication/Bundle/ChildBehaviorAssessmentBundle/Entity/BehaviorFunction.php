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
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="behavior_functions")
 * @Gedmo\TranslationEntity(class="WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\Translation\BehaviorFunctionTranslation")
 */
class BehaviorFunction
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
     * @ORM\Column(name="behavior_function", type="string", length=255, unique=true)
     * @Gedmo\Translatable
     */
    private $behaviorFunction;


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
        return $this->behaviorFunction;
    }

    /**
     * Set the locale for the translatable function
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
     * Set behavior function
     *
     * @param string $behaviorFunction
     * @return BehaviorFunction
     */
    public function setBehaviorFunction($behaviorFunction)
    {
        $this->behaviorFunction = $behaviorFunction;

        return $this;
    }

    /**
     * Get subtype
     *
     * @return string
     */
    public function getBehaviorFunction()
    {
        return $this->behaviorFunction;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BehaviorFunction
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
     * @return BehaviorFunction
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
}
