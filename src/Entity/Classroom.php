<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @package App\Entities
 *
 * @ORM\Entity(repositoryClass="App\Repository\ClassroomRepository")
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
     * @JMS\Groups({"whitelists"})
     */
    protected $whitelists;

    /**
     * @var ArrayCollection
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="classes")
     * @ORM\JoinTable(name="classroom_user")
     * @JMS\Groups({"registered"})
     */
    protected $users;

    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizAccess",mappedBy="classroom")
     * @JMS\Groups({"access"})
     */
    protected $quizAccess;

    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="classroom")
     * @JMS\Groups({"quiz_sessions"})
     */
    protected $quizSessions;

    /**
     * @JMS\VirtualProperty
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


    public function isUserRegistered(User $user){
        return $this->getUsers()->matching(Criteria::create()->where(Criteria::expr()->eq('id',$user->getId())))->count() > 0;
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
     * @return Classroom
     */
    public function setName(string $name): Classroom
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}