<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="quiz_session_breakdown")
 * @ORM\Entity(readOnly=true)
 * @ApiResource(
 *     itemOperations={
 *      "get"= {"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *      "get" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     paginationEnabled=false
 * )
 */
class QuizSessionBreakdown
{

    /**
     * @ORM\Id()
     * @ORM\Column(name="id",type="bigint")
     * @ApiProperty(identifier=true)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    protected $quiz;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Classroom")
     * @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     */
    protected $classroom;

    /**
     * @ORM\Column(name="average_score",type="integer")
     */
    protected $averageScore;

    /**
     * @ORM\Column(name="max_score",type="integer")
     */
    protected $maxScore;

    /**
     * @ORM\Column(name="target_score",type="integer")
     */
    protected $targetScore;

    /**
     * @ORM\Column(name="attempts",type="integer")
     */
    protected $attempts;

    /**
     * @return mixed
     */
    public function getTargetScore()
    {
        return $this->targetScore;
    }

    /**
     * @return mixed
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAverageScore()
    {
        return $this->averageScore;
    }

    /**
     * @return mixed
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * @return mixed
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getClassroom()
    {
        return $this->classroom;
    }
}