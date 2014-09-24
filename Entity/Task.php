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
}
