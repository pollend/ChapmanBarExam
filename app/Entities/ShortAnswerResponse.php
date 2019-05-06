<?php

namespace App\Entities;


use App\QuizResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;
/**
 * Class Quiz
 * @package App
 * @ORM\Entity()
 * @ORM\Table(name="short_answer_response")
 * @ORM\HasLifecycleCallbacks
 *
 */
class ShortAnswerResponse extends QuizResponse
{
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
     * @ORM\Column(name="content", type="text",nullable=true)
     */
    protected $content;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="ShortAnswerQuestion",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="short_answer_question_id",referencedColumnName="id")
     * })
     */
    protected $question;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="QuizSession")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="session_id",referencedColumnName="id")
     * })
     */
    protected $session;

    function scopeBySession($query, $session)
    {
        // TODO: Implement scopeBySession() method.
    }
}