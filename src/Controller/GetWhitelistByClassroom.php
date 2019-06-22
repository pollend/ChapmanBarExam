<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Entity\UserWhitelist;

class GetWhitelistByClassroom
{

    public function __construct()
    {
    }

    public function __invoke(Classroom $classroom)
    {
        $result = [];
        /** @var UserWhitelist $whitelist */
        foreach ($classroom->getEmailWhitelist() as $whitelist){
            $result[] = $whitelist->getEmail();
        }
        return $result;

    }
}