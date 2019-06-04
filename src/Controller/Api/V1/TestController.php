<?php

namespace App\Controller\Api\V1;


use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/v1/")
 */
class TestController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("test",
     *     options = { "expose" = true },
     *     name="get_test")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function getTest(Request $request,UserPasswordEncoderInterface $encoder)
    {
        return $this->view(['test' => 'hello']);
    }

}