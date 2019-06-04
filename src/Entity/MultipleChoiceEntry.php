<?php


namespace App\Entity;

use App\Entity\Traits\TimestampTrait;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity(repositoryClass="App\Repository\MultipleChoiceEntryRepository")
 * @ORM\Table(name="multiple_choice")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MultipleChoiceEntry
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"detail"})
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="`order`",type="smallint",nullable=false)
     * @JMS\Groups({"detail"})
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     * @JMS\Groups({"detail"})
     */
    protected $content;

    /**
     * @var MultipleChoiceQuestion
     * @ORM\ManyToOne(targetEntity="MultipleChoiceQuestion",inversedBy="entries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="multiple_choice_question_id", referencedColumnName="id")
     * })
     */
    protected $question;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): MultipleChoiceEntry
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param Quiz $question
     */
    public function setQuestion(MultipleChoiceQuestion $question): MultipleChoiceEntry
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): MultipleChoiceEntry
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    public function getId(){
        return $this->id;
    }

}