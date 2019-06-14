<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizResponseRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_response")
 *
 * @DiscriminatorMap(typeProperty = "type", mapping= {
 *    "multiple_choice": "App\Entity\MultipleChoiceResponse",
 *    "short_answer": "App\Entity\ShortAnswerResponse"
 * })
 * @ApiResource()
 */
abstract class QuizResponse
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"list","detail"})
     */
    protected $id;

    /**
     * @var QuizSession
     * @ORM\ManyToOne(targetEntity="QuizSession",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="session_id",referencedColumnName="id")
     * })
     * @Groups({"session_info"})
     */
    protected $session;

    /**
     * @var QuizQuestion
     * @ORM\ManyToOne(targetEntity="QuizQuestion",inversedBy="responses")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_id",referencedColumnName="id")
     * })
     * @Groups({"detail"})
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
