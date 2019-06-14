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
 * @ApiResource()
 */
class MultipleChoiceResponse extends QuizResponse
{
    /**
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="choice_entry_id", referencedColumnName="id")
     * })
     * @Groups({"user_response"})
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
