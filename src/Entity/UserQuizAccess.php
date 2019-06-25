<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class UserQuizAccess
 * @package App\Entity
 *
 * @ORM\Table(name="user_quiz_access")
 * @ORM\Entity(readOnly=true,repositoryClass="App\Repository\UserQuizAccessRepository")
 * @ApiResource(
 *     itemOperations={
 *      "get",
 *     },
 *     collectionOperations={
 *      "get"
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class,properties={"owner" = "exact"})
 */
class UserQuizAccess
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="bigint")
     * @ORM\OneToOne(targetEntity="QuizAccess")
     * @ORM\JoinColumn(name="id",referencedColumnName="id")
     */
    protected $id;

    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="Quiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    protected $quiz;

    /**
     * @var bool
     * @ORM\Column(name="is_hidden",type="boolean")
     */
    protected $isHidden;

    /**
     * @var int
     * @ORM\Column(name="num_attempts",type="integer")
     */
    protected $numAttempts;

    /**
     * @var \DateTime
     * @ORM\Column(name="open_date",type="datetime",nullable=true)
     */
    protected $openDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     */
    protected $closeDate;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     *
     */
    protected $owner;

    /**
     * @var int
     * @ORM\Column(name="user_attempts",type="integer")
     */
    protected $userAttempts;


    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return int
     */
    public function getUserAttempts(): int
    {
        return $this->userAttempts;
    }

    /**
     * @return int
     */
    public function getNumAttempts(): int
    {
        return $this->numAttempts;
    }


}