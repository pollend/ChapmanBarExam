<?php

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Support\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MultipleChoiceQuestionRepository")
 * @ORM\Table(name="multiple_choice_question")
 * @ORM\HasLifecycleCallbacks
 */
class MultipleChoiceQuestion extends QuizQuestion
{
    /**
     * @var string
     * @ORM\Column(name="content",type="text",nullable=false)
     * @JMS\Groups({"detail"})
     */
    protected $content;

    /**
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="correct_entry_id", referencedColumnName="id")
     * })
     * @JMS\Groups({"question_correct_answer"})
     */
    protected $correctEntry;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="MultipleChoiceResponse",mappedBy="question")
     * @JMS\Groups({"responses"})
     */
    protected $responses;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="MultipleChoiceEntry",mappedBy="question")
     * @JMS\Groups({"detail"})
     */
    protected $entries;

    /**
     * @return MultipleChoiceEntry
     */
    public function getCorrectEntry(): MultipleChoiceEntry
    {
        return $this->correctEntry;
    }

    public function toCharacter(MultipleChoiceEntry $entry)
    {
        $ar = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'K'];
        foreach (Collection::make($this->entries->matching(Criteria::create()->orderBy(['order' => 'ASC'])))->values() as $key => $value) {
            if ($entry == $value) {
                return $ar[$key];
            }
        }

        return 'N';
    }

    public function answers()
    {
        return $this->responses;
    }

    public function answersBySession($session)
    {
        return $this->responses->matching(Criteria::create()->where(Criteria::expr()->eq('session', $session)));
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
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function setCorrectAnswer($entry)
    {
        $this->correctEntry = $entry;
    }
}
