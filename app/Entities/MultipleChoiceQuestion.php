<?php


namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use App\Entities\QuizQuestion;
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
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="multiple_choice_entry_id", referencedColumnName="id")
     * })
     */
    protected $correctEntry;

    protected $multipleChoiceEntries;


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

    public function setCorrectAnswer($entry){
        $this->correctEntry = $entry;
    }

}