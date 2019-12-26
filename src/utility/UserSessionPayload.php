<?php


namespace App\utility;


use App\Entity\QuizSession;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class UserSessionPayload
{
    /**
     * @Groups({"quiz_session:get"})
     */
    private $user;

    /**
     * @Groups({"quiz_session:get"})
     */
    private $sessions;

    public function __construct(User $user,array $sessions)
    {
        $this->user = $user;
        $this->sessions = $sessions;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getSessions()
    {
        return $this->sessions;
    }

}
