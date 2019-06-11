<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Form\ClassroomType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/", name="index")
     */
    public function indexAction(Request $request)
    {
        return $this->handleView($this->view()
            ->setTemplate('default/index.html.twig')
            ->setTemplateData(['base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR]));

    }

}