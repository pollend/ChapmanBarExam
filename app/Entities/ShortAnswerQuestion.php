<?php

namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity(repositoryClass="App\Repositories\ShortAnswerQuestionRepository")
 * @ORM\Table(name="short_answer_question")
 * @ORM\HasLifecycleCallbacks
 */
class ShortAnswerQuestion extends QuizQuestion
{
    use TimestampTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     */
    protected $content;

    public function answers(){
       return $this->responses;
    }

    public function answersBySession($session){
        return $this->responses->matching(Criteria::create()->where(Criteria::expr()->eq('session',$session)));
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): ShortAnswerQuestion
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

}