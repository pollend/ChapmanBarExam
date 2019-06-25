<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\MultipleChoiceResponseRepository")
 * @ORM\Table(name="multiple_choice_response")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 */
class MultipleChoiceResponse extends QuizResponse
{
    /**
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="choice_entry_id", referencedColumnName="id")
     * })
     * @Groups({"quiz_response:get"})
     */
    protected $choice;

    /**
     * @return MultipleChoiceEntry
     */
    public function getChoice(): MultipleChoiceEntry
    {
        return $this->choice;
    }

    /**
     * @param MultipleChoiceEntry $choice
     *
     */
    public function setChoice(MultipleChoiceEntry $choice): void
    {
        $this->choice = $choice;
    }

    /**
     * @return bool
     * @Groups({"quiz_response:get"})
     */
    public function isCorrectResponse()
    {
        $question = $this->getQuestion();
        if($question instanceof MultipleChoiceQuestion){
            return $question->getCorrectEntry() === $this->getChoice();

        }
        return false;

    }
}
