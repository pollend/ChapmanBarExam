<?php


namespace App\Entities;

use App\Entities\Traits\SoftDeleteTrait;
use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @package App\Entities
 *
 * @ORM\Entity(repositoryClass="App\Repositories\ClassroomRepository")
 * @ORM\Table(name="classroom")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Classroom
{
    use TimestampTrait;
    use SoftDeleteTrait;


    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("int")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     * @JMS\Groups({"list","detail"})
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserWhitelist",mappedBy="classroom")
     */
    protected $whitelists;

    /**
     * @var ArrayCollection
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="classes")
     * @ORM\JoinTable(name="classroom_user")
     * @JMS\Groups({"detail"})
     */
    protected $users;


    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizAccess",mappedBy="classroom")
     * @JMS\Groups({"detail"})
     */
    protected $quizAccess;

    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="classroom")
     *
     */
    protected $quizSessions;



    /**
     * @JMS\VirtualProperty
     * @JMS\Groups({"detail","list"})
     * @return string
     */
    public function getNumberOfStudents(){
        return $this->users->count();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getWhitelist() : PersistentCollection
    {
        return $this->whitelists;
    }


    public function addToWhitelist(UserWhitelist $whitelist){
        $this->whitelists->add($whitelist);
    }


    /**
     * @return mixed
     */
    public function getQuizAccess() : PersistentCollection
    {
        return $this->quizAccess;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}