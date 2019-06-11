<?php

namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation As JMS;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank(message="Classroom Name Required")
     * @Assert\NotNull()
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description",type="string",length=50,nullable=true)
     * @JMS\Groups({"list","detail"})
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="course_number",type="string",length=50,nullable=true)
     * @JMS\Groups({"list","detail"})
     */
    protected $courseNumber;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserWhitelist",mappedBy="classroom")
     * @JMS\Groups({"classroom_whitelists"})
     */
    protected $emailWhitelist;

    /**
     * @var ArrayCollection
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="classes")
     * @ORM\JoinTable(name="classroom_user")
     * @JMS\Groups({"classroom_registered"})
     */
    protected $users;

    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizAccess",mappedBy="classroom")
     * @JMS\Groups({"classroom_access"})
     */
    protected $quizAccess;

    /**
     * @var ArrayCollection
     * One Product has One Shipment.
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="classroom")
     * @JMS\Groups({"classroom_quiz_sessions"})
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
     * @return ArrayCollection
     */
    public function getEmailWhitelist(): ArrayCollection
    {
        return $this->emailWhitelist;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCourseNumber(): ?string
    {
        return $this->courseNumber;
    }

    /**
     * @param string $courseNumber
     */
    public function setCourseNumber(string $courseNumber): void
    {
        $this->courseNumber = $courseNumber;
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