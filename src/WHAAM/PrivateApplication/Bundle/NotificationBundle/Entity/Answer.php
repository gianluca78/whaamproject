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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\BaseMessage;

/**
 * @ORM\Entity()
 * @ORM\Table(name="messages_answers")
 */
class Answer extends BaseMessage
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message", inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * @ORM\OneToMany(targetEntity="AnswerStatus", mappedBy="answer", cascade={"persist"})
     **/
    private $statuses;

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
     * Set message
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message $message
     * @return MessageStatus
     */
    public function setMessage(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
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
     * Add statuses
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\AnswerStatus $statuses
     * @return Message
     */
    public function addStatus(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\AnswerStatus $statuses)
    {
        $this->statuses[] = $statuses;

        return $this;
    }

    /**
     * Remove statuses
     *
     * @param \WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\AnswerStatus $statuses
     */
    public function removeStatus(\WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\AnswerStatus $statuses)
    {
        $this->statuses->removeElement($statuses);
    }
}
