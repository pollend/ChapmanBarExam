<?php


namespace App\Entities;


use App\Entities\ShortAnswerResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\QuestionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_response")
 */
class QuizResponse
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
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="QuizSession",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="session_id",referencedColumnName="id")
     * })
     */
    protected $session;

    /**
     * @var ArrayCollection
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
     * @param ArrayCollection $session
     */
    public function setSession(ArrayCollection $session): QuizResponse
    {
        $this->session = $session;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSession(): ArrayCollection
    {
        return $this->session;
    }
}