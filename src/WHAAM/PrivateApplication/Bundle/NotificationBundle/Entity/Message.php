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
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\BaseMessage;

/**
 * @ORM\Entity(repositoryClass="MessageRepository")
 * @ORM\Table(name="messages")
 */
class Message extends BaseMessage
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
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $subject;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"subject"})
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="messages_users",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $recipientUsers;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="message", cascade={"persist"})
     **/
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity="MessageStatus", mappedBy="message", cascade={"persist"})
     **/
    private $statuses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->recipientUsers = new ArrayCollection();
        $this->statuses = new ArrayCollection();
    }

    public function hasNotReadElementsForUser($user)
    {
        foreach ($this->getStatuses() as $status) {
            if($status->getIsRead() == 0 && $status->getUser() == $user) {
                return true;
            }
        }

        foreach ($this->getAnswers() as $answer) {
            foreach ($answer->getStatuses() as $status) {
                if($status->getIsRead() == 0 && $status->getUser() == $user) {
                    return true;
                }
            }
        }

        return false;
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
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Message
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
     * Set child
     *
     * @param \WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child $child
     * @return Message
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
     * Add recipientUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $recipientUsers
     * @return Message
     */
    public function addRecipientUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $recipientUsers)
    {
        $this->recipientUsers[] = $recipientUsers;

        return $this;
    }

    /**
     * Remove recipientUsers
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $recipientUsers
     */
    public function removeRecipientUser(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $recipientUsers)
    {
        $this->recipientUsers->removeElement($recipientUsers);
    }

    /**
     * Get recipientUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipientUsers()
    {
        return $this->recipientUsers;
    }

    /**
     * Add answers
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answers
     * @return Message
     */
    public function addAnswer(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answers
     */
    public function removeAnswer(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add statuses
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\MessageStatus $statuses
     * @return Message
     */
    public function addStatus(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\MessageStatus $statuses)
    {
        $this->statuses[] = $statuses;

        return $this;
    }

    /**
     * Remove statuses
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\MessageStatus $statuses
     */
    public function removeStatus(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\MessageStatus $statuses)
    {
        $this->statuses->removeElement($statuses);
    }

    /**
     * Get statuses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Set sender
     *
     * @param \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }
}
