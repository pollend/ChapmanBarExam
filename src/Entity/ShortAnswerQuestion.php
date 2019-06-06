<?php

namespace App\Entity;

use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity(repositoryClass="App\Repository\ShortAnswerQuestionRepository")
 * @ORM\Table(name="short_answer_question")
 * @ORM\HasLifecycleCallbacks
 */
class ShortAnswerQuestion extends QuizQuestion
{
    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)\
     * @JMS\Groups({"detail"})
     */
    protected $content;

    public function answers(){
       return $this->responses;
    }

    /**
     * @param $session
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
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