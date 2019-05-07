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
     *   @ORM\JoinColumn(name="multiple_choice_entry_id", referencedColumnName="id")
     * })
     */
    protected $multipleChoiceEntry;

    /**
     * @var MultipleChoiceQuestion
     * @ORM\ManyToOne(targetEntity="MultipleChoiceQuestion",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="multiple_choice_question_id",referencedColumnName="id")
     * })
     */
    protected $question;

    /**
     * @return Quiz
     */
    public function getMultipleChoiceEntry(): MultipleChoiceEntry
    {
        return $this->multipleChoiceEntry;
    }

}