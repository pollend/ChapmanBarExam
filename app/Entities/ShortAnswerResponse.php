<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
/**
 * Class Quiz
 * @package App
 * @ORM\Entity()
 * @ORM\Table(name="short_answer_response")
 * @ORM\HasLifecycleCallbacks
 *
 */
class ShortAnswerResponse extends QuizResponse
{

    /**
     * @var string
     * @ORM\Column(name="content", type="text",nullable=true)
     */
    protected $content;

    /**
     * @var ShortAnswerQuestion
     * @ORM\ManyToOne(targetEntity="ShortAnswerQuestion",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="short_answer_question_id",referencedColumnName="id")
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
     * @param ShortAnswerQuestion $question
     */
    public function setQuestion(ShortAnswerQuestion $question): ShortAnswerResponse
    {
        $this->question = $question;
        return $this;
    }


    /**
     * @param string $content
     */
    public function setContent(string $content): ShortAnswerResponse
    {
        $this->content = $content;
        return $this;
    }



}