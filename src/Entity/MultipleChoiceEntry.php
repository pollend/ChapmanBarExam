<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MultipleChoiceEntryRepository")
 * @ORM\Table(name="multiple_choice")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     collectionOperations={
 *          "get" = {
 *              "normalization_context"={"groups"={"tag:get"}}
 *          }
 *     },
 *     itemOperations={
 *          "get"= {
 *              "normalization_context"={"groups"={"tag:get"}}
 *          }
 *     }
 * )
 */
class MultipleChoiceEntry
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"quiz_question:get","quiz_response:get","tag:get"})
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="`order`",type="smallint",nullable=false)
     * @Groups({"quiz_question:get","quiz_response:get","tag:get"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     * @Groups({"quiz_question:get","quiz_response:get","tag:get"})
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

    public function getId()
    {
        return $this->id;
    }
}
