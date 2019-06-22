<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class UserWhitelist.
 *
 * @ORM\Entity()
 * @ORM\Table(name="`user_whitelist`")
 * @ORM\HasLifecycleCallbacks
 */
class UserWhitelist
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     *
     */
    protected $id;

    /**
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="emailWhitelist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     * })
     */
    protected $classroom;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=50,nullable=false)
     */
    protected $email;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setClassroom(Classroom $classroom): UserWhitelist
    {
        $this->classroom = $classroom;

        return $this;
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
    public function setEmail(string $email): UserWhitelist
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
