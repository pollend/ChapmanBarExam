<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MultipleChoiceResponseRepository")
 * @ORM\Table(name="multiple_choice_response")
 * @ORM\HasLifecycleCallbacks
 */
class MultipleChoiceResponse extends QuizResponse
{
    /**
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="choice_entry_id", referencedColumnName="id")
     * })
     * @JMS\Groups({"user_response"})
     */
    protected $choice;

    /**
     * @return Quiz
     */
    public function getChoice(): MultipleChoiceEntry
    {
        return $this->choice;
    }

    /**
     * @param MultipleChoiceEntry $choice
     */
    public function setChoice(MultipleChoiceEntry $choice): void
    {
        $this->choice = $choice;
    }
}
