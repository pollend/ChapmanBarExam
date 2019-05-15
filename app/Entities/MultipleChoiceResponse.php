<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity()
 * @ORM\Table(name="multiple_choice_response")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MultipleChoiceResponse extends  QuizResponse
{

    /**
     * @var MultipleChoiceEntry
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="choice_entry_id", referencedColumnName="id")
     * })
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