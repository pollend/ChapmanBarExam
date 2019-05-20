<?php


namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class UserWhiteList
 * @ORM\Entity()
 * @ORM\Table(name="user_white_list")
 */
class UserWhiteList
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
     * @ORM\Column(name="email",type="string",length=50,nullable=false)
     * @JMS\Groups({"list","detail"})
     */
    protected $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

}