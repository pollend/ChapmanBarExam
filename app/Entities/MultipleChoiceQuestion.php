<?php


namespace App\Entities;


use App\Entities\Traits\TimestampTrait;
use App\QuizQuestion;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity()
 * @ORM\Table(name="multiple_choice_question")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MultipleChoiceQuestion extends QuizQuestion
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
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="multipleChoiceQuestions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     */
    protected $quiz;


    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="multiple_choice_entry_id", referencedColumnName="id")
     * })
     */
    protected $correctEntry;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setCorrectAnswer($entry){
        $this->correctEntry = $entry;
    }

    function getTypeAttribute()
    {
        // TODO: Implement getTypeAttribute() method.
    }

    function answers()
    {
        // TODO: Implement answers() method.
    }
}