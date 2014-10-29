<?php

namespace Cekurte\InsightlyTaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="insightly_task_comment")
 * @ORM\Entity
 */
class Comment
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
     * @var \Cekurte\InsightlyTaskBundle\Entity\Task
     *
     * @ORM\ManyToOne(targetEntity="\Cekurte\InsightlyTaskBundle\Entity\Task")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     * })
     */
    private $task;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="insightly_task_comment_id", type="integer")
     */
    private $insightlyTaskCommentId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
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
     * Set insightlyTaskCommentId
     *
     * @param integer $insightlyTaskCommentId
     *
     * @return Comment
     */
    public function setInsightlyTaskCommentId($insightlyTaskCommentId)
    {
        $this->insightlyTaskCommentId = $insightlyTaskCommentId;

        return $this;
    }

    /**
     * Get insightlyTaskCommentId
     *
     * @return integer
     */
    public function getInsightlyTaskCommentId()
    {
        return $this->insightlyTaskCommentId;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comment
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
     * Set task
     *
     * @param \Cekurte\InsightlyTaskBundle\Entity\Task $task
     *
     * @return Comment
     */
    public function setTask(\Cekurte\InsightlyTaskBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Cekurte\InsightlyTaskBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }
}
