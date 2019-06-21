<?php


namespace App\Serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Classroom;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class ClassroomContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;

    }

    /**
     * Creates a serialization context from a Request.
     *
     * @throws RuntimeException
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request,$normalization,$extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;


        if(isset($context['groups']) && $this->authorizationChecker->isGranted("ROLE_ADMIN")){

            $role_permissions = [];
            foreach ($context['groups'] as $group){
                $converter = new CamelCaseToSnakeCaseNameConverter();
                if(strpos($group, $converter->normalize($resourceClass)) !== false){
                    $role_permissions[] = $group . ':' . 'ROLE_ADMIN';
                }
            }
            $context['groups'] = array_merge($context['groups'],$role_permissions);
        }
        return $context;
    }
}