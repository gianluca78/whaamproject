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
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table(name="messages_answers_statuses")
 */
class AnswerStatus
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
     * @var boolean
     *
     * @ORM\Column(name="is_read", type="boolean")
     *
     */
    protected $isRead = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_displayed", type="boolean")
     *
     */
    protected $isDisplayed = 0;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer", inversedBy="statuses", cascade={"persist"})
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $answer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    

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
     * Set isRead
     *
     * @param boolean $isRead
     * @return AnswerStatus
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set isDisplayed
     *
     * @param boolean $isDisplayed
     * @return AnswerStatus
     */
    public function setIsDisplayed($isDisplayed)
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
    }

    /**
     * Get isDisplayed
     *
     * @return boolean 
     */
    public function getIsDisplayed()
    {
        return $this->isDisplayed;
    }

    /**
     * Set answer
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answer
     * @return AnswerStatus
     */
    public function setAnswer(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set user
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user
     * @return AnswerStatus
     */
    public function setUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
