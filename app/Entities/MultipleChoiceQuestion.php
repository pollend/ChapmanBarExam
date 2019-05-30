<?php


namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use App\Entities\QuizQuestion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;
use Illuminate\Support\Collection;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity(repositoryClass="App\Repositories\MultipleChoiceQuestionRepository")
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
     *   @ORM\JoinColumn(name="correct_entry_id", referencedColumnName="id")
     * })
     */
    protected $correctEntry;


    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="MultipleChoiceResponse",mappedBy="question")
     */
    protected $responses;


    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="MultipleChoiceEntry",mappedBy="question")
     */
    protected $entries;

    /**
     * @return MultipleChoiceEntry
     */
    public function getCorrectEntry(): MultipleChoiceEntry
    {
        return $this->correctEntry;
    }

    public function toCharacter(MultipleChoiceEntry $entry){
        $ar = ['A','B','C','D','E','F','G','H','I','K'];
       foreach (Collection::make($this->entries->matching(Criteria::create()->orderBy(['order' => 'ASC'])))->values() as $key => $value){
           if($entry == $value){
               return $ar[$key];
           }
       }
       return 'N';
    }

    public function answers(){
        return $this->responses;
    }

    public function answersBySession($session){
        return $this->responses->matching(Criteria::create()->where(Criteria::expr()->eq('session',$session)));
    }

    /**
     * @return PersistentCollection
     */
    public function getEntries(): PersistentCollection
    {
        return $this->entries;
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

    public function setCorrectAnswer($entry){
        $this->correctEntry = $entry;
    }

}