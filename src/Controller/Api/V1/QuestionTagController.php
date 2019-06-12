<?php

namespace App\Controller\Api\V1;

use App\Entity\QuestionTag;
use App\Repository\QuestionTagRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/")
 */
class QuestionTagController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("question/tag",
     *     options = { "expose" = true },
     *     name="get_question_tag")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getTags(Request $request)
    {
        $tag = $request->get('tag', '');

        /** @var QuestionTagRepository $repository */
        $repository = $this->getDoctrine()->getRepository(QuestionTag::class);

        return $this->view(['tags' => $repository->filter($tag)->toArray()]);
    }

    /**
     * @Rest\Put("question/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_question_tag")
     * @Rest\View(serializerGroups={"list"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function putTag(Request $request, $tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QuestionTagRepository $repository */
        $repository = $this->getDoctrine()->getRepository(QuestionTag::class);
        if (!$repository->findOneBy(['name' => $tag])) {
            $tag = new QuestionTag();
            $tag->setName($tag);
            $em->persist($tag);
            $em->flush();

            return $this->view($tag);
        }

        return $this->createNotFoundException();
    }

    /**
     * @Rest\Put("question/tag/{tag}",
     *     options = { "expose" = true },
     *     name="get_question_tag")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getTag(Request $request, $tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QuestionTagRepository $repository */
        $repository = $this->getDoctrine()->getRepository(QuestionTag::class);
        if ($tag = $repository->findOneBy(['name' => $tag])) {
            return $this->view($tag);
        }

        return $this->createNotFoundException();
    }
}
