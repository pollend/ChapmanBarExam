<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Doctrine\ORM\Mapping AS ORM;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * @ORM\Entity()
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 *
 */
class User extends Authenticatable
{

    use HasApiTokens, Notifiable;
    use TimestampTrait;

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
     * @ORM\Column(name="name",type="string",length=100,nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=100,nullable=false)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="email_verified_at",type="datetime",nullable=false)
     */
    protected $emailVerifiedAt;

    /**
     * @var string
     * @ORM\Column(name="password",type="string",length=255,nullable=false)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="azure_id",type="string",length=50,nullable=false)
     */
    protected $azureId;

    /**
     * @var string
     * @ORM\Column(name="remember_token",type="string",length=100,nullable=false)
     */
    protected $rememberToken;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="owner")
     */
    protected $quizSessions;

    public function getId(){
        return $this->id;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }


}
