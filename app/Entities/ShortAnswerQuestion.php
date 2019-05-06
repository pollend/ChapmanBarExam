<?php

namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use App\QuizQuestion;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity()
 * @ORM\Table(name="short_answer_question")
 * @ORM\HasLifecycleCallbacks
 *
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

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="shortAnswerQuestions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     */
    protected $quiz;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ShortAnswerResponse",mappedBy="question")
     */
    protected $responses;

    public function answers(){
       return $this->responses;
    }

    function getTypeAttribute()
    {
        return 'shortAnswer';
    }
}