<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizResponseRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_response")
 *
 * @JMS\Discriminator(field = "type", map = {
 *    "multiple_choice": "App\Entity\MultipleChoiceResponse",
 *    "short_answer": "App\Entity\ShortAnswerResponse"
 * })
 */
abstract class QuizResponse
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"list"})
     */
    protected $id;


    /**
     * @var QuizSession
     * @ORM\ManyToOne(targetEntity="QuizSession",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="session_id",referencedColumnName="id")
     * })
     */
    protected $session;

    /**
     * @var QuizQuestion
     * @ORM\ManyToOne(targetEntity="QuizQuestion",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_id",referencedColumnName="id")
     * })
     */
    protected $question;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param QuizQuestion $question
     */
    public function setQuestion(QuizQuestion $question): QuizResponse
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestion(): QuizQuestion
    {
        return $this->question;
    }

    /**
     * @param ArrayCollection $session
     */
    public function setSession(QuizSession $session): QuizResponse
    {
        $this->session = $session;
        return $this;
    }

    /**
     * @return QuizSession
     */
    public function getSession(): QuizSession
    {
        return $this->session;
    }
    
}