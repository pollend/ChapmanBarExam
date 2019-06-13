<?php

namespace App\Controller\Api\V1;

use App\Entity\QuestionTag;
use App\Repository\QuestionTagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class QuestionTagController extends AbstractController
{
    /**
     * @Route("question/tag",
     *     options = { "expose" = true },
     *     name="get_question_tag", methods={"GET"})
     */
    public function getTags(Request $request)
    {
        $tag = $request->get('tag', '');

        /** @var QuestionTagRepository $repository */
        $repository = $this->getDoctrine()->getRepository(QuestionTag::class);

        return $this->json(['tags' => $repository->filter($tag)->toArray()],200,[],['groups' => ['list']]);
    }

    /**
     * @Route("question/tag/{tag}",
     *     options = { "expose" = true },
     *     name="put_question_tag", methods={"PUT"})
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

            return $this->json($tag,200,[],['groups' => ['list']]);
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("question/tag/{tag}",
     *     options = { "expose" = true },
     *     name="get_question_tag", methods={"GET"})
     */
    public function getTag(Request $request, $tag)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QuestionTagRepository $repository */
        $repository = $this->getDoctrine()->getRepository(QuestionTag::class);
        if ($tag = $repository->findOneBy(['name' => $tag])) {
            return $this->json($tag,200,[],['groups' => ['list']]);
        }

        return $this->createNotFoundException();
    }
}
