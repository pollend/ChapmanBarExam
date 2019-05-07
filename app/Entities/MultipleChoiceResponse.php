<?php

namespace App\Entities;

use App\QuizResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
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
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    protected $id;

    public $timestamps = false;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="MultipleChoiceEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="multiple_choice_entry_id", referencedColumnName="id")
     * })
     */
    protected $multipleChoiceEntry;

    /**
     * @var QuizSession
     * @ORM\ManyToOne(targetEntity="QuizSession")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_session_id", referencedColumnName="id")
     * })
     */
    protected $session;

    function scopeBySession($query, $session)
    {
        // TODO: Implement scopeBySession() method.
    }
}