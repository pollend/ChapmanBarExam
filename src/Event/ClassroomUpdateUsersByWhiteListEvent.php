<?php


namespace App\Event;


use App\Entity\Classroom;

/**
 *
 * Class ClassroomUpdateUsersByWhiteListEvent
 * @package App\Event
 */
class ClassroomUpdateUsersByWhiteListEvent
{
    const CLASSROOM_UPDATE_USERLIST = 'app.classroom.update_user_list';

    private $classroom;

    /**
     *
     * @var array
     */
    private $usersAdded;

    public function __construct(Classroom $classroom)
    {
        $this->classroom = $classroom;
        $this->usersAdded = [];
    }

    /**
     * @return Classroom
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;
    }
    /**
     * @return array
     */
    public function getAddedUsers(): array
    {
        return $this->usersAdded;
    }

    public function addUserAdded($user)
    {
        $this->usersAdded[] = $user;
    }

}