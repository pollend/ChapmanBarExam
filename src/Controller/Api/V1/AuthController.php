<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace App\Controller\Api\V1;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * @Route("/api/v1/")
 */
class AuthController extends AbstractFOSRestController
{

    private $jwtManager;
    public function __construct(JWTTokenManagerInterface $JWTManager)
    {
        $this->jwtManager = $JWTManager;
    }

    /**
     * @Rest\Get("auth/me",
     *     options = { "expose" = true },
     *     name="get_me")
     * @Rest\View(serializerGroups={"owner"})
     */
    public function getMe(){
        return $this->view(['user' => $this->getUser()]);
    }


}