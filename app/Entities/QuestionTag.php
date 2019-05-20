<?php


namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\QuestionTagRepository")
 * @ORM\Table(name="question_tag")
 */
class QuestionTag
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
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     * @JMS\Groups({"list","detail"})
     */
    protected $name;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="QuizQuestion", inversedBy="tags")
     * @ORM\JoinTable(name="quiz_question_question_tag")
     */
    protected $questions;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}