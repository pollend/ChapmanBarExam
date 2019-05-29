<?php


namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;


/**
 * Class UserWhitelist
 *
 * @ORM\Entity()
 * @ORM\Table(name="`user_whitelist`")
 * @ORM\HasLifecycleCallbacks
 *
 */
class UserWhitelist
{
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
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="whitelists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     * })
     *
     * @JMS\Groups({"list","detail"})
     */
    protected $classroom;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=50,nullable=false)
     * @JMS\Groups({"list"})
     */
    protected $email;

    public function setClassroom(Classroom $classroom): void
    {
        $this->classroom = $classroom;
    }

    /**
     * @return Quiz
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

}