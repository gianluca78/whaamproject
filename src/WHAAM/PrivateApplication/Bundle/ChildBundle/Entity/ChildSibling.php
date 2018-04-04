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

/**
 * @ORM\Entity(repositoryClass="ChildSiblingRepository")
 * @ORM\Table(name="children_siblings")
 */
class ChildSibling
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
     * @ORM\Column(type="encrypted_string", length=255, unique=true)
     */
    private $nickname;

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
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="siblings", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var Sex
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex", cascade={"persist"})
     */
    private $sex;

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
     * @param encrypted_string $name
     * @return ChildSibling
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ChildSibling
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
     * @return ChildSibling
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
     * @return ChildSibling
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
     * Set sex
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex $sex
     * @return ChildSibling
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
     * Set nickname
     *
     * @param string $nickname
     * @return ChildSibling
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
}
