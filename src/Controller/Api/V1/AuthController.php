<?php

// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved

namespace App\Controller\Api\V1;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class AuthController extends AbstractController
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $JWTManager)
    {
        $this->jwtManager = $JWTManager;
    }

    /**
     * @Route("auth/me",
     *     options = { "expose" = true },
     *     name="get_me",methods={"GET"})
     */
    public function getMe()
    {
        return $this->json(['user' => $this->getUser()],200,[],['groups'=>['owner']]);
    }
}
