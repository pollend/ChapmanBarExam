<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AuthMe
{
    private  $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        return $this->security->getUser();
    }

}