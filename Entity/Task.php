<?php

namespace Cekurte\InsightlyTaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="insightly_task")
 * @ORM\Entity(repositoryClass="Cekurte\InsightlyTaskBundle\Entity\Repository\TaskRepository")
 */
class Task
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="insightly_task_id", type="integer")
     */
    private $insightlyTaskId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insightly_task_date_created", type="datetime", nullable=true)
     */
    private $insightlyTaskDateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insightly_task_date_updated", type="datetime", nullable=true)
     */
    private $insightlyTaskDateUpdated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="insightly_task_completed", type="boolean", nullable=true)
     */
    private $insightlyTaskCompleted;

    /**
     * @var string
     *
     * @ORM\Column(name="insightly_task_status", type="string", length=20, nullable=true)
     */
    private $insightlyTaskStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="insightly_task_percent_complete", type="integer", nullable=true)
     */
    private $insightlyTaskPercentComplete;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OrderBy({"createdAt" = "ASC"})
     * @ORM\OneToMany(targetEntity="\Cekurte\InsightlyTaskBundle\Entity\Comment", mappedBy="task")
     */
    private $comments;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @return Task
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
     * Set content
     *
     * @param string $content
     *
     * @return Task
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set insightlyTaskId
     *
     * @param integer $insightlyTaskId
     *
     * @return Task
     */
    public function setInsightlyTaskId($insightlyTaskId)
    {
        $this->insightlyTaskId = $insightlyTaskId;

        return $this;
    }

    /**
     * Get insightlyTaskId
     *
     * @return integer
     */
    public function getInsightlyTaskId()
    {
        return $this->insightlyTaskId;
    }

    /**
     * Set insightlyTaskDateCreated
     *
     * @param \DateTime $insightlyTaskDateCreated
     *
     * @return Task
     */
    public function setInsightlyTaskDateCreated($insightlyTaskDateCreated)
    {
        $this->insightlyTaskDateCreated = $insightlyTaskDateCreated;

        return $this;
    }

    /**
     * Get insightlyTaskDateCreated
     *
     * @return \DateTime
     */
    public function getInsightlyTaskDateCreated()
    {
        return $this->insightlyTaskDateCreated;
    }

    /**
     * Set insightlyTaskDateUpdated
     *
     * @param \DateTime $insightlyTaskDateUpdated
     *
     * @return Task
     */
    public function setInsightlyTaskDateUpdated($insightlyTaskDateUpdated)
    {
        $this->insightlyTaskDateUpdated = $insightlyTaskDateUpdated;

        return $this;
    }

    /**
     * Get insightlyTaskDateUpdated
     *
     * @return \DateTime
     */
    public function getInsightlyTaskDateUpdated()
    {
        return $this->insightlyTaskDateUpdated;
    }

    /**
     * Set insightlyTaskCompleted
     *
     * @param boolean $insightlyTaskCompleted
     *
     * @return Task
     */
    public function setInsightlyTaskCompleted($insightlyTaskCompleted)
    {
        $this->insightlyTaskCompleted = $insightlyTaskCompleted;

        return $this;
    }

    /**
     * Get insightlyTaskCompleted
     *
     * @return boolean
     */
    public function getInsightlyTaskCompleted()
    {
        return $this->insightlyTaskCompleted;
    }

    /**
     * Set insightlyTaskStatus
     *
     * @param string $insightlyTaskStatus
     *
     * @return Task
     */
    public function setInsightlyTaskStatus($insightlyTaskStatus)
    {
        $this->insightlyTaskStatus = $insightlyTaskStatus;

        return $this;
    }

    /**
     * Get insightlyTaskStatus
     *
     * @return string
     */
    public function getInsightlyTaskStatus()
    {
        return $this->insightlyTaskStatus;
    }

    /**
     * Set insightlyTaskPercentComplete
     *
     * @param integer $insightlyTaskPercentComplete
     *
     * @return Task
     */
    public function setInsightlyTaskPercentComplete($insightlyTaskPercentComplete)
    {
        $this->insightlyTaskPercentComplete = $insightlyTaskPercentComplete;

        return $this;
    }

    /**
     * Get insightlyTaskPercentComplete
     *
     * @return integer
     */
    public function getInsightlyTaskPercentComplete()
    {
        return $this->insightlyTaskPercentComplete;
    }

    /**
     * Add comment
     *
     * @param \Cekurte\InsightlyTaskBundle\Entity\Comment $comment
     *
     * @return Task
     */
    public function addComment(\Cekurte\InsightlyTaskBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Cekurte\InsightlyTaskBundle\Entity\Comment $comment
     */
    public function removeComment(\Cekurte\InsightlyTaskBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
